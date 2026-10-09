<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberPortfolioModel extends Model
{
    public const MAX_ITEMS_PER_MEMBER = 6;

    protected $table            = 'member_portfolios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_profile_id',
        'title',
        'description',
        'project_year',
        'event_location',
        'cover_image',
        'external_url',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'member_profile_id' => 'required|is_natural_no_zero',
        'title'             => 'required|min_length[3]|max_length[150]',
        'description'       => 'required|min_length[10]',
        'project_year'      => 'required|max_length[10]',
        'event_location'    => 'permit_empty|max_length[150]',
        'cover_image'       => 'required|max_length[255]',
        'external_url'      => 'permit_empty|valid_url|max_length[255]',
        'sort_order'        => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Judul proyek / event wajib diisi.',
            'min_length' => 'Judul minimal 3 karakter.',
        ],
        'description' => [
            'required'   => 'Deskripsi proyek wajib diisi.',
            'min_length' => 'Deskripsi minimal 10 karakter.',
        ],
        'project_year' => [
            'required' => 'Tahun pelaksanaan event wajib diisi.',
        ],
        'cover_image' => [
            'required' => 'Gambar sampul portofolio wajib diunggah.',
        ],
        'external_url' => [
            'valid_url' => 'Format tautan eksternal harus berupa URL yang valid.',
        ],
    ];

    /**
     * Get all portfolio items for a profile ordered by sort_order ASC, id DESC
     */
    public function getByProfileId(int $profileId): array
    {
        return $this->where('member_profile_id', $profileId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Count portfolio items for a profile
     */
    public function countByProfileId(int $profileId): int
    {
        return $this->where('member_profile_id', $profileId)->countAllResults();
    }

    /**
     * Find portfolio item verifying ownership
     */
    public function findOwnedItem(int $id, int $profileId): ?array
    {
        return $this->where('id', $id)
            ->where('member_profile_id', $profileId)
            ->first();
    }
}
