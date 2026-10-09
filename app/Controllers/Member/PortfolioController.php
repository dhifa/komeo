<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MemberPortfolioModel;
use App\Models\MemberProfileModel;
use CodeIgniter\HTTP\RedirectResponse;

class PortfolioController extends BaseController
{
    /**
     * Portfolio list view
     */
    public function index(): string|RedirectResponse
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->to('login');
        }

        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->findByUserId((int) $user->id);

        if (! $profile) {
            return redirect()->to('dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        $portfolioModel = model(MemberPortfolioModel::class);
        $portfolios = $portfolioModel->getByProfileId($profile->id);
        $totalCount = count($portfolios);
        $maxLimit   = MemberPortfolioModel::MAX_ITEMS_PER_MEMBER;

        return view('dashboard/portfolio/index', [
            'title'      => 'Manajemen Portofolio - KOMEO.ID',
            'user'       => $user,
            'profile'    => $profile,
            'portfolios' => $portfolios,
            'totalCount' => $totalCount,
            'maxLimit'   => $maxLimit,
        ]);
    }

    /**
     * Save (create or update) portfolio item
     */
    public function save(): RedirectResponse
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->to('login');
        }

        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->findByUserId((int) $user->id);

        if (! $profile) {
            return redirect()->to('dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        $portfolioModel = model(MemberPortfolioModel::class);
        $id = (int) $this->request->getPost('id');

        // Check ownership if editing
        $existing = null;
        if ($id > 0) {
            $existing = $portfolioModel->findOwnedItem($id, $profile->id);
            if (! $existing) {
                return redirect()->to('dashboard/portofolio')
                    ->with('error', 'Anda tidak memiliki hak akses untuk mengubah portofolio ini.');
            }
        } else {
            // Check max limit
            $currentCount = $portfolioModel->countByProfileId($profile->id);
            if ($currentCount >= MemberPortfolioModel::MAX_ITEMS_PER_MEMBER) {
                return redirect()->to('dashboard/portofolio')
                    ->with('error', 'Batas maksimal portofolio (' . MemberPortfolioModel::MAX_ITEMS_PER_MEMBER . ' proyek) telah tercapai.');
            }
        }

        // Validation rules
        $rules = [
            'title'          => 'required|min_length[3]|max_length[150]',
            'description'    => 'required|min_length[10]',
            'project_year'   => 'required|max_length[10]',
            'event_location' => 'permit_empty|max_length[150]',
            'external_url'   => 'permit_empty|valid_url|max_length[255]',
            'sort_order'     => 'permit_empty|integer',
        ];

        // Cover image is required for new items, optional when updating
        if ($id === 0) {
            $rules['cover_image'] = 'uploaded[cover_image]|max_size[cover_image,3072]|ext_in[cover_image,png,jpg,jpeg,webp]|mime_in[cover_image,image/png,image/jpeg,image/webp]';
        } else {
            $rules['cover_image'] = 'permit_empty|uploaded[cover_image]|max_size[cover_image,3072]|ext_in[cover_image,png,jpg,jpeg,webp]|mime_in[cover_image,image/png,image/jpeg,image/webp]';
        }

        $messages = [
            'title' => [
                'required'   => 'Judul proyek / event wajib diisi.',
                'min_length' => 'Judul proyek minimal 3 karakter.',
            ],
            'description' => [
                'required'   => 'Deskripsi proyek wajib diisi.',
                'min_length' => 'Deskripsi proyek minimal 10 karakter.',
            ],
            'project_year' => [
                'required' => 'Tahun pelaksanaan event wajib diisi.',
            ],
            'cover_image' => [
                'uploaded' => 'Gambar sampul portofolio wajib diunggah.',
                'max_size' => 'Ukuran gambar sampul maksimal 3MB.',
                'ext_in'   => 'Format gambar harus PNG, JPG, atau WEBP.',
                'mime_in'  => 'Format file gambar tidak valid.',
            ],
            'external_url' => [
                'valid_url' => 'Format URL eksternal tidak valid.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('dashboard/portofolio')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Handle Image Upload
        $coverFile = $this->request->getFile('cover_image');
        $coverPath = $existing['cover_image'] ?? '';

        if ($coverFile && $coverFile->isValid() && ! $coverFile->hasMoved()) {
            $uploadTargetDir = FCPATH . 'uploads/portfolios/';
            if (! is_dir($uploadTargetDir)) {
                mkdir($uploadTargetDir, 0755, true);
            }

            // Remove previous image file
            if (! empty($coverPath) && file_exists(FCPATH . $coverPath) && str_starts_with($coverPath, 'uploads/portfolios/')) {
                @unlink(FCPATH . $coverPath);
            }

            $ext = $coverFile->guessExtension() ?: $coverFile->getClientExtension();
            $newFileName = 'port_' . $profile->id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $coverFile->move($uploadTargetDir, $newFileName);
            $coverPath = 'uploads/portfolios/' . $newFileName;
        }

        $data = [
            'member_profile_id' => $profile->id,
            'title'             => trim((string) $this->request->getPost('title')),
            'description'       => trim((string) $this->request->getPost('description')),
            'project_year'      => trim((string) $this->request->getPost('project_year')),
            'event_location'    => trim((string) $this->request->getPost('event_location')),
            'cover_image'       => $coverPath,
            'external_url'      => trim((string) $this->request->getPost('external_url')),
            'sort_order'        => (int) ($this->request->getPost('sort_order') ?: 0),
        ];

        if ($id > 0) {
            $portfolioModel->update($id, $data);
            $msg = 'Portofolio event berhasil diperbarui.';
        } else {
            $portfolioModel->insert($data);
            $msg = 'Portofolio event baru berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/portofolio')->with('message', $msg);
    }

    /**
     * Delete portfolio item with strict ownership verification
     */
    public function delete(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->to('login');
        }

        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->findByUserId((int) $user->id);

        if (! $profile) {
            return redirect()->to('dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        $portfolioModel = model(MemberPortfolioModel::class);
        $item = $portfolioModel->findOwnedItem($id, $profile->id);

        if (! $item) {
            return redirect()->to('dashboard/portofolio')
                ->with('error', 'Portofolio tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Delete image file safely
        if (! empty($item['cover_image']) && file_exists(FCPATH . $item['cover_image']) && str_starts_with($item['cover_image'], 'uploads/portfolios/')) {
            @unlink(FCPATH . $item['cover_image']);
        }

        $portfolioModel->delete($id);

        return redirect()->to('dashboard/portofolio')
            ->with('message', 'Item portofolio berhasil dihapus.');
    }
}
