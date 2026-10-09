<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BadgeDefinitionModel;
use App\Models\EventCategoryModel;
use App\Models\MemberBadgeModel;
use App\Models\MemberPortfolioModel;
use App\Models\MemberProfileModel;
use App\Models\MemberSpecializationModel;
use App\Models\MembershipActivationRequestModel;
use App\Models\MembershipModel;
use App\Models\MembershipStatusHistoryModel;
use App\Models\MemberWarningModel;
use App\Services\MembershipService;
use App\Services\MemberPasswordService;
use App\Services\MemberWarningService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class MemberController extends BaseController
{
    /**
     * Member listing with pagination, search, and filters
     */
    public function index(): string|RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $categoryModel   = model(EventCategoryModel::class);

        $filters = [
            'q'           => trim((string) $this->request->getGet('q')),
            'status'      => trim((string) $this->request->getGet('status')),
            'category_id' => $this->request->getGet('category_id'),
            'location'    => trim((string) $this->request->getGet('location')),
            'sort'        => trim((string) $this->request->getGet('sort') ?: 'latest'),
        ];

        $perPage = 15;
        $query = $membershipModel->filterMembers($filters);
        $members = $query->paginate($perPage, 'default');
        $pager   = $membershipModel->pager;

        $stats      = $membershipModel->getStats();
        $categories = $categoryModel->getActiveCategories();

        return view('admin/members/index', [
            'title'      => 'Manajemen Member & Persetujuan - KOMEO.ID',
            'members'    => $members,
            'pager'      => $pager,
            'stats'      => $stats,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * View member detail and review completeness
     */
    public function show(int $id): string|RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $member = $membershipModel->getDetailedMember($id);

        if (! $member) {
            throw PageNotFoundException::forPageNotFound("Data member dengan ID #{$id} tidak ditemukan.");
        }

        $profileModel     = model(MemberProfileModel::class);
        $memberSpecModel  = model(MemberSpecializationModel::class);
        $portfolioModel   = model(MemberPortfolioModel::class);
        $historyModel     = model(MembershipStatusHistoryModel::class);

        $profile = null;
        if (! empty($member['profile_id'])) {
            $profile = $profileModel->find($member['profile_id']);
        }

        $specializations = [];
        $portfolios      = [];
        $completion      = null;

        if ($profile) {
            $specializations = $memberSpecModel->getSpecializationsByProfileId($profile->id);
            $portfolios      = $portfolioModel->getByProfileId($profile->id);
            $completion      = $profile->calculateCompletion(count($specializations));
        }

        $history = $historyModel->getByMembershipId($id);

        $warningModel = model(MemberWarningModel::class);
        $warnings     = $warningModel->getByMembershipId($id);

        // Phase 5 Extension: Badges and Activation Request
        $badgeModel = model(BadgeDefinitionModel::class);
        $badgeModel->seedDefaultBadgesIfEmpty();
        $availableBadges = $badgeModel->getActiveBadges();

        $memberBadgeModel = model(MemberBadgeModel::class);
        $memberBadges     = $memberBadgeModel->getAllBadgesForUser((int) $member['user_id']);

        $activationModel   = model(MembershipActivationRequestModel::class);
        $activationRequest = $activationModel->getOrCreateForUser((int) $member['user_id']);

        // Identity & Business Verification
        $verificationModel = model(\App\Models\MemberVerificationModel::class);
        $verification      = $verificationModel->getByUserId((int) $member['user_id']);

        return view('admin/members/show', [
            'title'             => 'Detail Member: ' . ($member['full_name'] ?: $member['account_username']) . ' - KOMEO.ID',
            'member'            => $member,
            'profile'           => $profile,
            'specializations'   => $specializations,
            'portfolios'        => $portfolios,
            'completion'        => $completion,
            'history'           => $history,
            'warnings'          => $warnings,
            'availableBadges'   => $availableBadges,
            'memberBadges'      => $memberBadges,
            'activationRequest' => $activationRequest,
            'verification'      => $verification,
        ]);
    }

    /**
     * Approve pending member application
     */
    public function approve(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($id);

        $reason = (string) $this->request->getPost('reason');
        $result = MembershipService::approve($id, (int) $admin->id, $reason);

        if ($result['success']) {
            if ($membership) {
                $actModel = model(MembershipActivationRequestModel::class);
                $actModel->recordApproval((int) $membership->user_id, (int) $admin->id, $reason);
            }
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Reject membership application with reason
     */
    public function reject(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $reason = trim((string) $this->request->getPost('reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi untuk memberi kejelasan kepada pemohon.');
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($id);

        $result = MembershipService::reject($id, (int) $admin->id, $reason);

        if ($result['success']) {
            if ($membership) {
                $actModel = model(MembershipActivationRequestModel::class);
                $actModel->recordRejection((int) $membership->user_id, (int) $admin->id, $reason);
            }
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Suspend active membership with reason
     */
    public function suspend(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $reason = trim((string) $this->request->getPost('reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penangguhan akun wajib diisi.');
        }

        $result = MembershipService::suspend($id, (int) $admin->id, $reason);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Reactivate suspended or rejected member
     */
    public function reactivate(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $reason = (string) $this->request->getPost('reason');
        $result = MembershipService::reactivate($id, (int) $admin->id, $reason);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Reset member password by admin and send via Email and/or WhatsApp
     */
    public function resetPassword(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $member = $membershipModel->getDetailedMember($id);
        if (! $member) {
            return redirect()->back()->with('error', 'Data member tidak ditemukan.');
        }

        $mode           = (string) $this->request->getPost('mode'); // auto or manual
        $customPassword = trim((string) $this->request->getPost('custom_password'));
        $notifyEmail    = (bool) $this->request->getPost('notify_email');
        $notifyWhatsapp = (bool) $this->request->getPost('notify_whatsapp');

        $result = MemberPasswordService::resetPasswordByAdmin(
            (int) $member['user_id'],
            $mode === 'manual' ? $customPassword : null
        );

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $newPassword = $result['password'];
        $memberName  = $member['full_name'] ?: ($member['display_name'] ?: $member['account_username']);
        $emailStatus = null;
        $waUrl       = null;

        // Send Email if requested and user has email
        if ($notifyEmail && ! empty($member['email'])) {
            $emailRes = MemberPasswordService::sendResetEmail($member['email'], $memberName, $newPassword);
            $emailStatus = $emailRes['message'];
        }

        // Generate WhatsApp URL if requested
        if ($notifyWhatsapp) {
            $waUrl = MemberPasswordService::buildWhatsAppUrl(
                $member['whatsapp'] ?? null,
                $memberName,
                $member['email'] ?: $member['account_username'],
                $newPassword
            );
        }

        session()->setFlashdata('reset_result', [
            'member_name'  => $memberName,
            'email'        => $member['email'],
            'phone'        => $member['whatsapp'] ?? null,
            'password'     => $newPassword,
            'wa_url'       => $waUrl,
            'email_status' => $emailStatus,
        ]);

        return redirect()->back()->with('message', "Password member '{$memberName}' berhasil direset.");
    }

    /**
     * Issue an official warning (SP1, SP2, SP3) to a member
     */
    public function issueWarning(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $warningLevel = (string) $this->request->getPost('warning_level');
        $reason       = trim((string) $this->request->getPost('reason'));
        $notes        = trim((string) $this->request->getPost('notes'));

        $result = MemberWarningService::issueWarning(
            $id,
            $warningLevel,
            $reason,
            $notes ?: null,
            (int) $admin->id
        );

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Resolve an active warning
     */
    public function resolveWarning(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $resolutionNotes = trim((string) $this->request->getPost('resolution_notes'));
        $reactivate      = (bool) $this->request->getPost('reactivate');

        $result = MemberWarningService::resolveWarning(
            $id,
            (int) $admin->id,
            $resolutionNotes ?: null,
            $reactivate
        );

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Revoke / cancel a warning
     */
    public function revokeWarning(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $notes      = trim((string) $this->request->getPost('notes'));
        $reactivate = (bool) $this->request->getPost('reactivate');

        $result = MemberWarningService::revokeWarning(
            $id,
            (int) $admin->id,
            $notes ?: null,
            $reactivate
        );

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Delete warning permanently (restores to clean/SP0)
     */
    public function deleteWarning(int $id): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $reactivate = (bool) $this->request->getPost('reactivate');
        $result = MemberWarningService::deleteWarning($id, (int) $admin->id, $reactivate);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Phase 5 Extension: Assign custom badge to member
     */
    public function assignBadge(int $userId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $badgeId      = (int) $this->request->getPost('badge_id');
        $expiresAt    = $this->request->getPost('expires_at') ? (string) $this->request->getPost('expires_at') : null;
        $internalNote = $this->request->getPost('internal_note') ? (string) $this->request->getPost('internal_note') : null;

        if ($badgeId <= 0) {
            return redirect()->back()->with('error', 'Silakan pilih badge yang valid.');
        }

        $memberBadgeModel = model(MemberBadgeModel::class);
        $result = $memberBadgeModel->assignBadge($badgeId, $userId, (int) $admin->id, $expiresAt, $internalNote);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Phase 5 Extension: Revoke custom badge from member
     */
    public function revokeBadge(int $memberBadgeId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $memberBadgeModel = model(MemberBadgeModel::class);
        $result = $memberBadgeModel->revokeBadge($memberBadgeId, (int) $admin->id);

        if ($result['success']) {
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Phase 5 Extension: Record contact confirmation from applicant
     */
    public function confirmContact(int $userId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $note = trim((string) $this->request->getPost('note'));
        $activationModel = model(MembershipActivationRequestModel::class);
        $success = $activationModel->confirmContact($userId, (int) $admin->id, $note ?: null);

        if ($success) {
            return redirect()->back()->with('message', 'Konfirmasi kontak pemohon berhasil dicatat. Status kini menunggu tinjauan admin.');
        }

        return redirect()->back()->with('error', 'Gagal mencatat konfirmasi kontak.');
    }

    /**
     * Phase 5 Extension: Approve & activate member application
     */
    public function approveActivation(int $membershipId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($membershipId);
        if (! $membership) {
            return redirect()->back()->with('error', 'Data keanggotaan tidak ditemukan.');
        }

        $reason = (string) $this->request->getPost('reason');
        $overrideContact = (bool) $this->request->getPost('override_contact');

        $activationModel = model(MembershipActivationRequestModel::class);
        $actReq = $activationModel->getOrCreateForUser((int) $membership->user_id);

        // Check contact confirmation unless explicitly overridden
        if (empty($actReq['contact_confirmed_at']) && ! $overrideContact) {
            return redirect()->back()->with('error', 'Pemohon belum mengonfirmasi kontak dengan admin. Centang opsi lewati/override jika ingin menyetujui langsung.');
        }

        $result = MembershipService::approve($membershipId, (int) $admin->id, $reason);

        if ($result['success']) {
            $activationModel->recordApproval((int) $membership->user_id, (int) $admin->id, $reason);
            return redirect()->back()->with('message', 'Keanggotaan berhasil disetujui & diaktifkan! Nomor anggota diterbitkan.');
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Phase 5 Extension: Reject member application with reason
     */
    public function rejectActivation(int $membershipId): RedirectResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->inGroup('admin', 'superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($membershipId);
        if (! $membership) {
            return redirect()->back()->with('error', 'Data keanggotaan tidak ditemukan.');
        }

        $reason = trim((string) $this->request->getPost('reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penolakan permohonan aktivasi wajib diisi.');
        }

        $result = MembershipService::reject($membershipId, (int) $admin->id, $reason);

        if ($result['success']) {
            $activationModel = model(MembershipActivationRequestModel::class);
            $activationModel->recordRejection((int) $membership->user_id, (int) $admin->id, $reason);
            return redirect()->back()->with('message', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
