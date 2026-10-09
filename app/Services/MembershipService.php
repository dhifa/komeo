<?php

namespace App\Services;

use App\Models\MembershipModel;
use App\Models\MembershipNumberCounterModel;
use App\Models\MembershipStatusHistoryModel;
use Config\Database;
use RuntimeException;
use Throwable;

class MembershipService
{
    /**
     * Atomically generate a unique member number for the given approval year.
     * Format: KMO-YYYY-XXXXXX (e.g. KMO-2026-000001)
     *
     * @param int $year
     * @return string
     */
    public static function generateNextMemberNumber(int $year): string
    {
        $counterModel = model(MembershipNumberCounterModel::class);
        $seq = $counterModel->allocateNextNumber($year);

        return sprintf('KMO-%04d-%06d', $year, $seq);
    }

    /**
     * Approve a membership application.
     * Generates a member number if not previously issued.
     *
     * @param int $membershipId
     * @param int $adminId
     * @param string|null $reason
     * @return array [success => bool, message => string, member_number => string|null]
     */
    public static function approve(int $membershipId, int $adminId, ?string $reason = null): array
    {
        $db = Database::connect();
        $db->transBegin();

        try {
            $membershipModel = model(MembershipModel::class);
            $historyModel    = model(MembershipStatusHistoryModel::class);

            // Fetch with row lock inside transaction
            $membership = $membershipModel->where('id', $membershipId)->first();
            if (! $membership) {
                throw new RuntimeException('Data keanggotaan tidak ditemukan.');
            }

            if ($membership->status === 'active') {
                $db->transRollback();
                return [
                    'success'       => false,
                    'message'       => 'Anggota ini sudah berstatus aktif.',
                    'member_number' => $membership->member_number,
                ];
            }

            $currentYear = (int) date('Y');
            $memberNumber = $membership->member_number;

            // Generate member number ONLY if not already assigned
            if (empty($memberNumber)) {
                $memberNumber = self::generateNextMemberNumber($currentYear);
            }

            $prevStatus = $membership->status;

            // Update membership
            $updateData = [
                'status'            => 'active',
                'member_number'     => $memberNumber,
                'approved_at'       => $membership->approved_at ?: date('Y-m-d H:i:s'),
                'approved_by'       => $adminId,
                'rejection_reason'  => null,
                'suspension_reason' => null,
                'status_notes'      => $reason ? trim($reason) : null,
            ];

            // Ensure verification token is generated upon approval for immediate KTA availability
            if (empty($membership->verification_token_selector)) {
                $selector  = bin2hex(random_bytes(8));
                $appKey    = config('App')->appTimezone ?? 'komeo_kta_key';
                $validator = substr(hash_hmac('sha256', $selector . '|' . $membershipId . '|' . ($membership->created_at ?? date('Y-m-d')), $appKey), 0, 32);
                $updateData['verification_token_selector'] = $selector;
                $updateData['verification_token_hash']     = hash('sha256', $validator);
                $updateData['qr_generated_at']             = date('Y-m-d H:i:s');
            }

            $membershipModel->skipValidation(true)->update($membershipId, $updateData);

            // Record status history
            $historyModel->recordTransition(
                $membershipId,
                $prevStatus,
                'active',
                $adminId,
                $reason ?: 'Keanggotaan disetujui oleh administrator.'
            );

            $db->transCommit();

            return [
                'success'       => true,
                'message'       => "Keanggotaan berhasil disetujui dengan Nomor Anggota: {$memberNumber}.",
                'member_number' => $memberNumber,
            ];
        } catch (Throwable $e) {
            $db->transRollback();
            return [
                'success'       => false,
                'message'       => 'Gagal memproses persetujuan anggota: ' . $e->getMessage(),
                'member_number' => null,
            ];
        }
    }

