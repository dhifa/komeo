<?php

namespace App\Models;

use CodeIgniter\Model;

class KtaExportBatchModel extends Model
{
    protected $table            = 'kta_export_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'admin_id',
        'batch_code',
        'export_mode',
        'paper_size',
        'member_count',
        'file_path',
        'file_name',
        'file_size',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get recent batches with admin details
     */
    public function getRecentBatches(int $limit = 20): array
    {
        return $this->select('kta_export_batches.*, u.username as admin_username')
            ->join('users u', 'u.id = kta_export_batches.admin_id', 'left')
            ->orderBy('kta_export_batches.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
