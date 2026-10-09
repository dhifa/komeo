<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentAccessLogModel extends Model
{
    protected $table            = 'document_access_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_share_id',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected $useTimestamps = false;
}
