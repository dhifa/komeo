<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionCategoryModel;
use App\Models\TransactionModel;
use App\Models\TransactionPaymentModel;
use App\Models\TransactionUpdateModel;
use App\Models\TransactionIssueModel;
use App\Models\TransactionAuditLogModel;
use App\Services\SettingsService;

class TransactionController extends BaseController
{
    protected TransactionModel $trxModel;
    protected TransactionCategoryModel $categoryModel;
    protected TransactionPaymentModel $paymentModel;
    protected TransactionUpdateModel $updateModel;
    protected TransactionIssueModel $issueModel;
    protected TransactionAuditLogModel $auditModel;

    public function __construct()
    {
        $this->trxModel       = new TransactionModel();
        $this->categoryModel  = new TransactionCategoryModel();
        $this->paymentModel   = new TransactionPaymentModel();
        $this->updateModel    = new TransactionUpdateModel();
        $this->issueModel     = new TransactionIssueModel();
        $this->auditModel     = new TransactionAuditLogModel();
    }

    /**
     * Admin Transactions Dashboard & Listing
     */
    public function index()
    {
        $filters = [
            'search'         => trim((string) $this->request->getGet('search')),
            'category_id'    => $this->request->getGet('category_id'),
            'work_stage'     => $this->request->getGet('work_stage'),
            'payment_status' => $this->request->getGet('payment_status'),
            'issue_status'   => $this->request->getGet('issue_status'),
            'is_published'   => $this->request->getGet('is_published'),
        ];

        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = 15;
        $offset  = ($page - 1) * perPage;

        $totalRecords = $this->trxModel->countAdminTransactions($filters);
        $transactions = $this->trxModel->getAdminTransactions($filters, $perPage, $offset);
        $statistics   = $this->trxModel->getStatistics(false);
        $categories   = $this->categoryModel->getActiveCategories();

        $totalPages = (int) ceil($totalRecords / $perPage);

        return view('admin/transactions/index', [
            'title'        => 'Live Transaksi - Panel Admin KOMEO.ID',
            'transactions' => $transactions,
            'statistics'   => $statistics,
            'categories'   => $categories,
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'workStages'   => TransactionModel::WORK_STAGES,
            'payStatuses'  => TransactionModel::PAYMENT_STATUSES,
            'issueStatuses'=> TransactionModel::CONTRACT_ISSUE_STATUSES,
        ]);
    }

    /**
     * Create Transaction View
     */
    public function create()
    {
        $categories = $this->categoryModel->getActiveCategories();

        return view('admin/transactions/create', [
            'title'             => 'Tambah Transaksi Baru - KOMEO.ID',
            'categories'        => $categories,
            'workStages'        => TransactionModel::WORK_STAGES,
            'visibilityOptions' => TransactionModel::VISIBILITY_OPTIONS,
            'generatedCode'     => $this->trxModel->generateUniqueCode(),
        ]);
    }

