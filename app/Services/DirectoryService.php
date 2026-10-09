<?php

namespace App\Services;

use App\Models\EventCategoryModel;
use App\Models\SpecializationModel;
use Config\Database;

class DirectoryService
{
    /**
     * Search and filter members for directory
     *
     * @param array<string, mixed> $params
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     total: int,
     *     page: int,
     *     per_page: int,
     *     total_pages: int,
     *     has_prev: bool,
     *     has_next: bool,
     *     filters: array<string, mixed>
     * }
     */
    public function search(array $params = []): array
    {
        $db = Database::connect();

        $page     = max(1, (int) ($params['page'] ?? 1));
        $perPage  = max(1, min(50, (int) ($params['per_page'] ?? site_setting('Directory.per_page', 12))));
        $keyword  = trim((string) ($params['q'] ?? ''));
        $type     = trim((string) ($params['type'] ?? ''));
        $category = trim((string) ($params['category'] ?? ''));
        $spec     = trim((string) ($params['specialization'] ?? ''));
        $province = trim((string) ($params['province'] ?? ''));
        $city     = trim((string) ($params['city'] ?? ''));
        $sort     = trim((string) ($params['sort'] ?? site_setting('Directory.default_sort', 'newest')));
        $featured = ! empty($params['only_featured']);

        // 1. Build Base Builder for Counting and Fetching
        $builder = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left')
            ->join('member_verifications', 'member_verifications.user_id = member_profiles.user_id', 'left')
            ->where('memberships.status', 'active')
            ->where('member_profiles.is_public', 1);

        // Filter: Featured only
        if ($featured) {
            $builder->where('member_profiles.is_featured', 1);
        }

        // Filter: Member type (individual / business)
        if (in_array($type, ['individual', 'business'], true)) {
            $builder->where('member_profiles.member_type', $type);
        }

        // Filter: Industry Category
        if ($category !== '') {
            if (is_numeric($category)) {
                $builder->where('member_profiles.category_id', (int) $category);
            } else {
                $builder->where('event_categories.slug', $category);
            }
        }

        // Filter: Specialization
        if ($spec !== '') {
            if (is_numeric($spec)) {
                $builder->whereIn('member_profiles.id', static function ($sub) use ($spec) {
                    return $sub->select('member_profile_id')
                        ->from('member_specializations')
                        ->where('specialization_id', (int) $spec);
                });
            } else {
                $builder->whereIn('member_profiles.id', static function ($sub) use ($spec) {
                    return $sub->select('ms.member_profile_id')
                        ->from('member_specializations ms')
                        ->join('specializations s', 's.id = ms.specialization_id')
                        ->where('s.slug', $spec);
                });
            }
        }

        // Filter: Province
        if ($province !== '') {
            $builder->where('member_profiles.province', $province);
        }

        // Filter: City
        if ($city !== '') {
            $builder->where('member_profiles.city', $city);
        }

        // Filter: Search Keyword
        if ($keyword !== '') {
            $escapedSubQueryKeyword = $db->escape('%' . $keyword . '%');
            $builder->groupStart()
                ->like('member_profiles.display_name', $keyword)
                ->orLike('member_profiles.full_name', $keyword)
                ->orLike('member_profiles.username', $keyword)
                ->orLike('member_profiles.business_name', $keyword)
                ->orLike('memberships.member_number', $keyword)
                ->orLike('member_profiles.bio', $keyword)
                ->orLike('member_profiles.city', $keyword)
                ->orLike('member_profiles.province', $keyword)
                ->orLike('event_categories.name', $keyword)
                ->orWhere("member_profiles.id IN (SELECT ms2.member_profile_id FROM member_specializations ms2 JOIN specializations s2 ON s2.id = ms2.specialization_id WHERE s2.name LIKE {$escapedSubQueryKeyword})")
                ->groupEnd();
        }

        // Count Total Results
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Sorting
        switch ($sort) {
            case 'oldest':
                $builder->orderBy('memberships.approved_at', 'ASC')
                    ->orderBy('member_profiles.id', 'ASC');
                break;
            case 'name_asc':
                $builder->orderBy("COALESCE(NULLIF(member_profiles.display_name, ''), member_profiles.full_name)", 'ASC', false);
                break;
            case 'name_desc':
                $builder->orderBy("COALESCE(NULLIF(member_profiles.display_name, ''), member_profiles.full_name)", 'DESC', false);
                break;
            case 'newest':
            default:
                $builder->orderBy('memberships.approved_at', 'DESC')
                    ->orderBy('member_profiles.id', 'DESC');
                $sort = 'newest';
                break;
        }

        // Select columns and Paginate
        $items = $builder->select([
            'member_profiles.id',
            'member_profiles.user_id',
            'member_profiles.full_name',
            'member_profiles.display_name',
            'member_profiles.username',
            'member_profiles.member_type',
            'member_profiles.category_id',
            'member_profiles.business_name',
            'member_profiles.business_description',
            'member_profiles.years_of_experience',
            'member_profiles.photo_path',
            'member_profiles.company_logo_path',
            'member_profiles.bio',
            'member_profiles.city',
            'member_profiles.province',
            'member_profiles.is_public',
            'member_profiles.show_whatsapp',
            'member_profiles.show_social',
            'member_profiles.show_location',
            'member_profiles.is_featured',
            'member_profiles.created_at',
            'memberships.member_number',
            'memberships.status as membership_status',
            'memberships.approved_at',
            'memberships.joined_at',
            'event_categories.name as category_name',
            'event_categories.slug as category_slug',
            'event_categories.icon as category_icon',
            'member_verifications.verification_status',
            'member_verifications.verification_level',
            'member_verifications.verification_method',
        ])
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        // 2. Batch load Specializations & Portfolios for the retrieved profiles (Avoid N+1)
        if (! empty($items)) {
            $profileIds = array_column($items, 'id');
            $specializationsMap = $this->batchLoadSpecializations($profileIds);
            $portfoliosMap      = $this->batchLoadPortfolios($profileIds);

            foreach ($items as &$item) {
                $pid = (int) $item['id'];
                $item['specializations'] = $specializationsMap[$pid] ?? [];
                $item['portfolios']      = $portfoliosMap[$pid] ?? [];
                $item['portfolio_count'] = count($item['portfolios']);
            }
            unset($item);
        }

        $totalPages = (int) ceil($total / $perPage);

        return [
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
            'has_prev'    => ($page > 1),
            'has_next'    => ($page < $totalPages),
            'filters'     => [
                'q'             => $keyword,
                'type'          => $type,
                'category'      => $category,
                'specialization'=> $spec,
                'province'      => $province,
                'city'          => $city,
                'sort'          => $sort,
            ],
        ];
    }

