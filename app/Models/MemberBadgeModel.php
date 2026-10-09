<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberBadgeModel extends Model
{
    protected $table            = 'member_badges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'badge_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'expires_at',
        'revoked_at',
        'revoked_by',
        'internal_note',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active badges for a specific user
     */
    public function getActiveBadgesForUser(int $userId, int $limit = 0): array
    {
        $now = date('Y-m-d H:i:s');
        $builder = $this->db->table('member_badges mb')
            ->select('mb.id as member_badge_id, mb.assigned_at, mb.expires_at, mb.internal_note, bd.id as badge_id, bd.name, bd.slug, bd.description, bd.icon, bd.background_color, bd.text_color, bd.sort_order')
            ->join('badge_definitions bd', 'bd.id = mb.badge_id')
            ->where('mb.user_id', $userId)
            ->where('mb.revoked_at IS NULL')
            ->groupStart()
                ->where('mb.expires_at IS NULL')
                ->orWhere('mb.expires_at >', $now)
            ->groupEnd()
            ->where('bd.is_active', 1)
            ->orderBy('bd.sort_order', 'ASC')
            ->orderBy('bd.name', 'ASC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Check if a member currently has an active badge assignment
     */
    public function hasActiveBadge(int $userId, int $badgeId): bool
    {
        $now = date('Y-m-d H:i:s');
        $count = $this->db->table('member_badges')
            ->where('user_id', $userId)
            ->where('badge_id', $badgeId)
            ->where('revoked_at IS NULL')
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >', $now)
            ->groupEnd()
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Assign badge to a member with duplicate prevention
     */
    public function assignBadge(int $badgeId, int $userId, int $adminId, ?string $expiresAt = null, ?string $internalNote = null): array
    {
        if ($this->hasActiveBadge($userId, $badgeId)) {
            return [
                'success' => false,
                'message' => 'Anggota ini sudah memiliki badge tersebut yang masih aktif.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        $cleanExpires = null;
        if (! empty($expiresAt)) {
            $cleanExpires = date('Y-m-d H:i:s', strtotime($expiresAt));
        }

        $data = [
            'badge_id'      => $badgeId,
            'user_id'       => $userId,
            'assigned_by'   => $adminId,
            'assigned_at'   => $now,
            'expires_at'    => $cleanExpires,
            'revoked_at'    => null,
            'revoked_by'    => null,
            'internal_note' => $internalNote ? trim(strip_tags($internalNote)) : null,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        $insertId = $this->insert($data);

        return [
            'success' => (bool) $insertId,
            'id'      => $insertId,
            'message' => $insertId ? 'Badge berhasil disematkan kepada anggota.' : 'Gagal menyematkan badge.',
        ];
    }

    /**
     * Revoke badge assignment
     */
    public function revokeBadge(int $memberBadgeId, int $adminId): array
    {
        $existing = $this->find($memberBadgeId);
        if (! $existing) {
            return [
                'success' => false,
                'message' => 'Data penugasan badge tidak ditemukan.',
            ];
        }

        if (! empty($existing['revoked_at'])) {
            return [
                'success' => false,
                'message' => 'Badge ini sudah dicabut sebelumnya.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        $updated = $this->update($memberBadgeId, [
            'revoked_at' => $now,
            'revoked_by' => $adminId,
            'updated_at' => $now,
        ]);

        return [
            'success' => $updated,
            'message' => $updated ? 'Badge anggota berhasil dicabut.' : 'Gagal mencabut badge.',
        ];
    }

    /**
     * Get all badge assignments for a user (history/audit view)
     */
    public function getAllBadgesForUser(int $userId): array
    {
        return $this->db->table('member_badges mb')
            ->select('mb.*, bd.name as badge_name, bd.slug as badge_slug, bd.description, bd.icon, bd.background_color, bd.text_color, bd.sort_order, u_assign.username as assigned_by_username, u_revoke.username as revoked_by_username')
            ->join('badge_definitions bd', 'bd.id = mb.badge_id')
            ->join('users u_assign', 'u_assign.id = mb.assigned_by', 'left')
            ->join('users u_revoke', 'u_revoke.id = mb.revoked_by', 'left')
            ->where('mb.user_id', $userId)
            ->orderBy('mb.assigned_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get members assigned to a specific badge
     */
    public function getAssignedMembers(int $badgeId): array
    {
        return $this->db->table('member_badges mb')
            ->select('mb.*, p.full_name, p.display_name, p.username, p.member_type, p.business_name, p.photo_path, m.status as membership_status, m.member_number, u_assign.username as assigned_by_username, u_revoke.username as revoked_by_username')
            ->join('member_profiles p', 'p.user_id = mb.user_id', 'left')
            ->join('memberships m', 'm.user_id = mb.user_id', 'left')
            ->join('users u_assign', 'u_assign.id = mb.assigned_by', 'left')
            ->join('users u_revoke', 'u_revoke.id = mb.revoked_by', 'left')
            ->where('mb.badge_id', $badgeId)
            ->orderBy('mb.assigned_at', 'DESC')
            ->get()->getResultArray();
    }
}
