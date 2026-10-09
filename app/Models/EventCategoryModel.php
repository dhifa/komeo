<?php

namespace App\Models;

use CodeIgniter\Model;

class EventCategoryModel extends Model
{
    protected $table            = 'event_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'       => 'required|min_length[2]|max_length[100]',
        'slug'       => 'required|alpha_dash|max_length[100]',
        'sort_order' => 'required|integer',
        'is_active'  => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama kategori wajib diisi.',
        ],
        'slug' => [
            'required'   => 'Slug kategori wajib diisi.',
            'alpha_dash' => 'Slug hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
        ],
    ];

    /**
     * Get all active categories ordered by sort order
     */
    public function getActiveCategories(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get categories with members count
     */
    public function getWithMemberCount(): array
    {
        return $this->select('event_categories.*, COUNT(member_profiles.id) as member_count')
            ->join('member_profiles', 'member_profiles.category_id = event_categories.id', 'left')
            ->groupBy('event_categories.id')
            ->orderBy('event_categories.sort_order', 'ASC')
            ->orderBy('event_categories.name', 'ASC')
            ->findAll();
    }

    /**
     * Check if a category is currently assigned to any member
     */
    public function isUsedByMembers(int $categoryId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('member_profiles')->where('category_id', $categoryId)->countAllResults() > 0;
    }
}
