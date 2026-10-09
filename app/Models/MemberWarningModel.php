<?php

namespace App\Models;

use App\Entities\MemberWarning;
use CodeIgniter\Model;

class MemberWarningModel extends Model
{
    protected $table            = 'member_warnings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = MemberWarning::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'membership_id',
        'user_id',
        'warning_level',
        'reason',
        'notes',
        'issued_by',
        'status',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'membership_id' => 'required|is_natural_no_zero',
        'user_id'       => 'required|is_natural_no_zero',
        'warning_level' => 'required|in_list[sp1,sp2,sp3]',
        'reason'        => 'required',
    ];

    protected $validationMessages = [
        'reason' => [
            'required' => 'Alasan surat peringatan wajib diisi.',
        ],
        'warning_level' => [
            'required' => 'Tingkat peringatan wajib dipilih.',
            'in_list'  => 'Tingkat peringatan harus SP1, SP2, atau SP3.',
        ],
    ];

    /**
     * Get all warnings for a membership with issuer and resolver names
     */
    public function getByMembershipId(int $membershipId): array
    {
        return $this->select('member_warnings.*, issuer.username as issuer_username, resolver.username as resolver_username, issuer_profile.full_name as issuer_name, resolver_profile.full_name as resolver_name')
            ->join('users as issuer', 'issuer.id = member_warnings.issued_by', 'left')
            ->join('member_profiles as issuer_profile', 'issuer_profile.user_id = member_warnings.issued_by', 'left')
            ->join('users as resolver', 'resolver.id = member_warnings.resolved_by', 'left')
            ->join('member_profiles as resolver_profile', 'resolver_profile.user_id = member_warnings.resolved_by', 'left')
            ->where('member_warnings.membership_id', $membershipId)
            ->orderBy('member_warnings.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get active warnings for a user (for member dashboard)
     */
    public function getActiveByUserId(int $userId): array
    {
        return $this->select('member_warnings.*, issuer.username as issuer_username, issuer_profile.full_name as issuer_name')
            ->join('users as issuer', 'issuer.id = member_warnings.issued_by', 'left')
            ->join('member_profiles as issuer_profile', 'issuer_profile.user_id = member_warnings.issued_by', 'left')
            ->where('member_warnings.user_id', $userId)
            ->where('member_warnings.status', 'active')
            ->orderBy('FIELD(member_warnings.warning_level, "sp3", "sp2", "sp1")')
            ->orderBy('member_warnings.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Determine highest active warning level for a membership
     */
    public function getHighestActiveWarningLevel(int $membershipId): ?string
    {
        $warnings = $this->where('membership_id', $membershipId)
            ->where('status', 'active')
            ->findAll();

        if (empty($warnings)) {
            return null;
        }

        $hasSp3 = false;
        $hasSp2 = false;
        $hasSp1 = false;

        foreach ($warnings as $w) {
            $lvl = strtolower($w->warning_level);
            if ($lvl === 'sp3') {
                $hasSp3 = true;
            } elseif ($lvl === 'sp2') {
                $hasSp2 = true;
            } elseif ($lvl === 'sp1') {
                $hasSp1 = true;
            }
        }

        if ($hasSp3) {
            return 'sp3';
        }
        if ($hasSp2) {
            return 'sp2';
        }
        if ($hasSp1) {
            return 'sp1';
        }

        return null;
    }
}
