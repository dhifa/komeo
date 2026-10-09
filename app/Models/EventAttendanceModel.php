<?php

namespace App\Models;

use CodeIgniter\Model;

class EventAttendanceModel extends Model
{
    protected $table            = 'event_attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_id',
        'registration_id',
        'checkin_method', // qr_ticket, kta_qr, manual
        'checked_in_by',
        'checked_in_at',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
