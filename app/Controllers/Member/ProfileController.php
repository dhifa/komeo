<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use App\Models\MemberPortfolioModel;
use App\Models\MemberProfileModel;
use App\Models\MemberSpecializationModel;
use App\Models\MembershipModel;
use App\Models\SpecializationModel;
use CodeIgniter\HTTP\RedirectResponse;

class ProfileController extends BaseController
{
    /**
     * Show member's own profile page
     */
    public function show(): string|RedirectResponse
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

        $categoryModel      = model(EventCategoryModel::class);
        $memberSpecModel    = model(MemberSpecializationModel::class);
        $portfolioModel     = model(MemberPortfolioModel::class);
        $membershipModel    = model(MembershipModel::class);

        $category        = $profile->category_id ? $categoryModel->find($profile->category_id) : null;
        $specializations = $memberSpecModel->getSpecializationsByProfileId($profile->id);
        $portfolios      = $portfolioModel->getByProfileId($profile->id);
        $membership      = $membershipModel->findByUserId((int) $user->id);
        $completion      = $profile->calculateCompletion(count($specializations));

        return view('dashboard/profile/show', [
            'title'           => 'Profil Saya - KOMEO.ID',
            'user'            => $user,
            'profile'         => $profile,
            'category'        => $category,
            'specializations' => $specializations,
            'portfolios'      => $portfolios,
            'membership'      => $membership,
            'completion'      => $completion,
        ]);
    }

    /**
     * Edit member profile view
     */
    public function edit(): string|RedirectResponse
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

        $categoryModel    = model(EventCategoryModel::class);
        $specModel        = model(SpecializationModel::class);
        $memberSpecModel  = model(MemberSpecializationModel::class);

        $categories       = $categoryModel->getActiveCategories();
        $specializations  = $specModel->getActiveSpecializations();
        $selectedSpecIds  = $memberSpecModel->getSpecializationIds($profile->id);
        $completion       = $profile->calculateCompletion(count($selectedSpecIds));

        return view('dashboard/profile/edit', [
            'title'           => 'Edit Profil Member - KOMEO.ID',
            'user'            => $user,
            'profile'         => $profile,
            'categories'      => $categories,
            'specializations' => $specializations,
            'selectedSpecIds' => $selectedSpecIds,
            'completion'      => $completion,
        ]);
    }

    /**
     * Update member profile action
     */
    public function update(): RedirectResponse
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

        $memberType = (string) $this->request->getPost('member_type');
        $username   = strtolower(trim((string) $this->request->getPost('username')));

        // Dynamic validation rules
        $rules = [
            'full_name'           => 'required|min_length[2]|max_length[150]',
            'display_name'        => 'permit_empty|max_length[100]',
            'username'            => 'required|alpha_dash|min_length[3]|max_length[50]',
            'member_type'         => 'required|in_list[individual,business]',
            'category_id'         => 'permit_empty|is_natural_no_zero',
            'business_name'       => ($memberType === 'business') ? 'required|min_length[2]|max_length[150]' : 'permit_empty|max_length[150]',
            'business_description'=> 'permit_empty',
            'years_of_experience' => 'permit_empty|is_natural|less_than[70]',
            'bio'                 => 'permit_empty',
            'city'                => 'permit_empty|max_length[100]',
            'province'            => 'permit_empty|max_length[100]',
            'whatsapp'            => 'permit_empty|max_length[30]',
            'instagram'           => 'permit_empty|max_length[100]',
            'tiktok'              => 'permit_empty|max_length[100]',
            'linkedin'            => 'permit_empty|valid_url|max_length[255]',
            'youtube'             => 'permit_empty|valid_url|max_length[255]',
            'website'             => 'permit_empty|valid_url|max_length[255]',
            'is_public'           => 'permit_empty|in_list[0,1]',
            'show_whatsapp'       => 'permit_empty|in_list[0,1]',
            'show_social'         => 'permit_empty|in_list[0,1]',
            'show_location'       => 'permit_empty|in_list[0,1]',
            'photo'               => 'permit_empty|uploaded[photo]|max_size[photo,2048]|ext_in[photo,png,jpg,jpeg,webp]|mime_in[photo,image/png,image/jpeg,image/webp]',
            'company_logo'        => 'permit_empty|uploaded[company_logo]|max_size[company_logo,2048]|ext_in[company_logo,png,jpg,jpeg,webp]|mime_in[company_logo,image/png,image/jpeg,image/webp]',
        ];

        $messages = [
            'full_name' => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama lengkap minimal 2 karakter.',
            ],
            'business_name' => [
                'required' => 'Nama perusahaan / bisnis wajib diisi untuk tipe keanggotaan bisnis.',
            ],
            'username' => [
                'required'   => 'Username wajib diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'alpha_dash' => 'Username hanya boleh huruf, angka, tanda hubung, dan garis bawah.',
            ],
            'linkedin' => ['valid_url' => 'Format URL LinkedIn tidak valid (harus menyertakan http:// atau https://).'],
            'youtube'  => ['valid_url' => 'Format URL YouTube tidak valid.'],
            'website'  => ['valid_url' => 'Format URL Website tidak valid.'],
            'photo' => [
                'max_size' => 'Ukuran foto profil maksimal 2MB.',
                'ext_in'   => 'Format foto profil harus JPG, PNG, atau WEBP.',
                'mime_in'  => 'Tipe MIME foto profil tidak valid.',
            ],
            'company_logo' => [
                'max_size' => 'Ukuran logo perusahaan maksimal 2MB.',
                'ext_in'   => 'Format logo perusahaan harus JPG, PNG, atau WEBP.',
                'mime_in'  => 'Tipe MIME logo perusahaan tidak valid.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('dashboard/profil/edit')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Validate username uniqueness
        if ($profileModel->isUsernameTaken($username, $profile->id)) {
            return redirect()->to('dashboard/profil/edit')
                ->withInput()
                ->with('errors', ['username' => "Username '{$username}' sudah digunakan oleh member lain. Silakan pilih username lain."]);
        }

        // Validate specializations limit (max 5)
        $rawSpecializations = (array) $this->request->getPost('specializations');
        $specializationIds  = array_values(array_unique(array_filter(array_map('intval', $rawSpecializations))));
        if (count($specializationIds) > 5) {
            return redirect()->to('dashboard/profil/edit')
                ->withInput()
                ->with('errors', ['specializations' => 'Pilihan keahlian spesialisasi maksimal 5 item.']);
        }

        // Handle Photo Upload
        $photoFile = $this->request->getFile('photo');
        $photoPath = $profile->photo_path;
        if ($photoFile && $photoFile->isValid() && ! $photoFile->hasMoved()) {
            $ext = $photoFile->guessExtension() ?: $photoFile->getClientExtension();
            $newFileName = 'avatar_' . $user->id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $uploadTargetDir = FCPATH . 'uploads/avatars/';

            if (! is_dir($uploadTargetDir)) {
                mkdir($uploadTargetDir, 0755, true);
            }

            // Remove old photo
            if (! empty($photoPath) && file_exists(FCPATH . $photoPath) && str_starts_with($photoPath, 'uploads/avatars/')) {
                @unlink(FCPATH . $photoPath);
            }

            $photoFile->move($uploadTargetDir, $newFileName);
            $photoPath = 'uploads/avatars/' . $newFileName;
        }

        // Handle Company Logo Upload
        $logoFile = $this->request->getFile('company_logo');
        $companyLogoPath = $profile->company_logo_path;
        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $ext = $logoFile->guessExtension() ?: $logoFile->getClientExtension();
            $newLogoName = 'logo_' . $user->id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $logoTargetDir = FCPATH . 'uploads/company_logos/';

            if (! is_dir($logoTargetDir)) {
                mkdir($logoTargetDir, 0755, true);
            }

            // Remove old logo
            if (! empty($companyLogoPath) && file_exists(FCPATH . $companyLogoPath) && str_starts_with($companyLogoPath, 'uploads/company_logos/')) {
                @unlink(FCPATH . $companyLogoPath);
            }

            $logoFile->move($logoTargetDir, $newLogoName);
            $companyLogoPath = 'uploads/company_logos/' . $newLogoName;
        }

        // Prepare update data
        $updateData = [
            'full_name'            => trim((string) $this->request->getPost('full_name')),
            'display_name'         => trim((string) $this->request->getPost('display_name')) ?: trim((string) $this->request->getPost('full_name')),
            'username'             => $username,
            'member_type'          => $memberType,
            'category_id'          => $this->request->getPost('category_id') ? (int) $this->request->getPost('category_id') : null,
            'business_name'        => ($memberType === 'business') ? trim((string) $this->request->getPost('business_name')) : null,
            'business_description' => ($memberType === 'business') ? trim((string) $this->request->getPost('business_description')) : null,
            'years_of_experience'  => $this->request->getPost('years_of_experience') !== '' ? (int) $this->request->getPost('years_of_experience') : null,
            'photo_path'           => $photoPath,
            'company_logo_path'    => $companyLogoPath,
            'bio'                  => trim((string) $this->request->getPost('bio')),
            'city'                 => trim((string) $this->request->getPost('city')),
            'province'             => trim((string) $this->request->getPost('province')),
            'instagram'            => trim((string) $this->request->getPost('instagram')),
            'tiktok'               => trim((string) $this->request->getPost('tiktok')),
            'linkedin'             => trim((string) $this->request->getPost('linkedin')),
            'youtube'              => trim((string) $this->request->getPost('youtube')),
            'website'              => trim((string) $this->request->getPost('website')),
            'whatsapp'             => trim((string) $this->request->getPost('whatsapp')),
            'is_public'            => $this->request->getPost('is_public') ? 1 : 0,
            'show_whatsapp'        => $this->request->getPost('show_whatsapp') ? 1 : 0,
            'show_social'          => $this->request->getPost('show_social') ? 1 : 0,
            'show_location'        => $this->request->getPost('show_location') ? 1 : 0,
        ];

        // Save profile
        $profileModel->update($profile->id, $updateData);

        // Update username on Shield user entity if changed
        if ($user->username !== $username) {
            $userProvider = auth()->getProvider();
            $user->username = $username;
            $userProvider->save($user);
        }

        // Sync specializations
        $memberSpecModel = model(MemberSpecializationModel::class);
        $memberSpecModel->syncSpecializations($profile->id, $specializationIds);

        return redirect()->to('dashboard/profil/edit')
            ->with('message', 'Profil berhasil disimpan dan diperbarui.');
    }
}
