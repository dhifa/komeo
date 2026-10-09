<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventAttendanceModel;
use App\Models\EventCheckinLogModel;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;
use App\Models\EventStaffAssignmentModel;
use App\Models\MembershipModel;
use App\Models\MembershipVerificationLogModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Services\AuditLogService;
use App\Services\EventTicketService;
use App\Services\NotificationEmailService;

class EventController extends BaseController
{
    protected EventModel $eventModel;
    protected EventRegistrationModel $registrationModel;
    protected EventAttendanceModel $attendanceModel;
    protected EventStaffAssignmentModel $staffModel;
    protected EventCheckinLogModel $checkinLogModel;

    public function __construct()
    {
        $this->eventModel        = new EventModel();
        $this->registrationModel = new EventRegistrationModel();
        $this->attendanceModel   = new EventAttendanceModel();
        $this->staffModel        = new EventStaffAssignmentModel();
        $this->checkinLogModel   = new EventCheckinLogModel();
    }

    /**
     * Helper: Check if current user is Super Admin or Admin
     */
    protected function isAdmin(): bool
    {
        $user = auth()->user();
        return $user && ($user->inGroup('superadmin') || $user->inGroup('admin'));
    }

    /**
     * Helper: Check if staff has access to this event
     */
    protected function authorizeEventAccess(int $eventId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $userId = auth()->id();
        return $this->staffModel->isUserAssignedToEvent((int) $userId, $eventId);
    }

    /**
     * List Events
     */
    public function index()
    {
        $userId = auth()->id();
        $builder = $this->eventModel->builder();

        // If event_staff only (not admin), filter only assigned events
        if (! $this->isAdmin()) {
            $builder->join('event_staff_assignments esa', 'esa.event_id = events.id')
                    ->where('esa.user_id', $userId);
        }

        // Filters
        $status = $this->request->getGet('status');
        if (! empty($status)) {
            $builder->where('events.status', $status);
        }

        $type = $this->request->getGet('type');
        if (! empty($type)) {
            $builder->where('events.event_type', $type);
        }

        $search = $this->request->getGet('q');
        if (! empty($search)) {
            $builder->groupStart()
                ->like('events.title', $search)
                ->orLike('events.venue_name', $search)
                ->orLike('events.city', $search)
                ->groupEnd();
        }

        $events = $builder->select('events.*')
            ->orderBy('events.start_date', 'DESC')
            ->get()->getResultArray();

        // Attach stats to each event
        foreach ($events as &$evt) {
            $evt['stats'] = $this->eventModel->getEventStatistics((int) $evt['id']);
        }
        unset($evt);

        return view('admin/events/index', [
            'title'   => 'Manajemen Kegiatan',
            'events'  => $events,
            'isAdmin' => $this->isAdmin(),
            'status'  => $status,
            'type'    => $type,
            'search'  => $search,
        ]);
    }

    /**
     * Form Create Event (Admin Only)
     */
    public function create()
    {
        if (! $this->isAdmin()) {
            return redirect()->to('admin/events')->with('error', 'Hanya administrator yang dapat membuat kegiatan baru.');
        }

        $userModel = new UserModel();
        $allStaff = $userModel->findAll();

        return view('admin/events/form', [
            'title'    => 'Tambah Kegiatan Baru',
            'event'    => null,
            'allStaff' => $allStaff,
            'assignedStaffIds' => [],
        ]);
    }

