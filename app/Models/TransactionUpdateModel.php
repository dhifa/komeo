<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionUpdateModel extends Model
{
    protected $table            = 'komeo_transaction_updates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'update_type',
        'previous_status',
        'new_status',
        'public_description',
        'internal_note',
        'visible_to_members',
        'created_by',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get timeline entries for a transaction
     */
    public function getTimeline(int $transactionId, bool $membersOnly = false): array
    {
        $builder = $this->where('transaction_id', $transactionId);
        if ($membersOnly) {
            $builder->where('visible_to_members', 1);
        }
        return $builder->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
