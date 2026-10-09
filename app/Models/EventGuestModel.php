<?php

namespace App\Models;

use CodeIgniter\Model;

class EventGuestModel extends Model
{
    protected $table            = 'event_guests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'whatsapp',
        'company',
        'job_title',
        'profession_category',
        'city',
        'province',
        'is_email_verified',
        'verified_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'  => 'required|min_length[3]|max_length[150]',
        'email' => 'required|valid_email|max_length[255]',
    ];
}
