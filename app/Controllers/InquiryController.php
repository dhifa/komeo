<?php

namespace App\Controllers;

use App\Models\DocumentAccessLogModel;
use App\Models\DocumentShareModel;
use App\Models\InquiryMessageModel;
use App\Models\MemberDocumentModel;
use App\Models\MemberInquiryModel;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Services\AuditLogService;
use App\Services\NotificationEmailService;
use App\Services\VerificationChallengeService;

class InquiryController extends BaseController
{
    protected MemberInquiryModel $inquiryModel;
    protected InquiryMessageModel $messageModel;
    protected MemberDocumentModel $docModel;
    protected DocumentShareModel $shareModel;
    protected DocumentAccessLogModel $accessLogModel;
    protected UserModel $userModel;
    protected MemberProfileModel $profileModel;
    protected MembershipModel $membershipModel;

    public function __construct()
    {
        $this->inquiryModel    = new MemberInquiryModel();
        $this->messageModel    = new InquiryMessageModel();
        $this->docModel        = new MemberDocumentModel();
        $this->shareModel      = new DocumentShareModel();
        $this->accessLogModel  = new DocumentAccessLogModel();
        $this->userModel       = new UserModel();
        $this->profileModel    = new MemberProfileModel();
        $this->membershipModel = new MembershipModel();
    }

    /**
     * Show Public Contact Form for a Member
     */
    public function contactMember(string $username)
    {
        $user = $this->userModel->where('username', $username)->first();
        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Member tidak ditemukan.');
        }

        $membership = $this->membershipModel->findByUserId((int) $user->id);
        $memberStatus = is_object($membership) ? (string) $membership->status : (string) ($membership['status'] ?? '');

        // Only active approved members can receive public inquiries
        if ($memberStatus !== 'approved' && $memberStatus !== 'active') {
            return redirect()->to('member')->with('error', 'Member ini sedang tidak aktif menerima permintaan bisnis.');
        }

        $profile = $this->profileModel->findByUserId((int) $user->id);
        $currentUser = auth()->loggedIn() ? auth()->user() : null;
        $currentProfile = $currentUser ? $this->profileModel->findByUserId((int) $currentUser->id) : null;

