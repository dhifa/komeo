<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BadgeDefinitionModel;
use App\Models\MemberBadgeModel;
use App\Models\MemberProfileModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class BadgeController extends BaseController
{
    protected BadgeDefinitionModel $badgeModel;
    protected MemberBadgeModel $memberBadgeModel;

    public function __construct()
    {
        $this->badgeModel       = new BadgeDefinitionModel();
        $this->memberBadgeModel = new MemberBadgeModel();
    }

    /**
     * List all badges with usage count and preview
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        // Seed defaults if table is empty
        $this->badgeModel->seedDefaultBadgesIfEmpty();

        $badges = $this->badgeModel->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll();

        // Calculate active assigned member counts for each badge
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $counts = $db->table('member_badges')
            ->select('badge_id, COUNT(*) as total_active')
            ->where('revoked_at IS NULL')
            ->groupStart()
                ->where('expires_at IS NULL')
                ->orWhere('expires_at >', $now)
            ->groupEnd()
            ->groupBy('badge_id')
            ->get()->getResultArray();

        $countMap = [];
        foreach ($counts as $c) {
            $countMap[$c['badge_id']] = (int) $c['total_active'];
        }

        foreach ($badges as &$b) {
            $b['active_members_count'] = $countMap[$b['id']] ?? 0;
        }
        unset($b);

        return view('admin/badges/index', [
            'title'  => 'Manajemen Badge Anggota - KOMEO.ID',
            'badges' => $badges,
        ]);
    }

    /**
     * Show create badge form
     */
    public function create(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        return view('admin/badges/form', [
            'title'        => 'Tambah Badge Baru - KOMEO.ID',
            'badge'        => null,
            'allowedIcons' => komeo_allowed_badge_icons(),
        ]);
    }

    /**
     * Store new badge
     */
    public function store(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'description'      => 'permit_empty|max_length[500]',
            'icon'             => 'required|max_length[50]',
            'background_color' => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
            'text_color'       => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
            'sort_order'       => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim(strip_tags((string) $this->request->getPost('name')));
        $slug = $this->badgeModel->generateUniqueSlug($name);

        $data = [
            'name'             => $name,
            'slug'             => $slug,
            'description'      => trim(strip_tags((string) $this->request->getPost('description'))),
            'icon'             => trim(strip_tags((string) $this->request->getPost('icon'))),
            'background_color' => strtoupper(trim((string) $this->request->getPost('background_color'))),
            'text_color'       => strtoupper(trim((string) $this->request->getPost('text_color'))),
            'sort_order'       => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->badgeModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->badgeModel->errors());
        }

        return redirect()->to('admin/badges')->with('message', "Badge '{$name}' berhasil dibuat.");
    }

    /**
     * Show edit badge form
     */
    public function edit(int $id): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badge = $this->badgeModel->find($id);
        if (! $badge) {
            throw PageNotFoundException::forPageNotFound("Badge ID #{$id} tidak ditemukan.");
        }

        return view('admin/badges/form', [
            'title'        => "Edit Badge: {$badge['name']} - KOMEO.ID",
            'badge'        => $badge,
            'allowedIcons' => komeo_allowed_badge_icons(),
        ]);
    }

    /**
     * Update existing badge
     */
    public function update(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badge = $this->badgeModel->find($id);
        if (! $badge) {
            throw PageNotFoundException::forPageNotFound("Badge ID #{$id} tidak ditemukan.");
        }

        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'description'      => 'permit_empty|max_length[500]',
            'icon'             => 'required|max_length[50]',
            'background_color' => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
            'text_color'       => 'required|regex_match[/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/]',
            'sort_order'       => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim(strip_tags((string) $this->request->getPost('name')));

        $data = [
            'name'             => $name,
            'description'      => trim(strip_tags((string) $this->request->getPost('description'))),
            'icon'             => trim(strip_tags((string) $this->request->getPost('icon'))),
            'background_color' => strtoupper(trim((string) $this->request->getPost('background_color'))),
            'text_color'       => strtoupper(trim((string) $this->request->getPost('text_color'))),
            'sort_order'       => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->badgeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->badgeModel->errors());
        }

        return redirect()->to('admin/badges')->with('message', "Badge '{$name}' berhasil diperbarui.");
    }

    /**
     * Toggle active status
     */
    public function toggle(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badge = $this->badgeModel->find($id);
        if (! $badge) {
            return redirect()->back()->with('error', 'Badge tidak ditemukan.');
        }

        $newStatus = $badge['is_active'] ? 0 : 1;
        $this->badgeModel->update($id, ['is_active' => $newStatus]);

        $statusLabel = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('message', "Badge '{$badge['name']}' berhasil {$statusLabel}.");
    }

    /**
     * View members assigned to this badge
     */
    public function members(int $id): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badge = $this->badgeModel->find($id);
        if (! $badge) {
            throw PageNotFoundException::forPageNotFound("Badge ID #{$id} tidak ditemukan.");
        }

        $assignments = $this->memberBadgeModel->getAssignedMembers($id);

        // All active members for assignment selection
        $db = \Config\Database::connect();
        $allMembers = $db->table('memberships m')
            ->select('m.user_id, m.member_number, m.status, p.full_name, p.display_name, p.username, p.member_type, p.business_name')
            ->join('member_profiles p', 'p.user_id = m.user_id', 'left')
            ->orderBy('p.full_name', 'ASC')
            ->get()->getResultArray();

        return view('admin/badges/members', [
            'title'       => "Anggota Penerima Badge: {$badge['name']} - KOMEO.ID",
            'badge'       => $badge,
            'assignments' => $assignments,
            'allMembers'  => $allMembers,
        ]);
    }

    /**
     * Assign badge to a member from the badge detail view
     */
    public function assignMember(int $badgeId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badge = $this->badgeModel->find($badgeId);
        if (! $badge) {
            return redirect()->back()->with('error', 'Badge tidak ditemukan.');
        }

        $userId       = (int) $this->request->getPost('user_id');
        $expiresAt    = $this->request->getPost('expires_at') ? (string) $this->request->getPost('expires_at') : null;
        $internalNote = $this->request->getPost('internal_note') ? (string) $this->request->getPost('internal_note') : null;

        if ($userId <= 0) {
            return redirect()->back()->with('error', 'Silakan pilih anggota yang valid.');
        }

        $result = $this->memberBadgeModel->assignBadge($badgeId, $userId, (int) $admin->id, $expiresAt, $internalNote);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Revoke badge from member
     */
    public function revokeMember(int $badgeId, int $memberBadgeId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $result = $this->memberBadgeModel->revokeBadge($memberBadgeId, (int) $admin->id);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
