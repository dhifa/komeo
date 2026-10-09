<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase5DirectoryTables extends Migration
{
    public function up()
    {
        // 1. Add `is_featured` column to `member_profiles` if not exists
        if (! $this->db->fieldExists('is_featured', 'member_profiles')) {
            $this->forge->addColumn('member_profiles', [
                'is_featured' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'show_location',
                ],
            ]);
        }

        // 2. Add search performance indexes safely
        $table = 'member_profiles';
        $indexes = [
            'idx_mp_is_featured'    => 'is_featured',
            'idx_mp_category_id'    => 'category_id',
            'idx_mp_public_type'    => '`is_public`, `member_type`',
            'idx_mp_public_feat'    => '`is_public`, `is_featured`',
        ];

        foreach ($indexes as $indexName => $columns) {
            // Check if index already exists
            $indexCheck = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'")->getResult();
            if (empty($indexCheck)) {
                $this->db->query("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columns})");
            }
        }
    }

    public function down()
    {
        $table = 'member_profiles';
        $indexes = [
            'idx_mp_is_featured',
            'idx_mp_category_id',
            'idx_mp_public_type',
            'idx_mp_public_feat',
        ];

        foreach ($indexes as $indexName) {
            $indexCheck = $this->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'")->getResult();
            if (! empty($indexCheck)) {
                $this->db->query("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
            }
        }

        if ($this->db->fieldExists('is_featured', 'member_profiles')) {
            $this->forge->dropColumn('member_profiles', 'is_featured');
        }
    }
}
