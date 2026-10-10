<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionIssueModel extends Model
{
    protected $table            = 'komeo_transaction_issues';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'issue_type',
        'status',
        'description',
        'public_label',
        'resolution_note',
        'reported_at',
        'resolved_at',
        'recorded_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get issues for a transaction
     */
    public function getByTransactionId(int $transactionId): array
    {
        return $this->where('transaction_id', $transactionId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get active (unresolved) issue for a transaction
     */
    public function getActiveIssue(int $transactionId): ?array
    {
        return $this->where('transaction_id', $transactionId)
            ->whereNotIn('status', ['none', 'resolved'])
            ->orderBy('id', 'DESC')
            ->first();
    }
}
