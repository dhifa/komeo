<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberVerificationModel extends Model
{
    protected $table            = 'member_verifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'verification_subject_type',
        'verification_method',
        'verification_level',
        'business_name',
        'pic_name',
        'nib_document_path',
        'ktp_document_path',
        'selfie_document_path',
        'verification_status',
        'rejection_reason',
        'admin_notes',
        'consent_at',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'revoked_by',
        'revoked_at',
        'documents_deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get verification record by user_id
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Get or create unverified record for user
     */
    public function getOrCreateForUser(int $userId, string $subjectType = 'individual'): array
    {
        $existing = $this->getByUserId($userId);
        if ($existing) {
            return $existing;
        }

        $id = $this->insert([
            'user_id'                   => $userId,
            'verification_subject_type' => in_array($subjectType, ['individual', 'business'], true) ? $subjectType : 'individual',
            'verification_method'       => $subjectType === 'business' ? 'pic_only' : 'ktp_selfie',
            'verification_level'        => 'none',
            'verification_status'       => 'unverified',
        ]);

        return $this->find($id);
    }

    /**
     * Count pending verification submissions
     */
    public function countPending(): int
    {
        return $this->where('verification_status', 'pending')->countAllResults();
    }

    /**
     * Submit verification request (from member)
     */
    public function submitVerification(int $userId, array $payload): array
    {
        $existing = $this->getByUserId($userId);
        $previousStatus = $existing ? $existing['verification_status'] : 'unverified';

        $data = [
            'verification_subject_type' => $payload['verification_subject_type'] ?? 'individual',
            'verification_method'       => $payload['verification_method'] ?? 'ktp_selfie',
            'verification_level'        => 'none', // Reset to none until approved
            'business_name'             => ! empty($payload['business_name']) ? trim($payload['business_name']) : null,
            'pic_name'                  => ! empty($payload['pic_name']) ? trim($payload['pic_name']) : null,
            'verification_status'       => 'pending',
            'rejection_reason'          => null,
            'consent_at'                => date('Y-m-d H:i:s'),
            'submitted_at'              => date('Y-m-d H:i:s'),
            'documents_deleted_at'      => null,
        ];

        if (! empty($payload['ktp_document_path'])) {
            $data['ktp_document_path'] = $payload['ktp_document_path'];
        }
        if (! empty($payload['selfie_document_path'])) {
            $data['selfie_document_path'] = $payload['selfie_document_path'];
        }
        if (! empty($payload['nib_document_path'])) {
            $data['nib_document_path'] = $payload['nib_document_path'];
        }

        if ($existing) {
            $this->update($existing['id'], $data);
            $verificationId = (int) $existing['id'];
        } else {
            $data['user_id'] = $userId;
            $verificationId = (int) $this->insert($data);
        }

        // Record history
        $historyModel = model(MemberVerificationHistoryModel::class);
        $action = ($previousStatus === 'rejected' || $previousStatus === 'revoked') ? 'resubmitted' : 'submitted';
        $historyModel->recordEvent(
            $verificationId,
            $userId,
            $action,
            $previousStatus,
            'pending',
            'none',
            $userId,
            'member',
            'Pengajuan dokumen verifikasi oleh anggota'
        );

        return [
            'success' => true,
            'id'      => $verificationId,
            'message' => 'Dokumen verifikasi berhasil dikirim dan sedang menunggu pemeriksaan admin.',
        ];
    }

    /**
     * Admin approves verification
     */
    public function approveVerification(int $id, int $adminId, string $targetLevel, ?string $adminNotes = null): array
    {
        $verification = $this->find($id);
        if (! $verification) {
            return ['success' => false, 'message' => 'Data verifikasi tidak ditemukan.'];
        }

        $validLevels = ['identity_verified', 'pic_verified', 'business_verified'];
        if (! in_array($targetLevel, $validLevels, true)) {
            return ['success' => false, 'message' => 'Level verifikasi tidak valid.'];
        }

        // Method compatibility checks
        if ($verification['verification_method'] === 'ktp_selfie' && $targetLevel !== 'identity_verified') {
            return ['success' => false, 'message' => 'Untuk metode KTP & Selfie individu, level verifikasi harus Identitas Terverifikasi.'];
        }
        if ($verification['verification_method'] === 'pic_only' && $targetLevel === 'business_verified') {
            return ['success' => false, 'message' => 'Metode Bisnis Perorangan (PIC Only) tidak dapat disetujui sebagai verifikasi bisnis ber-NIB.'];
        }
        if ($verification['verification_method'] === 'nib_pic' && empty($verification['nib_document_path']) && $targetLevel === 'business_verified') {
            return ['success' => false, 'message' => 'Dokumen NIB wajib tersedia untuk persetujuan bisnis ber-NIB.'];
        }

        $previousStatus = $verification['verification_status'];

        $this->update($id, [
            'verification_status' => 'approved',
            'verification_level'  => $targetLevel,
            'reviewed_by'         => $adminId,
            'reviewed_at'         => date('Y-m-d H:i:s'),
            'admin_notes'         => $adminNotes,
            'rejection_reason'    => null,
            'revoked_by'          => null,
            'revoked_at'          => null,
        ]);

        // Record history
        $historyModel = model(MemberVerificationHistoryModel::class);
        $historyModel->recordEvent(
            $id,
            (int) $verification['user_id'],
            'approved',
            $previousStatus,
            'approved',
            $targetLevel,
            $adminId,
            'admin',
            $adminNotes ?: "Persetujuan verifikasi level {$targetLevel}"
        );

        return [
            'success' => true,
            'message' => 'Verifikasi berhasil disetujui.',
        ];
    }

    /**
     * Admin rejects verification
     */
    public function rejectVerification(int $id, int $adminId, string $reason, ?string $adminNotes = null): array
    {
        $verification = $this->find($id);
        if (! $verification) {
            return ['success' => false, 'message' => 'Data verifikasi tidak ditemukan.'];
        }

        $previousStatus = $verification['verification_status'];

        $this->update($id, [
            'verification_status' => 'rejected',
            'verification_level'  => 'none',
            'rejection_reason'    => trim($reason),
            'admin_notes'         => $adminNotes,
            'reviewed_by'         => $adminId,
            'reviewed_at'         => date('Y-m-d H:i:s'),
        ]);

        $historyModel = model(MemberVerificationHistoryModel::class);
        $historyModel->recordEvent(
            $id,
            (int) $verification['user_id'],
            'rejected',
            $previousStatus,
            'rejected',
            'none',
            $adminId,
            'admin',
            "Ditolak: {$reason}"
        );

        return [
            'success' => true,
            'message' => 'Pengajuan verifikasi berhasil ditolak dengan alasan.',
        ];
    }

    /**
     * Admin revokes verification
     */
    public function revokeVerification(int $id, int $adminId, string $reason): array
    {
        $verification = $this->find($id);
        if (! $verification) {
            return ['success' => false, 'message' => 'Data verifikasi tidak ditemukan.'];
        }

        $previousStatus = $verification['verification_status'];

        $this->update($id, [
            'verification_status' => 'revoked',
            'verification_level'  => 'none',
            'rejection_reason'    => trim($reason),
            'revoked_by'          => $adminId,
            'revoked_at'          => date('Y-m-d H:i:s'),
        ]);

        $historyModel = model(MemberVerificationHistoryModel::class);
        $historyModel->recordEvent(
            $id,
            (int) $verification['user_id'],
            'revoked',
            $previousStatus,
            'revoked',
            'none',
            $adminId,
            'admin',
            "Pencabutan verifikasi: {$reason}"
        );

        return [
            'success' => true,
            'message' => 'Status verifikasi berhasil dicabut.',
        ];
    }

    /**
     * Delete document files (data minimization/retention period)
     */
    public function deleteDocuments(int $id, int $adminId): array
    {
        $verification = $this->find($id);
        if (! $verification) {
            return ['success' => false, 'message' => 'Data tidak ditemukan.'];
        }

        // Delete physical files
        $fields = ['ktp_document_path', 'selfie_document_path', 'nib_document_path'];
        foreach ($fields as $field) {
            if (! empty($verification[$field])) {
                $fullPath = WRITEPATH . $verification[$field];
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $this->update($id, [
            'ktp_document_path'    => null,
            'selfie_document_path' => null,
            'nib_document_path'    => null,
            'documents_deleted_at' => date('Y-m-d H:i:s'),
        ]);

        $historyModel = model(MemberVerificationHistoryModel::class);
        $historyModel->recordEvent(
            $id,
            (int) $verification['user_id'],
            'documents_deleted',
            $verification['verification_status'],
            $verification['verification_status'],
            $verification['verification_level'],
            $adminId,
            'admin',
            'Dokumen sensitif (KTP/Selfie/NIB) telah dibersihkan sesuai kebijakan retensi data.'
        );

        return ['success' => true, 'message' => 'Berkas dokumen berhasil dibersihkan dari penyimpanan aman.'];
    }

    /**
     * Get filtered verification list for admin
     */
    public function getFilteredList(array $filters = [], int $perPage = 20, int $page = 1): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('member_verifications mv')
            ->select('mv.*, mp.full_name, mp.display_name, mp.username as profile_username, mp.member_type as profile_member_type, mp.business_name as profile_business_name, m.member_number, m.status as membership_status, u.username as account_username, ai.secret as email, reviewer.username as reviewer_username')
            ->join('member_profiles mp', 'mp.user_id = mv.user_id', 'left')
            ->join('memberships m', 'm.user_id = mv.user_id', 'left')
            ->join('users u', 'u.id = mv.user_id', 'left')
            ->join('auth_identities ai', "ai.user_id = mv.user_id AND ai.type = 'email_password'", 'left')
            ->join('users reviewer', 'reviewer.id = mv.reviewed_by', 'left');

        if (! empty($filters['status'])) {
            $builder->where('mv.verification_status', $filters['status']);
        }

        if (! empty($filters['method'])) {
            $builder->where('mv.verification_method', $filters['method']);
        }

        if (! empty($filters['subject'])) {
            $builder->where('mv.verification_subject_type', $filters['subject']);
        }

        if (! empty($filters['q'])) {
            $q = trim($filters['q']);
            $builder->groupStart()
                ->like('mp.full_name', $q)
                ->orLike('mp.display_name', $q)
                ->orLike('mp.business_name', $q)
                ->orLike('mv.business_name', $q)
                ->orLike('mv.pic_name', $q)
                ->orLike('m.member_number', $q)
                ->orLike('u.username', $q)
                ->groupEnd();
        }

        // Count total
        $totalCount = (clone $builder)->countAllResults();

        // Sort: pending first, then newest
        $builder->orderBy("CASE WHEN mv.verification_status = 'pending' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('mv.updated_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage);

        $items = $builder->get()->getResultArray();

        return [
            'items'       => $items,
            'total'       => $totalCount,
            'per_page'    => $perPage,
            'page'        => $page,
            'total_pages' => max(1, (int) ceil($totalCount / $perPage)),
        ];
    }
}
