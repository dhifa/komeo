<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class MemberProfile extends Entity
{
    protected $datamap = [
        'company_name' => 'business_name',
        'account_type' => 'member_type',
        'avatar'       => 'photo_path',
    ];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'id'                  => 'integer',
        'user_id'             => 'integer',
        'category_id'         => '?integer',
        'years_of_experience' => '?integer',
        'is_public'           => 'boolean',
        'show_whatsapp'       => 'boolean',
        'show_social'         => 'boolean',
        'show_location'       => 'boolean',
        'is_featured'         => 'boolean',
    ];

    /**
     * Get avatar URL or default placeholder
     */
    public function getAvatarUrl(): string
    {
        if (! empty($this->attributes['photo_path']) && file_exists(FCPATH . $this->attributes['photo_path'])) {
            return base_url($this->attributes['photo_path']);
        }

        // Return a clean UI avatar with member initials
        $name = urlencode($this->attributes['display_name'] ?? $this->attributes['full_name'] ?? 'Member');
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=ffffff&bold=true&size=200";
    }

    /**
     * Get company logo URL if applicable
     */
    public function getCompanyLogoUrl(): ?string
    {
        if (! empty($this->attributes['company_logo_path']) && file_exists(FCPATH . $this->attributes['company_logo_path'])) {
            return base_url($this->attributes['company_logo_path']);
        }
        return null;
    }

    /**
     * Check if profile is business type
     */
    public function isBusiness(): bool
    {
        return ($this->attributes['member_type'] ?? 'individual') === 'business';
    }

    /**
     * Calculate profile completion percentage and missing fields
     *
     * @param int $specializationsCount
     * @return array{percentage: int, completed_count: int, total_count: int, missing_fields: array<string>}
     */
    public function calculateCompletion(int $specializationsCount = 0): array
    {
        $criteria = [
            'full_name' => [
                'label'     => 'Nama Lengkap',
                'completed' => ! empty(trim((string) ($this->attributes['full_name'] ?? ''))),
            ],
            'display_name' => [
                'label'     => 'Nama Tampilan / Panggilan',
                'completed' => ! empty(trim((string) ($this->attributes['display_name'] ?? ''))),
            ],
            'photo' => [
                'label'     => 'Foto Profil',
                'completed' => ! empty($this->attributes['photo_path']) && file_exists(FCPATH . $this->attributes['photo_path']),
            ],
            'member_type' => [
                'label'     => 'Tipe Keanggotaan',
                'completed' => ! empty($this->attributes['member_type']),
            ],
            'category' => [
                'label'     => 'Kategori Industri Utama',
                'completed' => ! empty($this->attributes['category_id']),
            ],
            'specializations' => [
                'label'     => 'Keahlian Spesialisasi (Minimal 1)',
                'completed' => $specializationsCount > 0,
            ],
            'city' => [
                'label'     => 'Kota Domisili',
                'completed' => ! empty(trim((string) ($this->attributes['city'] ?? ''))),
            ],
            'province' => [
                'label'     => 'Provinsi',
                'completed' => ! empty(trim((string) ($this->attributes['province'] ?? ''))),
            ],
            'bio' => [
                'label'     => 'Biografi / Tentang Anda',
                'completed' => ! empty(trim((string) ($this->attributes['bio'] ?? ''))),
            ],
            'contact' => [
                'label'     => 'Kontak atau Media Sosial (Minimal 1)',
                'completed' => ! empty($this->attributes['whatsapp']) 
                            || ! empty($this->attributes['instagram']) 
                            || ! empty($this->attributes['tiktok']) 
                            || ! empty($this->attributes['linkedin']) 
                            || ! empty($this->attributes['website'])
                            || ! empty($this->attributes['youtube']),
            ],
        ];

        // For business, also track business name
        if ($this->isBusiness()) {
            $criteria['business_name'] = [
                'label'     => 'Nama Perusahaan / Bisnis',
                'completed' => ! empty(trim((string) ($this->attributes['business_name'] ?? ''))),
            ];
        }

        $totalCount     = count($criteria);
        $completedCount = 0;
        $missingFields  = [];

        foreach ($criteria as $item) {
            if ($item['completed']) {
                $completedCount++;
            } else {
                $missingFields[] = $item['label'];
            }
        }

        $percentage = (int) round(($completedCount / $totalCount) * 100);

        return [
            'percentage'      => $percentage,
            'completed_count' => $completedCount,
            'total_count'     => $totalCount,
            'missing_fields'  => $missingFields,
        ];
    }
}
