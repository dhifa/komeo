<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase3MembershipManagementTables extends Migration
{
    public function up()
    {
        // 1. Add reason and status note columns to memberships table safely
        $fieldsToAdd = [];
        if (! $this->db->fieldExists('rejection_reason', 'memberships')) {
            $fieldsToAdd['rejection_reason'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ];
        }
        if (! $this->db->fieldExists('suspension_reason', 'memberships')) {
            $fieldsToAdd['suspension_reason'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rejection_reason',
            ];
        }
        if (! $this->db->fieldExists('status_notes', 'memberships')) {
            $fieldsToAdd['status_notes'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'suspension_reason',
            ];
        }

        if (! empty($fieldsToAdd)) {
            $this->forge->addColumn('memberships', $fieldsToAdd);
        }

        // 2. Create membership_status_history table
        if (! $this->db->tableExists('membership_status_history')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'membership_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'previous_status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'new_status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'admin_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('membership_id');
            $this->forge->addKey('admin_id');
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('membership_id', 'memberships', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('admin_id', 'users', 'id', 'SET NULL', 'CASCADE');

            $this->forge->createTable('membership_status_history', true);
        }

        // 3. Create membership_number_counters table (atomic allocation for KMO-YYYY-XXXXXX)
        if (! $this->db->tableExists('membership_number_counters')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'year' => [
                    'type'       => 'INT',
                    'constraint' => 4,
                    'unsigned'   => true,
                ],
                'last_number' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('year');

            $this->forge->createTable('membership_number_counters', true);

            // Pre-seed current year counter (2026)
            $currentYear = (int) date('Y');
            $this->db->table('membership_number_counters')->insert([
                'year'        => $currentYear,
                'last_number' => 0,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('membership_number_counters', true);
        $this->forge->dropTable('membership_status_history', true);

        if ($this->db->fieldExists('rejection_reason', 'memberships')) {
            $this->forge->dropColumn('memberships', 'rejection_reason');
        }
        if ($this->db->fieldExists('suspension_reason', 'memberships')) {
            $this->forge->dropColumn('memberships', 'suspension_reason');
        }
        if ($this->db->fieldExists('status_notes', 'memberships')) {
            $this->forge->dropColumn('memberships', 'status_notes');
        }
    }
}
