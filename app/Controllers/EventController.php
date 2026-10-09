<?php

namespace App\Controllers;

use App\Models\EventAttendanceModel;
use App\Models\EventGuestModel;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;
use App\Services\EventTicketService;
use App\Services\NotificationEmailService;
use App\Services\VerificationChallengeService;

class EventController extends BaseController
{
    protected EventModel $eventModel;
    protected EventGuestModel $guestModel;
    protected EventRegistrationModel $registrationModel;
    protected EventTicketService $ticketService;

    public function __construct()
    {
        $this->eventModel        = new EventModel();
        $this->guestModel        = new EventGuestModel();
        $this->registrationModel = new EventRegistrationModel();
        $this->ticketService     = new EventTicketService();
    }

    /**
     * List Published Events
     */
    public function index()
    {
        $builder = $this->eventModel->where('status', 'published');

        // Optional filter by event type
        $type = $this->request->getGet('type');
        if (in_array($type, ['internal', 'external', 'hybrid'])) {
            $builder->where('event_type', $type);
        }

        $events = $builder->orderBy('start_date', 'ASC')->findAll();

        return view('events/index', [
            'title'       => 'Kegiatan & Acara Komunitas - KOMEO.ID',
            'events'      => $events,
            'currentType' => $type,
        ]);
    }

    /**
     * Show Event Details
     */
    public function show(string $slug)
    {
        $event = $this->eventModel->where('slug', $slug)->first();
        if (! $event || ($event['status'] !== 'published' && ! (auth()->loggedIn() && (auth()->user()->inGroup('admin', 'superadmin'))))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kegiatan tidak ditemukan atau belum dipublikasikan.');
        }

        $regActive = $this->eventModel->isRegistrationActive($event);
        $quotaStatus = $this->eventModel->checkQuotaAvailable((int) $event['id'], 'external');

        // Check if logged-in member is already registered
        $memberRegistration = null;
        if (auth()->loggedIn()) {
            $memberRegistration = $this->registrationModel
                ->where('event_id', $event['id'])
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('events/show', [
            'title'              => $event['title'] . ' - KOMEO.ID',
            'event'              => $event,
            'regActive'          => $regActive,
            'quotaStatus'        => $quotaStatus,
            'memberRegistration' => $memberRegistration,
        ]);
    }

    /**
     * Guest Registration Form
     */
    public function registerGuest(string $slug)
    {
        $event = $this->eventModel->where('slug', $slug)->first();
        if (! $event || $event['status'] !== 'published') {
            return redirect()->to('kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        if ($event['event_type'] === 'internal') {
            return redirect()->to('kegiatan/' . $slug)->with('error', 'Kegiatan ini khusus untuk Member resmi KOMEO. Silakan login dengan akun member Anda.');
        }

        $regActive = $this->eventModel->isRegistrationActive($event);
        if (! $regActive['active']) {
            return redirect()->to('kegiatan/' . $slug)->with('error', $regActive['reason']);
        }

        $quotaStatus = $this->eventModel->checkQuotaAvailable((int) $event['id'], 'external');
        if (! $quotaStatus['available']) {
            return redirect()->to('kegiatan/' . $slug)->with('error', $quotaStatus['reason']);
        }

        return view('events/register_guest', [
            'title' => 'Pendaftaran Peserta Umum: ' . $event['title'],
            'event' => $event,
        ]);
    }

    /**
     * Process Guest Registration
     */
    public function processGuestRegistration(string $slug)
    {
        $event = $this->eventModel->where('slug', $slug)->first();
        if (! $event || $event['status'] !== 'published') {
            return redirect()->to('kegiatan')->with('error', 'Kegiatan tidak ditemukan.');
        }

        if ($event['event_type'] === 'internal') {
            return redirect()->to('kegiatan/' . $slug)->with('error', 'Kegiatan ini khusus untuk Member KOMEO.');
        }

        // Form Validation
        $rules = [
            'name'            => 'required|min_length[3]|max_length[150]',
            'email'           => 'required|valid_email|max_length[255]',
            'whatsapp'        => 'permit_empty|min_length[8]|max_length[30]',
            'company'         => 'permit_empty|max_length[150]',
            'job_title'       => 'permit_empty|max_length[100]',
            'city'            => 'permit_empty|max_length[100]',
            'privacy_consent' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $name  = trim((string) $this->request->getPost('name'));

        $db = \Config\Database::connect();
        $db->transStart();

        // Check quota with DB lock
        $eventRow = $db->table('events')->where('id', $event['id'])->get()->getRowArray();
        $quotaStatus = $this->eventModel->checkQuotaAvailable((int) $event['id'], 'external');
        if (! $quotaStatus['available']) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $quotaStatus['reason']);
        }

        // Find or create guest
        $guest = $this->guestModel->where('email', $email)->first();
        $guestData = [
            'name'                => $name,
            'email'               => $email,
            'whatsapp'            => $this->request->getPost('whatsapp'),
            'company'             => $this->request->getPost('company'),
            'job_title'           => $this->request->getPost('job_title'),
            'city'                => $this->request->getPost('city'),
            'profession_category' => $this->request->getPost('profession_category'),
        ];

        if ($guest) {
            $this->guestModel->update($guest['id'], $guestData);
            $guestId = $guest['id'];
        } else {
            $guestData['is_email_verified'] = 0;
            $guestId = $this->guestModel->insert($guestData, true);
        }

        // Check if duplicate registration on this event
        $existingReg = $this->registrationModel
            ->where('event_id', $event['id'])
            ->where('guest_id', $guestId)
            ->whereIn('status', ['confirmed', 'pending_approval', 'pending_verification'])
            ->first();

        if ($existingReg) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Email ' . $email . ' sudah terdaftar pada kegiatan ini.');
        }

        // Generate Registration Number & Split Ticket Token
        $regNumber = EventRegistrationModel::generateRegistrationNumber();
        $ticketTokenData = EventRegistrationModel::generateTicketToken();

        $regId = $this->registrationModel->insert([
            'event_id'              => $event['id'],
            'registration_number'   => $regNumber,
            'participant_type'      => 'external',
            'user_id'               => null,
            'guest_id'              => $guestId,
            'status'                => 'pending_verification',
            'ticket_token_selector' => $ticketTokenData['selector'],
            'ticket_token_hash'     => $ticketTokenData['hash'],
            'registered_at'         => date('Y-m-d H:i:s'),
        ], true);

        // Create Verification Challenge Token
        $challenge = VerificationChallengeService::createChallenge(
            'event_registration',
            (int) $regId,
            $email,
            24 // expires in 24 hours
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat mendaftar. Silakan coba kembali.');
        }

        // Send Email Verification
        $verifyUrl = site_url('kegiatan/verifikasi-email/' . $challenge['token']);
        NotificationEmailService::sendGuestVerificationEmail($email, $name, $event['title'], $verifyUrl);

        return view('events/registration_success', [
            'title'       => 'Pendaftaran Berhasil Dikirim',
            'event'       => $event,
            'guestName'   => $name,
            'guestEmail'  => $email,
            'verifyUrl'   => $verifyUrl, // useful for local testing or dev fallback
        ]);
    }

