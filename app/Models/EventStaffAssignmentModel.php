<?php

namespace App\Models;

use CodeIgniter\Model;

class EventStaffAssignmentModel extends Model
{
    protected $table            = 'event_staff_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'user_id',
        'assigned_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Check if a user is assigned to an event or is superadmin/admin
     */
    public function isUserAssignedToEvent(int $userId, int $eventId): bool
    {
        // Admin or superadmin has global access
        $auth = auth();
        if ($auth->loggedIn()) {
            $user = $auth->user();
            if ($user && ($user->inGroup('superadmin') || $user->inGroup('admin'))) {
                return true;
            }
        }

        $assignment = $this->where('event_id', $eventId)->where('user_id', $userId)->first();
        return ! empty($assignment);
    }

    /**
     * Get staff users assigned to event
     */
    public function getStaffForEvent(int $eventId): array
    {
        return $this->db->table('event_staff_assignments esa')
            ->select('esa.*, u.username, u.email, mp.full_name, mp.display_name')
            ->join('users u', 'u.id = esa.user_id')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->where('esa.event_id', $eventId)
            ->get()->getResultArray();
    }
}
