<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\DocumentShareModel;
use App\Models\InquiryMessageModel;
use App\Models\MemberDocumentModel;
use App\Models\MemberInquiryModel;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use App\Services\AuditLogService;
use App\Services\NotificationEmailService;

class InquiryController extends BaseController
{
    protected MemberInquiryModel $inquiryModel;
    protected InquiryMessageModel $messageModel;
    protected MemberDocumentModel $docModel;
    protected DocumentShareModel $shareModel;
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;

    public function __construct()
    {
        $this->inquiryModel    = new MemberInquiryModel();
        $this->messageModel    = new InquiryMessageModel();
        $this->docModel        = new MemberDocumentModel();
        $this->shareModel      = new DocumentShareModel();
        $this->membershipModel = new MembershipModel();
        $this->profileModel    = new MemberProfileModel();
    }

    /**
     * Member Inquiries Dashboard
     */
    public function index()
    {
        $userId = (int) auth()->id();
        $membership = $this->membershipModel->findByUserId($userId);
        $profile = $this->profileModel->findByUserId($userId);

        $status = $this->request->getGet('status');
        $inquiries = $this->inquiryModel->getMemberInquiries($userId, $status);

        // Stats
        $db = \Config\Database::connect();
        $stats = [
            'total'       => $db->table('member_inquiries')->where('target_user_id', $userId)->countAllResults(),
            'new'         => $db->table('member_inquiries')->where('target_user_id', $userId)->where('status', 'new')->countAllResults(),
            'in_progress' => $db->table('member_inquiries')->where('target_user_id', $userId)->where('status', 'in_progress')->countAllResults(),
            'replied'     => $db->table('member_inquiries')->where('target_user_id', $userId)->where('status', 'replied')->countAllResults(),
            'closed'      => $db->table('member_inquiries')->where('target_user_id', $userId)->where('status', 'closed')->countAllResults(),
            'spam'        => $db->table('member_inquiries')->where('target_user_id', $userId)->where('status', 'spam')->countAllResults(),
        ];

        return view('member/inquiries/index', [
            'title'      => 'Pesan & Permintaan Klien',
            'membership' => $membership,
            'profile'    => $profile,
            'inquiries'  => $inquiries,
            'stats'      => $stats,
            'status'     => $status,
        ]);
    }

    /**
     * View Inquiry Thread
     */
    public function show(int $id)
    {
        $userId = (int) auth()->id();
        $inquiry = $this->inquiryModel->where('id', $id)->where('target_user_id', $userId)->first();

        if (! $inquiry) {
            return redirect()->to('dashboard/permintaan')->with('error', 'Permintaan tidak ditemukan.');
        }

        // Fetch thread messages
        $messages = $this->messageModel->getThread($id);

        // Fetch member documents available for sharing
        $myDocuments = $this->docModel->where('user_id', $userId)->where('is_active', 1)->findAll();

        // Fetch active shares for this inquiry
        $db = \Config\Database::connect();
        $shares = $db->table('document_shares ds')
            ->select('ds.*, md.title as doc_title, md.document_type')
            ->join('member_documents md', 'md.id = ds.document_id')
            ->where('ds.inquiry_id', $id)
            ->orderBy('ds.id', 'DESC')
            ->get()->getResultArray();

        return view('member/inquiries/show', [
            'title'       => 'Detail Permintaan: ' . $inquiry['subject'],
            'inquiry'     => $inquiry,
            'messages'    => $messages,
            'myDocuments' => $myDocuments,
            'shares'      => $shares,
        ]);
    }

    /**
     * Member Replies to Client
     */
    public function reply(int $id)
    {
        $userId = (int) auth()->id();
        $inquiry = $this->inquiryModel->where('id', $id)->where('target_user_id', $userId)->first();

        if (! $inquiry) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
        }

        $messageText = trim((string) $this->request->getPost('message'));
        if (empty($messageText)) {
            return redirect()->back()->with('error', 'Pesan balasan tidak boleh kosong.');
        }

