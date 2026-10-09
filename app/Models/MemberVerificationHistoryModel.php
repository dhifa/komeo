<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberVerificationHistoryModel extends Model
{
    protected $table            = 'member_verification_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'verification_id',
        'user_id',
        'action',
        'previous_status',
        'new_status',
        'verification_level',
        'actor_id',
        'actor_role',
        'notes',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Log history event
     */
    public function recordEvent(
        int $verificationId,
        int $userId,
        string $action,
        ?string $previousStatus,
        string $newStatus,
        string $verificationLevel,
        ?int $actorId = null,
        ?string $actorRole = 'member',
        ?string $notes = null
    ): int {
        return (int) $this->insert([
            'verification_id'    => $verificationId,
            'user_id'            => $userId,
            'action'             => $action,
            'previous_status'    => $previousStatus,
            'new_status'         => $newStatus,
            'verification_level' => $verificationLevel,
            'actor_id'           => $actorId,
            'actor_role'         => $actorRole,
            'notes'              => $notes,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get history records for a verification
     */
    public function getByVerificationId(int $verificationId): array
    {
        return $this->select('member_verification_history.*, users.username as actor_username')
            ->join('users', 'users.id = member_verification_history.actor_id', 'left')
            ->where('verification_id', $verificationId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
