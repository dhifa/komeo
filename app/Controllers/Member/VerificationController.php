<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MemberProfileModel;
use App\Models\MemberVerificationHistoryModel;
use App\Models\MemberVerificationModel;
use App\Models\MembershipModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class VerificationController extends BaseController
{
    protected MemberVerificationModel $verificationModel;
    protected MemberProfileModel $profileModel;
    protected MembershipModel $membershipModel;

    public function __construct()
    {
        $this->verificationModel = new MemberVerificationModel();
        $this->profileModel      = new MemberProfileModel();
        $this->membershipModel   = new MembershipModel();
    }

    /**
     * Member verification dashboard
     */
    public function index(): string|ResponseInterface
    {
        $userId  = (int) auth()->user()->id;
        $profile = $this->profileModel->findByUserId($userId);

        if (! $profile) {
            return redirect()->to(base_url('dashboard/profil/edit'))
                ->with('error', 'Silakan lengkapi profil dasar Anda terlebih dahulu sebelum mengajukan verifikasi.');
        }

        $membership   = $this->membershipModel->findByUserId($userId);
        $verification = $this->verificationModel->getOrCreateForUser($userId, $profile->member_type ?? 'individual');

        $historyModel = model(MemberVerificationHistoryModel::class);
        $history      = $historyModel->getByVerificationId((int) $verification['id']);

        return view('member/verification/index', [
            'title'        => 'Verifikasi Identitas & Bisnis - KOMEO.ID',
            'profile'      => $profile,
            'membership'   => $membership,
            'verification' => $verification,
            'history'      => $history,
        ]);
    }

    /**
     * Process verification submission
     */
    public function submit(): ResponseInterface
    {
        $userId  = (int) auth()->user()->id;
        $profile = $this->profileModel->findByUserId($userId);

        if (! $profile) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        $subjectType = $profile->isBusiness() ? 'business' : 'individual';
        $method      = $this->request->getPost('verification_method');

        if ($subjectType === 'individual') {
            $method = 'ktp_selfie';
        } else {
            if (! in_array($method, ['pic_only', 'nib_pic'], true)) {
                $method = 'pic_only';
            }
        }

        // Validate Consent
        $consent = $this->request->getPost('consent_agreement');
        if (! $consent) {
            return redirect()->back()->withInput()->with('error', 'Anda wajib menyetujui pernyataan kebenaran data dan izin verifikasi.');
        }

        $payload = [
            'verification_subject_type' => $subjectType,
            'verification_method'       => $method,
        ];

        // Specific fields for Business
        if ($subjectType === 'business') {
            $bizName = trim((string) $this->request->getPost('business_name'));
            $picName = trim((string) $this->request->getPost('pic_name'));

            if (empty($bizName) || empty($picName)) {
                return redirect()->back()->withInput()->with('error', 'Nama bisnis dan nama PIC wajib diisi.');
            }

            $payload['business_name'] = $bizName;
            $payload['pic_name']      = $picName;

            // PIC Declaration Check
            $picDecl = $this->request->getPost('pic_declaration');
            if (! $picDecl) {
                return redirect()->back()->withInput()->with('error', 'Pernyataan tanggung jawab/kewenangan PIC wajib disetujui.');
            }
        }

        // Directory for secure files
        $uploadDir = WRITEPATH . 'uploads/verifications/' . $userId . '/';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0750, true);
        }

        $existing = $this->verificationModel->getByUserId($userId);

        // 1. Upload KTP (Required if not already uploaded)
        $ktpFile = $this->request->getFile('ktp_document');
        if ($ktpFile && $ktpFile->isValid() && ! $ktpFile->hasMoved()) {
            $ktpRes = $this->handleFileUpload($ktpFile, $uploadDir, 'ktp', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
            if (! $ktpRes['success']) {
                return redirect()->back()->withInput()->with('error', 'Foto KTP: ' . $ktpRes['message']);
            }
            $payload['ktp_document_path'] = 'uploads/verifications/' . $userId . '/' . $ktpRes['filename'];
        } elseif (empty($existing['ktp_document_path'])) {
            return redirect()->back()->withInput()->with('error', 'Foto KTP penanggung jawab wajib diunggah.');
        }

        // 2. Upload Selfie (Required if not already uploaded)
        $selfieFile = $this->request->getFile('selfie_document');
        if ($selfieFile && $selfieFile->isValid() && ! $selfieFile->hasMoved()) {
            $selfieRes = $this->handleFileUpload($selfieFile, $uploadDir, 'selfie', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);
            if (! $selfieRes['success']) {
                return redirect()->back()->withInput()->with('error', 'Foto Selfie: ' . $selfieRes['message']);
            }
            $payload['selfie_document_path'] = 'uploads/verifications/' . $userId . '/' . $selfieRes['filename'];
        } elseif (empty($existing['selfie_document_path'])) {
            return redirect()->back()->withInput()->with('error', 'Foto Selfie penanggung jawab wajib diunggah.');
        }

        // 3. Upload NIB (Required if method is nib_pic)
        if ($method === 'nib_pic') {
            $nibFile = $this->request->getFile('nib_document');
            if ($nibFile && $nibFile->isValid() && ! $nibFile->hasMoved()) {
                $nibRes = $this->handleFileUpload($nibFile, $uploadDir, 'nib', ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg']);
                if (! $nibRes['success']) {
                    return redirect()->back()->withInput()->with('error', 'Dokumen NIB: ' . $nibRes['message']);
                }
                $payload['nib_document_path'] = 'uploads/verifications/' . $userId . '/' . $nibRes['filename'];
            } elseif (empty($existing['nib_document_path'])) {
                return redirect()->back()->withInput()->with('error', 'Dokumen NIB (Nomor Induk Berusaha) wajib diunggah untuk jalur bisnis ber-NIB.');
            }
        }

        $result = $this->verificationModel->submitVerification($userId, $payload);

        if ($result['success']) {
            return redirect()->to(base_url('dashboard/verifikasi-identitas'))
                ->with('success', 'Pengajuan verifikasi berhasil dikirim. Tim admin KOMEO akan meninjau dokumen Anda.');
        }

        return redirect()->back()->withInput()->with('error', $result['message']);
    }

    /**
     * Preview user's own verification document securely
     */
    public function previewMyDocument(string $type): ResponseInterface
    {
        $userId       = (int) auth()->user()->id;
        $verification = $this->verificationModel->getByUserId($userId);

        if (! $verification) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $field = match ($type) {
            'ktp'    => 'ktp_document_path',
            'selfie' => 'selfie_document_path',
            'nib'    => 'nib_document_path',
            default  => null,
        };

        if (! $field || empty($verification[$field])) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $verification[$field];
        if (! file_exists($fullPath)) {
            throw PageNotFoundException::forPageNotFound('Berkas dokumen fisik telah dihapus atau tidak ditemukan.');
        }

        $mime = function_exists('komeo_detect_mime_type')
            ? komeo_detect_mime_type($fullPath)
            : $this->detectMimeType($fullPath);
        $content = file_get_contents($fullPath);

        return $this->response
            ->setContentType($mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fullPath) . '"')
            ->setHeader('Cache-Control', 'private, no-cache, no-store, must-revalidate')
            ->setBody($content);
    }

    /**
     * Validate and store uploaded file securely
     */
    protected function handleFileUpload($file, string $targetDir, string $prefix, array $allowedMimes): array
    {
        if ($file->getSize() > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran berkas maksimal adalah 5MB.'];
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, $allowedMimes, true)) {
            return ['success' => false, 'message' => 'Format berkas tidak didukung. Harap unggah format JPG, PNG, atau PDF (untuk NIB).'];
        }

        // Deep validation
        $ext = strtolower($file->getExtension());
        if ($mime === 'application/pdf') {
            $handle = fopen($file->getTempName(), 'rb');
            $header = fread($handle, 5);
            fclose($handle);
            if (! str_starts_with($header, '%PDF-')) {
                return ['success' => false, 'message' => 'Berkas PDF tidak valid atau korup.'];
            }
        } else {
            // Verify real image
            $imageInfo = @getimagesize($file->getTempName());
            if ($imageInfo === false) {
                return ['success' => false, 'message' => 'Berkas gambar tidak valid.'];
            }
        }

        // Random filename to avoid guessability
        $randomName = $prefix . '_' . bin2hex(random_bytes(12)) . '.' . ($ext ?: 'bin');
        $file->move($targetDir, $randomName, true);

        return [
            'success'  => true,
            'filename' => $randomName,
        ];
    }

    /**
     * Safely detect MIME type of document without requiring ext-fileinfo
     */
    protected function detectMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            $mime = \Config\Mimes::guessTypeFromExtension($ext);
            if (! empty($mime)) {
                return is_array($mime) ? $mime[0] : $mime;
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        $map = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}