    /**
     * Get featured members for directory or homepage
     *
     * @param int $limit
     * @return array<int, array<string, mixed>>
     */
    public function getFeaturedMembers(int $limit = 6): array
    {
        $db = Database::connect();

        // First attempt: get explicitly marked featured members
        $builder = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left')
            ->join('member_verifications', 'member_verifications.user_id = member_profiles.user_id', 'left')
            ->where('memberships.status', 'active')
            ->where('member_profiles.is_public', 1)
            ->where('member_profiles.is_featured', 1)
            ->orderBy('memberships.approved_at', 'DESC')
            ->limit($limit);

        $items = $builder->select([
            'member_profiles.id',
            'member_profiles.user_id',
            'member_profiles.full_name',
            'member_profiles.display_name',
            'member_profiles.username',
            'member_profiles.member_type',
            'member_profiles.category_id',
            'member_profiles.business_name',
            'member_profiles.business_description',
            'member_profiles.years_of_experience',
            'member_profiles.photo_path',
            'member_profiles.company_logo_path',
            'member_profiles.bio',
            'member_profiles.city',
            'member_profiles.province',
            'member_profiles.is_public',
            'member_profiles.show_whatsapp',
            'member_profiles.show_social',
            'member_profiles.show_location',
            'member_profiles.is_featured',
            'memberships.member_number',
            'memberships.status as membership_status',
            'event_categories.name as category_name',
            'event_categories.slug as category_slug',
            'event_categories.icon as category_icon',
            'member_verifications.verification_status',
            'member_verifications.verification_level',
            'member_verifications.verification_method',
        ])->get()->getResultArray();

        // If fewer than requested limit, backfill with newest active public members
        if (count($items) < $limit) {
            $existingIds = array_column($items, 'id');
            $needed = $limit - count($items);

            $backfillBuilder = $db->table('member_profiles')
                ->join('memberships', 'memberships.user_id = member_profiles.user_id')
                ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left')
                ->join('member_verifications', 'member_verifications.user_id = member_profiles.user_id', 'left')
                ->where('memberships.status', 'active')
                ->where('member_profiles.is_public', 1);

            if (! empty($existingIds)) {
                $backfillBuilder->whereNotIn('member_profiles.id', $existingIds);
            }

            $backfill = $backfillBuilder->orderBy('memberships.approved_at', 'DESC')
                ->limit($needed)
                ->select([
                    'member_profiles.id',
                    'member_profiles.user_id',
                    'member_profiles.full_name',
                    'member_profiles.display_name',
                    'member_profiles.username',
                    'member_profiles.member_type',
                    'member_profiles.category_id',
                    'member_profiles.business_name',
                    'member_profiles.business_description',
                    'member_profiles.years_of_experience',
                    'member_profiles.photo_path',
                    'member_profiles.company_logo_path',
                    'member_profiles.bio',
                    'member_profiles.city',
                    'member_profiles.province',
                    'member_profiles.is_public',
                    'member_profiles.show_whatsapp',
                    'member_profiles.show_social',
                    'member_profiles.show_location',
                    'member_profiles.is_featured',
                    'memberships.member_number',
                    'memberships.status as membership_status',
                    'event_categories.name as category_name',
                    'event_categories.slug as category_slug',
                    'event_categories.icon as category_icon',
                    'member_verifications.verification_status',
                    'member_verifications.verification_level',
                    'member_verifications.verification_method',
                ])
                ->get()
                ->getResultArray();

            $items = array_merge($items, $backfill);
        }

        if (! empty($items)) {
            $profileIds = array_column($items, 'id');
            $specializationsMap = $this->batchLoadSpecializations($profileIds);
            $portfoliosMap      = $this->batchLoadPortfolios($profileIds);

            foreach ($items as &$item) {
                $pid = (int) $item['id'];
                $item['specializations'] = $specializationsMap[$pid] ?? [];
                $item['portfolios']      = $portfoliosMap[$pid] ?? [];
                $item['portfolio_count'] = count($item['portfolios']);
            }
            unset($item);
        }

        return $items;
    }

