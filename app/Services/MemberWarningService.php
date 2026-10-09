<?php

namespace App\Services;

use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use App\Models\MemberWarningModel;
use Config\Database;
use RuntimeException;
use Throwable;

class MemberWarningService
{
    /**
     * Issue an official warning (SP1, SP2, SP3) to a member.
     * Note: If SP3 is issued, the membership is automatically suspended!
     *
     * @param int $membershipId
     * @param string $warningLevel 'sp1'|'sp2'|'sp3'
     * @param string $reason
     * @param string|null $notes
     * @param int $adminId
     * @return array [success => bool, message => string, warning_id => ?int]
     */
    public static function issueWarning(
        int $membershipId,
        string $warningLevel,
        string $reason,
        ?string $notes,
        int $adminId
    ): array {
        $warningLevel = strtolower(trim($warningLevel));
        if (! in_array($warningLevel, ['sp1', 'sp2', 'sp3'], true)) {
            return ['success' => false, 'message' => 'Tingkat peringatan tidak valid (harus SP1, SP2, atau SP3).', 'warning_id' => null];
        }

        $reason = trim($reason);
        if (empty($reason)) {
            return ['success' => false, 'message' => 'Alasan penerbitan surat peringatan wajib diisi.', 'warning_id' => null];
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($membershipId);
        if (! $membership) {
            return ['success' => false, 'message' => 'Data keanggotaan tidak ditemukan.', 'warning_id' => null];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $warningModel = model(MemberWarningModel::class);

            $warningData = [
                'membership_id' => $membershipId,
                'user_id'       => (int) $membership->user_id,
                'warning_level' => $warningLevel,
                'reason'        => $reason,
                'notes'         => ! empty($notes) ? trim($notes) : null,
                'issued_by'     => $adminId,
                'status'        => 'active',
            ];

            $warningId = $warningModel->insert($warningData);
            if (! $warningId) {
                $errs = $warningModel->errors();
                $errText = ! empty($errs) ? implode(', ', $errs) : 'Gagal menyimpan data ke database.';
                throw new RuntimeException($errText);
            }

            // Update membership active warning level
            $highestLevel = $warningModel->getHighestActiveWarningLevel($membershipId) ?: $warningLevel;
            $membershipModel->skipValidation(true)->update($membershipId, [
                'active_warning_level' => $highestLevel,
            ]);

            // If SP3, automatically suspend the membership!
            if ($warningLevel === 'sp3') {
                $suspendResult = MembershipService::suspend(
                    $membershipId,
                    $adminId,
                    'Surat Peringatan 3 (SP3): ' . $reason
                );

                if (! $suspendResult['success']) {
                    throw new RuntimeException($suspendResult['message']);
                }
            }

            $db->transCommit();

            $levelLabel = match ($warningLevel) {
                'sp1' => 'Surat Peringatan 1 (SP1)',
                'sp2' => 'Surat Peringatan 2 (SP2)',
                'sp3' => 'Surat Peringatan 3 (SP3 - Akun Ditangguhkan)',
            };

            return [
                'success'    => true,
                'message'    => "{$levelLabel} berhasil diterbitkan dan akan muncul di akun member.",
                'warning_id' => (int) $warningId,
            ];
        } catch (Throwable $e) {
            $db->transRollback();
            return [
                'success'    => false,
                'message'    => 'Gagal menerbitkan surat peringatan: ' . $e->getMessage(),
                'warning_id' => null,
            ];
        }
    }

    /**
     * Resolve an active warning
     *
     * @param int $warningId
     * @param int $adminId
     * @param string|null $resolutionNotes
     * @param bool $reactivate
     * @return array [success => bool, message => string]
     */
    public static function resolveWarning(
        int $warningId,
        int $adminId,
        ?string $resolutionNotes,
        bool $reactivate = false
    ): array {
        $warningModel = model(MemberWarningModel::class);
        $warning = $warningModel->find($warningId);
        if (! $warning) {
            return ['success' => false, 'message' => 'Data surat peringatan tidak ditemukan.'];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $warningModel->update($warningId, [
                'status'           => 'resolved',
                'resolved_at'      => date('Y-m-d H:i:s'),
                'resolved_by'      => $adminId,
                'resolution_notes' => ! empty($resolutionNotes) ? trim($resolutionNotes) : 'Diselesaikan oleh admin.',
            ]);

            $membershipId = (int) $warning->membership_id;
            $highestLevel = $warningModel->getHighestActiveWarningLevel($membershipId);

            $membershipModel = model(MembershipModel::class);
            $membershipModel->skipValidation(true)->update($membershipId, [
                'active_warning_level' => $highestLevel,
            ]);

            // Reactivate membership if requested and no remaining SP3
            if ($reactivate && $highestLevel !== 'sp3') {
                $membership = $membershipModel->find($membershipId);
                if ($membership && $membership->status === 'suspended') {
                    MembershipService::reactivate(
                        $membershipId,
                        $adminId,
                        'Pemulihan akun setelah penyelesaian SP: ' . ($resolutionNotes ?: 'Klarifikasi diterima.')
                    );
                }
            }

            $db->transCommit();

            return ['success' => true, 'message' => 'Status surat peringatan berhasil diselesaikan.'];
        } catch (Throwable $e) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Gagal memperbarui peringatan: ' . $e->getMessage()];
        }
    }

