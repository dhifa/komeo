<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventCategoryModel;
use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use App\Models\MembershipVerificationLogModel;
use App\Services\AuditLogService;

class KtaScannerController extends BaseController
{
    protected MembershipModel $membershipModel;
    protected MemberProfileModel $profileModel;
    protected EventCategoryModel $categoryModel;
    protected MembershipVerificationLogModel $verificationLogModel;

    public function __construct()
    {
        $this->membershipModel        = new MembershipModel();
        $this->profileModel           = new MemberProfileModel();
        $this->categoryModel          = new EventCategoryModel();
        $this->verificationLogModel   = new MembershipVerificationLogModel();
    }

    /**
     * Display Admin KTA Scanner UI
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Recent verification history
        $recentLogs = $db->table('membership_verification_logs mvl')
            ->select('mvl.*, m.member_number as membership_number, m.status as member_status, mp.full_name, mp.display_name, u.username as verified_by_username')
            ->join('memberships m', 'm.id = mvl.membership_id')
            ->join('users u_mem', 'u_mem.id = m.user_id', 'left')
            ->join('member_profiles mp', 'mp.user_id = u_mem.id', 'left')
            ->join('users u', 'u.id = mvl.verified_by', 'left')
            ->where('mvl.verification_type', 'admin_scan')
            ->orderBy('mvl.id', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return view('admin/kta/scan', [
            'title'      => 'Pemindai QR KTA Anggota',
            'recentLogs' => $recentLogs,
        ]);
    }

    /**
     * Process KTA Verification (from Camera scan or Manual input)
     */
    public function verify()
    {
        $rawInput = trim((string) $this->request->getPost('identifier'));
        if (empty($rawInput)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kode atau token tidak boleh kosong.']);
        }

        $db = \Config\Database::connect();
        $token = $rawInput;

        // Extract token if full URL scanned (e.g. https://komeo.id/verifikasi/{token})
        if (str_contains($rawInput, '/verifikasi/')) {
            $parts = explode('/verifikasi/', $rawInput);
            $token = trim(end($parts), " /#?&\t\n\r\0\x0B");
        }

        $membership = null;

        // 1. Try lookup by verification token selector (format: {selector}-{validator})
        if (str_contains($token, '-')) {
            $parts = explode('-', $token, 2);
            $selector = $parts[0];
            $membership = $this->membershipModel->where('verification_token_selector', $selector)->first();
        }

        // 2. Try lookup by direct selector match
        if (! $membership) {
            $membership = $this->membershipModel->where('verification_token_selector', $token)->first();
        }

        // 3. Try lookup by legacy membership_verifications table if present
        if (! $membership && $db->tableExists('membership_verifications')) {
            $vRecord = $db->table('membership_verifications')->where('token', $token)->get()->getRowArray();
            if ($vRecord) {
                $membership = $this->membershipModel->find($vRecord['membership_id']);
            }
        }

        // 4. Try lookup by Member Number directly
        if (! $membership) {
            $membership = $this->membershipModel->where('member_number', $rawInput)->first();
        }

        if (! $membership) {
            return $this->response->setJSON([
                'status'  => 'invalid',
                'message' => 'Data KTA tidak ditemukan dalam database resmi KOMEO.',
            ]);
        }

        $membershipId = is_object($membership) ? (int) $membership->id : (int) $membership['id'];
        $memberStatus = is_object($membership) ? (string) $membership->status : (string) ($membership['status'] ?? 'pending');
        $memberNumber = is_object($membership) ? (string) ($membership->member_number ?? $membership->membership_number ?? '-') : (string) ($membership['member_number'] ?? $membership['membership_number'] ?? '-');
        $userId       = is_object($membership) ? (int) $membership->user_id : (int) $membership['user_id'];

        $profile  = $this->profileModel->findByUserId($userId);
        $category = ($profile && ! empty($profile->category_id)) ? $this->categoryModel->find($profile->category_id) : null;

        // Verification status determination
        $verificationStatus = 'valid';
        if ($memberStatus === 'suspended') {
            $verificationStatus = 'suspended';
        } elseif ($memberStatus !== 'approved' && $memberStatus !== 'active') {
            $verificationStatus = 'invalid';
        }

        // Log admin scan verification audit
        $this->verificationLogModel->insert([
            'membership_id'     => $membershipId,
            'verification_type' => 'admin_scan',
            'verified_by'       => auth()->id(),
            'ip_address'        => $this->request->getIPAddress(),
            'user_agent'        => substr((string) $this->request->getUserAgent(), 0, 255),
            'status'            => $verificationStatus,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        $avatarUrl = $profile && method_exists($profile, 'getAvatarUrl')
            ? $profile->getAvatarUrl()
            : 'https://ui-avatars.com/api/?name=' . urlencode($profile->full_name ?? 'Member') . '&background=4f46e5&color=ffffff&bold=true';

        return $this->response->setJSON([
            'status' => $verificationStatus,
            'member' => [
                'name'              => $profile->full_name ?? $profile->display_name ?? 'Member KOMEO',
                'membership_number' => $memberNumber,
                'status'            => $memberStatus,
                'category'          => $category['name'] ?? 'Event Industry Professional',
                'avatar_url'        => $avatarUrl,
                'company'           => $profile->company_name ?? '-',
                'job_title'         => $profile->job_title ?? '-',
                'city'              => $profile->city ?? '-',
            ],
            'scanned_at' => date('d/m/Y H:i:s'),
        ]);
    }
}
