<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MembershipModel;
use App\Models\MemberProfileModel;
use App\Services\Kta\CardImageRenderer;
use App\Services\Kta\CardPdfRenderer;
use App\Services\MembershipVerificationService;

class KtaController extends BaseController
{
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;
    protected CardImageRenderer $imageRenderer;
    protected CardPdfRenderer $pdfRenderer;
    protected MembershipVerificationService $verificationService;

    public function __construct()
    {
        $this->membershipModel = new MembershipModel();
        $this->profileModel = new MemberProfileModel();
        $this->imageRenderer = new CardImageRenderer();
        $this->pdfRenderer = new CardPdfRenderer();
        $this->verificationService = new MembershipVerificationService();
    }

    /**
     * Display Member KTA Dashboard with flip preview & download links
     */
    public function index()
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);
        $profile = $this->profileModel->findByUserId((int) $userId);

        $verificationUrl = '';
        if ($membership) {
            $membershipId = is_object($membership) ? (int) $membership->id : (int) ($membership['id'] ?? 0);
            $tokenData = $this->verificationService->getOrCreateToken($membershipId);
            $verificationUrl = $tokenData['url'] ?? site_url('verifikasi/' . ($tokenData['token'] ?? ''));
        }

        return view('member/kta/index', [
            'title'           => 'Kartu Tanda Anggota (KTA) Digital',
            'membership'      => $membership,
            'profile'         => $profile,
            'verificationUrl' => $verificationUrl,
        ]);
    }

    /**
     * Preview Card Front or Back in browser
     */
    public function preview(string $side = 'front')
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);

        if (!$membership) {
            return $this->response->setStatusCode(404)->setBody('Data keanggotaan tidak ditemukan.');
        }

        $membershipId = is_object($membership) ? (int) $membership->id : (int) ($membership['id'] ?? 0);

        $imageStream = ($side === 'back')
            ? $this->imageRenderer->renderBack($membershipId)
            : $this->imageRenderer->renderFront($membershipId);

        return $this->response
            ->setContentType('image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($imageStream);
    }

    /**
     * Download Front PNG
     */
    public function downloadFront()
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);

        $status = is_object($membership) ? $membership->status : ($membership['status'] ?? '');
        if (!$membership || $status !== 'active') {
            return redirect()->to(site_url('dashboard/kta'))->with('error', 'Hanya anggota berstatus aktif yang dapat mengunduh KTA resmi.');
        }

        $membershipId = is_object($membership) ? (int) $membership->id : (int) ($membership['id'] ?? 0);
        $memberNumber = is_object($membership) ? ($membership->member_number ?? $membership->membership_number) : ($membership['member_number'] ?? $membership['membership_number'] ?? 'KMO-2026-000000');

        $profile = $this->profileModel->findByUserId((int) $userId);
        $fullName = is_object($profile) ? ($profile->full_name ?? $profile->display_name) : ($profile['full_name'] ?? 'Member');
        $safeName = $this->sanitizeFilename($memberNumber . '_' . $fullName . '_DEPAN.png');

        $imageStream = $this->imageRenderer->renderFront($membershipId);

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
            ->setContentType('image/png')
            ->setBody($imageStream);
    }

    /**
     * Download Back PNG
     */
    public function downloadBack()
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);

        $status = is_object($membership) ? $membership->status : ($membership['status'] ?? '');
        if (!$membership || $status !== 'active') {
            return redirect()->to(site_url('dashboard/kta'))->with('error', 'Hanya anggota berstatus aktif yang dapat mengunduh KTA resmi.');
        }

        $membershipId = is_object($membership) ? (int) $membership->id : (int) ($membership['id'] ?? 0);
        $memberNumber = is_object($membership) ? ($membership->member_number ?? $membership->membership_number) : ($membership['member_number'] ?? $membership['membership_number'] ?? 'KMO-2026-000000');

        $profile = $this->profileModel->findByUserId((int) $userId);
        $fullName = is_object($profile) ? ($profile->full_name ?? $profile->display_name) : ($profile['full_name'] ?? 'Member');
        $safeName = $this->sanitizeFilename($memberNumber . '_' . $fullName . '_BELAKANG.png');

        $imageStream = $this->imageRenderer->renderBack($membershipId);

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
            ->setContentType('image/png')
            ->setBody($imageStream);
    }

    /**
     * Download Two-Sided PDF
     */
    public function downloadPdf()
    {
        $userId = auth()->id();
        $membership = $this->membershipModel->findByUserId((int) $userId);

        $status = is_object($membership) ? $membership->status : ($membership['status'] ?? '');
        if (!$membership || $status !== 'active') {
            return redirect()->to(site_url('dashboard/kta'))->with('error', 'Hanya anggota berstatus aktif yang dapat mengunduh KTA resmi.');
        }

        $membershipId = is_object($membership) ? (int) $membership->id : (int) ($membership['id'] ?? 0);
        $memberNumber = is_object($membership) ? ($membership->member_number ?? $membership->membership_number) : ($membership['member_number'] ?? $membership['membership_number'] ?? 'KMO-2026-000000');

        $profile = $this->profileModel->findByUserId((int) $userId);
        $fullName = is_object($profile) ? ($profile->full_name ?? $profile->display_name) : ($profile['full_name'] ?? 'Member');
        $safeName = $this->sanitizeFilename($memberNumber . '_' . $fullName . '_KTA.pdf');

        $pdfContent = $this->pdfRenderer->renderIndividualPdf($membershipId);

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
            ->setContentType('application/pdf')
            ->setBody($pdfContent);
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^\w\-\.]+/u', '_', $filename);
        return trim($filename, '_');
    }
}
