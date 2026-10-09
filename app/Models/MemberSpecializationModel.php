<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberSpecializationModel extends Model
{
    protected $table         = 'member_specializations';
    protected $primaryKey    = 'member_profile_id';
    protected $returnType    = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'member_profile_id',
        'specialization_id',
        'created_at',
    ];

    /**
     * Get specialization IDs assigned to a member profile
     *
     * @return int[]
     */
    public function getSpecializationIds(int $profileId): array
    {
        $rows = $this->select('specialization_id')
            ->where('member_profile_id', $profileId)
            ->findAll();

        return array_map(static fn($r) => (int) $r['specialization_id'], $rows);
    }

    /**
     * Get specialization records assigned to a member profile
     */
    public function getSpecializationsByProfileId(int $profileId): array
    {
        return $this->select('specializations.id, specializations.name, specializations.slug, specializations.sort_order')
            ->join('specializations', 'specializations.id = member_specializations.specialization_id')
            ->where('member_specializations.member_profile_id', $profileId)
            ->where('specializations.is_active', 1)
            ->orderBy('specializations.sort_order', 'ASC')
            ->orderBy('specializations.name', 'ASC')
            ->findAll();
    }

    /**
     * Synchronize specializations for a member profile
     *
     * @param int $profileId
     * @param int[] $specIds
     */
    public function syncSpecializations(int $profileId, array $specIds): void
    {
        // Delete existing relations
        $this->where('member_profile_id', $profileId)->delete();

        if (empty($specIds)) {
            return;
        }

        // Clean & unique
        $specIds = array_values(array_unique(array_filter(array_map('intval', $specIds))));

        $now = date('Y-m-d H:i:s');
        $batch = [];
        foreach ($specIds as $id) {
            if ($id > 0) {
                $batch[] = [
                    'member_profile_id' => $profileId,
                    'specialization_id' => $id,
                    'created_at'        => $now,
                ];
            }
        }

        if (! empty($batch)) {
            $this->insertBatch($batch);
        }
    }
}
