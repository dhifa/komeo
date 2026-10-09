<?php

namespace App\Models;

use CodeIgniter\Model;

class BlacklistModel extends Model
{
    protected $table            = 'event_blacklists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'membership_id',
        'member_number',
        'name',
        'entity_type',
        'city',
        'case_category',
        'incident_date',
        'description',
        'status',
        'evidence_notes',
        'is_public',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'          => 'required|min_length[3]|max_length[150]',
        'entity_type'   => 'required|in_list[vendor,eo,freelance,lainnya]',
        'case_category' => 'required|min_length[3]|max_length[100]',
        'description'   => 'required',
        'status'        => 'required|in_list[blacklisted,monitoring,resolved]',
    ];

    /**
     * Get all public blacklisted entities
     */
    public function getPublicList(int $limit = 20): array
    {
        return $this->where('is_public', 1)
            ->orderBy('status', 'ASC') // 'blacklisted' comes before 'monitoring' / 'resolved'
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }
}
