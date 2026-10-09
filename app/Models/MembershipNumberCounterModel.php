<?php

namespace App\Models;

use CodeIgniter\Model;

class MembershipNumberCounterModel extends Model
{
    protected $table            = 'membership_number_counters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'year',
        'last_number',
        'updated_at',
    ];

    protected $useTimestamps = false;

    /**
     * Atomically allocate the next sequence number for a given year.
     * MUST be called within an active database transaction.
     *
     * @param int $year
     * @return int The newly allocated sequence number
     */
    public function allocateNextNumber(int $year): int
    {
        $db = $this->db;

        // 1. Ensure row exists for this year using INSERT IGNORE or check
        $row = $db->table($this->table)->where('year', $year)->get()->getRowArray();
        if (! $row) {
            $db->table($this->table)->insert([
                'year'        => $year,
                'last_number' => 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // 2. Lock the row for update
        $lockedRow = $db->query(
            "SELECT last_number FROM {$this->table} WHERE year = ? FOR UPDATE",
            [$year]
        )->getRowArray();

        $currentNumber = (int) ($lockedRow['last_number'] ?? 0);
        $nextNumber    = $currentNumber + 1;

        // 3. Increment the counter
        $db->table($this->table)
            ->where('year', $year)
            ->update([
                'last_number' => $nextNumber,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

        return $nextNumber;
    }
}
