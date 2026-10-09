<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentAccessLogModel;
use App\Models\DocumentShareModel;
use App\Models\MemberDocumentModel;
use App\Models\MemberInquiryModel;
use App\Services\AuditLogService;
use App\Services\SettingsService;

class ConnectController extends BaseController
{
    protected MemberInquiryModel $inquiryModel;
    protected MemberDocumentModel $docModel;
    protected DocumentShareModel $shareModel;

    public function __construct()
    {
        $this->inquiryModel = new MemberInquiryModel();
        $this->docModel     = new MemberDocumentModel();
        $this->shareModel   = new DocumentShareModel();
    }

    /**
     * KOMEO Connect Overview Dashboard
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // Calculate storage usage
        $storageDir = WRITEPATH . 'uploads/member-documents/';
        $totalStorageBytes = 0;
        $totalFiles = 0;

        if (is_dir($storageDir)) {
            $files = glob($storageDir . '*');
            if ($files) {
                foreach ($files as $f) {
                    if (is_file($f)) {
                        $totalStorageBytes += filesize($f);
                        $totalFiles++;
                    }
                }
            }
        }

        $storageFormatted = round($totalStorageBytes / (1024 * 1024), 2) . ' MB';

        // Stats
        $stats = [
            'total_inquiries'  => $db->table('member_inquiries')->countAllResults(),
            'new_inquiries'    => $db->table('member_inquiries')->where('status', 'new')->countAllResults(),
            'replied'          => $db->table('member_inquiries')->where('status', 'replied')->countAllResults(),
            'closed'           => $db->table('member_inquiries')->where('status', 'closed')->countAllResults(),
            'spam_count'       => $db->table('member_inquiries')->where('status', 'spam')->countAllResults(),
            'total_documents'  => $this->docModel->countAllResults(),
            'total_shares'     => $this->shareModel->countAllResults(),
            'active_shares'    => $this->shareModel->where('is_revoked', 0)->where('expires_at >=', date('Y-m-d H:i:s'))->countAllResults(),
            'storage_used'     => $storageFormatted,
            'storage_files'    => $totalFiles,
        ];

        // Recent Inquiries
        $recentInquiries = $db->table('member_inquiries mi')
            ->select('mi.*, u.username as member_username, mp.full_name as member_name')
            ->join('users u', 'u.id = mi.target_user_id')
            ->join('member_profiles mp', 'mp.user_id = u.id', 'left')
            ->orderBy('mi.id', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        // Settings
        $settings = [
            'cv_max_mb'        => SettingsService::get('Connect.cv_max_mb', 5),
            'portfolio_max_mb' => SettingsService::get('Connect.portfolio_max_mb', 10),
            'portfolio_limit'  => SettingsService::get('Connect.portfolio_limit', 5),
            'enable_inquiries' => SettingsService::get('Connect.enable_inquiries', 1),
            'default_expiry'   => SettingsService::get('Connect.default_expiry_days', 7),
        ];

        return view('admin/connect/index', [
            'title'           => 'KOMEO Connect & Dokumen Profesional',
            'stats'           => $stats,
            'recentInquiries' => $recentInquiries,
            'settings'        => $settings,
        ]);
    }

    /**
     * Update Connect Settings
     */
    public function updateSettings()
    {
        $cvMax        = (int) $this->request->getPost('cv_max_mb') ?: 5;
        $portMax      = (int) $this->request->getPost('portfolio_max_mb') ?: 10;
        $portLimit    = (int) $this->request->getPost('portfolio_limit') ?: 5;
        $enableInq    = (int) $this->request->getPost('enable_inquiries');
        $defaultExp   = (int) $this->request->getPost('default_expiry_days') ?: 7;

        SettingsService::set('Connect.cv_max_mb', (string) $cvMax);
        SettingsService::set('Connect.portfolio_max_mb', (string) $portMax);
        SettingsService::set('Connect.portfolio_limit', (string) $portLimit);
        SettingsService::set('Connect.enable_inquiries', (string) $enableInq);
        SettingsService::set('Connect.default_expiry_days', (string) $defaultExp);

        AuditLogService::log(auth()->id(), 'connect.update_settings', 'settings', null, [
            'cv_max_mb' => $cvMax,
            'portfolio_max_mb' => $portMax,
        ]);

        return redirect()->to('admin/connect')->with('success', 'Pengaturan KOMEO Connect berhasil disimpan.');
    }

    /**
     * Mark inquiry spam or close by admin
     */
    public function updateInquiryStatus(int $id)
    {
        $status = $this->request->getPost('status');
        if (! in_array($status, ['new', 'in_progress', 'replied', 'closed', 'spam'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->inquiryModel->update($id, ['status' => $status]);
        AuditLogService::log(auth()->id(), 'connect.admin_update_inquiry', 'member_inquiries', $id, ['status' => $status]);

        return redirect()->to('admin/connect')->with('success', 'Status permintaan berhasil diperbarui.');
    }
}
