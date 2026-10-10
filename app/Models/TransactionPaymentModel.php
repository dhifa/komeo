<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionPaymentModel extends Model
{
    protected $table            = 'komeo_transaction_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_id',
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'internal_note',
        'recorded_by',
        'voided_at',
        'voided_by',
        'void_reason',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get payments for a transaction
     */
    public function getByTransactionId(int $transactionId, bool $activeOnly = false): array
    {
        $builder = $this->where('transaction_id', $transactionId);
        if ($activeOnly) {
            $builder->where('voided_at IS NULL');
        }
        return $builder->orderBy('payment_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Calculate net received amount for a transaction
     * Net received = Valid positive payments minus refunds/reversals
     */
    public function getNetReceived(int $transactionId): float
    {
        $payments = $this->where('transaction_id', $transactionId)
            ->where('voided_at IS NULL')
            ->findAll();

        $net = 0.0;
        foreach ($payments as $p) {
            $amount = (float) ($p['amount'] ?? 0);
            $type = strtolower($p['payment_type'] ?? '');
            if ($type === 'refund' || $type === 'koreksi_minus') {
                $net -= abs($amount);
            } else {
                $net += $amount;
            }
        }

        return max(0.0, round($net, 2));
    }

    /**
     * Get sum of DP payments
     */
    public function getDpReceived(int $transactionId): float
    {
        $payments = $this->where('transaction_id', $transactionId)
            ->where('voided_at IS NULL')
            ->where('payment_type', 'DP')
            ->findAll();

        $total = 0.0;
        foreach ($payments as $p) {
            $total += (float) ($p['amount'] ?? 0);
        }
        return round($total, 2);
    }

    /**
     * Void a payment record
     */
    public function voidPayment(int $paymentId, int $adminId, string $reason): bool
    {
        return $this->update($paymentId, [
            'voided_at'   => date('Y-m-d H:i:s'),
            'voided_by'   => $adminId,
            'void_reason' => $reason,
        ]);
    }
}
