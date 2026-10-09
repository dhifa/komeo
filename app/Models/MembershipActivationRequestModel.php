<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipActivationRequestModel extends Model
{
    protected $table            = 'membership_activation_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'application_reference',
        'status',
        'contact_confirmed_at',
        'contact_confirmed_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique application reference formatted as APP-YYYYMMDD-XXXXX
     */
    public function generateReference(): string
    {
        $datePrefix = date('Ymd');
        do {
            $randomSuffix = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $reference = "APP-{$datePrefix}-{$randomSuffix}";
            $exists = $this->where('application_reference', $reference)->countAllResults();
        } while ($exists > 0);

        return $reference;
    }

    /**
     * Get latest activation request for user
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Get or automatically create activation request for user
     */
    public function getOrCreateForUser(int $userId): array
    {
        $existing = $this->getByUserId($userId);
        if ($existing) {
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        $reference = $this->generateReference();

        $data = [
            'user_id'               => $userId,
            'application_reference' => $reference,
            'status'                => 'awaiting_contact',
            'contact_confirmed_at'  => null,
            'contact_confirmed_by'  => null,
            'reviewed_by'           => null,
            'reviewed_at'           => null,
            'review_notes'          => null,
            'created_at'            => $now,
            'updated_at'            => $now,
        ];

        $insertId = $this->insert($data);
        return $this->find($insertId);
    }

    /**
     * Record contact confirmation by an administrator
     */
    public function confirmContact(int $userId, int $adminId, ?string $note = null): bool
    {
        $record = $this->getOrCreateForUser($userId);
        $now = date('Y-m-d H:i:s');

        $updateData = [
            'status'               => 'awaiting_review',
            'contact_confirmed_at' => $now,
            'contact_confirmed_by' => $adminId,
            'updated_at'           => $now,
        ];

        if ($note) {
            $prevNotes = $record['review_notes'] ?? '';
            $updateData['review_notes'] = trim($prevNotes . "\n[" . date('d/m/Y H:i') . " Kontak Dikonfirmasi]: " . $note);
        }

        return $this->update($record['id'], $updateData);
    }

    /**
     * Record approval of activation request
     */
    public function recordApproval(int $userId, int $adminId, ?string $note = null): bool
    {
        $record = $this->getOrCreateForUser($userId);
        $now = date('Y-m-d H:i:s');

        return $this->update($record['id'], [
            'status'       => 'approved',
            'reviewed_by'  => $adminId,
            'reviewed_at'  => $now,
            'review_notes' => $note ? trim($note) : ($record['review_notes'] ?? null),
            'updated_at'   => $now,
        ]);
    }

    /**
     * Record rejection of activation request
     */
    public function recordRejection(int $userId, int $adminId, string $reason): bool
    {
        $record = $this->getOrCreateForUser($userId);
        $now = date('Y-m-d H:i:s');

        return $this->update($record['id'], [
            'status'       => 'rejected',
            'reviewed_by'  => $adminId,
            'reviewed_at'  => $now,
            'review_notes' => trim($reason),
            'updated_at'   => $now,
        ]);
    }
}
