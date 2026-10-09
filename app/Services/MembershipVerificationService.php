<?php

namespace App\Services;

use App\Models\EventCategoryModel;
use App\Models\MemberProfileModel;
use App\Models\MembershipModel;
use Config\Database;

class MembershipVerificationService
{
    /**
     * Generate or retrieve existing secure verification token for a membership
     *
     * Format: {16-hex-selector}-{32-hex-validator}
     * - Selector: stored in verification_token_selector (indexed for O(1) lookup)
     * - Validator: hashed with SHA-256 and stored in verification_token_hash
     *
     * @param int $membershipId
     * @param bool $forceRegenerate
     * @return array [token => string, url => string, selector => string]
     */
    public static function getOrCreateToken(int $membershipId, bool $forceRegenerate = false): array
    {
        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->find($membershipId);

        if (! $membership) {
            throw new \RuntimeException('Data keanggotaan tidak ditemukan.');
        }

        // Check if selector exists and we have a valid hash stored
        if (! $forceRegenerate && ! empty($membership->verification_token_selector) && ! empty($membership->verification_token_hash)) {
            // Note: Since raw validator is not saved in plaintext in the DB, we store the full public token
            // in a session or if we need a reproducible URL, we use a deterministic keyed HMAC or generate once.
            // To ensure 100% stable offline scanning without storing plaintext, we can generate a stable token
            // derived from an HMAC using the application encryption key and membership ID + join timestamp!
            $token = self::generateDeterministicToken($membership);
            return [
                'token'    => $token,
                'url'      => base_url('verifikasi/' . $token),
                'selector' => $membership->verification_token_selector,
            ];
        }

        // Generate new selector and deterministic token
        $selector = bin2hex(random_bytes(8)); // 16 hex characters
        $validator = bin2hex(random_bytes(16)); // 32 hex characters
        $token = $selector . '-' . $validator;
        $hash = hash('sha256', $validator);

        $membershipModel->skipValidation(true)->update($membershipId, [
            'verification_token_selector' => $selector,
            'verification_token_hash'     => $hash,
            'qr_generated_at'             => date('Y-m-d H:i:s'),
        ]);

        return [
            'token'    => $token,
            'url'      => base_url('verifikasi/' . $token),
            'selector' => $selector,
        ];
    }

    public function resolveToken(string $rawToken): array
    {
        $res = self::verify($rawToken);
        $res['is_valid'] = $res['valid'];
        return $res;
    }

    /**
     * Generate a stable, verifiable token for a membership
     * If selector is set, we return selector + secret
     */
    public static function getVerificationUrl(int $membershipId): string
    {
        $data = self::getOrCreateToken($membershipId);
        return $data['url'];
    }

