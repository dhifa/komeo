<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberDocumentModel extends Model
{
    protected $table            = 'member_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'document_type', // cv, portfolio_pdf, portfolio_external
        'title',
        'description',
        'file_path',
        'file_size',
        'mime_type',
        'external_url',
        'external_platform',
        'visibility', // public, request_only, private
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active CV for a user
     */
    public function getUserCv(int $userId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('document_type', 'cv')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Get portfolio documents for a user
     */
    public function getUserPortfolios(int $userId, ?string $visibility = null): array
    {
        $builder = $this->where('user_id', $userId)
            ->whereIn('document_type', ['portfolio_pdf', 'portfolio_external'])
            ->where('is_active', 1);

        if ($visibility !== null) {
            $builder->where('visibility', $visibility);
        }

        return $builder->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Get count of PDF portfolios for a user (max 5 default)
     */
    public function countUserPdfPortfolios(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('document_type', 'portfolio_pdf')
            ->where('is_active', 1)
            ->countAllResults();
    }
}