    /**
     * Verify Guest Email Link
     */
    public function verifyGuestEmail(string $token)
    {
        $verification = VerificationChallengeService::verifyToken($token, 'event_registration');

        if (! $verification['success']) {
            return view('events/verify_result', [
                'title'   => 'Verifikasi Gagal',
                'success' => false,
                'message' => $verification['message'],
            ]);
        }

        $regId = $verification['reference_id'];
        $reg = $this->registrationModel->find($regId);

        if (! $reg) {
            return view('events/verify_result', [
                'title'   => 'Verifikasi Gagal',
                'success' => false,
                'message' => 'Data pendaftaran kegiatan tidak ditemukan.',
            ]);
        }

        // Mark guest email as verified
        if (! empty($reg['guest_id'])) {
            $this->guestModel->update($reg['guest_id'], [
                'is_email_verified' => 1,
                'verified_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        $event = $this->eventModel->find($reg['event_id']);
        $requiresApproval = (bool) ($event['requires_approval'] ?? false);

        if ($requiresApproval) {
            $this->registrationModel->update($regId, ['status' => 'pending_approval']);
            $msg = 'Email Anda berhasil diverifikasi. Pendaftaran Anda saat ini menunggu persetujuan (approval) dari panitia penyelenggara.';
            $ticketUrl = null;
        } else {
            $this->registrationModel->update($regId, [
                'status'       => 'confirmed',
                'confirmed_at' => date('Y-m-d H:i:s'),
            ]);
            $msg = 'Email berhasil diverifikasi! Pendaftaran Anda telah dikonfirmasi secara resmi.';
            $ticketUrl = site_url('kegiatan/tiket/' . $reg['ticket_token_selector']);

            // Send Ticket Confirmation Email
            $regDetails = $this->registrationModel->getWithDetails($regId);
            if ($regDetails && ! empty($regDetails['participant_email'])) {
                NotificationEmailService::sendTicketConfirmation(
                    $regDetails['participant_email'],
                    $regDetails['participant_name'],
                    $regDetails['event_title'],
                    $regDetails['registration_number'],
                    $ticketUrl
                );
            }
        }

        return view('events/verify_result', [
            'title'     => 'Verifikasi Email Berhasil',
            'success'   => true,
            'message'   => $msg,
            'event'     => $event,
            'reg'       => $reg,
            'ticketUrl' => $ticketUrl,
        ]);
    }

    /**
     * View QR Ticket
     */
    public function ticketView(string $selector)
    {
        $reg = $this->registrationModel->where('ticket_token_selector', $selector)->first();
        if (! $reg) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tiket tidak ditemukan atau kode tidak valid.');
        }

        $regDetails = $this->registrationModel->getWithDetails((int) $reg['id']);
        if (! $regDetails) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data tiket tidak ditemukan.');
        }

        return view('events/ticket', [
            'title'        => 'Tiket Elektronik - ' . $regDetails['event_title'],
            'registration' => $regDetails,
        ]);
    }

    /**
     * Download Ticket PNG
     */
    public function downloadTicketPng(string $selector)
    {
        $reg = $this->registrationModel->where('ticket_token_selector', $selector)->first();
        if (! $reg) {
            return $this->response->setStatusCode(404)->setBody('Tiket tidak ditemukan.');
        }

        $regDetails = $this->registrationModel->getWithDetails((int) $reg['id']);
        $pngData    = $this->ticketService->renderTicketPng($regDetails);

        $filename = 'Tiket_' . $regDetails['registration_number'] . '.png';

        return $this->response
            ->setContentType('image/png')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($pngData);
    }

    /**
     * Download Ticket PDF
     */
    public function downloadTicketPdf(string $selector)
    {
        $reg = $this->registrationModel->where('ticket_token_selector', $selector)->first();
        if (! $reg) {
            return $this->response->setStatusCode(404)->setBody('Tiket tidak ditemukan.');
        }

        $regDetails = $this->registrationModel->getWithDetails((int) $reg['id']);
        $pdfData    = $this->ticketService->renderTicketPdf($regDetails);

        $filename = 'Tiket_' . $regDetails['registration_number'] . '.pdf';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($pdfData);
    }
}
