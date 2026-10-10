<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionAuditLogModel extends Model
{
    protected $table            = 'komeo_transaction_audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'action',
        'previous_data',
        'new_data',
        'admin_user_id',
        'ip_address',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Log an administrative action on transactions
     */
    public function log(
        ?int $transactionId,
        string $action,
        ?array $previousData = null,
        ?array $newData = null,
        ?int $adminUserId = null,
        string $ipAddress = ''
    ): int {
        $data = [
            'transaction_id' => $transactionId,
            'action'         => $action,
            'previous_data'  => $previousData !== null ? json_encode($previousData, JSON_UNESCAPED_UNICODE) : null,
            'new_data'       => $newData !== null ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'admin_user_id'  => $adminUserId ?? (auth()->id() ? (int) auth()->id() : null),
            'ip_address'     => $ipAddress ?: (service('request')->getIPAddress() ?? ''),
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        return (int) $this->insert($data);
    }
}
