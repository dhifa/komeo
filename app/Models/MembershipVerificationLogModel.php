<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipVerificationLogModel extends Model
{
    protected $table            = 'membership_verification_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'membership_id',
        'verification_type', // public_qr, admin_scan, event_checkin
        'verified_by',
        'ip_address',
        'user_agent',
        'status', // valid, invalid, suspended
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
