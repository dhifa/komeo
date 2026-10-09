<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    /**
     * Display list of users and their assigned roles
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('admin')->with('error', 'Hanya Administrator yang dapat mengelola peran (roles) pengguna.');
        }

        $db = \Config\Database::connect();
        
        $search = trim((string) $this->request->getGet('q'));
        
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.active, u.created_at, ai.secret as email, mp.full_name, m.status as membership_status')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->join('memberships m', 'm.user_id = u.id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('ai.secret', $search)
                ->orLike('mp.full_name', $search)
                ->groupEnd();
        }

        $users = $builder->orderBy('u.id', 'DESC')->get()->getResultArray();

        // Fetch groups for each user
        $groupsUsers = $db->table('auth_groups_users')->get()->getResultArray();
        $userGroupsMap = [];
        foreach ($groupsUsers as $gu) {
            $userGroupsMap[$gu['user_id']][] = $gu['group'];
        }

        foreach ($users as &$u) {
            $u['groups'] = $userGroupsMap[$u['id']] ?? ['user'];
        }
        unset($u);

        $availableRoles = [
            'superadmin' => 'Super Admin (Akses Penuh)',
            'admin'      => 'Admin (Pengelola Sistem)',
            'moderator'  => 'Moderator (Verifikasi & Blacklist)',
            'editor'     => 'Editor Konten (Website & Footer)',
            'user'       => 'User / Member Biasa',
        ];

        return view('admin/users/index', [
            'title'          => 'Manajemen Role & Pengguna - KOMEO.ID',
            'users'          => $users,
            'search'         => $search,
            'availableRoles' => $availableRoles,
            'currentUser'    => $user,
        ]);
    }

    /**
     * Update user role / group
     */
    public function updateRole(int $userId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $userModel = model(\CodeIgniter\Shield\Models\UserModel::class);
        $targetUser = $userModel->find($userId);

        if (! $targetUser) {
            return redirect()->to('admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Prevent modifying own superadmin role if only one superadmin
        if ((int)$admin->id === $userId && ! $admin->inGroup('superadmin')) {
            return redirect()->to('admin/users')->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        $newRole = trim((string) $this->request->getPost('role'));
        $validRoles = ['superadmin', 'admin', 'moderator', 'editor', 'user'];

        if (! in_array($newRole, $validRoles, true)) {
            return redirect()->to('admin/users')->with('error', 'Peran (role) yang dipilih tidak valid.');
        }

        // Remove existing groups and add the new one
        $db = \Config\Database::connect();
        $db->table('auth_groups_users')->where('user_id', $userId)->delete();
        $db->table('auth_groups_users')->insert([
            'user_id'    => $userId,
            'group'      => $newRole,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('admin/users')->with('success', "Peran untuk user @{$targetUser->username} berhasil diperbarui menjadi " . strtoupper($newRole) . ".");
    }
}
