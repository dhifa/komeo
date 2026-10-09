<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMemberWarningsTable extends Migration
{
    public function up()
    {
        // 1. Add active_warning_level column to memberships if not exists
        if (! $this->db->fieldExists('active_warning_level', 'memberships')) {
            $this->forge->addColumn('memberships', [
                'active_warning_level' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'status_notes',
                ],
            ]);
        }

        // 2. Create member_warnings table
        if (! $this->db->tableExists('member_warnings')) {
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
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'warning_level' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'sp1',
                    'comment'    => 'sp1, sp2, sp3',
                ],
                'reason' => [
                    'type' => 'TEXT',
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'issued_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'active',
                    'comment'    => 'active, resolved, revoked',
                ],
                'resolved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'resolved_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'resolution_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('membership_id');
            $this->forge->addKey('user_id');
            $this->forge->addKey('warning_level');
            $this->forge->addKey('status');

            // Foreign keys
            $this->forge->addForeignKey('membership_id', 'memberships', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('issued_by', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('resolved_by', 'users', 'id', 'SET NULL', 'CASCADE');

            $this->forge->createTable('member_warnings', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('member_warnings')) {
            $this->forge->dropTable('member_warnings', true);
        }

        if ($this->db->fieldExists('active_warning_level', 'memberships')) {
            $this->forge->dropColumn('memberships', 'active_warning_level');
        }
    }
}
