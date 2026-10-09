<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentShareModel extends Model
{
    protected $table            = 'document_shares';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'inquiry_id',
        'shared_by_user_id',
        'recipient_email',
        'share_token_selector',
        'share_token_hash',
        'expires_at',
        'is_revoked',
        'revoked_at',
        'access_count',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate split secure share token
     */
    public static function generateShareToken(): array
    {
        $selector  = bin2hex(random_bytes(8));   // 16 chars
        $validator = bin2hex(random_bytes(16));  // 32 chars
        $rawToken  = $selector . '.' . $validator;
        $hash      = hash('sha256', $validator);

        return [
            'raw_token' => $rawToken,
            'selector'  => $selector,
            'hash'      => $hash,
        ];
    }

    /**
     * Validate and retrieve active share
     */
    public function getActiveShare(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$selector, $validator] = $parts;

        $share = $this->where('share_token_selector', $selector)->first();
        if (! $share) {
            return null;
        }

        $expectedHash = hash('sha256', $validator);
        if (! hash_equals($share['share_token_hash'], $expectedHash)) {
            return null;
        }

        // Check if revoked
        if ((int) $share['is_revoked'] === 1) {
            return null;
        }

        // Check if expired
        if (date('Y-m-d H:i:s') > $share['expires_at']) {
            return null;
        }

        // Join document details
        $doc = $this->db->table('member_documents')
            ->where('id', $share['document_id'])
            ->where('is_active', 1)
            ->get()->getRowArray();

        if (! $doc) {
            return null;
        }

        $share['document'] = $doc;
        return $share;
    }
}