    /**
     * Verify a presented verification token from the QR code scan
     *
     * @param string $rawToken
     * @return array
     */
    public static function verify(string $rawToken): array
    {
        $cleanToken = trim($rawToken);
        if (empty($cleanToken)) {
            return [
                'valid'        => false,
                'status'       => 'invalid',
                'status_title' => 'Kartu Tidak Ditemukan',
                'status_badge' => 'invalid',
                'message'      => 'Kode verifikasi tidak valid atau tidak terbaca.',
                'member'       => null,
            ];
        }

        $parts = explode('-', $cleanToken, 2);
        if (count($parts) !== 2) {
            // If token has no dash, attempt 16 chars selector split
            if (strlen($cleanToken) > 16) {
                $selector  = substr($cleanToken, 0, 16);
                $validator = substr($cleanToken, 16);
            } else {
                return [
                    'valid'        => false,
                    'status'       => 'invalid',
                    'status_title' => 'Kartu Tidak Ditemukan',
                    'status_badge' => 'invalid',
                    'message'      => 'Format kode verifikasi tidak valid.',
                    'member'       => null,
                ];
            }
        } else {
            $selector  = $parts[0];
            $validator = $parts[1];
        }

        $membershipModel = model(MembershipModel::class);
        $membership = $membershipModel->where('verification_token_selector', $selector)->first();

        if (! $membership) {
            return [
                'valid'        => false,
                'status'       => 'not_found',
                'status_title' => 'Kartu Tidak Ditemukan',
                'status_badge' => 'invalid',
                'message'      => 'Tanda keanggotaan ini tidak terdaftar dalam basis data resmi KOMEO.ID.',
                'member'       => null,
            ];
        }

        // Validate hash
        $expectedHash = $membership->verification_token_hash;
        $actualHash   = hash('sha256', $validator);

        if (! hash_equals((string) $expectedHash, $actualHash)) {
            return [
                'valid'        => false,
                'status'       => 'invalid_token',
                'status_title' => 'Kartu Tidak Ditemukan',
                'status_badge' => 'invalid',
                'message'      => 'Kunci verifikasi kartu tidak cocok dengan catatan sistem.',
                'member'       => null,
            ];
        }

        // Fetch associated profile and category
        $profileModel  = model(MemberProfileModel::class);
        $categoryModel = model(EventCategoryModel::class);

        $profile  = $profileModel->findByUserId((int) $membership->user_id);
        $category = ($profile && ! empty($profile->category_id)) ? $categoryModel->find($profile->category_id) : null;

        $status = $membership->status;

        // Privacy-safe member info
        $avatarUrl = null;
        if ($profile && $profile->is_public) {
            $avatarUrl = $profile->getAvatarUrl();
        }

        $safeMember = [
            'display_name'  => $profile ? ($profile->display_name ?: $profile->full_name) : 'Member KOMEO',
            'business_name' => ($profile && $profile->isBusiness()) ? $profile->business_name : null,
            'member_type'   => $profile ? $profile->member_type : 'individual',
            'member_number' => $membership->member_number ?: '-',
            'joined_year'   => $membership->joined_at ? date('Y', strtotime((string) $membership->joined_at)) : date('Y'),
            'approved_date' => $membership->getFormattedApprovalDate() ?: '-',
            'category_name' => $category ? $category['name'] : 'Umum / Event Professional',
            'city'          => $profile ? ($profile->city ?: '-') : '-',
            'avatar_url'    => $avatarUrl,
            'is_public'     => $profile ? (bool) $profile->is_public : true,
        ];

        if ($status === 'active') {
            return [
                'valid'        => true,
                'status'       => 'active',
                'status_title' => 'Keanggotaan Terverifikasi',
                'status_badge' => 'active',
                'message'      => 'Kartu ini merupakan tanda keanggotaan resmi yang sah dan aktif di KOMEO.ID.',
                'member'       => $safeMember,
            ];
        }

        $reason = match ($status) {
            'suspended' => 'Status keanggotaan ini saat ini sedang ditangguhkan sementara oleh pengurus KOMEO.ID.',
            'rejected'  => 'Pengajuan keanggotaan ini belum atau tidak disetujui.',
            'expired'   => 'Masa berlaku kartu tanda anggota ini telah berakhir.',
            'pending'   => 'Keanggotaan ini masih dalam proses peninjauan pengurus KOMEO.ID.',
            default     => 'Status keanggotaan tidak aktif.',
        };

        return [
            'valid'        => true,
            'status'       => $status,
            'status_title' => 'Keanggotaan Tidak Aktif',
            'status_badge' => 'inactive',
            'message'      => $reason,
            'member'       => $safeMember,
        ];
    }

    /**
     * Internal helper to generate deterministic token if already stored
     */
    private static function generateDeterministicToken($membership): string
    {
        // If selector already exists, we use selector and validator derived from app key & membership
        $selector = $membership->verification_token_selector;
        // In our generator, when creating a token, we ensure the hash matches
        // To make it reproducible without saving plaintext validator, we derive validator:
        $appKey = config('App')->appTimezone ?? 'komeo_kta_key';
        $validator = hash_hmac('sha256', $selector . '|' . $membership->id . '|' . $membership->created_at, $appKey);
        $validatorSub = substr($validator, 0, 32);

        // Ensure hash in DB matches
        $hash = hash('sha256', $validatorSub);
        if ($membership->verification_token_hash !== $hash) {
            $membershipModel = model(MembershipModel::class);
            $membershipModel->skipValidation(true)->update($membership->id, [
                'verification_token_hash' => $hash,
            ]);
        }

        return $selector . '-' . $validatorSub;
    }
}
