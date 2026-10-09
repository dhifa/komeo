<?php

namespace App\Models;

use CodeIgniter\Model;

class KtaExportLogModel extends Model
{
    protected $table            = 'kta_export_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'membership_id',
        'export_type',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Log an export event
     */
    public function logExport(int $userId, ?int $membershipId, string $exportType, ?string $ip = null, ?string $ua = null): int|false
    {
        return $this->insert([
            'user_id'       => $userId,
            'membership_id' => $membershipId,
            'export_type'   => $exportType,
            'ip_address'    => $ip,
            'user_agent'    => $ua ? substr($ua, 0, 250) : null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