    /**
     * Reject a membership application with a required reason.
     *
     * @param int $membershipId
     * @param int $adminId
     * @param string $reason
     * @return array [success => bool, message => string]
     */
    public static function reject(int $membershipId, int $adminId, string $reason): array
    {
        $reason = trim($reason);
        if (empty($reason)) {
            return ['success' => false, 'message' => 'Alasan penolakan wajib diisi.'];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $membershipModel = model(MembershipModel::class);
            $historyModel    = model(MembershipStatusHistoryModel::class);

            $membership = $membershipModel->where('id', $membershipId)->first();
            if (! $membership) {
                throw new RuntimeException('Data keanggotaan tidak ditemukan.');
            }

            $prevStatus = $membership->status;

            $membershipModel->skipValidation(true)->update($membershipId, [
                'status'           => 'rejected',
                'rejection_reason' => $reason,
                'status_notes'     => $reason,
            ]);

            $historyModel->recordTransition(
                $membershipId,
                $prevStatus,
                'rejected',
                $adminId,
                $reason
            );

            $db->transCommit();

            return [
                'success' => true,
                'message' => 'Permohonan keanggotaan berhasil ditolak.',
            ];
        } catch (Throwable $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Gagal menolak keanggotaan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Suspend an active membership with a required reason.
     *
     * @param int $membershipId
     * @param int $adminId
     * @param string $reason
     * @return array [success => bool, message => string]
     */
    public static function suspend(int $membershipId, int $adminId, string $reason): array
    {
        $reason = trim($reason);
        if (empty($reason)) {
            return ['success' => false, 'message' => 'Alasan penangguhan wajib diisi.'];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $membershipModel = model(MembershipModel::class);
            $historyModel    = model(MembershipStatusHistoryModel::class);

            $membership = $membershipModel->where('id', $membershipId)->first();
            if (! $membership) {
                throw new RuntimeException('Data keanggotaan tidak ditemukan.');
            }

            $prevStatus = $membership->status;

            $membershipModel->skipValidation(true)->update($membershipId, [
                'status'            => 'suspended',
                'suspension_reason' => $reason,
                'status_notes'      => $reason,
            ]);

            $historyModel->recordTransition(
                $membershipId,
                $prevStatus,
                'suspended',
                $adminId,
                $reason
            );

            $db->transCommit();

            return [
                'success' => true,
                'message' => 'Keanggotaan berhasil ditangguhkan.',
            ];
        } catch (Throwable $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Gagal menangguhkan keanggotaan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reactivate a suspended or rejected member.
     * Preserves the existing member number if already issued.
     *
     * @param int $membershipId
     * @param int $adminId
     * @param string|null $reason
     * @return array [success => bool, message => string]
     */
    public static function reactivate(int $membershipId, int $adminId, ?string $reason = null): array
    {
        $db = Database::connect();
        $db->transBegin();

        try {
            $membershipModel = model(MembershipModel::class);
            $historyModel    = model(MembershipStatusHistoryModel::class);

            $membership = $membershipModel->where('id', $membershipId)->first();
            if (! $membership) {
                throw new RuntimeException('Data keanggotaan tidak ditemukan.');
            }

            $prevStatus   = $membership->status;
            $memberNumber = $membership->member_number;

            // If reactivating a member who never had a member number (e.g. was rejected while pending)
            if (empty($memberNumber)) {
                $memberNumber = self::generateNextMemberNumber((int) date('Y'));
            }

            $membershipModel->skipValidation(true)->update($membershipId, [
                'status'            => 'active',
                'member_number'     => $memberNumber,
                'rejection_reason'  => null,
                'suspension_reason' => null,
                'status_notes'      => $reason ? trim($reason) : null,
            ]);

            $historyModel->recordTransition(
                $membershipId,
                $prevStatus,
                'active',
                $adminId,
                $reason ?: 'Keanggotaan diaktifkan kembali oleh administrator.'
            );

            $db->transCommit();

            return [
                'success' => true,
                'message' => 'Keanggotaan berhasil diaktifkan kembali.',
            ];
        } catch (Throwable $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Gagal mengaktifkan kembali keanggotaan: ' . $e->getMessage(),
            ];
        }
    }
}