    /**
     * Store New Transaction
     */
    public function store()
    {
        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'public_title' => 'required|min_length[3]|max_length[255]',
            'category_id'  => 'required|is_natural_no_zero',
            'total_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminId = (int) auth()->id();
        $code    = $this->trxModel->generateUniqueCode();

        $totalAmount = $this->request->getPost('total_amount');
        $totalAmount = ($totalAmount !== '' && $totalAmount !== null) ? (float) $totalAmount : null;

        $agreedDpAmount = $this->request->getPost('agreed_dp_amount');
        $agreedDpAmount = ($agreedDpAmount !== '' && $agreedDpAmount !== null) ? (float) $agreedDpAmount : null;

        $agreedDpPercentage = $this->request->getPost('agreed_dp_percentage');
        $agreedDpPercentage = ($agreedDpPercentage !== '' && $agreedDpPercentage !== null) ? (float) $agreedDpPercentage : null;

        $stage = $this->request->getPost('current_work_stage') ?: 'Transaksi Masuk';
        if (! isset(TransactionModel::WORK_STAGES[$stage])) {
            $stage = 'Transaksi Masuk';
        }

        $visibility = $this->request->getPost('amount_visibility') ?: 'hide';
        if (! isset(TransactionModel::VISIBILITY_OPTIONS[$visibility])) {
            $visibility = 'hide';
        }

        $trxData = [
            'transaction_code'      => $code,
            'title'                 => trim($this->request->getPost('title')),
            'public_title'          => trim($this->request->getPost('public_title')),
            'category_id'           => (int) $this->request->getPost('category_id'),
            'description'           => trim((string) $this->request->getPost('description')),
            'internal_client_name'  => trim((string) $this->request->getPost('internal_client_name')),
            'internal_project_ref'  => trim((string) $this->request->getPost('internal_project_ref')),
            'currency'              => 'IDR',
            'total_amount'          => $totalAmount,
            'agreed_dp_amount'      => $agreedDpAmount,
            'agreed_dp_percentage'  => $agreedDpPercentage,
            'dp_due_date'           => $this->request->getPost('dp_due_date') ?: null,
            'final_due_date'        => $this->request->getPost('final_due_date') ?: null,
            'current_work_stage'    => $stage,
            'payment_status'        => ($agreedDpAmount > 0 || $agreedDpPercentage > 0) ? 'AWAITING_DP' : 'UNPAID',
            'is_overdue'            => 0,
            'contract_issue_status' => 'none',
            'amount_visibility'     => $visibility,
            'is_published'          => $this->request->getPost('is_published') ? 1 : 0,
            'created_by'            => $adminId,
            'updated_by'            => $adminId,
            'transaction_date'      => $this->request->getPost('transaction_date') ?: date('Y-m-d'),
        ];

        $trxId = (int) $this->trxModel->insert($trxData);

        // Record initial timeline entry
        $this->updateModel->insert([
            'transaction_id'     => $trxId,
            'update_type'        => 'work_stage',
            'previous_status'    => null,
            'new_status'         => $stage,
            'public_description' => 'Transaksi resmi dicatat dalam sistem monitoring KOMEO.ID.',
            'internal_note'      => 'Inisiasi awal data transaksi oleh admin.',
            'visible_to_members' => 1,
            'created_by'         => $adminId,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        // Audit Log
        $this->auditModel->log($trxId, 'create_transaction', null, $trxData, $adminId);

        return redirect()->to(site_url('admin/transactions/' . $trxId . '/edit'))
            ->with('success', 'Transaksi ' . $code . ' berhasil dibuat.');
    }

    /**
     * Edit Transaction General Info
     */
    public function edit(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $categories = $this->categoryModel->getActiveCategories();
        $financials = $this->trxModel->getComputedFinancials($trx);

        return view('admin/transactions/edit', [
            'title'             => 'Edit Transaksi ' . $trx['transaction_code'],
            'trx'               => $trx,
            'financials'        => $financials,
            'categories'        => $categories,
            'workStages'        => TransactionModel::WORK_STAGES,
            'visibilityOptions' => TransactionModel::VISIBILITY_OPTIONS,
            'activeTab'         => 'general',
        ]);
    }

    /**
     * Update Transaction General Info
     */
    public function update(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $rules = [
            'title'        => 'required|min_length[3]|max_length[255]',
            'public_title' => 'required|min_length[3]|max_length[255]',
            'category_id'  => 'required|is_natural_no_zero',
            'total_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminId = (int) auth()->id();

        $totalAmount = $this->request->getPost('total_amount');
        $totalAmount = ($totalAmount !== '' && $totalAmount !== null) ? (float) $totalAmount : null;

        $agreedDpAmount = $this->request->getPost('agreed_dp_amount');
        $agreedDpAmount = ($agreedDpAmount !== '' && $agreedDpAmount !== null) ? (float) $agreedDpAmount : null;

        $agreedDpPercentage = $this->request->getPost('agreed_dp_percentage');
        $agreedDpPercentage = ($agreedDpPercentage !== '' && $agreedDpPercentage !== null) ? (float) $agreedDpPercentage : null;

        $visibility = $this->request->getPost('amount_visibility') ?: 'hide';
        if (! isset(TransactionModel::VISIBILITY_OPTIONS[$visibility])) {
            $visibility = 'hide';
        }

        $updateData = [
            'title'                => trim($this->request->getPost('title')),
            'public_title'         => trim($this->request->getPost('public_title')),
            'category_id'          => (int) $this->request->getPost('category_id'),
            'description'          => trim((string) $this->request->getPost('description')),
            'internal_client_name' => trim((string) $this->request->getPost('internal_client_name')),
            'internal_project_ref' => trim((string) $this->request->getPost('internal_project_ref')),
            'total_amount'         => $totalAmount,
            'agreed_dp_amount'     => $agreedDpAmount,
            'agreed_dp_percentage' => $agreedDpPercentage,
            'dp_due_date'          => $this->request->getPost('dp_due_date') ?: null,
            'final_due_date'       => $this->request->getPost('final_due_date') ?: null,
            'amount_visibility'    => $visibility,
            'is_published'         => $this->request->getPost('is_published') ? 1 : 0,
            'updated_by'           => $adminId,
            'transaction_date'     => $this->request->getPost('transaction_date') ?: $trx['transaction_date'],
        ];

        $this->trxModel->update($id, $updateData);
        $this->trxModel->recalculateFinancials($id);

        $this->auditModel->log($id, 'update_transaction_info', $trx, $updateData, $adminId);

        return redirect()->to(site_url('admin/transactions/' . $id . '/edit'))
            ->with('success', 'Data transaksi berhasil diperbarui.');
    }

    /**
     * Timeline & Work Progress Tab
     */
    public function timeline(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $timeline = $this->updateModel->getTimeline($id, false);
        $financials = $this->trxModel->getComputedFinancials($trx);

        return view('admin/transactions/timeline', [
            'title'       => 'Progres & Linimasa - ' . $trx['transaction_code'],
            'trx'         => $trx,
            'timeline'    => $timeline,
            'financials'  => $financials,
            'workStages'  => TransactionModel::WORK_STAGES,
            'activeTab'   => 'timeline',
        ]);
    }

    /**
     * Add Work Progress / Timeline Update
     */
    public function addUpdate(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $newStage = $this->request->getPost('new_stage') ?: $trx['current_work_stage'];
        $pubDesc  = trim((string) $this->request->getPost('public_description'));
        $intNote  = trim((string) $this->request->getPost('internal_note'));
        $visible  = $this->request->getPost('visible_to_members') ? 1 : 0;
        $adminId  = (int) auth()->id();

        $oldStage = $trx['current_work_stage'];

        // If stage changed, update transaction record
        if ($newStage !== $oldStage) {
            $this->trxModel->update($id, [
                'current_work_stage' => $newStage,
                'updated_by'         => $adminId,
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        $this->updateModel->insert([
            'transaction_id'     => $id,
            'update_type'        => 'work_stage',
            'previous_status'    => $oldStage,
            'new_status'         => $newStage,
            'public_description' => $pubDesc ?: ('Tahap pekerjaan diperbarui menjadi: ' . $newStage),
            'internal_note'      => $intNote ?: null,
            'visible_to_members' => $visible,
            'created_by'         => $adminId,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->log($id, 'update_work_stage', ['previous_stage' => $oldStage], ['new_stage' => $newStage, 'public_desc' => $pubDesc], $adminId);

        return redirect()->to(site_url('admin/transactions/' . $id . '/timeline'))
            ->with('success', 'Pembaruan progres berhasil disimpan.');
    }

    /**
     * Payments Tab
     */
    public function payments(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $payments   = $this->paymentModel->getByTransactionId($id, false);
        $financials = $this->trxModel->getComputedFinancials($trx);

        return view('admin/transactions/payments', [
            'title'       => 'Pencatatan Pembayaran - ' . $trx['transaction_code'],
            'trx'         => $trx,
            'payments'    => $payments,
            'financials'  => $financials,
            'activeTab'   => 'payments',
        ]);
    }

    /**
     * Record Incoming / Adjustment Payment
     */
    public function recordPayment(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $rules = [
            'payment_type' => 'required|in_list[DP,Cicilan / Termin,Pelunasan,Refund,Penyesuaian,Koreksi]',
            'amount'       => 'required|numeric|greater_than[0]',
            'payment_date' => 'required|valid_date',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminId = (int) auth()->id();
        $type    = $this->request->getPost('payment_type');
        $amount  = (float) $this->request->getPost('amount');
        $date    = $this->request->getPost('payment_date');
        $method  = trim((string) $this->request->getPost('payment_method'));
        $ref     = trim((string) $this->request->getPost('reference'));
        $note    = trim((string) $this->request->getPost('internal_note'));

        $this->paymentModel->insert([
            'transaction_id' => $id,
            'payment_type'   => $type,
            'amount'         => $amount,
            'payment_date'   => $date,
            'payment_method' => $method ?: null,
            'reference'      => $ref ?: null,
            'internal_note'  => $note ?: null,
            'recorded_by'    => $adminId,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Recalculate transaction financials
        $fin = $this->trxModel->recalculateFinancials($id);

        // Record public timeline entry without leaking private bank accounts
        $this->updateModel->insert([
            'transaction_id'     => $id,
            'update_type'        => 'payment',
            'previous_status'    => null,
            'new_status'         => $fin['payment_status'] ?? $trx['payment_status'],
            'public_description' => "Pencatatan pembayaran ({$type}) berhasil diverifikasi dan dibukukan.",
            'internal_note'      => "Nominal: " . komeo_format_rupiah($amount) . " via " . ($method ?: 'Transfer') . " (" . ($ref ?: 'Tanpa Ref') . ")",
            'visible_to_members' => 1,
            'created_by'         => $adminId,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->log($id, 'record_payment', null, ['type' => $type, 'amount' => $amount, 'date' => $date], $adminId);

        return redirect()->to(site_url('admin/transactions/' . $id . '/payments'))
            ->with('success', 'Pembayaran ' . $type . ' berhasil dicatat.');
    }

    /**
     * Auditable Void / Cancel of a Payment Record
     */
    public function voidPayment(int $id)
    {
        $paymentId = (int) $this->request->getPost('payment_id');
        $reason    = trim((string) $this->request->getPost('void_reason'));

        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan pembatalan pembayaran wajib diisi.');
        }

        $payment = $this->paymentModel->find($paymentId);
        if (! $payment || (int) $payment['transaction_id'] !== $id) {
            return redirect()->back()->with('error', 'Catatan pembayaran tidak valid.');
        }

        $adminId = (int) auth()->id();

        $this->paymentModel->update($paymentId, [
            'voided_at'   => date('Y-m-d H:i:s'),
            'voided_by'   => $adminId,
            'void_reason' => $reason,
        ]);

        // Recalculate
        $fin = $this->trxModel->recalculateFinancials($id);

        $this->updateModel->insert([
            'transaction_id'     => $id,
            'update_type'        => 'payment_void',
            'previous_status'    => null,
            'new_status'         => $fin['payment_status'] ?? 'UPDATE',
            'public_description' => "Penyesuaian administrasi pembukuan pembayaran telah diproses.",
            'internal_note'      => "Pembayaran ID #{$paymentId} dibatalkan oleh admin. Alasan: {$reason}",
            'visible_to_members' => 1,
            'created_by'         => $adminId,
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->log($id, 'void_payment', $payment, ['void_reason' => $reason], $adminId);

        return redirect()->to(site_url('admin/transactions/' . $id . '/payments'))
            ->with('success', 'Catatan pembayaran berhasil dibatalkan secara tertib audit.');
    }

    /**
     * Contract Issues & Wanprestasi Tab
     */
    public function issues(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $issues     = $this->issueModel->getByTransactionId($id);
        $financials = $this->trxModel->getComputedFinancials($trx);

        return view('admin/transactions/issues', [
            'title'         => 'Permasalahan Kontrak - ' . $trx['transaction_code'],
            'trx'           => $trx,
            'issues'        => $issues,
            'financials'    => $financials,
            'issueStatuses' => TransactionModel::CONTRACT_ISSUE_STATUSES,
            'activeTab'     => 'issues',
        ]);
    }

    /**
     * Save / Update Contract Issue
     */
    public function saveIssue(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $rules = [
            'issue_type'  => 'required|min_length[3]|max_length[100]',
            'status'      => 'required|in_list[none,under_review,suspected_breach,disputed,handling,resolved]',
            'description' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminId     = (int) auth()->id();
        $status      = $this->request->getPost('status');
        $type        = trim($this->request->getPost('issue_type'));
        $description = trim($this->request->getPost('description'));
        $resolution  = trim((string) $this->request->getPost('resolution_note'));

        // Neutral default public label
        $publicLabel = ($status === 'none' || $status === 'resolved') 
            ? 'Tidak Ada Permasalahan' 
            : 'Dalam Penanganan';

        $this->issueModel->insert([
            'transaction_id'  => $id,
            'issue_type'      => $type,
            'status'          => $status,
            'description'     => $description,
            'public_label'    => $publicLabel,
            'resolution_note' => $resolution ?: null,
            'reported_at'     => date('Y-m-d H:i:s'),
            'resolved_at'     => ($status === 'resolved') ? date('Y-m-d H:i:s') : null,
            'recorded_by'     => $adminId,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // Update transaction master issue status
        $this->trxModel->update($id, [
            'contract_issue_status' => $status,
            'updated_by'            => $adminId,
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->log($id, 'save_contract_issue', ['old_status' => $trx['contract_issue_status']], ['new_status' => $status, 'type' => $type], $adminId);

        return redirect()->to(site_url('admin/transactions/' . $id . '/issues'))
            ->with('success', 'Catatan penanganan kontrak berhasil disimpan.');
    }

    /**
     * Toggle Publication Status
     */
    public function togglePublish(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $adminId = (int) auth()->id();
        $newPub = $trx['is_published'] ? 0 : 1;

        $this->trxModel->update($id, [
            'is_published' => $newPub,
            'updated_by'   => $adminId,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->log($id, 'toggle_publish', ['is_published' => $trx['is_published']], ['is_published' => $newPub], $adminId);

        $msg = $newPub ? 'Transaksi dipublikasikan ke portal member.' : 'Publikasi transaksi dicabut (arsip internal).';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Preview Transaction Exactly As An Active Member Sees It
     */
    public function previewMember(int $id)
    {
        $trx = $this->trxModel->find($id);
        if (! $trx) {
            return redirect()->to(site_url('admin/transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $globalPolicy = site_setting('Transaction.monetary_visibility_policy', 'hide_all');
        $maskedTrx    = $this->trxModel->maskForMember($trx, $globalPolicy);
        $timeline     = $this->updateModel->getTimeline($id, true);

        return view('admin/transactions/preview_member', [
            'title'        => 'Pratinjau Tampilan Member - ' . $trx['transaction_code'],
            'trx'          => $trx,
            'maskedTrx'    => $maskedTrx,
            'timeline'     => $timeline,
            'globalPolicy' => $globalPolicy,
        ]);
    }

    /**
     * Global Settings for Live Transactions
     */
    public function settings()
    {
        $policy   = site_setting('Transaction.monetary_visibility_policy', 'hide_all');
        $interval = site_setting('Transaction.live_refresh_interval', '60');
        $ticker   = site_setting('Transaction.enable_member_live_ticker', '1');

        return view('admin/transactions/settings', [
            'title'    => 'Pengaturan Live Transaksi - KOMEO.ID',
            'policy'   => $policy,
            'interval' => $interval,
            'ticker'   => $ticker,
        ]);
    }

    /**
     * Update Global Settings
     */
    public function updateSettings()
    {
        $policy   = $this->request->getPost('monetary_visibility_policy');
        $interval = (int) $this->request->getPost('live_refresh_interval');
        $ticker   = $this->request->getPost('enable_member_live_ticker') ? '1' : '0';

        if (! in_array($policy, ['hide_all', 'per_transaction'], true)) {
            $policy = 'hide_all';
        }

        if ($interval < 10) {
            $interval = 60;
        }

        SettingsService::set('Transaction.monetary_visibility_policy', $policy);
        SettingsService::set('Transaction.live_refresh_interval', (string) $interval);
        SettingsService::set('Transaction.enable_member_live_ticker', $ticker);

        $this->auditModel->log(null, 'update_transaction_settings', null, [
            'policy'   => $policy,
            'interval' => $interval,
            'ticker'   => $ticker,
        ], (int) auth()->id());

        return redirect()->to(site_url('admin/transactions/settings'))
            ->with('success', 'Pengaturan Live Transaksi berhasil disimpan.');
    }

    /**
     * Admin Reports View
     */
    public function reports()
    {
        $filters = [
            'search'         => trim((string) $this->request->getGet('search')),
            'category_id'    => $this->request->getGet('category_id'),
            'work_stage'     => $this->request->getGet('work_stage'),
            'payment_status' => $this->request->getGet('payment_status'),
            'issue_status'   => $this->request->getGet('issue_status'),
            'is_published'   => $this->request->getGet('is_published'),
        ];

        $transactions = $this->trxModel->getAdminTransactions($filters, 0, 0);
        $categories   = $this->categoryModel->getActiveCategories();
        $statistics   = $this->trxModel->getStatistics(false);

        // Aggregate monetary totals for admin reports
        $totalAgreed = 0.0;
        $totalNet    = 0.0;
        foreach ($transactions as $t) {
            if ($t['total_amount'] !== null) {
                $totalAgreed += (float) $t['total_amount'];
            }
            $net = $this->paymentModel->getNetReceived((int) $t['id']);
            $totalNet += $net;
        }

        return view('admin/transactions/reports', [
            'title'        => 'Laporan Live Transaksi - Panel Admin KOMEO.ID',
            'transactions' => $transactions,
            'categories'   => $categories,
            'statistics'   => $statistics,
            'filters'      => $filters,
            'totalAgreed'  => $totalAgreed,
            'totalNet'     => $totalNet,
            'workStages'   => TransactionModel::WORK_STAGES,
            'payStatuses'  => TransactionModel::PAYMENT_STATUSES,
            'issueStatuses'=> TransactionModel::CONTRACT_ISSUE_STATUSES,
        ]);
    }

    /**
     * Export Transactions to CSV with Spreadsheet Formula Injection Defense
     */
    public function exportCsv()
    {
        $filters = [
            'search'         => trim((string) $this->request->getGet('search')),
            'category_id'    => $this->request->getGet('category_id'),
            'work_stage'     => $this->request->getGet('work_stage'),
            'payment_status' => $this->request->getGet('payment_status'),
            'issue_status'   => $this->request->getGet('issue_status'),
            'is_published'   => $this->request->getGet('is_published'),
        ];

        $transactions = $this->trxModel->getAdminTransactions($filters, 0, 0);

        $filename = 'laporan_transaksi_komeo_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel compatibility
        fputs($out, "\xEF\xBB\xBF");

        // Headers
        fputcsv($out, [
            'Kode Transaksi',
            'Judul Internal',
            'Judul Publik',
            'Kategori',
            'Klien Internal',
            'Tanggal',
            'Tahap Pekerjaan',
            'Status Pembayaran',
            'Terlambat Bayar',
            'Total Nilai (IDR)',
            'Net Diterima (IDR)',
            'Sisa Saldo (IDR)',
            'Status Masalah Kontrak',
            'Publikasi',
            'Terakhir Update',
        ]);

        foreach ($transactions as $t) {
            $id = (int) $t['id'];
            $fin = $this->trxModel->getComputedFinancials($t);

            // Formula injection defense: escape leading '=', '+', '-', '@'
            $sanitize = static function ($val) {
                $str = (string) $val;
                if ($str !== '' && in_array($str[0], ['=', '+', '-', '@'], true)) {
                    return "'" . $str;
                }
                return $str;
            };

            fputcsv($out, [
                $sanitize($t['transaction_code']),
                $sanitize($t['title']),
                $sanitize($t['public_title']),
                $sanitize($t['category_name'] ?? '-'),
                $sanitize($t['internal_client_name'] ?? '-'),
                $t['transaction_date'] ?? '-',
                $t['current_work_stage'] ?? '-',
                $t['payment_status'] ?? '-',
                ! empty($t['is_overdue']) ? 'YA' : 'TIDAK',
                $t['total_amount'] !== null ? number_format((float) $t['total_amount'], 2, '.', '') : '0.00',
                number_format((float) $fin['net_received'], 2, '.', ''),
                $fin['outstanding_balance'] !== null ? number_format((float) $fin['outstanding_balance'], 2, '.', '') : '0.00',
                $t['contract_issue_status'] ?? 'none',
                ! empty($t['is_published']) ? 'Dipublikasikan' : 'Draft / Privat',
                $t['updated_at'] ?? '-',
            ]);
        }

        fclose($out);
        exit;
    }
}
