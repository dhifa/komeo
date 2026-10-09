<?php

namespace App\Models;

use CodeIgniter\Model;

class BlacklistCaseCategoryModel extends Model
{
    protected $table            = 'blacklist_case_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
    ];

    /**
     * Get list of active categories for dropdown
     */
    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
