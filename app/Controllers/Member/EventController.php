<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use App\Services\AuditLogService;
use App\Services\NotificationEmailService;

class EventController extends BaseController
{
    protected EventModel $eventModel;
    protected EventRegistrationModel $registrationModel;
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;

    public function __construct()
    {
        $this->eventModel        = new EventModel();
        $this->registrationModel = new EventRegistrationModel();
        $this->membershipModel   = new MembershipModel();
        $this->profileModel      = new MemberProfileModel();
    }

    /**
     * Member Events Dashboard
     */
    public function index()
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);
        $profile = $this->profileModel->findByUserId((int) $userId);

        // Member's registrations
        $db = \Config\Database::connect();
        $myRegistrations = $db->table('event_registrations r')
            ->select('r.*, e.title as event_title, e.slug as event_slug, e.start_date, e.end_date, e.venue_name, e.city, e.banner_path, att.id as attendance_id, att.checked_in_at')
            ->join('events e', 'e.id = r.event_id')
            ->join('event_attendance att', 'att.registration_id = r.id', 'left')
            ->where('r.user_id', $userId)
            ->orderBy('e.start_date', 'DESC')
            ->get()->getResultArray();

        // Available upcoming events for members (internal & hybrid)
        $registeredEventIds = array_column($myRegistrations, 'event_id');

        $upcomingBuilder = $this->eventModel
            ->where('status', 'published')
            ->whereIn('event_type', ['internal', 'hybrid']);

        if (! empty($registeredEventIds)) {
            $upcomingBuilder->whereNotIn('id', $registeredEventIds);
        }

        $upcomingEvents = $upcomingBuilder
            ->where('start_date >=', date('Y-m-d H:i:s', strtotime('-1 day')))
            ->orderBy('start_date', 'ASC')
            ->findAll();

        return view('member/events/index', [
            'title'           => 'Kegiatan & Acara Saya',
            'membership'      => $membership,
            'profile'         => $profile,
            'myRegistrations' => $myRegistrations,
            'upcomingEvents'  => $upcomingEvents,
        ]);
    }

    /**
     * Register Member to an Event
     */
    public function register(int $eventId)
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);

        // 1. Validate Membership Status
        $memberStatus = is_object($membership) ? (string) $membership->status : (string) ($membership['status'] ?? '');
        if ($memberStatus !== 'approved' && $memberStatus !== 'active') {
            return redirect()->back()->with('error', 'Pendaftaran kegiatan khusus member hanya dapat dilakukan oleh anggota dengan status keanggotaan aktif yang telah disetujui.');
        }

        $event = $this->eventModel->find($eventId);
        if (! $event || $event['status'] !== 'published') {
            return redirect()->back()->with('error', 'Kegiatan tidak ditemukan atau belum dipublikasikan.');
        }

        if ($event['event_type'] === 'external') {
            return redirect()->back()->with('error', 'Kegiatan ini berjenis "External — Peserta Umum". Silakan mendaftar melalui jalur pendaftaran umum di halaman publik.');
        }

        // 2. Validate Registration Period
        $regActive = $this->eventModel->isRegistrationActive($event);
        if (! $regActive['active']) {
            return redirect()->back()->with('error', $regActive['reason']);
        }

        // 3. Validate Quota
        $quotaStatus = $this->eventModel->checkQuotaAvailable($eventId, 'member');
        if (! $quotaStatus['available']) {
            return redirect()->back()->with('error', $quotaStatus['reason']);
        }

        // 4. Prevent duplicate registration
        $existing = $this->registrationModel
            ->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'pending_approval', 'pending_verification'])
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah terdaftar pada kegiatan ini.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $regNumber = EventRegistrationModel::generateRegistrationNumber();
        $ticketTokenData = EventRegistrationModel::generateTicketToken();

        $requiresApproval = (bool) ($event['requires_approval'] ?? false);
        $status = $requiresApproval ? 'pending_approval' : 'confirmed';
        $confirmedAt = $requiresApproval ? null : date('Y-m-d H:i:s');

        $regId = $this->registrationModel->insert([
            'event_id'              => $eventId,
            'registration_number'   => $regNumber,
            'participant_type'      => 'member',
            'user_id'               => $userId,
            'guest_id'              => null,
            'status'                => $status,
            'ticket_token_selector' => $ticketTokenData['selector'],
            'ticket_token_hash'     => $ticketTokenData['hash'],
            'registered_at'         => date('Y-m-d H:i:s'),
            'confirmed_at'          => $confirmedAt,
        ], true);

        AuditLogService::log($userId, 'event.member_register', 'event_registrations', (int) $regId, ['event_id' => $eventId]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pendaftaran kegiatan. Silakan coba kembali.');
        }

        // If confirmed immediately, send ticket email
        if ($status === 'confirmed') {
            $user = auth()->user();
            $ticketUrl = site_url('kegiatan/tiket/' . $ticketTokenData['selector']);
            NotificationEmailService::sendTicketConfirmation(
                $user->email,
                $user->username,
                $event['title'],
                $regNumber,
                $ticketUrl
            );

            return redirect()->to('dashboard/kegiatan')->with('success', 'Pendaftaran berhasil dikonfirmasi! Tiket QR kegiatan telah terbit.');
        }

        return redirect()->to('dashboard/kegiatan')->with('success', 'Pendaftaran berhasil dikirim. Menunggu persetujuan panitia.');
    }

    /**
     * Cancel Registration (if permitted)
     */
    public function cancel(int $regId)
    {
        $userId = auth()->id();
        $reg = $this->registrationModel->where('id', $regId)->where('user_id', $userId)->first();

        if (! $reg) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Cannot cancel if already attended
        $db = \Config\Database::connect();
        $attendance = $db->table('event_attendance')->where('registration_id', $regId)->get()->getRowArray();
        if ($attendance) {
            return redirect()->back()->with('error', 'Tidak dapat membatalkan pendaftaran yang telah melakukan check-in / hadir.');
        }

        $this->registrationModel->update($regId, [
            'status' => 'cancelled',
            'notes'  => 'Dibatalkan sendiri oleh member pada ' . date('d/m/Y H:i'),
        ]);

        AuditLogService::log($userId, 'event.member_cancel', 'event_registrations', $regId);
        return redirect()->back()->with('success', 'Pendaftaran kegiatan berhasil dibatalkan.');
    }
}