        return view('inquiry/contact_form', [
            'title'          => 'Hubungi ' . ($profile->display_name ?? $profile->full_name ?? $username),
            'targetUser'     => $user,
            'profile'        => $profile,
            'membership'     => $membership,
            'currentUser'    => $currentUser,
            'currentProfile' => $currentProfile,
        ]);
    }

    /**
     * Submit Client Inquiry
     */
    public function submitInquiry(string $username)
    {
        $user = $this->userModel->where('username', $username)->first();
        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Member tidak ditemukan.');
        }

        // Honeypot check
        if (! empty($this->request->getPost('website_hp'))) {
            return redirect()->to('/')->with('error', 'Terjadi kesalahan pengiriman.');
        }

        $rules = [
            'client_name'         => 'required|min_length[2]|max_length[150]',
            'client_email'        => 'required|valid_email|max_length[255]',
            'inquiry_type'        => 'required|in_list[job_offer,cv_request,portfolio_request,collaboration,general]',
            'subject'             => 'required|min_length[3]|max_length[255]',
            'initial_message'     => 'required|min_length[5]|max_length[5000]',
            'privacy_consent'     => 'required',
            'client_whatsapp'     => 'permit_empty|min_length[6]|max_length[30]',
            'client_organization' => 'permit_empty|max_length[150]',
            'event_location'      => 'permit_empty|max_length[255]',
        ];

        // Only validate date if provided
        $eventDateVal = trim((string) $this->request->getPost('event_date'));
        if ($eventDateVal !== '') {
            $rules['event_date'] = 'valid_date[Y-m-d]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clientEmail = strtolower(trim((string) $this->request->getPost('client_email')));
        $clientName  = trim((string) $this->request->getPost('client_name'));

        // If the sender is already logged in as a KOMEO member, their identity & email are verified
        $isLoggedIn = auth()->loggedIn();
        $senderUser = $isLoggedIn ? auth()->user() : null;
        $isAutoVerified = $isLoggedIn ? 1 : 0;

        // Generate split client access token
        $tokenData = MemberInquiryModel::generateClientAccessToken();

        $inquiryId = $this->inquiryModel->insert([
            'target_user_id'               => (int) $user->id,
            'client_name'                  => $clientName,
            'client_email'                 => $clientEmail,
            'client_whatsapp'              => $this->request->getPost('client_whatsapp') ?: null,
            'client_organization'          => $this->request->getPost('client_organization') ?: null,
            'inquiry_type'                 => $this->request->getPost('inquiry_type'),
            'subject'                      => $this->request->getPost('subject'),
            'initial_message'              => $this->request->getPost('initial_message'),
            'event_location'               => $this->request->getPost('event_location') ?: null,
            'event_date'                   => $eventDateVal ?: null,
            'is_email_verified'            => $isAutoVerified,
            'status'                       => 'new',
            'client_access_token_selector' => $tokenData['selector'],
            'client_access_token_hash'     => $tokenData['hash'],
            'last_activity_at'             => date('Y-m-d H:i:s'),
        ], true);

        // Also add initial message to thread
        $this->messageModel->insert([
            'inquiry_id'  => $inquiryId,
            'sender_type' => 'client',
            'sender_id'   => $senderUser ? (int) $senderUser->id : null,
            'sender_name' => $clientName,
            'message'     => $this->request->getPost('initial_message'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $profile = $this->profileModel->findByUserId((int) $user->id);
        $memberName = $profile ? ($profile->full_name ?: $profile->display_name ?: $username) : $username;

        AuditLogService::log($senderUser ? (int) $senderUser->id : null, 'inquiry.submit', 'member_inquiries', (int) $inquiryId, ['client_email' => $clientEmail]);

        // If sender is already logged in, notify target member directly and redirect to conversation
        if ($isAutoVerified) {
            $memberDashboardUrl = site_url('dashboard/permintaan/' . $inquiryId);
            NotificationEmailService::sendNewInquiryNotification(
                $user->email,
                $user->username,
                $clientName,
                (string) $this->request->getPost('subject'),
                (string) $this->request->getPost('initial_message'),
                $memberDashboardUrl
            );

            return redirect()->to(site_url('inquiry/conversation/' . $tokenData['selector']))
                ->with('message', 'Pesan dan penawaran Anda berhasil dikirim langsung kepada ' . $memberName . '!');
        }

        // For non-logged-in guest clients: Create email verification challenge
        $challenge = VerificationChallengeService::createChallenge(
            'client_inquiry',
            (int) $inquiryId,
            $clientEmail,
            24 // expires in 24 hours
        );

        $verifyUrl = site_url('inquiry/verify/' . $challenge['token']);

        // Send email verification
        NotificationEmailService::sendInquiryVerificationEmail(
            $clientEmail,
            $clientName,
            $memberName,
            $verifyUrl
        );

        return view('inquiry/success_notice', [
            'title'       => 'Permintaan Terkirim - Verifikasi Email',
            'clientEmail' => $clientEmail,
            'clientName'  => $clientName,
            'memberName'  => $memberName,
            'verifyUrl'   => $verifyUrl, // fallback for local environment
        ]);
    }

    /**
     * Verify Client Email Challenge
     */
    public function verifyClientEmail(string $token)
    {
        $verification = VerificationChallengeService::verifyToken($token, 'client_inquiry');

        if (! $verification['success']) {
            return view('inquiry/verify_result', [
                'title'   => 'Verifikasi Gagal',
                'success' => false,
                'message' => $verification['message'],
            ]);
        }

        $inquiryId = $verification['reference_id'];
        $inquiry = $this->inquiryModel->find($inquiryId);

        if (! $inquiry) {
            return view('inquiry/verify_result', [
                'title'   => 'Verifikasi Gagal',
                'success' => false,
                'message' => 'Data permintaan tidak ditemukan.',
            ]);
        }

        $this->inquiryModel->update($inquiryId, ['is_email_verified' => 1]);

        // Notify member of verified new inquiry
        $targetUser = $this->userModel->find($inquiry['target_user_id']);
        if ($targetUser) {
            $memberDashboardUrl = site_url('dashboard/permintaan/' . $inquiryId);
            NotificationEmailService::sendNewInquiryNotification(
                $targetUser->email,
                $targetUser->username,
                $inquiry['client_name'],
                $inquiry['subject'],
                $inquiry['initial_message'],
                $memberDashboardUrl
            );
        }

        $conversationUrl = site_url('inquiry/conversation/' . $inquiry['client_access_token_selector']);
        return redirect()->to($conversationUrl)->with('success', 'Email Anda berhasil diverifikasi! Permintaan telah diteruskan langsung ke Member KOMEO.');
    }

    /**
     * Client Conversation View
     */
    public function conversation(string $selector)
    {
        $inquiry = $this->inquiryModel->where('client_access_token_selector', $selector)->first();
        if (! $inquiry) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Percakapan tidak ditemukan.');
        }

        $messages = $this->messageModel->getThread((int) $inquiry['id']);
        $targetUser = $this->userModel->find($inquiry['target_user_id']);
        $profile = $this->profileModel->findByUserId((int) $inquiry['target_user_id']);

        // Check for shared documents in this inquiry
        $db = \Config\Database::connect();
        $shares = $db->table('document_shares ds')
            ->select('ds.*, md.title as doc_title, md.document_type')
            ->join('member_documents md', 'md.id = ds.document_id')
            ->where('ds.inquiry_id', $inquiry['id'])
            ->where('ds.is_revoked', 0)
            ->where('ds.expires_at >=', date('Y-m-d H:i:s'))
            ->get()->getResultArray();

        return view('inquiry/conversation', [
            'title'      => 'Percakapan: ' . $inquiry['subject'],
            'inquiry'    => $inquiry,
            'messages'   => $messages,
            'targetUser' => $targetUser,
            'profile'    => $profile,
            'shares'     => $shares,
        ]);
    }

    /**
     * Client Sends a Reply in the Conversation
     */
    public function clientReply(string $selector)
    {
        $inquiry = $this->inquiryModel->where('client_access_token_selector', $selector)->first();
        if (! $inquiry) {
            return redirect()->back()->with('error', 'Percakapan tidak ditemukan.');
        }

        if ($inquiry['status'] === 'closed') {
            return redirect()->back()->with('error', 'Percakapan ini telah ditutup.');
        }

        $messageText = trim((string) $this->request->getPost('message'));
        if (empty($messageText)) {
            return redirect()->back()->with('error', 'Pesan tidak boleh kosong.');
        }

        $this->messageModel->insert([
            'inquiry_id'  => $inquiry['id'],
            'sender_type' => 'client',
            'sender_id'   => null,
            'message'     => $messageText,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->inquiryModel->update($inquiry['id'], [
            'status'           => 'in_progress',
            'last_activity_at' => date('Y-m-d H:i:s'),
        ]);

        // Notify member
        $targetUser = $this->userModel->find($inquiry['target_user_id']);
        if ($targetUser) {
            $memberDashboardUrl = site_url('dashboard/permintaan/' . $inquiry['id']);
            NotificationEmailService::sendNewInquiryNotification(
                $targetUser->email,
                $targetUser->username,
                $inquiry['client_name'],
                'Pesan baru: ' . $inquiry['subject'],
                $messageText,
                $memberDashboardUrl
            );
        }

        AuditLogService::log(null, 'inquiry.client_reply', 'member_inquiries', (int) $inquiry['id']);
        return redirect()->to('inquiry/conversation/' . $selector)->with('success', 'Balasan Anda berhasil dikirim.');
    }

    /**
     * Secure Document Download & Verification (Section 26 & 30)
     */
    public function viewSharedDocument(string $token)
    {
        $share = $this->shareModel->getActiveShare($token);
        if (! $share) {
            return view('inquiry/shared_document_error', [
                'title'   => 'Dokumen Tidak Dapat Diakses',
                'message' => 'Tautan dokumen ini tidak valid, telah kedaluwarsa (expired), atau akses telah dicabut oleh pemilik.',
            ]);
        }

        $doc = $share['document'];

        // If External portfolio link, redirect securely to external URL
        if ($doc['document_type'] === 'portfolio_external' && ! empty($doc['external_url'])) {
            // Record access log
            $this->recordShareAccess((int) $share['id']);
            return redirect()->to($doc['external_url']);
        }

        // File download / preview
        $storageDir = WRITEPATH . 'uploads/member-documents/';
        $filePath   = $storageDir . $doc['file_path'];

        if (empty($doc['file_path']) || ! file_exists($filePath)) {
            return view('inquiry/shared_document_error', [
                'title'   => 'Berkas Tidak Ditemukan',
                'message' => 'Berkas fisik dokumen tidak ditemukan di server.',
            ]);
        }

        // Record access log
        $this->recordShareAccess((int) $share['id']);

        $cleanTitle = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $doc['title'] ?: 'Dokumen') . '.pdf';
        $content    = file_get_contents($filePath);
        $fileSize   = strlen($content);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $cleanTitle . '"')
            ->setHeader('Content-Length', (string) $fileSize)
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody($content);
    }

    /**
     * Record Document Share Access
     */
    protected function recordShareAccess(int $shareId): void
    {
        $db = \Config\Database::connect();
        $db->table('document_shares')->where('id', $shareId)->increment('access_count');

        $this->accessLogModel->insert([
            'document_share_id' => $shareId,
            'ip_address'        => $this->request->getIPAddress(),
            'user_agent'        => substr((string) $this->request->getUserAgent(), 0, 255),
            'accessed_at'       => date('Y-m-d H:i:s'),
        ]);
    }
}