    /**
     * Store Event (Admin Only)
     */
    public function store()
    {
        if (! $this->isAdmin()) {
            return redirect()->to('admin/events')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'event_type'  => 'required|in_list[internal,external,hybrid]',
            'start_date'  => 'required|valid_date[Y-m-d\TH:i]',
            'end_date'    => 'required|valid_date[Y-m-d\TH:i]',
            'venue_name'  => 'permit_empty|max_length[255]',
            'total_quota' => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = $this->request->getPost('title');
        $slug  = mb_url_title($title, '-', true) . '-' . substr(bin2hex(random_bytes(3)), 0, 5);

        // Handle Banner Upload
        $bannerPath = null;
        $bannerFile = $this->request->getFile('banner');
        if ($bannerFile && $bannerFile->isValid() && ! $bannerFile->hasMoved()) {
            $bannerExt = $bannerFile->getClientExtension();
            if (in_array(strtolower($bannerExt), ['jpg', 'jpeg', 'png', 'webp'])) {
                $newName = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $bannerExt;
                $bannerFile->move(FCPATH . 'uploads/events', $newName);
                $bannerPath = 'uploads/events/' . $newName;
            }
        }

        $data = [
            'title'                => $title,
            'slug'                 => $slug,
            'description'          => $this->request->getPost('description'),
            'banner_path'          => $bannerPath,
            'venue_name'           => $this->request->getPost('venue_name'),
            'address'              => $this->request->getPost('address'),
            'city'                 => $this->request->getPost('city'),
            'start_date'           => str_replace('T', ' ', (string) $this->request->getPost('start_date')),
            'end_date'             => str_replace('T', ' ', (string) $this->request->getPost('end_date')),
            'timezone'             => $this->request->getPost('timezone') ?: 'Asia/Jakarta',
            'event_type'           => $this->request->getPost('event_type'),
            'reg_start_date'       => $this->request->getPost('reg_start_date') ? str_replace('T', ' ', (string) $this->request->getPost('reg_start_date')) : null,
            'reg_end_date'         => $this->request->getPost('reg_end_date') ? str_replace('T', ' ', (string) $this->request->getPost('reg_end_date')) : null,
            'checkin_start_date'   => $this->request->getPost('checkin_start_date') ? str_replace('T', ' ', (string) $this->request->getPost('checkin_start_date')) : null,
            'checkin_end_date'     => $this->request->getPost('checkin_end_date') ? str_replace('T', ' ', (string) $this->request->getPost('checkin_end_date')) : null,
            'total_quota'          => (int) $this->request->getPost('total_quota'),
            'member_quota'         => (int) $this->request->getPost('member_quota'),
            'external_quota'       => (int) $this->request->getPost('external_quota'),
            'requires_approval'    => (int) $this->request->getPost('requires_approval'),
            'is_registration_open' => (int) $this->request->getPost('is_registration_open'),
            'allow_kta_checkin'    => (int) $this->request->getPost('allow_kta_checkin'),
            'status'               => $this->request->getPost('status') ?: 'draft',
            'confirmation_message' => $this->request->getPost('confirmation_message'),
            'privacy_consent_text' => $this->request->getPost('privacy_consent_text'),
            'created_by'           => auth()->id(),
        ];

        $eventId = $this->eventModel->insert($data, true);

        // Assign staff if provided
        $staffIds = $this->request->getPost('staff_ids') ?: [];
        if (is_array($staffIds)) {
            foreach ($staffIds as $sId) {
                if (! empty($sId)) {
                    $this->staffModel->insert([
                        'event_id'    => $eventId,
                        'user_id'     => (int) $sId,
                        'assigned_by' => auth()->id(),
                    ]);
                }
            }
        }

        AuditLogService::log(
            auth()->id(),
            'event.create',
            'events',
            (int) $eventId,
            ['title' => $title, 'status' => $data['status']]
        );

        return redirect()->to('admin/events')->with('success', 'Kegiatan baru berhasil dibuat.');
    }

    /**
     * Form Edit Event
     */
    public function edit(int $id)
    {
        if (! $this->isAdmin()) {
            return redirect()->to('admin/events')->with('error', 'Hanya administrator yang dapat mengubah data kegiatan.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $userModel = new UserModel();
        $allStaff  = $userModel->findAll();

        $assignedStaffRows = $this->staffModel->where('event_id', $id)->findAll();
        $assignedStaffIds  = array_column($assignedStaffRows, 'user_id');

        return view('admin/events/form', [
            'title'            => 'Edit Kegiatan: ' . $event['title'],
            'event'            => $event,
            'allStaff'         => $allStaff,
            'assignedStaffIds' => $assignedStaffIds,
        ]);
    }

    /**
     * Update Event
     */
    public function update(int $id)
    {
        if (! $this->isAdmin()) {
            return redirect()->to('admin/events')->with('error', 'Akses ditolak.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'event_type'  => 'required|in_list[internal,external,hybrid]',
            'start_date'  => 'required|valid_date[Y-m-d\TH:i]',
            'end_date'    => 'required|valid_date[Y-m-d\TH:i]',
            'venue_name'  => 'permit_empty|max_length[255]',
            'total_quota' => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $title = $this->request->getPost('title');

        // Banner update
        $bannerPath = $event['banner_path'];
        $bannerFile = $this->request->getFile('banner');
        if ($bannerFile && $bannerFile->isValid() && ! $bannerFile->hasMoved()) {
            $bannerExt = $bannerFile->getClientExtension();
            if (in_array(strtolower($bannerExt), ['jpg', 'jpeg', 'png', 'webp'])) {
                $newName = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $bannerExt;
                $bannerFile->move(FCPATH . 'uploads/events', $newName);
                $bannerPath = 'uploads/events/' . $newName;
            }
        }

        $data = [
            'title'                => $title,
            'description'          => $this->request->getPost('description'),
            'banner_path'          => $bannerPath,
            'venue_name'           => $this->request->getPost('venue_name'),
            'address'              => $this->request->getPost('address'),
            'city'                 => $this->request->getPost('city'),
            'start_date'           => str_replace('T', ' ', (string) $this->request->getPost('start_date')),
            'end_date'             => str_replace('T', ' ', (string) $this->request->getPost('end_date')),
            'timezone'             => $this->request->getPost('timezone') ?: 'Asia/Jakarta',
            'event_type'           => $this->request->getPost('event_type'),
            'reg_start_date'       => $this->request->getPost('reg_start_date') ? str_replace('T', ' ', (string) $this->request->getPost('reg_start_date')) : null,
            'reg_end_date'         => $this->request->getPost('reg_end_date') ? str_replace('T', ' ', (string) $this->request->getPost('reg_end_date')) : null,
            'checkin_start_date'   => $this->request->getPost('checkin_start_date') ? str_replace('T', ' ', (string) $this->request->getPost('checkin_start_date')) : null,
            'checkin_end_date'     => $this->request->getPost('checkin_end_date') ? str_replace('T', ' ', (string) $this->request->getPost('checkin_end_date')) : null,
            'total_quota'          => (int) $this->request->getPost('total_quota'),
            'member_quota'         => (int) $this->request->getPost('member_quota'),
            'external_quota'       => (int) $this->request->getPost('external_quota'),
            'requires_approval'    => (int) $this->request->getPost('requires_approval'),
            'is_registration_open' => (int) $this->request->getPost('is_registration_open'),
            'allow_kta_checkin'    => (int) $this->request->getPost('allow_kta_checkin'),
            'status'               => $this->request->getPost('status') ?: 'draft',
            'confirmation_message' => $this->request->getPost('confirmation_message'),
            'privacy_consent_text' => $this->request->getPost('privacy_consent_text'),
        ];

        $this->eventModel->update($id, $data);

        // Update Staff Assignments
        $this->staffModel->where('event_id', $id)->delete();
        $staffIds = $this->request->getPost('staff_ids') ?: [];
        if (is_array($staffIds)) {
            foreach ($staffIds as $sId) {
                if (! empty($sId)) {
                    $this->staffModel->insert([
                        'event_id'    => $id,
                        'user_id'     => (int) $sId,
                        'assigned_by' => auth()->id(),
                    ]);
                }
            }
        }

        AuditLogService::log(
            auth()->id(),
            'event.update',
            'events',
            $id,
            ['title' => $title, 'status' => $data['status']]
        );

        return redirect()->to('admin/events')->with('success', 'Perubahan kegiatan berhasil disimpan.');
    }

    /**
     * Manage Participants for an Event
     */
    public function participants(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return redirect()->to('admin/events')->with('error', 'Anda tidak memiliki hak akses untuk kegiatan ini.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('event_registrations r')
            ->select('r.*, u.username, ai.secret as user_email, m.member_number as membership_number, m.status as membership_status, mp.full_name as member_name, g.name as guest_name, g.email as guest_email, g.whatsapp as guest_whatsapp, g.company as guest_company, att.id as attendance_id, att.checked_in_at')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('auth_identities ai', "ai.user_id = u.id AND ai.type = 'email_password'", 'left')
            ->join('memberships m', 'm.user_id = u.id', 'left')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('event_guests g', 'g.id = r.guest_id', 'left')
            ->join('event_attendance att', 'att.registration_id = r.id', 'left')
            ->where('r.event_id', $id);

        // Filters
        $type = $this->request->getGet('type');
        if (! empty($type)) {
            $builder->where('r.participant_type', $type);
        }

        $status = $this->request->getGet('status');
        if (! empty($status)) {
            $builder->where('r.status', $status);
        }

        $search = $this->request->getGet('q');
        if (! empty($search)) {
            $builder->groupStart()
                ->like('r.registration_number', $search)
                ->orLike('mp.full_name', $search)
                ->orLike('g.name', $search)
                ->orLike('g.email', $search)
                ->orLike('ai.secret', $search)
                ->orLike('m.member_number', $search)
                ->groupEnd();
        }

        $participants = $builder->orderBy('r.id', 'DESC')->get()->getResultArray();
        $stats = $this->eventModel->getEventStatistics($id);

        return view('admin/events/participants', [
            'title'        => 'Daftar Peserta: ' . $event['title'],
            'event'        => $event,
            'participants' => $participants,
            'stats'        => $stats,
            'isAdmin'      => $this->isAdmin(),
            'type'         => $type,
            'status'       => $status,
            'search'       => $search,
        ]);
    }

    /**
     * Approve Participant Registration
     */
    public function approveParticipant(int $eventId, int $regId)
    {
        if (! $this->isAdmin()) {
            return redirect()->back()->with('error', 'Hanya admin yang dapat menyetujui pendaftaran.');
        }

        $reg = $this->registrationModel->where('event_id', $eventId)->where('id', $regId)->first();
        if (! $reg) {
            return redirect()->back()->with('error', 'Data registrasi tidak ditemukan.');
        }

        $this->registrationModel->update($regId, [
            'status'       => 'confirmed',
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);

        // Send ticket confirmation email
        $regDetails = $this->registrationModel->getWithDetails($regId);
        if ($regDetails && ! empty($regDetails['participant_email'])) {
            $ticketService = new EventTicketService();
            $ticketUrl = site_url('kegiatan/tiket/' . $regDetails['ticket_token_selector']);
            NotificationEmailService::sendTicketConfirmation(
                $regDetails['participant_email'],
                $regDetails['participant_name'],
                $regDetails['event_title'],
                $regDetails['registration_number'],
                $ticketUrl
            );
        }

        AuditLogService::log(auth()->id(), 'participant.approve', 'event_registrations', $regId);
        return redirect()->back()->with('success', 'Peserta berhasil disetujui dan tiket telah diterbitkan.');
    }

    /**
     * Reject Participant Registration
     */
    public function rejectParticipant(int $eventId, int $regId)
    {
        if (! $this->isAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $reason = $this->request->getPost('reason') ?: 'Pendaftaran tidak memenuhi kualifikasi kegiatan.';

        $this->registrationModel->update($regId, [
            'status' => 'rejected',
            'notes'  => $reason,
        ]);

        AuditLogService::log(auth()->id(), 'participant.reject', 'event_registrations', $regId, ['reason' => $reason]);
        return redirect()->back()->with('success', 'Pendaftaran peserta berhasil ditolak.');
    }

    /**
     * Attendance Dashboard
     */
    public function attendance(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return redirect()->to('admin/events')->with('error', 'Akses ditolak.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $stats = $this->eventModel->getEventStatistics($id);

        // Fetch recent check-ins
        $db = \Config\Database::connect();
        $recentCheckins = $db->table('event_attendance att')
            ->select('att.*, r.registration_number, r.participant_type, mp.full_name as member_name, g.name as guest_name, u.username as staff_name')
            ->join('event_registrations r', 'r.id = att.registration_id')
            ->join('users staff', 'staff.id = att.checked_in_by', 'left')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('event_guests g', 'g.id = r.guest_id', 'left')
            ->where('att.event_id', $id)
            ->orderBy('att.checked_in_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        return view('admin/events/attendance', [
            'title'          => 'Presensi & Kehadiran: ' . $event['title'],
            'event'          => $event,
            'stats'          => $stats,
            'recentCheckins' => $recentCheckins,
            'isAdmin'        => $this->isAdmin(),
        ]);
    }

    /**
     * Export Attendance CSV (Safe formula injection protection)
     */
    public function exportCsv(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return redirect()->to('admin/events')->with('error', 'Akses ditolak.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $rows = $db->table('event_registrations r')
            ->select('r.registration_number, r.participant_type, r.status as registration_status, r.registered_at, mp.full_name as member_name, m.member_number as membership_number, g.name as guest_name, g.email as guest_email, g.whatsapp as guest_whatsapp, g.company as guest_company, att.checked_in_at, att.checkin_method, staff.username as staff_username')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('memberships m', 'm.user_id = u.id', 'left')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('event_guests g', 'g.id = r.guest_id', 'left')
            ->join('event_attendance att', 'att.registration_id = r.id', 'left')
            ->join('users staff', 'staff.id = att.checked_in_by', 'left')
            ->where('r.event_id', $id)
            ->orderBy('r.id', 'ASC')
            ->get()->getResultArray();

        $filename = 'Presensi_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $event['title']) . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header
        fputcsv($output, [
            'No. Registrasi',
            'Tipe Peserta',
            'Nama Lengkap',
            'No. Anggota / Instansi',
            'Email',
            'WhatsApp',
            'Status Registrasi',
            'Status Kehadiran',
            'Waktu Check-in',
            'Metode Check-in',
            'Petugas Check-in',
        ]);

        foreach ($rows as $row) {
            $name = $row['participant_type'] === 'member' ? ($row['member_name'] ?: 'Member') : ($row['guest_name'] ?: 'Umum');
            $identity = $row['participant_type'] === 'member' ? ($row['membership_number'] ?: '-') : ($row['guest_company'] ?: '-');
            $email = $row['participant_type'] === 'member' ? '-' : ($row['guest_email'] ?: '-');
            $wa = $row['participant_type'] === 'member' ? '-' : ($row['guest_whatsapp'] ?: '-');
            $attended = ! empty($row['checked_in_at']) ? 'HADIR' : 'BELUM HADIR';

            // Protect against spreadsheet formula injection (=, +, -, @)
            $cleanRow = [
                $this->sanitizeCsvField($row['registration_number']),
                $row['participant_type'] === 'member' ? 'Member KOMEO' : 'Peserta Umum',
                $this->sanitizeCsvField($name),
                $this->sanitizeCsvField($identity),
                $this->sanitizeCsvField($email),
                $this->sanitizeCsvField($wa),
                strtoupper($row['registration_status']),
                $attended,
                $row['checked_in_at'] ? date('d/m/Y H:i:s', strtotime($row['checked_in_at'])) : '-',
                $row['checkin_method'] ?: '-',
                $this->sanitizeCsvField($row['staff_username'] ?: '-'),
            ];

            fputcsv($output, $cleanRow);
        }

        fclose($output);
        exit();
    }

    /**
     * Prevent CSV Formula Injection
     */
    protected function sanitizeCsvField(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $firstChar = substr($value, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Live Check-in Interface (Scanner + Manual Search)
     */
    public function checkin(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return redirect()->to('admin/events')->with('error', 'Akses ditolak.');
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('admin/events')->with('error', 'Kegiatan tidak ditemukan.');
        }

        $window = $this->eventModel->isCheckinWindowOpen($event);

        return view('admin/events/checkin', [
            'title'       => 'Check-in & Presensi: ' . $event['title'],
            'event'       => $event,
            'isWindowOpen'=> $window['allowed'],
            'windowReason'=> $window['reason'],
        ]);
    }

    /**
     * Process Check-in API (Supports QR Ticket, Member KTA QR, or Manual Code)
     */
    public function processCheckin(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses staf tidak sah.']);
        }

        $event = $this->eventModel->find($id);
        if (! $event) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kegiatan tidak ditemukan.']);
        }

        // 1. Check check-in window
        $window = $this->eventModel->isCheckinWindowOpen($event);
        if (! $window['allowed']) {
            return $this->response->setJSON(['status' => 'error', 'message' => $window['reason']]);
        }

        $rawInput = trim((string) $this->request->getPost('identifier'));
        if (empty($rawInput)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Identitas / kode scan tidak boleh kosong.']);
        }

        $db = \Config\Database::connect();
        $staffUserId = auth()->id();

        // 2. Determine scanned token type
        $registration = null;
        $tokenType = 'manual';

        // Check if input is a URL (e.g. https://.../kegiatan/tiket/{selector} or https://.../verifikasi/{kta_token})
        if (str_contains($rawInput, '/kegiatan/tiket/')) {
            $parts = explode('/kegiatan/tiket/', $rawInput);
            $tokenSelector = trim(end($parts), " /#?&\t\n\r\0\x0B");
            $registration = $this->registrationModel->where('ticket_token_selector', $tokenSelector)->first();
            $tokenType = 'event_ticket';
        } elseif (str_contains($rawInput, '/verifikasi/')) {
            // Member KTA QR Scanned
            $parts = explode('/verifikasi/', $rawInput);
            $ktaToken = trim(end($parts), " /#?&\t\n\r\0\x0B");
            $tokenType = 'kta_qr';

            if (! (bool) ($event['allow_kta_checkin'] ?? true)) {
                $this->logCheckinAttempt($id, null, 'kta_qr', $rawInput, 'failed', 'Kegiatan tidak mengizinkan presensi menggunakan KTA.', $staffUserId);
                return $this->response->setJSON(['status' => 'error', 'message' => 'Kegiatan ini tidak mengaktifkan presensi menggunakan KTA Member. Harap tunjukkan Tiket QR Kegiatan.']);
            }

            // Find member by verification token
            $vRecord = $db->table('membership_verifications')->where('token', $ktaToken)->get()->getRowArray();
            if ($vRecord) {
                $membership = $db->table('memberships')->where('id', $vRecord['membership_id'])->get()->getRowArray();
                if ($membership) {
                    // Find registration for this user in this event
                    $registration = $this->registrationModel
                        ->where('event_id', $id)
                        ->where('user_id', $membership['user_id'])
                        ->first();
                }
            }
        } elseif (str_starts_with(strtoupper($rawInput), 'EVT-')) {
            // Registration Number entered
            $registration = $this->registrationModel
                ->where('event_id', $id)
                ->where('registration_number', strtoupper($rawInput))
                ->first();
            $tokenType = 'manual';
        } else {
            // Could be selector directly, or member number, or name
            $registration = $this->registrationModel
                ->where('event_id', $id)
                ->where('ticket_token_selector', $rawInput)
                ->first();

            if ($registration) {
                $tokenType = 'event_ticket';
            } else {
                // Try searching by Member Number
                $membership = $db->table('memberships')
                    ->where('member_number', $rawInput)
                    ->get()->getRowArray();

                if ($membership) {
                    $registration = $this->registrationModel
                        ->where('event_id', $id)
                        ->where('user_id', $membership['user_id'])
                        ->first();
                    $tokenType = 'kta_qr';
                }
            }
        }

        // 3. If registration not found
        if (! $registration) {
            $this->logCheckinAttempt($id, null, $tokenType, $rawInput, 'invalid', 'Registrasi tidak ditemukan untuk kegiatan ini.', $staffUserId);
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Peserta tidak terdaftar pada kegiatan ini atau data tidak valid.',
            ]);
        }

        $regDetails = $this->registrationModel->getWithDetails((int) $registration['id']);

        // 4. Validate registration status (must be confirmed)
        if ($registration['status'] !== 'confirmed') {
            $msg = 'Status pendaftaran peserta adalah ' . strtoupper($registration['status']) . ' (belum dikonfirmasi).';
            $this->logCheckinAttempt($id, (int) $registration['id'], $tokenType, $rawInput, 'failed', $msg, $staffUserId);
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $msg,
                'participant' => $regDetails,
            ]);
        }

        // 5. If member participant, verify active membership
        if ($registration['participant_type'] === 'member') {
            $membershipStatus = $regDetails['membership_status'] ?? '';
            if ($membershipStatus === 'suspended') {
                $msg = 'Keanggotaan member saat ini berstatus DITANGGUHKAN (Suspended). Tidak dapat melakukan presensi.';
                $this->logCheckinAttempt($id, (int) $registration['id'], $tokenType, $rawInput, 'failed', $msg, $staffUserId);
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => $msg,
                    'participant' => $regDetails,
                ]);
            }
        }

        // 6. Check duplicate attendance
        $existingAttendance = $this->attendanceModel->where('registration_id', $registration['id'])->first();
        if ($existingAttendance) {
            $msg = 'Peserta SUDAH CHECK-IN sebelumnya pada ' . date('d/m/Y H:i', strtotime($existingAttendance['checked_in_at'])) . ' WIB.';
            $this->logCheckinAttempt($id, (int) $registration['id'], $tokenType, $rawInput, 'duplicate', $msg, $staffUserId);
            return $this->response->setJSON([
                'status'        => 'duplicate',
                'message'       => $msg,
                'participant'   => $regDetails,
                'checked_in_at' => $existingAttendance['checked_in_at'],
            ]);
        }

        // 7. Check if explicit confirmation request or direct commit
        $commit = $this->request->getPost('confirm_checkin');
        if (! $commit) {
            // Return participant preview for staff confirmation
            return $this->response->setJSON([
                'status'       => 'ready',
                'message'      => 'Peserta valid. Konfirmasi kehadiran peserta.',
                'participant'  => $regDetails,
                'token_type'   => $tokenType,
            ]);
        }

        // 8. Commit check-in in database transaction
        $db->transStart();

        $this->attendanceModel->insert([
            'event_id'        => $id,
            'registration_id' => $registration['id'],
            'checkin_method'  => $tokenType === 'kta_qr' ? 'kta_qr' : ($tokenType === 'event_ticket' ? 'qr_ticket' : 'manual'),
            'checked_in_by'   => $staffUserId,
            'checked_in_at'   => date('Y-m-d H:i:s'),
            'notes'           => $this->request->getPost('notes') ?: 'Presensi terverifikasi',
        ]);

        $this->logCheckinAttempt($id, (int) $registration['id'], $tokenType, $rawInput, 'success', 'Presensi berhasil dicatat.', $staffUserId);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan presensi. Silakan coba lagi.']);
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'message'       => 'Check-in BERHASIL! Selamat datang, ' . $regDetails['participant_name'] . '.',
            'participant'   => $regDetails,
            'checked_in_at' => date('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Search Participants for Manual Check-in
     */
    public function searchParticipant(int $id)
    {
        if (! $this->authorizeEventAccess($id)) {
            return $this->response->setJSON(['results' => []]);
        }

        $term = trim((string) $this->request->getGet('q'));
        if (strlen($term) < 2) {
            return $this->response->setJSON(['results' => []]);
        }

        $db = \Config\Database::connect();
        $results = $db->table('event_registrations r')
            ->select('r.id, r.registration_number, r.participant_type, r.status, mp.full_name as member_name, m.member_number as membership_number, g.name as guest_name, g.email as guest_email, att.id as is_checked_in')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->join('memberships m', 'm.user_id = u.id', 'left')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('event_guests g', 'g.id = r.guest_id', 'left')
            ->join('event_attendance att', 'att.registration_id = r.id', 'left')
            ->where('r.event_id', $id)
            ->groupStart()
                ->like('r.registration_number', $term)
                ->orLike('mp.full_name', $term)
                ->orLike('g.name', $term)
                ->orLike('m.member_number', $term)
                ->orLike('g.email', $term)
            ->endGroup()
            ->limit(10)
            ->get()->getResultArray();

        return $this->response->setJSON(['results' => $results]);
    }

    /**
     * Helper to log check-in attempts
     */
    protected function logCheckinAttempt(int $eventId, ?int $regId, string $tokenType, string $identifier, string $status, ?string $reason, ?int $staffId): void
    {
        $this->checkinLogModel->insert([
            'event_id'           => $eventId,
            'registration_id'    => $regId,
            'scanned_token_type' => in_array($tokenType, ['event_ticket', 'kta_qr', 'manual']) ? $tokenType : 'manual',
            'scanned_identifier' => substr($identifier, 0, 255),
            'status'             => $status,
            'reason'             => $reason,
            'staff_user_id'      => $staffId,
        ]);
    }
}
