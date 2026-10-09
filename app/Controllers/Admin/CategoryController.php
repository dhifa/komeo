<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use CodeIgniter\HTTP\RedirectResponse;

class CategoryController extends BaseController
{
    /**
     * Categories list view
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $categoryModel = model(EventCategoryModel::class);
        $categories = $categoryModel->getWithMemberCount();

        return view('admin/categories/index', [
            'title'      => 'Manajemen Kategori Industri - KOMEO.ID',
            'categories' => $categories,
        ]);
    }

    /**
     * Save (create or update) category
     */
    public function save(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $categoryModel = model(EventCategoryModel::class);
        $id = (int) $this->request->getPost('id');

        $rules = [
            'id'          => 'permit_empty|is_natural_no_zero',
            'name'        => 'required|min_length[2]|max_length[100]',
            'slug'        => 'required|alpha_dash|max_length[100]',
            'description' => 'permit_empty',
            'icon'        => 'permit_empty|max_length[50]',
            'sort_order'  => 'required|integer',
            'is_active'   => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('admin/categories')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $slug = strtolower(trim((string) $this->request->getPost('slug')));

        // Check slug uniqueness
        $existing = $categoryModel->where('slug', $slug);
        if ($id > 0) {
            $existing->where('id !=', $id);
        }
        if ($existing->countAllResults() > 0) {
            return redirect()->to('admin/categories')
                ->withInput()
                ->with('errors', ['slug' => "Slug '{$slug}' sudah digunakan oleh kategori lain."]);
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'slug'        => $slug,
            'description' => trim((string) $this->request->getPost('description')),
            'icon'        => trim((string) $this->request->getPost('icon')),
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id > 0) {
            $categoryModel->update($id, $data);
            $msg = 'Kategori industri berhasil diperbarui.';
        } else {
            $categoryModel->insert($data);
            $msg = 'Kategori industri baru berhasil ditambahkan.';
        }

        return redirect()->to('admin/categories')->with('message', $msg);
    }

    /**
     * Delete category (with protection against deletion of categories in use)
     */
    public function delete(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $categoryModel = model(EventCategoryModel::class);
        $category = $categoryModel->find($id);

        if (! $category) {
            return redirect()->to('admin/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        // Check if used by members
        if ($categoryModel->isUsedByMembers($id)) {
            return redirect()->to('admin/categories')
                ->with('error', "Kategori '{$category['name']}' tidak dapat dihapus karena sedang digunakan oleh anggota. Anda dapat menonaktifkan statusnya.");
        }

        $categoryModel->delete($id);

        return redirect()->to('admin/categories')
            ->with('message', "Kategori '{$category['name']}' berhasil dihapus.");
    }
}
