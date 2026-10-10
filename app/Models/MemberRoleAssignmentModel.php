<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberRoleAssignmentModel extends Model
{
    protected $table            = 'komeo_member_role_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role_id',
        'is_primary',
        'assigned_by',
        'assigned_at',
        'expires_at',
        'revoked_at',
        'revoked_by',
        'internal_note',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active role assignments for a user
     */
    public function getActiveRolesForUser(int $userId): array
    {
        $now = date('Y-m-d H:i:s');
        return $this->db->table('komeo_member_role_assignments mra')
            ->select('mra.id as assignment_id, mra.user_id, mra.role_id, mra.is_primary, mra.assigned_at, mra.expires_at, mra.internal_note, mr.role_key, mr.name, mr.description, mr.icon, mr.background_color, mr.text_color, mr.sort_order, mr.is_public')
            ->join('komeo_member_roles mr', 'mr.id = mra.role_id')
            ->where('mra.user_id', $userId)
            ->where('mra.revoked_at IS NULL')
            ->groupStart()
                ->where('mra.expires_at IS NULL')
                ->orWhere('mra.expires_at >', $now)
            ->groupEnd()
            ->where('mr.is_active', 1)
            ->orderBy('mra.is_primary', 'DESC')
            ->orderBy('mr.sort_order', 'ASC')
            ->orderBy('mr.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get primary active role for a user
     */
    public function getPrimaryRoleForUser(int $userId): ?array
    {
        $roles = $this->getActiveRolesForUser($userId);
        foreach ($roles as $r) {
            if (! empty($r['is_primary'])) {
                return $r;
            }
        }
        return $roles[0] ?? null;
    }

    /**
     * Get public active roles for a user
     */
    public function getUserPublicRoles(int $userId): array
    {
        $roles = $this->getActiveRolesForUser($userId);
        return array_values(array_filter($roles, static fn ($r) => (int) ($r['is_public'] ?? 0) === 1));
    }

    /**
     * Assign a role to a user safely with transaction
     */
    public function assignRole(
        int $userId,
        int $roleId,
        bool $isPrimary = false,
        ?int $adminId = null,
        ?string $expiresAt = null,
        ?string $internalNote = null
    ): bool {
        $now = date('Y-m-d H:i:s');
        $this->db->transStart();

        // 1. If role is already actively assigned, update or return true
        $existing = $this->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->where('revoked_at IS NULL')
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >', $now)
            ->groupEnd()
            ->first();

        // If setting as primary, demote any other existing primary role
        if ($isPrimary) {
            $this->where('user_id', $userId)
                ->where('revoked_at IS NULL')
                ->set(['is_primary' => 0, 'updated_at' => $now])
                ->update();
        }

        if ($existing) {
            $this->update($existing['id'], [
                'is_primary'    => $isPrimary ? 1 : $existing['is_primary'],
                'expires_at'    => $expiresAt ?: $existing['expires_at'],
                'internal_note' => $internalNote ?: $existing['internal_note'],
                'updated_at'    => $now,
            ]);
        } else {
            // If user currently has NO active primary role, make this primary automatically if requested or if first role
            if (! $isPrimary) {
                $hasPrimary = $this->where('user_id', $userId)
                    ->where('is_primary', 1)
                    ->where('revoked_at IS NULL')
                    ->groupStart()
                        ->where('expires_at IS NULL')
                        ->orWhere('expires_at >', $now)
                    ->groupEnd()
                    ->countAllResults();
                if ($hasPrimary === 0) {
                    $isPrimary = true;
                }
            }

            $this->insert([
                'user_id'       => $userId,
                'role_id'       => $roleId,
                'is_primary'    => $isPrimary ? 1 : 0,
                'assigned_by'   => $adminId,
                'assigned_at'   => $now,
                'expires_at'    => $expiresAt ?: null,
                'internal_note' => $internalNote ?: null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Revoke a role assignment
     */
    public function revokeRole(int $assignmentId, ?int $adminId = null, ?string $reason = null): bool
    {
        $assignment = $this->find($assignmentId);
        if (! $assignment || $assignment['revoked_at'] !== null) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $note = $assignment['internal_note'] ?? '';
        if ($reason) {
            $note = trim($note . "\n[Dicabut: " . date('d/m/Y H:i') . "] Alasan: " . $reason);
        }

        $this->db->transStart();

        $this->update($assignmentId, [
            'revoked_at'    => $now,
            'revoked_by'    => $adminId,
            'internal_note' => $note,
            'updated_at'    => $now,
        ]);

        // If the revoked role was primary, promote another active role if available
        if (! empty($assignment['is_primary'])) {
            $nextRole = $this->where('user_id', $assignment['user_id'])
                ->where('id !=', $assignmentId)
                ->where('revoked_at IS NULL')
                ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >', $now)
                ->groupEnd()
                ->first();

            if ($nextRole) {
                $this->update($nextRole['id'], ['is_primary' => 1, 'updated_at' => $now]);
            }
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Revoke by user and role id
     */
    public function revokeUserRole(int $userId, int $roleId, ?int $adminId = null, ?string $reason = null): bool
    {
        $assignment = $this->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->where('revoked_at IS NULL')
            ->first();
        if (! $assignment) {
            return false;
        }
        return $this->revokeRole((int) $assignment['id'], $adminId, $reason);
    }

    /**
     * Idempotently ensure an active member has the default role
     */
    public function ensureDefaultRole(int $userId, ?int $adminId = null): bool
    {
        $existing = $this->getActiveRolesForUser($userId);
        if (! empty($existing)) {
            return true; // Already has at least one active community role
        }

        $roleModel = model(MemberRoleModel::class);
        $defaultKey = site_setting('MemberRole.default_role_key', 'anggota-reguler');
        $defaultRole = $roleModel->where('role_key', $defaultKey)->where('is_active', 1)->first();

        if (! $defaultRole) {
            $defaultRole = $roleModel->getDefaultRole();
        }

        if ($defaultRole) {
            return $this->assignRole($userId, (int) $defaultRole['id'], true, $adminId, null, 'Penetapan role bawaan sistem saat aktivasi.');
        }

        return false;
    }
}
