<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberInquiryModel extends Model
{
    protected $table            = 'member_inquiries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'target_user_id',
        'client_name',
        'client_email',
        'client_whatsapp',
        'client_organization',
        'inquiry_type', // job_offer, cv_request, portfolio_request, collaboration, general
        'subject',
        'initial_message',
        'event_location',
        'event_date',
        'is_email_verified',
        'status', // new, in_progress, replied, closed, spam
        'client_access_token_selector',
        'client_access_token_hash',
        'last_activity_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate secure split client access token
     */
    public static function generateClientAccessToken(): array
    {
        $selector  = bin2hex(random_bytes(8));   // 16 hex chars
        $validator = bin2hex(random_bytes(16));  // 32 hex chars
        $rawToken  = $selector . '.' . $validator;
        $hash      = hash('sha256', $validator);

        return [
            'raw_token' => $rawToken,
            'selector'  => $selector,
            'hash'      => $hash,
        ];
    }

    /**
     * Get inquiry by client access token
     */
    public function getByClientToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$selector, $validator] = $parts;

        $inquiry = $this->where('client_access_token_selector', $selector)->first();
        if (! $inquiry) {
            return null;
        }

        $expectedHash = hash('sha256', $validator);
        if (! hash_equals($inquiry['client_access_token_hash'], $expectedHash)) {
            return null;
        }

        return $inquiry;
    }

    /**
     * Get inquiries for a member with counts & pagination support
     */
    public function getMemberInquiries(int $userId, ?string $status = null): array
    {
        $builder = $this->where('target_user_id', $userId);

        if ($status !== null && $status !== 'all') {
            $builder->where('status', $status);
        }

        return $builder->orderBy('last_activity_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
