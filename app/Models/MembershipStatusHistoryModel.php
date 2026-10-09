<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipStatusHistoryModel extends Model
{
    protected $table            = 'membership_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'membership_id',
        'previous_status',
        'new_status',
        'admin_id',
        'reason',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get history timeline by membership ID with admin details
     *
     * @param int $membershipId
     * @return array
     */
    public function getByMembershipId(int $membershipId): array
    {
        return $this->select('membership_status_history.*, users.username as admin_username, member_profiles.full_name as admin_full_name')
            ->join('users', 'users.id = membership_status_history.admin_id', 'left')
            ->join('member_profiles', 'member_profiles.user_id = membership_status_history.admin_id', 'left')
            ->where('membership_status_history.membership_id', $membershipId)
            ->orderBy('membership_status_history.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Record a new status transition
     */
    public function recordTransition(
        int $membershipId,
        ?string $prevStatus,
        string $newStatus,
        ?int $adminId,
        ?string $reason = null
    ): int {
        return $this->insert([
            'membership_id'   => $membershipId,
            'previous_status' => $prevStatus,
            'new_status'      => $newStatus,
            'admin_id'        => $adminId,
            'reason'          => $reason ? trim($reason) : null,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }
}
