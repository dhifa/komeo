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
        $publicCv = $docModel->where('user_id', $profile->user_id)
            ->where('document_type', 'cv')
            ->where('visibility', 'public')
            ->where('is_active', 1)
            ->first();

        $hasAnyCv = $docModel->where('user_id', $profile->user_id)
            ->where('document_type', 'cv')
            ->where('is_active', 1)
            ->countAllResults() > 0;

        $publicDocs = $docModel->where('user_id', $profile->user_id)
            ->whereIn('document_type', ['portfolio_pdf', 'portfolio_external'])
            ->where('visibility', 'public')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->findAll();

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
}
