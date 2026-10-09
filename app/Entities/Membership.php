<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Membership extends Entity
{
    protected $datamap = [
        'membership_number' => 'member_number',
    ];
    protected $dates   = ['approved_at', 'joined_at', 'expires_at', 'created_at', 'updated_at'];
    protected $casts   = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'approved_by' => '?integer',
    ];

    /**
     * Getter for member_number (seamless compatibility with both property and column names)
     */
    public function getMemberNumber(): ?string
    {
        return $this->attributes['member_number'] ?? null;
    }

    /**
     * Getter for membership_number
     */
    public function getMembershipNumber(): ?string
    {
        return $this->attributes['member_number'] ?? null;
    }

    /**
     * Support empty() and isset() for both member_number and membership_number
     */
    public function __isset(string $key): bool
    {
        if ($key === 'member_number' || $key === 'membership_number') {
            return ! empty($this->attributes['member_number']);
        }

        return parent::__isset($key);
    }

    public function isPending(): bool
    {
        return ($this->attributes['status'] ?? '') === 'pending';
    }

    public function isActive(): bool
    {
        return ($this->attributes['status'] ?? '') === 'active';
    }

    public function isRejected(): bool
    {
        return ($this->attributes['status'] ?? '') === 'rejected';
    }

    public function isSuspended(): bool
    {
        return ($this->attributes['status'] ?? '') === 'suspended';
    }

    public function isExpired(): bool
    {
        return ($this->attributes['status'] ?? '') === 'expired';
    }

    /**
     * Helper to get translated status label in Bahasa Indonesia
     */
    public function getStatusLabel(): string
    {
        return match ($this->attributes['status'] ?? 'pending') {
            'pending'   => 'Menunggu Persetujuan',
            'active'    => 'Aktif',
            'rejected'  => 'Ditolak',
            'suspended' => 'Ditangguhkan',
            'expired'   => 'Kadaluarsa',
            default     => 'Tidak Diketahui',
        };
    }

    /**
     * Helper to get user-friendly description for member dashboard
     */
    public function getStatusDescription(): string
    {
        return match ($this->attributes['status'] ?? 'pending') {
            'pending'   => 'Keanggotaan Anda sedang menunggu persetujuan admin.',
            'active'    => 'Keanggotaan KOMEO Anda telah aktif.',
            'rejected'  => ! empty($this->attributes['rejection_reason']) 
                            ? 'Permohonan keanggotaan Anda belum disetujui: ' . $this->attributes['rejection_reason']
                            : 'Permohonan keanggotaan Anda ditolak oleh administrator.',
            'suspended' => ! empty($this->attributes['suspension_reason']) 
                            ? 'Keanggotaan Anda sedang ditangguhkan sementara: ' . $this->attributes['suspension_reason']
                            : 'Keanggotaan Anda sedang ditangguhkan oleh administrator.',
            'expired'   => 'Masa berlaku keanggotaan KOMEO Anda telah berakhir.',
            default     => 'Status keanggotaan tidak diketahui.',
        };
    }

    /**
     * Helper to get status color badge styling classes
     */
    public function getStatusBadgeClasses(): string
    {
        return match ($this->attributes['status'] ?? 'pending') {
            'pending'   => 'bg-amber-100 text-amber-800 border border-amber-200',
            'active'    => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'rejected'  => 'bg-rose-100 text-rose-800 border border-rose-200',
            'suspended' => 'bg-orange-100 text-orange-800 border border-orange-200',
            'expired'   => 'bg-slate-100 text-slate-800 border border-slate-200',
            default     => 'bg-slate-100 text-slate-800 border border-slate-200',
        };
    }

    /**
     * Format approval date in Indonesian readable format
     */
    public function getFormattedApprovalDate(): ?string
    {
        if (empty($this->attributes['approved_at'])) {
            return null;
        }

        $timestamp = strtotime($this->attributes['approved_at']);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $d = date('j', $timestamp);
        $m = $months[(int) date('n', $timestamp)];
        $y = date('Y', $timestamp);

        return "{$d} {$m} {$y}";
    }
}
