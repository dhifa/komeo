<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberRoleModel;
use App\Models\MemberRoleAssignmentModel;
use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use App\Services\SettingsService;

class MemberRoleController extends BaseController
{
    protected MemberRoleModel $roleModel;
    protected MemberRoleAssignmentModel $assignmentModel;
    protected MemberProfileModel $profileModel;
    protected MembershipModel $membershipModel;

    public function __construct()
    {
        $this->roleModel       = new MemberRoleModel();
        $this->assignmentModel = new MemberRoleAssignmentModel();
        $this->profileModel    = new MemberProfileModel();
        $this->membershipModel = new MembershipModel();
    }

    /**
     * List all custom member roles
     */
    public function index()
    {
        $roles = $this->roleModel->getRolesWithStats();

        // Statistics
        $totalRoles  = count($roles);
        $activeRoles = count(array_filter($roles, fn($r) => ! empty($r['is_active'])));
        $defaultKey  = site_setting('MemberRole.default_role_key', 'anggota-reguler');

        return view('admin/roles/index', [
            'title'       => 'Manajemen Role Member Komunitas - Panel Admin KOMEO.ID',
            'roles'       => $roles,
            'totalRoles'  => $totalRoles,
            'activeRoles' => $activeRoles,
            'defaultKey'  => $defaultKey,
        ]);
    }

    /**
     * Create Role View
     */
    public function create()
    {
        return view('admin/roles/create', [
            'title' => 'Buat Role Member Baru - KOMEO.ID',
        ]);
    }

    /**
     * Store Custom Role
     */
    public function store()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[150]',
            'role_key'         => 'required|alpha_dash|is_unique[komeo_member_roles.role_key]|max_length[100]',
            'background_color' => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'text_color'       => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'sort_order'       => 'permit_empty|is_natural',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $key = strtolower(trim($this->request->getPost('role_key')));
        $isDefault = $this->request->getPost('is_default') ? 1 : 0;

        if ($isDefault) {
            // Unset previous default
            $this->roleModel->where('is_default', 1)->set(['is_default' => 0])->update();
            SettingsService::set('MemberRole.default_role_key', $key);
        }

        $roleData = [
            'role_key'         => $key,
            'name'             => trim($this->request->getPost('name')),
            'description'      => trim((string) $this->request->getPost('description')),
            'icon'             => trim((string) $this->request->getPost('icon')) ?: 'user',
            'background_color' => strtoupper($this->request->getPost('background_color')),
            'text_color'       => strtoupper($this->request->getPost('text_color')),
            'sort_order'       => (int) $this->request->getPost('sort_order'),
            'is_active'        => 1,
            'is_public'        => $this->request->getPost('is_public') ? 1 : 0,
            'is_default'       => $isDefault,
        ];

        $this->roleModel->insert($roleData);

