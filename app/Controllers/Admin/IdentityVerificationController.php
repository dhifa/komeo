<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberProfileModel;
use App\Models\MemberVerificationHistoryModel;
use App\Models\MemberVerificationModel;
use App\Models\MembershipModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class IdentityVerificationController extends BaseController
{
    protected MemberVerificationModel $verificationModel;
    protected MemberProfileModel $profileModel;
    protected MembershipModel $membershipModel;

    public function __construct()
    {
        $this->verificationModel = new MemberVerificationModel();
        $this->profileModel      = new MemberProfileModel();
        $this->membershipModel   = new MembershipModel();
    }

    /**
     * List verification applications with filters
     */
    public function index(): string
    {
        $filters = [
            'status'  => $this->request->getGet('status'),
            'method'  => $this->request->getGet('method'),
            'subject' => $this->request->getGet('subject'),
            'q'       => $this->request->getGet('q'),
        ];

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $data = $this->verificationModel->getFilteredList($filters, 20, $page);

        // Counts for summary pills
        $pendingCount  = $this->verificationModel->where('verification_status', 'pending')->countAllResults();
        $approvedCount = $this->verificationModel->where('verification_status', 'approved')->countAllResults();
        $rejectedCount = $this->verificationModel->where('verification_status', 'rejected')->countAllResults();
        $revokedCount  = $this->verificationModel->where('verification_status', 'revoked')->countAllResults();

        return view('admin/verifications/index', [
            'title'         => 'Verifikasi Identitas & Usaha - Admin KOMEO.ID',
            'verifications' => $data['items'],
            'total'         => $data['total'],
            'page'          => $data['page'],
            'totalPages'    => $data['total_pages'],
            'filters'       => $filters,
            'counts'        => [
                'pending'  => $pendingCount,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount,
                'revoked'  => $revokedCount,
            ],
        ]);
    }

    /**
     * Detail review page for a verification request
     */
    public function show(int $id): string|ResponseInterface
    {
        $verification = $this->verificationModel->find($id);
        if (! $verification) {
            throw PageNotFoundException::forPageNotFound('Data verifikasi tidak ditemukan.');
        }

        $userId  = (int) $verification['user_id'];
        $profile = $this->profileModel->findByUserId($userId);
        $membership = $this->membershipModel->findByUserId($userId);

        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('users.*, auth_identities.secret as email')
            ->join('auth_identities', "auth_identities.user_id = users.id AND auth_identities.type = 'email_password'", 'left')
            ->where('users.id', $userId)
            ->get()
            ->getRowArray();

        $historyModel = model(MemberVerificationHistoryModel::class);
        $history      = $historyModel->getByVerificationId($id);

        return view('admin/verifications/show', [
            'title'        => 'Tinjau Verifikasi #' . $id . ' - Admin KOMEO.ID',
            'verification' => $verification,
            'profile'      => $profile,
            'membership'   => $membership,
            'user'         => $user,
            'history'      => $history,
        ]);
    }

    /**
     * Approve verification request
     */
    public function approve(int $id): ResponseInterface
    {
        $adminId     = (int) auth()->user()->id;
        $targetLevel = (string) $this->request->getPost('target_level');
        $notes       = (string) $this->request->getPost('admin_notes');

        $result = $this->verificationModel->approveVerification($id, $adminId, $targetLevel, $notes);

        if ($result['success']) {
            return redirect()->to(base_url('admin/identity-verifications/view/' . $id))
                ->with('success', 'Permohonan verifikasi berhasil disetujui (Level: ' . $targetLevel . ').');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Reject verification request
     */
    public function reject(int $id): ResponseInterface
    {
        $adminId = (int) auth()->user()->id;
        $reason  = trim((string) $this->request->getPost('reason'));
        $notes   = (string) $this->request->getPost('admin_notes');

        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi agar pemohon mengetahui perbaikan yang diperlukan.');
        }

        $result = $this->verificationModel->rejectVerification($id, $adminId, $reason, $notes);

        if ($result['success']) {
            return redirect()->to(base_url('admin/identity-verifications/view/' . $id))
                ->with('success', 'Pengajuan verifikasi berhasil ditolak dengan alasan.');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Revoke approved verification
     */
    public function revoke(int $id): ResponseInterface
    {
        $adminId = (int) auth()->user()->id;
        $reason  = trim((string) $this->request->getPost('reason'));

        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan pencabutan verifikasi wajib diisi.');
        }

        $result = $this->verificationModel->revokeVerification($id, $adminId, $reason);

        if ($result['success']) {
            return redirect()->to(base_url('admin/identity-verifications/view/' . $id))
                ->with('success', 'Status verifikasi berhasil dicabut.');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Clean up physical documents after retention period
     */
    public function deleteDocuments(int $id): ResponseInterface
    {
        $adminId = (int) auth()->user()->id;
        $result = $this->verificationModel->deleteDocuments($id, $adminId);

        if ($result['success']) {
            return redirect()->to(base_url('admin/identity-verifications/view/' . $id))
                ->with('success', 'Berkas dokumen sensitif berhasil dibersihkan.');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Securely stream verification document for admin inspection with audit logging
     */
    public function viewDocument(int $id, string $type): ResponseInterface
    {
        $verification = $this->verificationModel->find($id);
        if (! $verification) {
            throw PageNotFoundException::forPageNotFound('Data verifikasi tidak ditemukan.');
        }

        $field = match ($type) {
            'ktp'    => 'ktp_document_path',
            'selfie' => 'selfie_document_path',
            'nib'    => 'nib_document_path',
            default  => null,
        };

        if (! $field || empty($verification[$field])) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $verification[$field];
        if (! file_exists($fullPath)) {
            throw PageNotFoundException::forPageNotFound('Berkas dokumen fisik tidak ditemukan di server.');
        }

        // Audit Log Document Access
        try {
            $db = \Config\Database::connect();
            $adminUser = auth()->user();
            $db->table('audit_logs')->insert([
                'user_id'     => $adminUser ? (int) $adminUser->id : null,
                'action'      => 'view_verification_document',
                'entity_type' => 'member_verifications',
                'entity_id'   => $id,
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'details'     => json_encode([
                    'doc_type'       => $type,
                    'target_user_id' => $verification['user_id'],
                    'filename'       => basename($fullPath),
                ]),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Fail open on audit log without blocking inspection
        }

        $mime = function_exists('komeo_detect_mime_type')
            ? komeo_detect_mime_type($fullPath)
            : $this->detectMimeType($fullPath);
        $content = file_get_contents($fullPath);

        return $this->response
            ->setContentType($mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"')
            ->setHeader('Cache-Control', 'private, no-cache, no-store, must-revalidate')
            ->setBody($content);
    }

    /**
     * Safely detect MIME type of document without requiring ext-fileinfo
     */
    protected function detectMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            $mime = \Config\Mimes::guessTypeFromExtension($ext);
            if (! empty($mime)) {
                return is_array($mime) ? $mime[0] : $mime;
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        $map = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}