    /**
     * Revoke / cancel a warning
     *
     * @param int $warningId
     * @param int $adminId
     * @param string|null $notes
     * @param bool $reactivate
     * @return array [success => bool, message => string]
     */
    public static function revokeWarning(
        int $warningId,
        int $adminId,
        ?string $notes,
        bool $reactivate = false
    ): array {
        $warningModel = model(MemberWarningModel::class);
        $warning = $warningModel->find($warningId);
        if (! $warning) {
            return ['success' => false, 'message' => 'Data surat peringatan tidak ditemukan.'];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            $warningModel->update($warningId, [
                'status'           => 'revoked',
                'resolved_at'      => date('Y-m-d H:i:s'),
                'resolved_by'      => $adminId,
                'resolution_notes' => 'Dicabut oleh admin' . (! empty($notes) ? ': ' . trim($notes) : '.'),
            ]);

            $membershipId = (int) $warning->membership_id;
            $highestLevel = $warningModel->getHighestActiveWarningLevel($membershipId);

            $membershipModel = model(MembershipModel::class);
            $membershipModel->skipValidation(true)->update($membershipId, [
                'active_warning_level' => $highestLevel,
            ]);

            // Reactivate membership if requested and no remaining SP3
            if ($reactivate && $highestLevel !== 'sp3') {
                $membership = $membershipModel->find($membershipId);
                if ($membership && $membership->status === 'suspended') {
                    MembershipService::reactivate(
                        $membershipId,
                        $adminId,
                        'Pemulihan akun setelah pencabutan SP: ' . ($notes ?: 'Peringatan dibatalkan.')
                    );
                }
            }

            $db->transCommit();

            return ['success' => true, 'message' => 'Surat peringatan berhasil dicabut (status akun kembali ke kondisi bersih/SP0).'];
        } catch (Throwable $e) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Gagal mencabut peringatan: ' . $e->getMessage()];
        }
    }

    /**
     * Delete warning permanently and recalculate remaining active warning level (returning to SP0/null if empty)
     */
    public static function deleteWarning(
        int $warningId,
        int $adminId,
        bool $reactivate = false
    ): array {
        $warningModel = model(MemberWarningModel::class);
        $warning = $warningModel->find($warningId);
        if (! $warning) {
            return ['success' => false, 'message' => 'Data surat peringatan tidak ditemukan.'];
        }

        $membershipId = (int) $warning->membership_id;

        $db = Database::connect();
        $db->transBegin();

        try {
            $warningModel->delete($warningId);

            $highestLevel = $warningModel->getHighestActiveWarningLevel($membershipId);

            $membershipModel = model(MembershipModel::class);
            $membershipModel->skipValidation(true)->update($membershipId, [
                'active_warning_level' => $highestLevel,
            ]);

            // Reactivate membership if requested and no remaining SP3
            if ($reactivate && $highestLevel !== 'sp3') {
                $membership = $membershipModel->find($membershipId);
                if ($membership && $membership->status === 'suspended') {
                    MembershipService::reactivate(
                        $membershipId,
                        $adminId,
                        'Pemulihan akun setelah penghapusan surat peringatan (kembali ke SP0/bersih).'
                    );
                }
            }

            $db->transCommit();

            return ['success' => true, 'message' => 'Surat peringatan berhasil dihapus permanen. Akun kini bersih (SP0).'];
        } catch (Throwable $e) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Gagal menghapus peringatan: ' . $e->getMessage()];
        }
    }
}
