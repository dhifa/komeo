<?php

namespace App\Models;

use CodeIgniter\Model;

class HomepageBenefitModel extends Model
{
    protected $table            = 'homepage_benefits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'title'       => 'required|min_length[3]|max_length[150]',
        'description' => 'required',
        'icon'        => 'required|max_length[50]',
        'sort_order'  => 'permit_empty|integer',
        'is_active'   => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Get all active benefits sorted by sort_order
     */
    public function getActiveBenefits(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
