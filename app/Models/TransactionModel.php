<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'komeo_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_code',
        'title',
        'public_title',
        'category_id',
        'description',
        'internal_client_name',
        'internal_project_ref',
        'currency',
        'total_amount',
        'agreed_dp_amount',
        'agreed_dp_percentage',
        'dp_due_date',
        'final_due_date',
        'current_work_stage',
        'payment_status',
        'is_overdue',
        'contract_issue_status',
        'amount_visibility',
        'is_published',
        'created_by',
        'updated_by',
        'transaction_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Allowed work stages
     */
    public const WORK_STAGES = [
        'Transaksi Masuk' => 'Transaksi Masuk',
        'Verifikasi'      => 'Verifikasi',
        'Negosiasi'       => 'Negosiasi',
        'Dalam Proses'    => 'Dalam Proses',
        'Selesai'         => 'Selesai',
        'Ditunda'         => 'Ditunda',
        'Dibatalkan'      => 'Dibatalkan',
    ];

    /**
     * Allowed payment statuses
     */
    public const PAYMENT_STATUSES = [
        'UNPAID'         => 'Belum Ada Pembayaran',
        'AWAITING_DP'    => 'Menunggu DP',
        'DP_RECEIVED'    => 'DP Diterima',
        'PARTIALLY_PAID' => 'Pembayaran Sebagian',
        'PAID'           => 'Lunas',
        'OVERDUE'        => 'Terlambat Bayar',
    ];

    /**
     * Contract issue statuses
     */
    public const CONTRACT_ISSUE_STATUSES = [
        'none'             => 'Tidak Ada Permasalahan',
        'under_review'     => 'Dalam Peninjauan',
        'suspected_breach' => 'Indikasi Wanprestasi',
        'disputed'         => 'Dalam Sengketa',
        'handling'         => 'Dalam Penanganan',
        'resolved'         => 'Selesai Ditangani',
    ];

    /**
     * Amount visibility options
     */
    public const VISIBILITY_OPTIONS = [
        'hide'                 => 'Sembunyikan Nominal',
        'total_only'           => 'Tampilkan Nilai Total',
        'total_and_percentage' => 'Tampilkan Total & Persentase Pembayaran',
        'detail'               => 'Tampilkan Detail Nominal',
    ];

    /**
     * Generate unique transaction code: KMO-TRX-YYYY-XXXXXX
     */
    public function generateUniqueCode(): string
    {
        $year = date('Y');
        $prefix = "KMO-TRX-{$year}-";

        // Query maximum sequence for current year
        $row = $this->db->table($this->table)
            ->select('transaction_code')
            ->like('transaction_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if (! empty($row['transaction_code'])) {
            $parts = explode('-', $row['transaction_code']);
            $lastNum = (int) end($parts);
            if ($lastNum > 0) {
                $nextSeq = $lastNum + 1;
            }
        }

        // Ensure collision safety
        do {
            $code = $prefix . str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
            $exists = $this->where('transaction_code', $code)->countAllResults();
            if ($exists > 0) {
                $nextSeq++;
            }
        } while ($exists > 0);

        return $code;
    }

    /**
     * Recalculate transaction financials from payments
     */
    public function recalculateFinancials(int $transactionId): array
    {
        $trx = $this->find($transactionId);
        if (! $trx) {
            return [];
        }

        $paymentModel = model(TransactionPaymentModel::class);
        $netReceived = $paymentModel->getNetReceived($transactionId);
        $dpReceived  = $paymentModel->getDpReceived($transactionId);

        $totalAmount = $trx['total_amount'] !== null ? (float) $trx['total_amount'] : null;
        $outstanding = null;
        $percentage  = null;
        $status      = $trx['payment_status'] ?? 'UNPAID';
        $isOverdue   = 0;
        $today       = date('Y-m-d');

        if ($totalAmount !== null && $totalAmount > 0) {
            $outstanding = max(0.0, round($totalAmount - $netReceived, 2));
            $percentage  = min(100.0, round(($netReceived / $totalAmount) * 100, 1));

            if ($netReceived <= 0.0) {
                $agreedDp = (float) ($trx['agreed_dp_amount'] ?? 0);
                $status = ($agreedDp > 0 || (float) ($trx['agreed_dp_percentage'] ?? 0) > 0) ? 'AWAITING_DP' : 'UNPAID';
            } elseif ($netReceived >= $totalAmount) {
                $status = 'PAID';
            } elseif ($dpReceived > 0 && abs($netReceived - $dpReceived) < 0.01) {
                $status = 'DP_RECEIVED';
            } else {
                $status = 'PARTIALLY_PAID';
            }

            // Check overdue condition without overriding legal wanprestasi
            if ($status !== 'PAID') {
                if (! empty($trx['final_due_date']) && $today > $trx['final_due_date'] && $outstanding > 0) {
                    $isOverdue = 1;
                } elseif (! empty($trx['dp_due_date']) && $today > $trx['dp_due_date'] && $dpReceived <= 0) {
                    $isOverdue = 1;
                }
            }
        }

        // Update record
        $updateData = [
            'payment_status' => $status,
            'is_overdue'     => $isOverdue,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        $this->update($transactionId, $updateData);

        return [
            'net_received'        => $netReceived,
            'dp_received'         => $dpReceived,
            'outstanding_balance' => $outstanding,
            'payment_percentage'  => $percentage,
            'payment_status'      => $status,
            'is_overdue'          => $isOverdue,
        ];
    }

    /**
     * Get computed financials for a transaction
     */
    public function getComputedFinancials(array $trx): array
    {
        $id = (int) $trx['id'];
        $paymentModel = model(TransactionPaymentModel::class);
        $netReceived = $paymentModel->getNetReceived($id);
        $dpReceived  = $paymentModel->getDpReceived($id);

        $totalAmount = $trx['total_amount'] !== null ? (float) $trx['total_amount'] : null;
        $outstanding = null;
        $percentage  = null;

        if ($totalAmount !== null && $totalAmount > 0) {
            $outstanding = max(0.0, round($totalAmount - $netReceived, 2));
            $percentage  = min(100.0, round(($netReceived / $totalAmount) * 100, 1));
        }

        return [
            'net_received'        => $netReceived,
            'dp_received'         => $dpReceived,
            'outstanding_balance' => $outstanding,
            'payment_percentage'  => $percentage,
        ];
    }

    /**
     * Mask transaction data for members according to global and per-transaction policies
     */
    public function maskForMember(array $trx, string $globalPolicy = 'hide_all'): array
    {
        $computed = $this->getComputedFinancials($trx);

        // Base safe member data
        $masked = [
            'id'                     => (int) $trx['id'],
            'transaction_code'       => $trx['transaction_code'],
            'public_title'           => $trx['public_title'] ?: $trx['title'],
            'category_id'            => $trx['category_id'],
            'category_name'          => $trx['category_name'] ?? null,
            'category_icon'          => $trx['category_icon'] ?? 'briefcase',
            'description'            => $trx['description'],
            'currency'               => $trx['currency'] ?: 'IDR',
            'current_work_stage'     => $trx['current_work_stage'],
            'payment_status'         => $trx['payment_status'],
            'is_overdue'             => (bool) ($trx['is_overdue'] ?? false),
            'transaction_date'       => $trx['transaction_date'],
            'updated_at'             => $trx['updated_at'],
            'dp_due_date'            => $trx['dp_due_date'] ?? null,
            'final_due_date'         => $trx['final_due_date'] ?? null,
            // Contract issue neutral label
            'contract_issue_display' => in_array($trx['contract_issue_status'] ?? 'none', ['none', 'resolved'], true) 
                ? 'Tidak Ada Permasalahan' 
                : 'Dalam Penanganan',
            // Default hidden financial amounts
            'amount_visible'         => false,
            'total_amount'           => null,
            'agreed_dp_amount'       => null,
            'net_received'           => null,
            'outstanding_balance'    => null,
            'payment_percentage'     => null,
        ];

        // Apply strict monetary visibility rules
        $perTrx = $trx['amount_visibility'] ?? 'hide';
        if ($globalPolicy === 'per_transaction' && $perTrx !== 'hide') {
            $masked['amount_visible'] = true;
            $masked['amount_mode'] = $perTrx;

            if ($perTrx === 'total_only') {
                $masked['total_amount'] = (float) $trx['total_amount'];
            } elseif ($perTrx === 'total_and_percentage') {
                $masked['total_amount']       = (float) $trx['total_amount'];
                $masked['payment_percentage'] = $computed['payment_percentage'];
            } elseif ($perTrx === 'detail') {
                $masked['total_amount']        = (float) $trx['total_amount'];
                $masked['agreed_dp_amount']    = $trx['agreed_dp_amount'] !== null ? (float) $trx['agreed_dp_amount'] : null;
                $masked['net_received']        = $computed['net_received'];
                $masked['outstanding_balance'] = $computed['outstanding_balance'];
                $masked['payment_percentage']  = $computed['payment_percentage'];
            }
        }

        return $masked;
    }

    /**
     * Get published transactions with category join
     */
    public function getPublishedTransactions(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $builder = $this->db->table('komeo_transactions t')
            ->select('t.*, c.name as category_name, c.icon as category_icon')
            ->join('komeo_transaction_categories c', 'c.id = t.category_id', 'left')
            ->where('t.is_published', 1)
            ->where('t.deleted_at IS NULL');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('t.transaction_code', $s)
                ->orLike('t.public_title', $s)
                ->orLike('t.title', $s)
            ->groupEnd();
        }

        if (! empty($filters['category_id'])) {
            $builder->where('t.category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['work_stage'])) {
            $builder->where('t.current_work_stage', $filters['work_stage']);
        }

        if (! empty($filters['payment_status'])) {
            $builder->where('t.payment_status', $filters['payment_status']);
        }

        $builder->orderBy('t.updated_at', 'DESC')
            ->orderBy('t.id', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Count published transactions
     */
    public function countPublishedTransactions(array $filters = []): int
    {
        $builder = $this->db->table('komeo_transactions t')
            ->where('t.is_published', 1)
            ->where('t.deleted_at IS NULL');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('t.transaction_code', $s)
                ->orLike('t.public_title', $s)
                ->orLike('t.title', $s)
            ->groupEnd();
        }

        if (! empty($filters['category_id'])) {
            $builder->where('t.category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['work_stage'])) {
            $builder->where('t.current_work_stage', $filters['work_stage']);
        }

        if (! empty($filters['payment_status'])) {
            $builder->where('t.payment_status', $filters['payment_status']);
        }

        return $builder->countAllResults();
    }

    /**
     * Get admin transactions with category join
     */
    public function getAdminTransactions(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $builder = $this->db->table('komeo_transactions t')
            ->select('t.*, c.name as category_name, c.icon as category_icon')
            ->join('komeo_transaction_categories c', 'c.id = t.category_id', 'left')
            ->where('t.deleted_at IS NULL');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('t.transaction_code', $s)
                ->orLike('t.title', $s)
                ->orLike('t.public_title', $s)
                ->orLike('t.internal_client_name', $s)
            ->groupEnd();
        }

        if (! empty($filters['category_id'])) {
            $builder->where('t.category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['work_stage'])) {
            $builder->where('t.current_work_stage', $filters['work_stage']);
        }

        if (! empty($filters['payment_status'])) {
            $builder->where('t.payment_status', $filters['payment_status']);
        }

        if (! empty($filters['issue_status'])) {
            $builder->where('t.contract_issue_status', $filters['issue_status']);
        }

        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $builder->where('t.is_published', (int) $filters['is_published']);
        }

        $builder->orderBy('t.id', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Count admin transactions
     */
    public function countAdminTransactions(array $filters = []): int
    {
        $builder = $this->db->table('komeo_transactions t')
            ->where('t.deleted_at IS NULL');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('t.transaction_code', $s)
                ->orLike('t.title', $s)
                ->orLike('t.public_title', $s)
                ->orLike('t.internal_client_name', $s)
            ->groupEnd();
        }

        if (! empty($filters['category_id'])) {
            $builder->where('t.category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['work_stage'])) {
            $builder->where('t.current_work_stage', $filters['work_stage']);
        }

        if (! empty($filters['payment_status'])) {
            $builder->where('t.payment_status', $filters['payment_status']);
        }

        if (! empty($filters['issue_status'])) {
            $builder->where('t.contract_issue_status', $filters['issue_status']);
        }

        if (isset($filters['is_published']) && $filters['is_published'] !== '') {
            $builder->where('t.is_published', (int) $filters['is_published']);
        }

        return $builder->countAllResults();
    }

    /**
     * Get aggregate statistics
     */
    public function getStatistics(bool $publishedOnly = false): array
    {
        $builder = $this->db->table('komeo_transactions')
            ->where('deleted_at IS NULL');

        if ($publishedOnly) {
            $builder->where('is_published', 1);
        }

        $all = $builder->get()->getResultArray();

        $stats = [
            'total'               => count($all),
            'in_progress'         => 0,
            'completed'           => 0,
            'awaiting_payment'    => 0,
            'dp_received'         => 0,
            'paid'                => 0,
            'overdue'             => 0,
            'issues'              => 0,
            'unpublished'         => 0,
        ];

        foreach ($all as $item) {
            $stage = $item['current_work_stage'] ?? '';
            $pay   = $item['payment_status'] ?? '';
            $issue = $item['contract_issue_status'] ?? 'none';
            $pub   = (int) ($item['is_published'] ?? 0);

            if ($stage === 'Dalam Proses' || $stage === 'Transaksi Masuk' || $stage === 'Verifikasi' || $stage === 'Negosiasi') {
                $stats['in_progress']++;
            }
            if ($stage === 'Selesai') {
                $stats['completed']++;
            }
            if ($pay === 'UNPAID' || $pay === 'AWAITING_DP') {
                $stats['awaiting_payment']++;
            }
            if ($pay === 'DP_RECEIVED') {
                $stats['dp_received']++;
            }
            if ($pay === 'PAID') {
                $stats['paid']++;
            }
            if (! empty($item['is_overdue']) || $pay === 'OVERDUE') {
                $stats['overdue']++;
            }
            if ($issue !== 'none' && $issue !== 'resolved') {
                $stats['issues']++;
            }
            if ($pub === 0) {
                $stats['unpublished']++;
            }
        }

        return $stats;
    }

    /**
     * Alias for published member dashboard statistics
     */
    public function getMemberDashboardStats(): array
    {
        return $this->getStatistics(true);
    }

    /**
     * Get records for admin reports and CSV export
     */
    public function getAdminReportRecords(array $filters = []): array
    {
        return $this->getAdminTransactions($filters, 0, 0);
    }
}