    /**
     * Batch load specializations for list of profile IDs
     *
     * @param int[] $profileIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function batchLoadSpecializations(array $profileIds): array
    {
        if (empty($profileIds)) {
            return [];
        }

        $db = Database::connect();
        $rows = $db->table('member_specializations')
            ->join('specializations', 'specializations.id = member_specializations.specialization_id')
            ->whereIn('member_specializations.member_profile_id', $profileIds)
            ->where('specializations.is_active', 1)
            ->orderBy('specializations.sort_order', 'ASC')
            ->orderBy('specializations.name', 'ASC')
            ->select([
                'member_specializations.member_profile_id',
                'specializations.id',
                'specializations.name',
                'specializations.slug',
            ])
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $pid = (int) $r['member_profile_id'];
            $map[$pid][] = $r;
        }

        return $map;
    }

    /**
     * Batch load portfolios for list of profile IDs
     *
     * @param int[] $profileIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function batchLoadPortfolios(array $profileIds): array
    {
        if (empty($profileIds)) {
            return [];
        }

        $db = Database::connect();
        $rows = $db->table('member_portfolios')
            ->whereIn('member_profile_id', $profileIds)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->select([
                'id',
                'member_profile_id',
                'title',
                'project_year',
                'event_location',
                'cover_image',
                'external_url',
            ])
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $pid = (int) $r['member_profile_id'];
            $map[$pid][] = $r;
        }

        return $map;
    }

    /**
     * Get available filter options from actual database records
     *
     * @return array{
     *     categories: array<int, array<string, mixed>>,
     *     specializations: array<int, array<string, mixed>>,
     *     provinces: string[],
     *     cities: string[]
     * }
     */
    public function getFilterOptions(): array
    {
        $db = Database::connect();

        $categoryModel = model(EventCategoryModel::class);
        $categories    = $categoryModel->getActiveCategories();

        $specModel       = model(SpecializationModel::class);
        $specializations = $specModel->getActiveSpecializations();

        // Provinces with active public members
        $provinces = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->where('memberships.status', 'active')
            ->where('member_profiles.is_public', 1)
            ->where('member_profiles.province IS NOT NULL')
            ->where('member_profiles.province !=', '')
            ->select('member_profiles.province')
            ->distinct()
            ->orderBy('member_profiles.province', 'ASC')
            ->get()
            ->getResultArray();

        // Cities with active public members
        $cities = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->where('memberships.status', 'active')
            ->where('member_profiles.is_public', 1)
            ->where('member_profiles.city IS NOT NULL')
            ->where('member_profiles.city !=', '')
            ->select('member_profiles.city')
            ->distinct()
            ->orderBy('member_profiles.city', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'categories'      => $categories,
            'specializations' => $specializations,
            'provinces'       => array_filter(array_column($provinces, 'province')),
            'cities'          => array_filter(array_column($cities, 'city')),
        ];
    }

    /**
     * Get directory statistics for admin dashboard or overview
     *
     * @return array{
     *     total_public: int,
     *     total_vendor: int,
     *     total_crew: int,
     *     total_featured: int
     * }
     */
    public function getStats(): array
    {
        $db = Database::connect();

        $base = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->where('memberships.status', 'active')
            ->where('member_profiles.is_public', 1);

        $totalPublic = (clone $base)->countAllResults();

        $totalVendor = (clone $base)
            ->where('member_profiles.member_type', 'business')
            ->countAllResults();

        $totalCrew = (clone $base)
            ->where('member_profiles.member_type', 'individual')
            ->countAllResults();

        $totalFeatured = (clone $base)
            ->where('member_profiles.is_featured', 1)
            ->countAllResults();

        return [
            'total_public'   => $totalPublic,
            'total_vendor'   => $totalVendor,
            'total_crew'     => $totalCrew,
            'total_featured' => $totalFeatured,
        ];
    }
}
