<?php

namespace App\Models;

use App\Entities\Membership;
use CodeIgniter\Model;

class MembershipModel extends Model
{
    protected $table            = 'memberships';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Membership::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'member_number',
        'status',
        'rejection_reason',
        'suspension_reason',
        'status_notes',
        'active_warning_level',
        'approved_at',
        'approved_by',
        'joined_at',
        'expires_at',
        'verification_token_hash',
        'verification_token_selector',
        'qr_generated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'       => 'permit_empty|is_natural_no_zero',
        'member_number' => 'permit_empty|is_unique[memberships.member_number,id,{id}]|max_length[50]',
        'status'        => 'permit_empty|in_list[pending,active,rejected,suspended,expired]',
    ];

    /**
     * Find membership by user ID
     */
    public function findByUserId(int $userId): ?Membership
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Alias for findByUserId
     */
    public function getByUserId(int $userId): ?Membership
    {
        return $this->findByUserId($userId);
    }

    /**
     * Count pending applications
     */
    public function countPending(): int
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Get membership statistics for admin dashboard
     *
     * @return array<string, int>
     */
    public function getStats(): array
    {
        $totalMembers = $this->builder()->countAllResults();
        $pending      = $this->builder()->where('memberships.status', 'pending')->countAllResults();
        $active       = $this->builder()->where('memberships.status', 'active')->countAllResults();
        $rejected     = $this->builder()->where('memberships.status', 'rejected')->countAllResults();
        $suspended    = $this->builder()->where('memberships.status', 'suspended')->countAllResults();

        // Recent in last 7 days
        $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
        $recent       = $this->builder()->where('memberships.created_at >=', $sevenDaysAgo)->countAllResults();

        return [
            'total'     => $totalMembers,
            'pending'   => $pending,
            'active'    => $active,
            'rejected'  => $rejected,
            'suspended' => $suspended,
            'recent'    => $recent,
        ];
    }

    /**
     * Get recent registrations with member profile and user details
     *
     * @param int $limit
     * @return array
     */
    public function getRecentWithDetails(int $limit = 10): array
    {
        return $this->select('memberships.*, member_profiles.full_name, member_profiles.display_name, member_profiles.member_type, member_profiles.business_name, member_profiles.city, member_profiles.whatsapp, users.username, auth_identities.secret as email')
            ->join('users', 'users.id = memberships.user_id')
            ->join('member_profiles', 'member_profiles.user_id = memberships.user_id', 'left')
            ->join('auth_identities', "auth_identities.user_id = memberships.user_id AND auth_identities.type = 'email_password'", 'left')
            ->orderBy('memberships.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Query builder for filtered members list (pagination compatible)
     *
     * @param array $filters
     * @return self
     */
    public function filterMembers(array $filters = []): self
    {
        $this->select('memberships.*, member_profiles.id as profile_id, member_profiles.full_name, member_profiles.display_name, member_profiles.username as profile_username, member_profiles.member_type, member_profiles.business_name, member_profiles.city, member_profiles.province, member_profiles.category_id, member_profiles.photo_path, member_profiles.company_logo_path, member_profiles.whatsapp, member_profiles.is_public, users.username as account_username, auth_identities.secret as email, event_categories.name as category_name')
            ->join('users', 'users.id = memberships.user_id')
            ->join('member_profiles', 'member_profiles.user_id = memberships.user_id', 'left')
            ->join('auth_identities', "auth_identities.user_id = memberships.user_id AND auth_identities.type = 'email_password'", 'left')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left');

        // Status filter
        if (! empty($filters['status']) && in_array($filters['status'], ['pending', 'active', 'rejected', 'suspended', 'expired'], true)) {
            $this->where('memberships.status', $filters['status']);
        }

        // Category filter
        if (! empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            $this->where('member_profiles.category_id', (int) $filters['category_id']);
        }

        // Location filter (city or province)
        if (! empty($filters['location'])) {
            $loc = trim($filters['location']);
            $this->groupStart()
                ->like('member_profiles.city', $loc)
                ->orLike('member_profiles.province', $loc)
                ->groupEnd();
        }

        // Search query
        if (! empty($filters['q'])) {
            $q = trim($filters['q']);
            $this->groupStart()
                ->like('member_profiles.full_name', $q)
                ->orLike('member_profiles.display_name', $q)
                ->orLike('member_profiles.business_name', $q)
                ->orLike('memberships.member_number', $q)
                ->orLike('users.username', $q)
                ->orLike('auth_identities.secret', $q)
                ->groupEnd();
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        if ($sort === 'oldest') {
            $this->orderBy('memberships.created_at', 'ASC');
        } elseif ($sort === 'name_asc') {
            $this->orderBy('member_profiles.full_name', 'ASC');
        } elseif ($sort === 'member_number') {
            $this->orderBy('memberships.member_number', 'ASC');
        } else {
            // Default latest
            $this->orderBy('memberships.created_at', 'DESC');
        }

        return $this;
    }

    /**
     * Get single member details by membership ID for admin view
     *
     * @param int $membershipId
     * @return array|null
     */
    public function getDetailedMember(int $membershipId): ?array
    {
        $member = $this->select('memberships.*, member_profiles.id as profile_id, member_profiles.full_name, member_profiles.display_name, member_profiles.username as profile_username, member_profiles.member_type, member_profiles.business_name, member_profiles.business_description, member_profiles.years_of_experience, member_profiles.bio, member_profiles.city, member_profiles.province, member_profiles.category_id, member_profiles.photo_path, member_profiles.company_logo_path, member_profiles.whatsapp, member_profiles.instagram, member_profiles.tiktok, member_profiles.linkedin, member_profiles.youtube, member_profiles.website, member_profiles.is_public, member_profiles.show_whatsapp, member_profiles.show_social, member_profiles.show_location, users.username as account_username, users.active as account_active, auth_identities.secret as email, event_categories.name as category_name, admin_user.username as approver_username')
            ->join('users', 'users.id = memberships.user_id')
            ->join('member_profiles', 'member_profiles.user_id = memberships.user_id', 'left')
            ->join('auth_identities', "auth_identities.user_id = memberships.user_id AND auth_identities.type = 'email_password'", 'left')
            ->join('event_categories', 'event_categories.id = member_profiles.category_id', 'left')
            ->join('users as admin_user', 'admin_user.id = memberships.approved_by', 'left')
            ->where('memberships.id', $membershipId)
            ->asArray()
            ->first();

        return $member ?: null;
    }
}
