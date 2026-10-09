<?php

namespace App\Services;

class VerificationChallengeService
{
    public const CONTEXT_EVENT_REGISTRATION = 'event_registration';
    public const CONTEXT_CLIENT_INQUIRY     = 'client_inquiry';

    /**
     * Create or refresh an email verification challenge
     *
     * @param string $context 'event_registration' or 'client_inquiry'
     * @param int $referenceId event_registration_id or member_inquiry_id
     * @param string $email
     * @param int $validHours
     * @return array [token => string, selector => string, expires_at => string]
     */
    public static function createChallenge(
        string $context,
        int $referenceId,
        string $email,
        int $validHours = 24
    ): array {
        if (! in_array($context, [self::CONTEXT_EVENT_REGISTRATION, self::CONTEXT_CLIENT_INQUIRY], true)) {
            throw new \InvalidArgumentException('Konteks verifikasi tidak valid.');
        }

        $db = \Config\Database::connect();

        // Expire any existing active challenges for this reference & context
        $db->table('verification_challenges')
            ->where('context', $context)
            ->where('reference_id', $referenceId)
            ->where('verified_at IS NULL')
            ->update([
                'expires_at' => date('Y-m-d H:i:s', strtotime('-1 minute')),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        // Generate split token
        $selector  = bin2hex(random_bytes(8));  // 16 hex characters
        $validator = bin2hex(random_bytes(16)); // 32 hex characters
        $token     = $selector . '-' . $validator;
        $hash      = hash('sha256', $validator);

        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$validHours} hours"));
        $now       = date('Y-m-d H:i:s');

        $db->table('verification_challenges')->insert([
            'token_selector' => $selector,
            'token_hash'     => $hash,
            'context'        => $context,
            'reference_id'   => $referenceId,
            'email'          => strtolower(trim($email)),
            'attempts'       => 0,
            'max_attempts'   => 5,
            'expires_at'     => $expiresAt,
            'verified_at'    => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        return [
            'token'      => $token,
            'selector'   => $selector,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Verify a challenge token strictly scoped by context
     *
     * @param string $context
     * @param string $token Format: {16-hex-selector}-{32-hex-validator}
     * @return array [success => bool, message => string, reference_id => int|null, email => string|null]
     */
    public static function verifyChallenge(string $context, string $token): array
    {
        $parts = explode('-', trim($token), 2);
        if (count($parts) !== 2) {
            return [
                'success'      => false,
                'message'      => 'Format token verifikasi tidak valid.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        [$selector, $validator] = $parts;
        if (strlen($selector) !== 16 || strlen($validator) !== 32) {
            return [
                'success'      => false,
                'message'      => 'Panjang token verifikasi tidak sesuai.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        $db = \Config\Database::connect();
        $row = $db->table('verification_challenges')
            ->where('token_selector', $selector)
            ->where('context', $context)
            ->get()
            ->getRow();

        if (! $row) {
            return [
                'success'      => false,
                'message'      => 'Tautan verifikasi tidak ditemukan atau tidak sesuai.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        // Check if already verified
        if (! empty($row->verified_at)) {
            return [
                'success'      => true,
                'message'      => 'Email telah berhasil diverifikasi sebelumnya.',
                'reference_id' => (int) $row->reference_id,
                'email'        => $row->email,
                'already'      => true,
            ];
        }

        // Check attempts limit
        if ($row->attempts >= $row->max_attempts) {
            return [
                'success'      => false,
                'message'      => 'Batas percobaan verifikasi telah habis. Silakan ajukan tautan baru.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        // Check expiration
        if (strtotime($row->expires_at) < time()) {
            return [
                'success'      => false,
                'message'      => 'Tautan verifikasi telah kadaluarsa. Silakan ajukan verifikasi ulang.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        // Increment attempts count
        $db->table('verification_challenges')
            ->where('id', $row->id)
            ->increment('attempts', 1);

        // Constant-time hash verification
        $computedHash = hash('sha256', $validator);
        if (! hash_equals($row->token_hash, $computedHash)) {
            return [
                'success'      => false,
                'message'      => 'Kode validator verifikasi tidak cocok.',
                'reference_id' => null,
                'email'        => null,
            ];
        }

        // Mark as verified
        $now = date('Y-m-d H:i:s');
        $db->table('verification_challenges')
            ->where('id', $row->id)
            ->update([
                'verified_at' => $now,
                'updated_at'  => $now,
            ]);

        return [
            'success'      => true,
            'message'      => 'Verifikasi email berhasil.',
            'reference_id' => (int) $row->reference_id,
            'email'        => $row->email,
            'already'      => false,
        ];
    }

    /**
     * Alias for verifyChallenge supporting ($token, $context) parameter order
     */
    public static function verifyToken(string $token, string $context): array
    {
        return self::verifyChallenge($context, $token);
    }
}
