<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use App\Models\SpecializationModel;
use CodeIgniter\HTTP\RedirectResponse;

class SpecializationController extends BaseController
{
    /**
     * Specializations list view
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $specModel     = model(SpecializationModel::class);
        $categoryModel = model(EventCategoryModel::class);

        $specializations = $specModel->getWithDetails();
        $categories      = $categoryModel->getActiveCategories();

        return view('admin/specializations/index', [
            'title'           => 'Manajemen Keahlian Spesialisasi - KOMEO.ID',
            'specializations' => $specializations,
            'categories'      => $categories,
        ]);
    }

    /**
     * Save (create or update) specialization
     */
    public function save(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $specModel = model(SpecializationModel::class);
        $id = (int) $this->request->getPost('id');

        $rules = [
            'id'          => 'permit_empty|is_natural_no_zero',
            'name'        => 'required|min_length[2]|max_length[100]',
            'slug'        => 'required|alpha_dash|max_length[100]',
            'category_id' => 'permit_empty|is_natural_no_zero',
            'sort_order'  => 'required|integer',
            'is_active'   => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/specializations')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $slug = strtolower(trim((string) $this->request->getPost('slug')));

        // Check slug uniqueness
        $existing = $specModel->where('slug', $slug);
        if ($id > 0) {
            $existing->where('id !=', $id);
        }
        if ($existing->countAllResults() > 0) {
            return redirect()->to('admin/specializations')
                ->withInput()
                ->with('errors', ['slug' => "Slug '{$slug}' sudah digunakan oleh keahlian lain."]);
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'slug'        => $slug,
            'category_id' => $this->request->getPost('category_id') ? (int) $this->request->getPost('category_id') : null,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id > 0) {
            $specModel->update($id, $data);
            $msg = 'Keahlian spesialisasi berhasil diperbarui.';
        } else {
            $specModel->insert($data);
            $msg = 'Keahlian spesialisasi baru berhasil ditambahkan.';
        }

        return redirect()->to('admin/specializations')->with('message', $msg);
    }

    /**
     * Delete specialization (with protection against deletion if in use)
     */
    public function delete(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $specModel = model(SpecializationModel::class);
        $spec = $specModel->find($id);

        if (! $spec) {
            return redirect()->to('admin/specializations')->with('error', 'Keahlian tidak ditemukan.');
        }

        if ($specModel->isUsedByMembers($id)) {
            return redirect()->to('admin/specializations')
                ->with('error', "Keahlian '{$spec['name']}' tidak dapat dihapus karena dipilih oleh member. Anda dapat menonaktifkan statusnya.");
        }

        $specModel->delete($id);

        return redirect()->to('admin/specializations')
            ->with('message', "Keahlian '{$spec['name']}' berhasil dihapus.");
    }
}
