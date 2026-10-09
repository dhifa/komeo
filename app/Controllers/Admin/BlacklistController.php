<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlacklistModel;
use CodeIgniter\HTTP\RedirectResponse;

class BlacklistController extends BaseController
{
    /**
     * Display list of blacklisted entities
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'moderator')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $blacklistModel = model(BlacklistModel::class);
        $search = trim((string) $this->request->getGet('q'));

        if ($search !== '') {
            $items = $blacklistModel->like('name', $search)
                ->orLike('city', $search)
                ->orLike('case_category', $search)
                ->orLike('description', $search)
                ->orLike('member_number', $search)
                ->orderBy('id', 'DESC')
                ->findAll();
        } else {
            $items = $blacklistModel->orderBy('id', 'DESC')->findAll();
        }

        // Fetch registered members for selection
        $membershipModel = model(\App\Models\MembershipModel::class);
        $members = $membershipModel->select('memberships.id as membership_id, memberships.user_id, memberships.member_number, memberships.status as member_status, member_profiles.full_name, member_profiles.business_name, member_profiles.member_type, member_profiles.city, event_categories.name as category_name')
            ->join('member_profiles', 'member_profiles.user_id = memberships.user_id', 'left')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left')
            ->orderBy('member_profiles.full_name', 'ASC')
            ->asArray()
            ->findAll();

        // Fetch case categories from database
        $caseCategoryModel = model(\App\Models\BlacklistCaseCategoryModel::class);
        $caseCategories = $caseCategoryModel->getActiveCategories();

        return view('admin/blacklist/index', [
            'title'          => 'Manajemen Daftar Blacklist Event - KOMEO.ID',
            'items'          => $items,
            'members'        => $members,
            'caseCategories' => $caseCategories,
            'search'         => $search,
        ]);
    }

    /**
     * Save (create or update) blacklist entry
     */
    public function save(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'moderator')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'name'          => 'required|min_length[3]|max_length[150]',
            'entity_type'   => 'required|in_list[vendor,eo,freelance,lainnya]',
            'city'          => 'permit_empty|max_length[100]',
            'case_category' => 'required|min_length[3]|max_length[100]',
            'incident_date' => 'permit_empty|max_length[50]',
            'description'   => 'required',
            'status'        => 'required|in_list[blacklisted,monitoring,resolved]',
            'is_public'     => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/blacklist')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $blacklistModel = model(BlacklistModel::class);
        $id = (int) $this->request->getPost('id');

        $userId = $this->request->getPost('user_id') ? (int) $this->request->getPost('user_id') : null;
        $membershipId = $this->request->getPost('membership_id') ? (int) $this->request->getPost('membership_id') : null;
        $memberNumber = trim((string) $this->request->getPost('member_number')) ?: null;
        $suspendMember = (bool) $this->request->getPost('suspend_member');

        $data = [
            'user_id'        => $userId,
            'membership_id'  => $membershipId,
            'member_number'  => $memberNumber,
            'name'           => trim((string) $this->request->getPost('name')),
            'entity_type'    => trim((string) $this->request->getPost('entity_type')),
            'city'           => trim((string) $this->request->getPost('city')),
            'case_category'  => trim((string) $this->request->getPost('case_category')),
            'incident_date'  => trim((string) $this->request->getPost('incident_date')),
            'description'    => trim((string) $this->request->getPost('description')),
            'evidence_notes' => trim((string) $this->request->getPost('evidence_notes')),
            'status'         => trim((string) $this->request->getPost('status')),
            'is_public'      => $this->request->getPost('is_public') ? 1 : 0,
        ];

        if ($id > 0) {
            $blacklistModel->update($id, $data);
            $msg = 'Entitas blacklist berhasil diperbarui.';
        } else {
            $blacklistModel->insert($data);
            $msg = 'Entitas blacklist baru berhasil ditambahkan.';
        }

        // Suspend KOMEO member if requested
        if ($suspendMember && $membershipId) {
            $membershipModel = model(\App\Models\MembershipModel::class);
            $membershipModel->update($membershipId, [
                'status'            => 'suspended',
                'suspension_reason' => 'Masuk daftar Blacklist: ' . $data['case_category'] . ' - ' . mb_substr($data['description'], 0, 150),
            ]);
        }

        return redirect()->to('admin/blacklist')->with('success', $msg);
    }

    /**
     * Delete blacklist entry
     */
    public function delete(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'moderator')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $blacklistModel = model(BlacklistModel::class);
        $blacklistModel->delete($id);

        return redirect()->to('admin/blacklist')->with('success', 'Data blacklist berhasil dihapus.');
    }

    /**
     * Save case category
     */
    public function saveCategory(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'moderator')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $name = trim((string) $this->request->getPost('name'));
        $description = trim((string) $this->request->getPost('description'));

        if ($name === '') {
            return redirect()->to('admin/blacklist')->with('error', 'Nama kategori kasus wajib diisi.');
        }

        $caseCategoryModel = model(\App\Models\BlacklistCaseCategoryModel::class);
        $caseCategoryModel->insert([
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'description' => $description,
            'is_active'   => 1,
        ]);

        return redirect()->to('admin/blacklist')->with('success', "Kategori kasus \"{$name}\" berhasil ditambahkan.");
    }

    /**
     * Delete case category
     */
    public function deleteCategory(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin', 'moderator')) {
            return redirect()->to('admin')->with('error', 'Akses ditolak.');
        }

        $caseCategoryModel = model(\App\Models\BlacklistCaseCategoryModel::class);
        $caseCategoryModel->delete($id);

        return redirect()->to('admin/blacklist')->with('success', 'Kategori kasus berhasil dihapus.');
    }
}