        return redirect()->to(site_url('admin/member-roles'))
            ->with('success', 'Role komunitas "' . $roleData['name'] . '" berhasil dibuat.');
    }

    /**
     * Edit Role View
     */
    public function edit(int $id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->to(site_url('admin/member-roles'))->with('error', 'Role tidak ditemukan.');
        }

        return view('admin/roles/edit', [
            'title' => 'Edit Role Member: ' . $role['name'],
            'role'  => $role,
        ]);
    }

    /**
     * Update Custom Role
     */
    public function update(int $id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->to(site_url('admin/member-roles'))->with('error', 'Role tidak ditemukan.');
        }

        $rules = [
            'name'             => 'required|min_length[3]|max_length[150]',
            'background_color' => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'text_color'       => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
            'sort_order'       => 'permit_empty|is_natural',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isDefault = $this->request->getPost('is_default') ? 1 : 0;
        if ($isDefault) {
            $this->roleModel->where('is_default', 1)->set(['is_default' => 0])->update();
            SettingsService::set('MemberRole.default_role_key', $role['role_key']);
        }

        $updateData = [
            'name'             => trim($this->request->getPost('name')),
            'description'      => trim((string) $this->request->getPost('description')),
            'icon'             => trim((string) $this->request->getPost('icon')) ?: $role['icon'],
            'background_color' => strtoupper($this->request->getPost('background_color')),
            'text_color'       => strtoupper($this->request->getPost('text_color')),
            'sort_order'       => (int) $this->request->getPost('sort_order'),
            'is_public'        => $this->request->getPost('is_public') ? 1 : 0,
            'is_default'       => $isDefault,
        ];

        $this->roleModel->update($id, $updateData);

        return redirect()->to(site_url('admin/member-roles'))
            ->with('success', 'Perubahan role "' . $updateData['name'] . '" berhasil disimpan.');
    }

    /**
     * Toggle Active State
     */
    public function toggle(int $id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $newActive = $role['is_active'] ? 0 : 1;
        $this->roleModel->update($id, ['is_active' => $newActive]);

        $msg = $newActive ? 'Role diaktifkan.' : 'Role dinonaktifkan.';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Set As Default Community Role
     */
    public function setDefault(int $id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $this->roleModel->where('is_default', 1)->set(['is_default' => 0])->update();
        $this->roleModel->update($id, ['is_default' => 1, 'is_active' => 1]);
        SettingsService::set('MemberRole.default_role_key', $role['role_key']);

        return redirect()->back()->with('success', 'Role "' . $role['name'] . '" ditetapkan sebagai role komunitas bawaan.');
    }

    /**
     * View Members Assigned to Role
     */
    public function members(int $id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->to(site_url('admin/member-roles'))->with('error', 'Role tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $members = $this->assignmentModel->db->table('komeo_member_role_assignments mra')
            ->select('mra.*, u.username, u.active, mp.full_name, mp.display_name, mp.member_type, m.member_number, m.status as membership_status')
            ->join('users u', 'u.id = mra.user_id')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('memberships m', 'm.user_id = u.id', 'left')
            ->where('mra.role_id', $id)
            ->where('mra.revoked_at IS NULL')
            ->groupStart()
                ->where('mra.expires_at IS NULL')
                ->orWhere('mra.expires_at >', $now)
            ->groupEnd()
            ->orderBy('mra.is_primary', 'DESC')
            ->orderBy('mra.assigned_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/roles/members', [
            'title'   => 'Anggota dengan Role ' . $role['name'] . ' - KOMEO.ID',
            'role'    => $role,
            'members' => $members,
        ]);
    }

    /**
     * Safe Bulk Assignment for Unassigned Active Members
     */
    public function bulkAssign()
    {
        $roleId = (int) $this->request->getPost('role_id');
        $role   = $this->roleModel->find($roleId);

        if (! $role) {
            return redirect()->back()->with('error', 'Role target tidak valid.');
        }

        $adminId = (int) auth()->id();
        $db = $this->assignmentModel->db;

        // Find all ACTIVE members who do NOT have any active role assignment
        $now = date('Y-m-d H:i:s');
        $activeMembers = $db->table('memberships m')
            ->select('m.user_id')
            ->where('m.status', 'active')
            ->get()
            ->getResultArray();

        $assignedCount = 0;
        foreach ($activeMembers as $mem) {
            $uId = (int) $mem['user_id'];
            $hasActive = $db->table('komeo_member_role_assignments')
                ->where('user_id', $uId)
                ->where('revoked_at IS NULL')
                ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >', $now)
                ->groupEnd()
                ->countAllResults();

            if ($hasActive === 0) {
                $this->assignmentModel->assignRole($uId, $roleId, true, $adminId, null, 'Penugasan massal role default komunitas.');
                $assignedCount++;
            }
        }

        return redirect()->back()->with('success', "Penugasan massal berhasil: {$assignedCount} anggota aktif diberikan role '{$role['name']}'.");
    }
}
