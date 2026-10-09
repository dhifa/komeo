<?php

namespace App\Models;

use CodeIgniter\Model;

class SpecializationModel extends Model
{
    protected $table            = 'specializations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[100]',
        'slug'        => 'required|alpha_dash|max_length[100]',
        'category_id' => 'permit_empty|is_natural_no_zero',
        'sort_order'  => 'required|integer',
        'is_active'   => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama keahlian spesialisasi wajib diisi.',
        ],
        'slug' => [
            'required'   => 'Slug spesialisasi wajib diisi.',
            'alpha_dash' => 'Slug hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
        ],
    ];

    /**
     * Get all active specializations
     */
    public function getActiveSpecializations(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get specializations with category and member count
     */
    public function getWithDetails(): array
    {
        return $this->select('specializations.*, event_categories.name as category_name, COUNT(member_specializations.member_profile_id) as member_count')
            ->join('event_categories', 'event_categories.id = specializations.category_id', 'left')
            ->join('member_specializations', 'member_specializations.specialization_id = specializations.id', 'left')
            ->groupBy('specializations.id')
            ->orderBy('specializations.sort_order', 'ASC')
            ->orderBy('specializations.name', 'ASC')
            ->findAll();
    }

    /**
     * Check if a specialization is used by any member
     */
    public function isUsedByMembers(int $specId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('member_specializations')->where('specialization_id', $specId)->countAllResults() > 0;
    }
}
