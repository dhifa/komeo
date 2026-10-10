<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MembershipModel;
use App\Models\TransactionCategoryModel;
use App\Models\TransactionModel;
use App\Models\TransactionUpdateModel;

class TransactionController extends BaseController
{
    protected TransactionModel $trxModel;
    protected TransactionCategoryModel $categoryModel;
    protected TransactionUpdateModel $updateModel;
    protected MembershipModel $membershipModel;

    public function __construct()
    {
        $this->trxModel        = new TransactionModel();
        $this->categoryModel   = new TransactionCategoryModel();
        $this->updateModel     = new TransactionUpdateModel();
        $this->membershipModel = new MembershipModel();
    }

    /**
     * Ensure current user is an authenticated active member
     */
    protected function ensureActiveMember(): bool
    {
        if (! auth()->loggedIn()) {
            return false;
        }

        $userId = (int) auth()->id();
        $membership = $this->membershipModel->findByUserId($userId);

        if (! $membership) {
            return false;
        }

        $status = is_object($membership) ? $membership->status : ($membership['status'] ?? '');
        return ($status === 'active');
    }

    /**
     * Member Transactions Directory
     */
    public function index()
    {
        if (! $this->ensureActiveMember()) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Akses Live Transaksi terbatas hanya untuk anggota aktif KOMEO.ID.');
        }

        $filters = [
            'search'         => trim((string) $this->request->getGet('search')),
            'category_id'    => $this->request->getGet('category_id'),
            'work_stage'     => $this->request->getGet('work_stage'),
            'payment_status' => $this->request->getGet('payment_status'),
        ];

        $page    = max(1, (int) $this->request->getGet('page'));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;

        $globalPolicy = site_setting('Transaction.monetary_visibility_policy', 'hide_all');

        $totalRecords = $this->trxModel->countPublishedTransactions($filters);
        $rawTrxList   = $this->trxModel->getPublishedTransactions($filters, $perPage, $offset);

        // Mask every transaction according to privacy rules
        $transactions = [];
        foreach ($rawTrxList as $raw) {
            $transactions[] = $this->trxModel->maskForMember($raw, $globalPolicy);
        }

        $statistics = $this->trxModel->getStatistics(true);
        $categories = $this->categoryModel->getActiveCategories();
        $totalPages = (int) ceil($totalRecords / $perPage);

        return view('member/transactions/index', [
            'title'        => 'Live Transaksi Komunitas - KOMEO.ID',
            'transactions' => $transactions,
            'statistics'   => $statistics,
            'categories'   => $categories,
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'globalPolicy' => $globalPolicy,
            'pollInterval' => (int) site_setting('Transaction.live_refresh_interval', '60'),
            'workStages'   => TransactionModel::WORK_STAGES,
            'payStatuses'  => TransactionModel::PAYMENT_STATUSES,
        ]);
    }

    /**
     * Member Transaction Detail
     */
    public function show(int $id)
    {
        if (! $this->ensureActiveMember()) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Akses Live Transaksi terbatas hanya untuk anggota aktif KOMEO.ID.');
        }

        $trx = $this->trxModel->where('id', $id)
            ->where('is_published', 1)
            ->where('deleted_at IS NULL')
            ->first();

        if (! $trx) {
            return redirect()->to(site_url('dashboard/transaksi'))
                ->with('error', 'Transaksi tidak ditemukan atau belum dipublikasikan.');
        }

        $category = null;
        if (! empty($trx['category_id'])) {
            $category = $this->categoryModel->find((int) $trx['category_id']);
        }
        $trx['category_name'] = $category['name'] ?? null;
        $trx['category_icon'] = $category['icon'] ?? 'briefcase';

        $globalPolicy = site_setting('Transaction.monetary_visibility_policy', 'hide_all');
        $maskedTrx    = $this->trxModel->maskForMember($trx, $globalPolicy);
        $timeline     = $this->updateModel->getTimeline($id, true);

        return view('member/transactions/show', [
            'title'        => 'Detail Transaksi ' . $maskedTrx['transaction_code'] . ' - KOMEO.ID',
            'trx'          => $maskedTrx,
            'timeline'     => $timeline,
            'globalPolicy' => $globalPolicy,
            'pollInterval' => (int) site_setting('Transaction.live_refresh_interval', '60'),
        ]);
    }

    /**
     * AJAX Polling Endpoint for Live Auto-Refresh
     */
    public function poll()
    {
        if (! $this->ensureActiveMember()) {
            return $this->response->setStatusCode(403)
                ->setHeader('Cache-Control', 'private, no-cache, no-store')
                ->setJSON(['status' => 'error', 'message' => 'Akses ditolak. Keanggotaan aktif diperlukan.']);
        }

        $globalPolicy = site_setting('Transaction.monetary_visibility_policy', 'hide_all');

        $singleId = (int) $this->request->getGet('id');
        if ($singleId > 0) {
            $trx = $this->trxModel->where('id', $singleId)
                ->where('is_published', 1)
                ->where('deleted_at IS NULL')
                ->first();

            if (! $trx) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Not found']);
            }

            $category = null;
            if (! empty($trx['category_id'])) {
                $category = $this->categoryModel->find((int) $trx['category_id']);
            }
            $trx['category_name'] = $category['name'] ?? null;
            $trx['category_icon'] = $category['icon'] ?? 'briefcase';

            $maskedTrx = $this->trxModel->maskForMember($trx, $globalPolicy);
            $timeline  = $this->updateModel->getTimeline($singleId, true);

            return $this->response
                ->setHeader('Cache-Control', 'private, no-cache, no-store')
                ->setJSON([
                    'status'       => 'ok',
                    'timestamp'    => date('d/m/Y H:i:s'),
                    'transaction'  => $maskedTrx,
                    'timeline'     => $timeline,
                ]);
        }

        // Aggregate directory polling
        $statistics = $this->trxModel->getStatistics(true);
        $rawRecent  = $this->trxModel->getPublishedTransactions([], 5, 0);

        $recent = [];
        foreach ($rawRecent as $raw) {
            $recent[] = $this->trxModel->maskForMember($raw, $globalPolicy);
        }

        return $this->response
            ->setHeader('Cache-Control', 'private, no-cache, no-store')
            ->setJSON([
                'status'       => 'ok',
                'timestamp'    => date('d/m/Y H:i:s'),
                'statistics'   => $statistics,
                'transactions' => $recent,
            ]);
    }
}
