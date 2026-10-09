<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class MemberWarning extends Entity
{
    protected $dates = ['resolved_at', 'created_at', 'updated_at'];
    protected $casts = [
        'id'            => 'integer',
        'membership_id' => 'integer',
        'user_id'       => 'integer',
        'issued_by'     => '?integer',
        'resolved_by'   => '?integer',
    ];

    public function isSp1(): bool
    {
        return strtolower((string) ($this->attributes['warning_level'] ?? '')) === 'sp1';
    }

    public function isSp2(): bool
    {
        return strtolower((string) ($this->attributes['warning_level'] ?? '')) === 'sp2';
    }

    public function isSp3(): bool
    {
        return strtolower((string) ($this->attributes['warning_level'] ?? '')) === 'sp3';
    }

    public function isActive(): bool
    {
        return strtolower((string) ($this->attributes['status'] ?? 'active')) === 'active';
    }

    public function getLevelLabel(): string
    {
        return match (strtolower((string) ($this->attributes['warning_level'] ?? 'sp1'))) {
            'sp1'   => 'Surat Peringatan 1 (SP1)',
            'sp2'   => 'Surat Peringatan 2 (SP2)',
            'sp3'   => 'Surat Peringatan 3 (SP3 - Ditangguhkan)',
            default => strtoupper((string) ($this->attributes['warning_level'] ?? 'SP1')),
        };
    }

    public function getLevelBadgeClass(): string
    {
        return match (strtolower((string) ($this->attributes['warning_level'] ?? 'sp1'))) {
            'sp1'   => 'bg-amber-100 text-amber-900 border border-amber-300',
            'sp2'   => 'bg-orange-100 text-orange-950 border border-orange-300',
            'sp3'   => 'bg-rose-100 text-rose-950 border border-rose-300',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public function getStatusLabel(): string
    {
        return match (strtolower((string) ($this->attributes['status'] ?? 'active'))) {
            'active'   => 'Aktif',
            'resolved' => 'Diselesaikan',
            'revoked'  => 'Dicabut',
            default    => ucfirst((string) ($this->attributes['status'] ?? 'active')),
        };
    }
}
