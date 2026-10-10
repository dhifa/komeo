<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberRoleModel extends Model
{
    protected $table            = 'komeo_member_roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'role_key',
        'name',
        'description',
        'icon',
        'background_color',
        'text_color',
        'sort_order',
        'is_active',
        'is_public',
        'is_default',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active roles ordered by sort order
     */
    public function getActiveRoles(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get default community role
     */
    public function getDefaultRole(): ?array
    {
        return $this->where('is_default', 1)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Get roles with member assignment statistics
     */
    public function getRolesWithStats(): array
    {
        $roles = $this->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $now = date('Y-m-d H:i:s');
        $db = $this->db;

        foreach ($roles as &$role) {
            $roleId = (int) $role['id'];

            // Active members count
            $activeCount = $db->table('komeo_member_role_assignments')
                ->where('role_id', $roleId)
                ->where('revoked_at IS NULL')
                ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >', $now)
                ->groupEnd()
                ->countAllResults();

            // Primary assignments count
            $primaryCount = $db->table('komeo_member_role_assignments')
                ->where('role_id', $roleId)
                ->where('is_primary', 1)
                ->where('revoked_at IS NULL')
                ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >', $now)
                ->groupEnd()
                ->countAllResults();

            $role['active_members_count'] = $activeCount;
            $role['primary_count']        = $primaryCount;
        }

        return $roles;
    }
}
