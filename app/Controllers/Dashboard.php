<?php

namespace App\Controllers;

use App\Models\EventCategoryModel;
use App\Models\MemberPortfolioModel;
use App\Models\MemberProfileModel;
use App\Models\MemberSpecializationModel;
use App\Models\MembershipModel;
use App\Models\MemberWarningModel;

class Dashboard extends BaseController
{
    /**
     * Member Dashboard index
     */
    public function index(): string
    {
        $user = auth()->user();
        $userId = (int) $user->id;

        $profileModel       = model(MemberProfileModel::class);
        $membershipModel    = model(MembershipModel::class);
        $categoryModel      = model(EventCategoryModel::class);
        $memberSpecModel    = model(MemberSpecializationModel::class);
        $portfolioModel     = model(MemberPortfolioModel::class);

        $profile    = $profileModel->findByUserId($userId);
        $membership = $membershipModel->findByUserId($userId);

        $specializations = [];
        $category        = null;
        $portfolios      = [];
        $completion      = [
            'percentage'      => 0,
            'completed_count' => 0,
            'total_count'     => 10,
            'missing_fields'  => [],
        ];

        if ($profile) {
            $specializations = $memberSpecModel->getSpecializationsByProfileId($profile->id);
            if (! empty($profile->category_id)) {
                $category = $categoryModel->find($profile->category_id);
            }
            $portfolios = $portfolioModel->getByProfileId($profile->id);
            $completion = $profile->calculateCompletion(count($specializations));
        }

        $warningModel = model(MemberWarningModel::class);
        $warnings     = $warningModel->getActiveByUserId($userId);

        // Phase 5 Extension: Member Badges & Activation Request
        $memberBadgeModel  = model(\App\Models\MemberBadgeModel::class);
        $myBadges          = $memberBadgeModel->getActiveBadgesForUser($userId);

        $activationModel   = model(\App\Models\MembershipActivationRequestModel::class);
        $activationRequest = $activationModel->getOrCreateForUser($userId);

        // Identity & Business Verification
        $verificationModel = model(\App\Models\MemberVerificationModel::class);
        $verification      = $verificationModel->getByUserId($userId);

        // Phase 6.3: Custom Member Roles
        $assignmentModel = model(\App\Models\MemberRoleAssignmentModel::class);
        if ($membership && ($membership->status ?? '') === 'active') {
            $assignmentModel->ensureDefaultRole($userId);
        }
        $myRoles = komeo_get_user_roles($userId, false);

        // Phase 6.3: Live Transactions (published records only for active members)
        $trxModel        = model(\App\Models\TransactionModel::class);
        $globalTrxPolicy = site_setting('Transaction.monetary_visibility_policy', 'hide_all');
        $trxStats        = $trxModel->getStatistics(true);
        $rawRecentTrx    = $trxModel->getPublishedTransactions([], 5, 0);
        $recentTrx       = array_map(fn($t) => $trxModel->maskForMember($t, $globalTrxPolicy), $rawRecentTrx);

        return view('dashboard/index', [
            'title'             => 'Dashboard Member - KOMEO.ID',
            'user'              => $user,
            'profile'           => $profile,
            'membership'        => $membership,
            'category'          => $category,
            'specializations'   => $specializations,
            'portfolios'        => $portfolios,
            'completion'        => $completion,
            'warnings'          => $warnings,
            'myBadges'          => $myBadges,
            'activationRequest' => $activationRequest,
            'verification'      => $verification,
            'myRoles'           => $myRoles,
            'trxStats'          => $trxStats,
            'recentTrx'         => $recentTrx,
            'globalTrxPolicy'   => $globalTrxPolicy,
        ]);
    }
}
