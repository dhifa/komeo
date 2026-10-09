<?php

namespace App\Models;

use CodeIgniter\Model;

class EventCheckinLogModel extends Model
{
    protected $table            = 'event_checkin_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'registration_id',
        'scanned_token_type', // event_ticket, kta_qr, manual
        'scanned_identifier',
        'status', // success, failed, duplicate, invalid
        'reason',
        'staff_user_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
