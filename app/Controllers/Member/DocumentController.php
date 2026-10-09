<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MemberDocumentModel;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use App\Services\AuditLogService;

class DocumentController extends BaseController
{
    protected MemberDocumentModel $docModel;
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;
    protected string $storageDir;

    public function __construct()
    {
        $this->docModel        = new MemberDocumentModel();
        $this->membershipModel = new MembershipModel();
        $this->profileModel    = new MemberProfileModel();
        $this->storageDir      = WRITEPATH . 'uploads/member-documents/';

        if (! is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    /**
     * Document Management View (CV & Portfolios)
     */
    public function index()
    {
        $userId = (int) auth()->id();
        $membership = $this->membershipModel->findByUserId($userId);
        $profile = $this->profileModel->findByUserId($userId);

        $cv = $this->docModel->getUserCv($userId);
        $portfolios = $this->docModel->getUserPortfolios($userId);
        $pdfPortfolioCount = $this->docModel->countUserPdfPortfolios($userId);

        return view('member/documents/index', [
            'title'             => 'CV & Portofolio Profesional',
            'membership'        => $membership,
            'profile'           => $profile,
            'cv'                => $cv,
            'portfolios'        => $portfolios,
            'pdfPortfolioCount' => $pdfPortfolioCount,
        ]);
    }

    /**
     * Upload or Replace CV (Max 5 MB, PDF only)
     */
    public function uploadCv()
    {
        $userId = (int) auth()->id();

        $rules = [
            'cv_file' => [
                'label' => 'Berkas CV',
                'rules' => 'uploaded[cv_file]|ext_in[cv_file,pdf]|max_size[cv_file,5120]|mime_in[cv_file,application/pdf]',
            ],
            'visibility' => 'required|in_list[public,request_only,private]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', $this->validator->getError('cv_file') ?: 'Format berkas CV tidak valid. Harap unggah berkas PDF maksimal 5 MB.');
        }

        $file = $this->request->getFile('cv_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Gagal membaca berkas yang diunggah.');
        }

        // Additional PDF Magic Number Validation
        $realPath = $file->getTempName();
        $handle = fopen($realPath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            return redirect()->back()->with('error', 'Berkas bukan berkas PDF yang valid.');
        }

        $filename = 'cv_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $file->move($this->storageDir, $filename);

        // Deactivate old CV
        $oldCv = $this->docModel->getUserCv($userId);
        if ($oldCv) {
            $this->docModel->update($oldCv['id'], ['is_active' => 0]);
            if (! empty($oldCv['file_path']) && file_exists($this->storageDir . $oldCv['file_path'])) {
                @unlink($this->storageDir . $oldCv['file_path']);
            }
        }

        $title = $this->request->getPost('title') ?: 'Curriculum Vitae';
        $visibility = $this->request->getPost('visibility');

        $docId = $this->docModel->insert([
            'user_id'       => $userId,
            'document_type' => 'cv',
            'title'         => $title,
            'description'   => $this->request->getPost('description'),
            'file_path'     => $filename,
            'file_size'     => $file->getSize(),
            'mime_type'     => 'application/pdf',
            'visibility'    => $visibility,
            'is_active'     => 1,
        ], true);

        AuditLogService::log($userId, 'document.upload_cv', 'member_documents', (int) $docId);
        return redirect()->to('dashboard/dokumen')->with('success', 'Curriculum Vitae berhasil diunggah.');
    }

    /**
     * Upload PDF Portfolio (Max 10 MB, max 5 files)
     */
    public function uploadPortfolioPdf()
    {
        $userId = (int) auth()->id();

        // Check max 5 portfolios limit
        if ($this->docModel->countUserPdfPortfolios($userId) >= 5) {
            return redirect()->back()->with('error', 'Batas maksimal 5 berkas portofolio PDF telah tercapai.');
        }

        $rules = [
            'portfolio_file' => [
                'label' => 'Berkas Portofolio PDF',
                'rules' => 'uploaded[portfolio_file]|ext_in[portfolio_file,pdf]|max_size[portfolio_file,10240]|mime_in[portfolio_file,application/pdf]',
            ],
            'title'      => 'required|min_length[3]|max_length[255]',
            'visibility' => 'required|in_list[public,request_only,private]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', $this->validator->getError('portfolio_file') ?: 'Harap unggah berkas PDF portofolio maksimal 10 MB.');
        }

