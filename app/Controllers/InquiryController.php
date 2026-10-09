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

        return view('inquiry/contact_form', [
            'title'      => 'Hubungi ' . ($profile->display_name ?? $profile->full_name ?? $username),
            'targetUser' => $user,
            'profile'    => $profile,
            'membership' => $membership,
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
            return redirect()->to('/')->with('error', 'Terjadi kesalahan.');
        }

        $rules = [
            'client_name'         => 'required|min_length[3]|max_length[150]',
            'client_email'        => 'required|valid_email|max_length[255]',
            'inquiry_type'        => 'required|in_list[job_offer,cv_request,portfolio_request,collaboration,general]',
            'subject'             => 'required|min_length[3]|max_length[255]',
            'initial_message'     => 'required|min_length[10]|max_length[5000]',
            'privacy_consent'     => 'required',
            'client_whatsapp'     => 'permit_empty|min_length[8]|max_length[30]',
            'client_organization' => 'permit_empty|max_length[150]',
            'event_location'      => 'permit_empty|max_length[255]',
            'event_date'          => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clientEmail = strtolower(trim((string) $this->request->getPost('client_email')));
        $clientName  = trim((string) $this->request->getPost('client_name'));

        // Generate split client access token
        $tokenData = MemberInquiryModel::generateClientAccessToken();

        $inquiryId = $this->inquiryModel->insert([
            'target_user_id'               => (int) $user->id,
            'client_name'                  => $clientName,
            'client_email'                 => $clientEmail,
            'client_whatsapp'              => $this->request->getPost('client_whatsapp'),
            'client_organization'          => $this->request->getPost('client_organization'),
            'inquiry_type'                 => $this->request->getPost('inquiry_type'),
            'subject'                      => $this->request->getPost('subject'),
            'initial_message'              => $this->request->getPost('initial_message'),
            'event_location'               => $this->request->getPost('event_location'),
            'event_date'                   => $this->request->getPost('event_date') ?: null,
            'is_email_verified'            => 0,
            'status'                       => 'new',
            'client_access_token_selector' => $tokenData['selector'],
            'client_access_token_hash'     => $tokenData['hash'],
            'last_activity_at'             => date('Y-m-d H:i:s'),
        ], true);

        // Also add initial message to thread
        $this->messageModel->insert([
            'inquiry_id'  => $inquiryId,
            'sender_type' => 'client',
            'sender_id'   => null,
            'message'     => $this->request->getPost('initial_message'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // Create email verification challenge
        $challenge = VerificationChallengeService::createChallenge(
            'client_inquiry',
            (int) $inquiryId,
            $clientEmail,
            24 // expires in 24 hours
        );

        $verifyUrl = site_url('inquiry/verify/' . $challenge['token']);
        $profile = $this->profileModel->findByUserId((int) $user->id);
        $memberName = $profile ? ($profile->full_name ?: $profile->display_name ?: $username) : $username;

        // Send email verification
        NotificationEmailService::sendInquiryVerificationEmail(
            $clientEmail,
            $clientName,
            $memberName,
            $verifyUrl
        );

        AuditLogService::log(null, 'inquiry.submit', 'member_inquiries', (int) $inquiryId, ['client_email' => $clientEmail]);

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

        return $this->response->download($filePath, null)
            ->setFileName($doc['title'] . '.pdf')
            ->inline();
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