        $this->messageModel->insert([
            'inquiry_id'  => $id,
            'sender_type' => 'member',
            'sender_id'   => $userId,
            'message'     => $messageText,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->inquiryModel->update($id, [
            'status'           => 'replied',
            'last_activity_at' => date('Y-m-d H:i:s'),
        ]);

        // Send Email notification to client with their secure conversation access link
        $clientAccessUrl = site_url('inquiry/conversation/' . $inquiry['client_access_token_selector']);
        $memberName = auth()->user()->username;
        $profile = $this->profileModel->findByUserId($userId);
        if ($profile) {
            $memberName = $profile->full_name ?: $profile->display_name ?: $memberName;
        }

        NotificationEmailService::sendMemberReplyNotification(
            $inquiry['client_email'],
            $inquiry['client_name'],
            $memberName,
            $inquiry['subject'],
            $clientAccessUrl
        );

        AuditLogService::log($userId, 'inquiry.member_reply', 'member_inquiries', $id);
        return redirect()->to('dashboard/permintaan/' . $id)->with('success', 'Balasan berhasil dikirim kepada klien.');
    }

    /**
     * Update Inquiry Status
     */
    public function updateStatus(int $id)
    {
        $userId = (int) auth()->id();
        $inquiry = $this->inquiryModel->where('id', $id)->where('target_user_id', $userId)->first();

        if (! $inquiry) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
        }

        $newStatus = $this->request->getPost('status');
        if (! in_array($newStatus, ['new', 'in_progress', 'replied', 'closed', 'spam'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->inquiryModel->update($id, [
            'status'           => $newStatus,
            'last_activity_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogService::log($userId, 'inquiry.update_status', 'member_inquiries', $id, ['status' => $newStatus]);
        return redirect()->back()->with('success', 'Status permintaan berhasil diperbarui.');
    }

    /**
     * Secure Document Sharing (Section 26)
     */
    public function shareDocument(int $id)
    {
        $userId = (int) auth()->id();
        $inquiry = $this->inquiryModel->where('id', $id)->where('target_user_id', $userId)->first();

        if (! $inquiry) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
        }

        $docId = (int) $this->request->getPost('document_id');
        $doc = $this->docModel->where('id', $docId)->where('user_id', $userId)->where('is_active', 1)->first();

        if (! $doc) {
            return redirect()->back()->with('error', 'Dokumen yang dipilih tidak valid.');
        }

        // Recipient email defaults to verified client email
        $recipientEmail = strtolower(trim((string) $this->request->getPost('recipient_email'))) ?: strtolower($inquiry['client_email']);
        $days = (int) ($this->request->getPost('expiry_days') ?: 7);
        $days = max(1, min(30, $days)); // 1 to 30 days

        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));

        $shareTokenData = DocumentShareModel::generateShareToken();

        $shareId = $this->shareModel->insert([
            'document_id'          => $docId,
            'inquiry_id'           => $id,
            'shared_by_user_id'    => $userId,
            'recipient_email'      => $recipientEmail,
            'share_token_selector' => $shareTokenData['selector'],
            'share_token_hash'     => $shareTokenData['hash'],
            'expires_at'           => $expiresAt,
            'is_revoked'           => 0,
            'access_count'         => 0,
        ], true);

        // Also add system message to conversation thread
        $this->messageModel->insert([
            'inquiry_id'  => $id,
            'sender_type' => 'member',
            'sender_id'   => $userId,
            'message'     => 'Saya telah membagikan dokumen: "' . $doc['title'] . '" (Tautan berlaku hingga ' . date('d/m/Y', strtotime($expiresAt)) . ').',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        // Send notification email to recipient
        $shareUrl = site_url('shared-document/' . $shareTokenData['raw_token']);
        $memberName = auth()->user()->username;
        $profile = $this->profileModel->findByUserId($userId);
        if ($profile) {
            $memberName = $profile->full_name ?: $profile->display_name ?: $memberName;
        }

        NotificationEmailService::sendDocumentShareNotification(
            $recipientEmail,
            $inquiry['client_name'],
            $memberName,
            $doc['title'],
            $shareUrl,
            $expiresAt
        );

        AuditLogService::log($userId, 'document.share_created', 'document_shares', (int) $shareId, ['recipient' => $recipientEmail]);
        return redirect()->to('dashboard/permintaan/' . $id)->with('success', 'Dokumen berhasil dibagikan secara aman kepada klien.');
    }

    /**
     * Revoke Shared Document Access
     */
    public function revokeShare(int $inquiryId, int $shareId)
    {
        $userId = (int) auth()->id();
        $share = $this->shareModel->where('id', $shareId)->where('shared_by_user_id', $userId)->first();

        if (! $share) {
            return redirect()->back()->with('error', 'Tautan dokumen tidak ditemukan.');
        }

        $this->shareModel->update($shareId, [
            'is_revoked' => 1,
            'revoked_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogService::log($userId, 'document.share_revoked', 'document_shares', $shareId);
        return redirect()->back()->with('success', 'Akses tautan dokumen berhasil dicabut (revoked).');
    }
}
