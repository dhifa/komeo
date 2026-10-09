<?php

namespace App\Models;

use CodeIgniter\Model;

class BadgeDefinitionModel extends Model
{
    protected $table            = 'badge_definitions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'icon',
        'background_color',
        'text_color',
        'sort_order',
        'is_active',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'             => 'required|min_length[2]|max_length[100]',
        'icon'             => 'required|max_length[50]',
        'background_color' => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
        'text_color'       => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
        'sort_order'       => 'permit_empty|integer',
        'is_active'        => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama badge wajib diisi.',
            'min_length' => 'Nama badge minimal 2 karakter.',
            'max_length' => 'Nama badge maksimal 100 karakter.',
        ],
        'background_color' => [
            'required'    => 'Warna latar belakang badge wajib diisi.',
            'regex_match' => 'Format warna background harus heksadesimal (contoh: #4F46E5).',
        ],
        'text_color' => [
            'required'    => 'Warna teks badge wajib diisi.',
            'regex_match' => 'Format warna teks harus heksadesimal (contoh: #FFFFFF).',
        ],
    ];

    protected $beforeInsert = ['sanitizeAndSlugify'];
    protected $beforeUpdate = ['sanitizeAndSlugify'];

    protected function sanitizeAndSlugify(array $data): array
    {
        if (isset($data['data']['name'])) {
            $data['data']['name'] = trim(strip_tags((string) $data['data']['name']));
            if (empty($data['data']['slug'])) {
                $id = $data['id'][0] ?? null;
                $data['data']['slug'] = $this->generateUniqueSlug($data['data']['name'], $id ? (int) $id : null);
            }
        }

        if (isset($data['data']['description'])) {
            $data['data']['description'] = trim(strip_tags((string) $data['data']['description']));
        }

        if (isset($data['data']['icon'])) {
            $data['data']['icon'] = trim(strip_tags((string) $data['data']['icon']));
        }

        return $data;
    }

    /**
     * Generate unique slug
     */
    public function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = url_title(strtolower($name), '-', true);
        if (empty($base)) {
            $base = 'badge-' . substr(bin2hex(random_bytes(3)), 0, 6);
        }

        $slug = $base;
        $i = 1;

        while (true) {
            $builder = $this->where('slug', $slug);
            if ($ignoreId !== null) {
                $builder->where('id !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                break;
            }
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Get active badge definitions ordered by sort_order ASC, name ASC
     */
    public function getActiveBadges(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Seed default standard badges if table is currently empty
     */
    public function seedDefaultBadgesIfEmpty(): void
    {
        if ($this->countAllResults() > 0) {
            return;
        }

        $defaults = [
            [
                'name'             => 'Tergercep',
                'slug'             => 'tergercep',
                'description'      => 'Penghargaan respon dan eksekusi tercepat dalam komunitas.',
                'icon'             => 'zap',
                'background_color' => '#F59E0B',
                'text_color'       => '#0F172A',
                'sort_order'       => 1,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Recommended',
                'slug'             => 'recommended',
                'description'      => 'Rekomendasi terpercaya dari pengurus KOMEO.ID.',
                'icon'             => 'star',
                'background_color' => '#4F46E5',
                'text_color'       => '#FFFFFF',
                'sort_order'       => 2,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Event Expert',
                'slug'             => 'event-expert',
                'description'      => 'Ahli berpengalaman dalam manajemen dan teknis event.',
                'icon'             => 'award',
                'background_color' => '#7C3AED',
                'text_color'       => '#FFFFFF',
                'sort_order'       => 3,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Top Vendor',
                'slug'             => 'top-vendor',
                'description'      => 'Vendor dengan reputasi dan fasilitas terbaik.',
                'icon'             => 'crown',
                'background_color' => '#EA580C',
                'text_color'       => '#FFFFFF',
                'sort_order'       => 4,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Active Contributor',
                'slug'             => 'active-contributor',
                'description'      => 'Kontributor aktif dalam kegiatan dan kolaborasi komunitas.',
                'icon'             => 'users',
                'background_color' => '#0D9488',
                'text_color'       => '#FFFFFF',
                'sort_order'       => 5,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Verified Business',
                'slug'             => 'verified-business',
                'description'      => 'Badan usaha/vendor yang telah diverifikasi profil perusahaannya.',
                'icon'             => 'shield-check',
                'background_color' => '#10B981',
                'text_color'       => '#FFFFFF',
                'sort_order'       => 6,
                'is_active'        => 1,
            ],
        ];

        foreach ($defaults as $item) {
            $this->insert($item);
        }
    }
}
