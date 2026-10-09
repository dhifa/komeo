<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use App\Models\MemberProfileModel;
use App\Services\DirectoryService;
use App\Services\SettingsService;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Database;

class DirectoryController extends BaseController
{
    protected DirectoryService $directoryService;

    public function __construct()
    {
        $this->directoryService = new DirectoryService();
    }

    /**
     * Admin Directory Management Overview (/admin/directory)
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $db = Database::connect();
        $stats = $this->directoryService->getStats();

        // Search & Filters for admin listing
        $q        = trim((string) $this->request->getGet('q'));
        $type     = trim((string) $this->request->getGet('type'));
        $catId    = (int) $this->request->getGet('category_id');
        $featured = $this->request->getGet('featured');
        $public   = $this->request->getGet('public');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = 20;

        $builder = $db->table('member_profiles')
            ->join('memberships', 'memberships.user_id = member_profiles.user_id')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('member_profiles.display_name', $q)
                ->orLike('member_profiles.full_name', $q)
                ->orLike('member_profiles.username', $q)
                ->orLike('member_profiles.business_name', $q)
                ->orLike('memberships.member_number', $q)
                ->groupEnd();
        }

        if (in_array($type, ['individual', 'business'], true)) {
            $builder->where('member_profiles.member_type', $type);
        }

        if ($catId > 0) {
            $builder->where('member_profiles.category_id', $catId);
        }

        if ($featured !== null && $featured !== '') {
            $builder->where('member_profiles.is_featured', (int) $featured);
        }

        if ($public !== null && $public !== '') {
            $builder->where('member_profiles.is_public', (int) $public);
        }

        // Count total for pagination
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $members = $builder->select([
            'member_profiles.id',
            'member_profiles.user_id',
            'member_profiles.full_name',
            'member_profiles.display_name',
            'member_profiles.username',
            'member_profiles.member_type',
            'member_profiles.category_id',
            'member_profiles.business_name',
            'member_profiles.city',
            'member_profiles.province',
            'member_profiles.photo_path',
            'member_profiles.company_logo_path',
            'member_profiles.is_public',
            'member_profiles.is_featured',
            'memberships.member_number',
            'memberships.status as membership_status',
            'memberships.approved_at',
            'event_categories.name as category_name',
        ])
            ->orderBy('member_profiles.is_featured', 'DESC')
            ->orderBy('member_profiles.id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        // Batch load portfolio counts
        if (! empty($members)) {
            $pids = array_column($members, 'id');
            $pfCounts = $db->table('member_portfolios')
                ->whereIn('member_profile_id', $pids)
                ->select('member_profile_id, COUNT(id) as total')
                ->groupBy('member_profile_id')
                ->get()
                ->getResultArray();

            $pfMap = [];
            foreach ($pfCounts as $pc) {
                $pfMap[(int) $pc['member_profile_id']] = (int) $pc['total'];
            }

            foreach ($members as &$m) {
                $m['portfolio_count'] = $pfMap[(int) $m['id']] ?? 0;
            }
            unset($m);
        }

        $categoryModel = model(EventCategoryModel::class);
        $categories    = $categoryModel->getActiveCategories();

        $totalPages = (int) ceil($total / $perPage);

        return view('admin/directory/index', [
            'title'      => 'Manajemen Direktori Profesional - KOMEO.ID',
            'stats'      => $stats,
            'members'    => $members,
            'categories' => $categories,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $totalPages,
            'filters'    => [
                'q'           => $q,
                'type'        => $type,
                'category_id' => $catId,
                'featured'    => $featured,
                'public'      => $public,
            ],
        ]);
    }

    /**
     * Toggle featured status of a member
     */
    public function toggleFeature(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->find($id);

        if (! $profile) {
            return redirect()->to('admin/directory')->with('error', 'Profil member tidak ditemukan.');
        }

        $newVal = empty($profile->is_featured) ? 1 : 0;
        $profileModel->update($id, ['is_featured' => $newVal]);

        $statusText = $newVal ? 'ditetapkan sebagai Member Pilihan (Featured)' : 'dihapus dari sorotan (Unfeatured)';
        $name = $profile->display_name ?: $profile->full_name;

        return redirect()->back()->with('message', "Member '{$name}' berhasil {$statusText}.");
    }

    /**
     * Toggle public directory visibility of a member
     */
    public function toggleVisibility(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->find($id);

        if (! $profile) {
            return redirect()->to('admin/directory')->with('error', 'Profil member tidak ditemukan.');
        }

        $newVal = empty($profile->is_public) ? 1 : 0;
        $profileModel->update($id, ['is_public' => $newVal]);

        $statusText = $newVal ? 'diaktifkan visibilitasnya ke direktori publik' : 'disembunyikan dari direktori publik';
        $name = $profile->display_name ?: $profile->full_name;

        return redirect()->back()->with('message', "Profil '{$name}' berhasil {$statusText}.");
    }

    /**
     * Directory Configuration View (/admin/settings/directory)
     */
    public function settings(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        return view('admin/directory/settings', [
            'title'    => 'Pengaturan Direktori Member & Vendor - KOMEO.ID',
            'settings' => SettingsService::getAll(),
        ]);
    }

    /**
     * Update Directory Configuration
     */
    public function updateSettings(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'directory_title'          => 'required|min_length[3]|max_length[150]',
            'directory_subtitle'       => 'required|min_length[3]|max_length[255]',
            'directory_featured_count' => 'required|is_natural_no_zero|less_than_equal_to[30]',
            'directory_per_page'       => 'required|is_natural_no_zero|less_than_equal_to[50]',
            'directory_default_sort'   => 'required|in_list[newest,oldest,name_asc,name_desc]',
            'directory_show_city'      => 'permit_empty|in_list[0,1]',
            'directory_show_specs'     => 'permit_empty|in_list[0,1]',
            'directory_enable_public'  => 'permit_empty|in_list[0,1]',
            'directory_enable_vendor'  => 'permit_empty|in_list[0,1]',
            'directory_enable_crew'    => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/settings/directory')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        SettingsService::setMultiple([
            'Directory.title'                => trim((string) $this->request->getPost('directory_title')),
            'Directory.subtitle'             => trim((string) $this->request->getPost('directory_subtitle')),
            'Directory.featured_count'       => (string) ((int) $this->request->getPost('directory_featured_count')),
            'Directory.per_page'             => (string) ((int) $this->request->getPost('directory_per_page')),
            'Directory.default_sort'         => trim((string) $this->request->getPost('directory_default_sort')),
            'Directory.show_city'            => $this->request->getPost('directory_show_city') ? '1' : '0',
            'Directory.show_specializations' => $this->request->getPost('directory_show_specs') ? '1' : '0',
            'Directory.enable_public'        => $this->request->getPost('directory_enable_public') ? '1' : '0',
            'Directory.enable_vendor'        => $this->request->getPost('directory_enable_vendor') ? '1' : '0',
            'Directory.enable_crew'          => $this->request->getPost('directory_enable_crew') ? '1' : '0',
        ]);

        return redirect()->to('admin/settings/directory')->with('message', 'Pengaturan direktori berhasil diperbarui.');
    }
}
