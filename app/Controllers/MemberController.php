<?php

namespace App\Controllers;

use App\Models\EventCategoryModel;
use App\Models\MemberPortfolioModel;
use App\Models\MemberProfileModel;
use App\Models\MemberSpecializationModel;
use App\Models\MembershipModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class MemberController extends BaseController
{
    /**
     * Display public or preview member profile page
     */
    public function publicProfile(string $username): string|ResponseInterface
    {
        $profileModel = model(MemberProfileModel::class);
        $profile = $profileModel->findByUsername(strtolower(trim($username)));

        if (! $profile) {
            throw PageNotFoundException::forPageNotFound("Profil member '@{$username}' tidak ditemukan.");
        }

        $currentUser = auth()->user();
        $isOwner     = ($currentUser && (int) $currentUser->id === (int) $profile->user_id);
        $isAdmin     = ($currentUser && $currentUser->inGroup('admin', 'superadmin'));

        $membershipModel = model(MembershipModel::class);
        $membership      = $membershipModel->findByUserId((int) $profile->user_id);
        $isActive        = ($membership && $membership->status === 'active');

        // Privacy & Membership authorization checks
        $isPrivate = ! $profile->is_public;
        $isPending = ! $isActive;

        // If profile is private or membership is not active, only owner or admin can view preview
        if ($isPrivate || $isPending) {
            if (! ($isOwner || $isAdmin)) {
                // Public cannot see private or pending members
                throw PageNotFoundException::forPageNotFound("Profil member '@{$username}' tidak tersedia atau belum dipublikasikan.");
            }
        }

        $categoryModel   = model(EventCategoryModel::class);
        $memberSpecModel = model(MemberSpecializationModel::class);
        $portfolioModel  = model(MemberPortfolioModel::class);

        $category        = $profile->category_id ? $categoryModel->find($profile->category_id) : null;
        $specializations = $memberSpecModel->getSpecializationsByProfileId($profile->id);
        $portfolios      = $portfolioModel->getByProfileId($profile->id);

        // Fetch Phase 5.6 Professional Documents (CV and PDF/External Portfolios)
        $docModel = model(\App\Models\MemberDocumentModel::class);
        $activeCv = $docModel->where('user_id', $profile->user_id)
            ->where('document_type', 'cv')
            ->where('is_active', 1)
            ->first();

        $publicCv = ($activeCv && $activeCv['visibility'] === 'public') ? $activeCv : null;
        $hasAnyCv = $activeCv !== null;

        $activeDocs = $docModel->where('user_id', $profile->user_id)
            ->whereIn('document_type', ['portfolio_pdf', 'portfolio_external'])
            ->whereIn('visibility', ['public', 'request_only'])
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->findAll();

        $publicDocs = array_filter($activeDocs, static fn($d) => $d['visibility'] === 'public');

        $previewNotice = null;
        if ($isPrivate && ($isOwner || $isAdmin)) {
            $previewNotice = 'Mode Pratinjau: Profil ini saat ini disetel privat dan tidak dapat diakses oleh publik.';
        } elseif ($isPending && ($isOwner || $isAdmin)) {
            $previewNotice = 'Mode Pratinjau: Keanggotaan Anda saat ini berstatus ' . ucfirst($membership->status ?? 'Pending') . ' dan belum dipublikasikan ke direktori publik.';
        }

        $displayName = $profile->display_name ?: $profile->full_name;
        $pageTitle   = "{$displayName} - Direktori Member KOMEO.ID";

        $metaDesc = ! empty($profile->bio)
            ? mb_strimwidth(strip_tags($profile->bio), 0, 160, '...')
            : (! empty($profile->business_description)
                ? mb_strimwidth(strip_tags($profile->business_description), 0, 160, '...')
                : "Profil profesional {$displayName} di KOMEO.ID - Komunitas Pelaku Industri Event Indonesia.");

        $canonicalUrl = base_url('member/' . esc($profile->username));

        $ogImage = null;
        if (! empty($profile->company_logo_path) && file_exists(FCPATH . $profile->company_logo_path)) {
            $ogImage = base_url($profile->company_logo_path);
        } elseif (! empty($profile->photo_path) && file_exists(FCPATH . $profile->photo_path)) {
            $ogImage = base_url($profile->photo_path);
        }

        $metaRobots = ($isPrivate || $isPending) ? 'noindex, nofollow' : 'index, follow';

        // Phase 5 Extension: Custom Member Badges (only visible if active & public)
        $memberBadges = [];
        if ($isActive && (int) $profile->is_public === 1) {
            $memberBadgeModel = model(\App\Models\MemberBadgeModel::class);
            $memberBadges     = $memberBadgeModel->getActiveBadgesForUser((int) $profile->user_id);
        }

        // Identity & Business Verification
        $verificationModel = model(\App\Models\MemberVerificationModel::class);
        $verification      = $verificationModel->getByUserId((int) $profile->user_id);

        return view('member/public_profile', [
            'title'           => $pageTitle,
            'metaDescription' => $metaDesc,
            'canonicalUrl'    => $canonicalUrl,
            'ogTitle'         => $pageTitle,
            'ogDescription'   => $metaDesc,
            'ogImage'         => $ogImage,
            'metaRobots'      => $metaRobots,
            'profile'         => $profile,
            'membership'      => $membership,
            'category'        => $category,
            'specializations' => $specializations,
            'portfolios'      => $portfolios,
            'activeCv'        => $activeCv,
            'activeDocs'      => $activeDocs,
            'publicCv'        => $publicCv,
            'hasAnyCv'        => $hasAnyCv,
            'publicDocs'      => $publicDocs,
            'isOwner'         => $isOwner,
            'isAdmin'         => $isAdmin,
            'isActive'        => $isActive,
            'previewNotice'   => $previewNotice,
            'memberBadges'    => $memberBadges,
            'verification'    => $verification,
        ]);
    }

    /**
     * View Public Document inline (CV or Portfolio PDF)
     */
    public function viewPublicDocument(int $id)
    {
        return $this->servePublicDocument($id, true);
    }

    /**
     * Force Download Public Document (CV or Portfolio PDF)
     */
    public function downloadPublicDocument(int $id)
    {
        return $this->servePublicDocument($id, false);
    }

    /**
     * Helper to serve a public document securely
     */
    protected function servePublicDocument(int $id, bool $inline = true)
    {
        $docModel = model(\App\Models\MemberDocumentModel::class);
        $doc = $docModel->where('id', $id)
            ->where('is_active', 1)
            ->where('visibility', 'public')
            ->first();

        if (! $doc) {
            throw PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan atau tidak dipublikasikan.');
        }

        // If external URL, redirect directly
        if ($doc['document_type'] === 'portfolio_external' && ! empty($doc['external_url'])) {
            return redirect()->to($doc['external_url']);
        }

        $storageDir = WRITEPATH . 'uploads/member-documents/';
        $cleanRelPath = ltrim(str_replace(['member-documents/', 'member-documents\\'], '', $doc['file_path']), '/\\');
        $filePath = $storageDir . $cleanRelPath;

        if (! file_exists($filePath)) {
            $filePath = WRITEPATH . 'uploads/' . ltrim($doc['file_path'], '/\\');
        }

        if (empty($doc['file_path']) || ! file_exists($filePath)) {
            throw PageNotFoundException::forPageNotFound('Berkas fisik dokumen tidak ditemukan.');
        }

        $cleanTitle = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $doc['title'] ?: 'Dokumen') . '.pdf';
        $content    = file_get_contents($filePath);
        $fileSize   = strlen($content);
        header_remove('Pragma');
        header_remove('Expires');

        if ($inline) {
            return $this->response
                ->setContentType('application/pdf', '')
                ->removeHeader('Pragma')
                ->removeHeader('Expires')
                ->setHeader('Content-Disposition', 'inline; filename="' . $cleanTitle . '"')
                ->setHeader('Content-Length', (string) $fileSize)
                ->setHeader('Accept-Ranges', 'bytes')
                ->setHeader('Cache-Control', 'public, max-age=86400, must-revalidate')
                ->setBody($content);
        }

        return $this->response
            ->setContentType('application/pdf', '')
            ->removeHeader('Pragma')
            ->removeHeader('Expires')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $cleanTitle . '"')
            ->setHeader('Content-Length', (string) $fileSize)
            ->setBody($content);
    }
}
