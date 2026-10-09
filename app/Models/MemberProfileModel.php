<?php

namespace App\Models;

use App\Entities\MemberProfile;
use CodeIgniter\Model;

class MemberProfileModel extends Model
{
    protected $table            = 'member_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = MemberProfile::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'full_name',
        'display_name',
        'username',
        'member_type',
        'category_id',
        'business_name',
        'business_description',
        'years_of_experience',
        'photo_path',
        'company_logo_path',
        'bio',
        'city',
        'province',
        'instagram',
        'tiktok',
        'linkedin',
        'youtube',
        'website',
        'whatsapp',
        'is_public',
        'show_whatsapp',
        'show_social',
        'show_location',
        'is_featured',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'             => 'required|is_natural_no_zero',
        'full_name'           => 'required|min_length[2]|max_length[150]',
        'display_name'        => 'permit_empty|max_length[100]',
        'username'            => 'required|alpha_dash|min_length[3]|max_length[50]',
        'member_type'         => 'required|in_list[individual,business]',
        'category_id'         => 'permit_empty|is_natural_no_zero',
        'business_name'       => 'permit_empty|max_length[150]',
        'years_of_experience' => 'permit_empty|is_natural|less_than[70]',
        'city'                => 'permit_empty|max_length[100]',
        'province'            => 'permit_empty|max_length[100]',
        'whatsapp'            => 'permit_empty|max_length[30]',
        'instagram'           => 'permit_empty|max_length[100]',
        'tiktok'              => 'permit_empty|max_length[100]',
        'linkedin'            => 'permit_empty|valid_url|max_length[255]',
        'youtube'             => 'permit_empty|valid_url|max_length[255]',
        'website'             => 'permit_empty|valid_url|max_length[255]',
        'is_public'           => 'permit_empty|in_list[0,1]',
        'show_whatsapp'       => 'permit_empty|in_list[0,1]',
        'show_social'         => 'permit_empty|in_list[0,1]',
        'show_location'       => 'permit_empty|in_list[0,1]',
        'is_featured'         => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'full_name' => [
            'required'   => 'Nama lengkap wajib diisi.',
            'min_length' => 'Nama lengkap minimal 2 karakter.',
        ],
        'username' => [
            'required'   => 'Nama pengguna (username) wajib diisi.',
            'min_length' => 'Nama pengguna minimal 3 karakter.',
            'alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ],
        'member_type' => [
            'required' => 'Pilih tipe keanggotaan.',
            'in_list'  => 'Tipe keanggotaan tidak valid.',
        ],
        'linkedin' => [
            'valid_url' => 'Format URL LinkedIn harus berupa alamat web yang valid.',
        ],
        'youtube' => [
            'valid_url' => 'Format URL YouTube harus berupa alamat web yang valid.',
        ],
        'website' => [
            'valid_url' => 'Format URL Website harus berupa alamat web yang valid.',
        ],
    ];

    /**
     * Find profile by user ID
     */
    public function findByUserId(int $userId): ?MemberProfile
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Alias for findByUserId
     */
    public function getByUserId(int $userId): ?MemberProfile
    {
        return $this->findByUserId($userId);
    }

    /**
     * Find profile by username
     */
    public function findByUsername(string $username): ?MemberProfile
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Check if username is already taken by another profile
     */
    public function isUsernameTaken(string $username, int $excludeProfileId = 0): bool
    {
        $builder = $this->where('username', $username);
        if ($excludeProfileId > 0) {
            $builder->where('id !=', $excludeProfileId);
        }
        return $builder->countAllResults() > 0;
    }
}