        $file = $this->request->getFile('portfolio_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Gagal memproses berkas portofolio.');
        }

        // Magic number verification
        $realPath = $file->getTempName();
        $handle = fopen($realPath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            return redirect()->back()->with('error', 'Berkas yang diunggah bukan PDF yang valid.');
        }

        $filename = 'port_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $file->move($this->storageDir, $filename);

        $docId = $this->docModel->insert([
            'user_id'       => $userId,
            'document_type' => 'portfolio_pdf',
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'file_path'     => $filename,
            'file_size'     => $file->getSize(),
            'mime_type'     => 'application/pdf',
            'visibility'    => $this->request->getPost('visibility'),
            'is_active'     => 1,
        ], true);

        AuditLogService::log($userId, 'document.upload_portfolio_pdf', 'member_documents', (int) $docId);
        return redirect()->to('dashboard/dokumen')->with('success', 'Portofolio PDF berhasil diunggah.');
    }

    /**
     * Add External Portfolio Link (Behance, Drive, Youtube, etc.)
     */
    public function addExternalLink()
    {
        $userId = (int) auth()->id();

        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'external_url' => 'required|valid_url_strict[https]',
            'visibility'   => 'required|in_list[public,request_only,private]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Tautan tidak valid. Pastikan tautan menggunakan protokol HTTPS yang aman.');
        }

        $url = trim((string) $this->request->getPost('external_url'));
        $platform = 'Website';

        if (str_contains($url, 'behance.net')) {
            $platform = 'Behance';
        } elseif (str_contains($url, 'drive.google.com')) {
            $platform = 'Google Drive';
        } elseif (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $platform = 'YouTube';
        } elseif (str_contains($url, 'vimeo.com')) {
            $platform = 'Vimeo';
        } elseif (str_contains($url, 'dribbble.com')) {
            $platform = 'Dribbble';
        }

        $docId = $this->docModel->insert([
            'user_id'           => $userId,
            'document_type'     => 'portfolio_external',
            'title'             => $this->request->getPost('title'),
            'description'       => $this->request->getPost('description'),
            'external_url'      => $url,
            'external_platform' => $platform,
            'visibility'        => $this->request->getPost('visibility'),
            'is_active'         => 1,
        ], true);

        AuditLogService::log($userId, 'document.add_external_link', 'member_documents', (int) $docId);
        return redirect()->to('dashboard/dokumen')->with('success', 'Tautan portofolio eksternal berhasil ditambahkan.');
    }

    /**
     * Update Visibility
     */
    public function updateVisibility(int $id)
    {
        $userId = (int) auth()->id();
        $doc = $this->docModel->where('id', $id)->where('user_id', $userId)->first();

        if (! $doc) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $visibility = $this->request->getPost('visibility');
        if (! in_array($visibility, ['public', 'request_only', 'private'])) {
            return redirect()->back()->with('error', 'Opsi visibilitas tidak valid.');
        }

        $this->docModel->update($id, ['visibility' => $visibility]);
        AuditLogService::log($userId, 'document.update_visibility', 'member_documents', $id, ['visibility' => $visibility]);

        return redirect()->to('dashboard/dokumen')->with('success', 'Visibilitas dokumen berhasil diperbarui.');
    }

    /**
     * Download Own Document
     */
    public function download(int $id)
    {
        $userId = (int) auth()->id();
        $doc = $this->docModel->where('id', $id)->where('user_id', $userId)->first();

        if (! $doc || empty($doc['file_path'])) {
            return $this->response->setStatusCode(404)->setBody('Dokumen tidak ditemukan.');
        }

        $fullPath = $this->storageDir . $doc['file_path'];
        if (! file_exists($fullPath)) {
            return $this->response->setStatusCode(404)->setBody('Berkas fisik tidak ditemukan.');
        }

        return $this->response->download($fullPath, null)->setFileName($doc['title'] . '.pdf');
    }

    /**
     * Delete Document
     */
    public function delete(int $id)
    {
        $userId = (int) auth()->id();
        $doc = $this->docModel->where('id', $id)->where('user_id', $userId)->first();

        if (! $doc) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        if (! empty($doc['file_path'])) {
            $fullPath = $this->storageDir . $doc['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $this->docModel->delete($id);
        AuditLogService::log($userId, 'document.delete', 'member_documents', $id);

        return redirect()->to('dashboard/dokumen')->with('success', 'Dokumen berhasil dihapus.');
    }
}
