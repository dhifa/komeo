<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase4KtaTables extends Migration
{
    public function up()
    {
        // 1. Add verification_token_selector and qr_generated_at to memberships table
        if ($this->db->tableExists('memberships')) {
            $fieldsToAdd = [];

            if (! $this->db->fieldExists('verification_token_selector', 'memberships')) {
                $fieldsToAdd['verification_token_selector'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'after'      => 'verification_token_hash',
                ];
            }

            if (! $this->db->fieldExists('qr_generated_at', 'memberships')) {
                $fieldsToAdd['qr_generated_at'] = [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'verification_token_selector',
                ];
            }

            if (! empty($fieldsToAdd)) {
                $this->forge->addColumn('memberships', $fieldsToAdd);
                $this->db->query('ALTER TABLE `memberships` ADD INDEX `idx_verification_token_selector` (`verification_token_selector`)');
            }
        }

        // 2. Create kta_export_batches table for mass printing & bulk download tracking
        if (! $this->db->tableExists('kta_export_batches')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'admin_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'batch_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                ],
                'export_mode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                ],
                'paper_size' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'member_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_size' => [
                    'type'       => 'BIGINT',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'completed',
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
            $this->forge->addUniqueKey('batch_code');
            $this->forge->addKey('admin_id');
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('admin_id', 'users', 'id', 'SET NULL', 'CASCADE');

            $this->forge->createTable('kta_export_batches', true);
        }

        // 3. Create kta_export_logs table for auditing
        if (! $this->db->tableExists('kta_export_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'membership_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'export_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                ],
                'user_agent' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('membership_id');
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('membership_id', 'memberships', 'id', 'SET NULL', 'CASCADE');

            $this->forge->createTable('kta_export_logs', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('kta_export_logs')) {
            $this->forge->dropTable('kta_export_logs', true);
        }

        if ($this->db->tableExists('kta_export_batches')) {
            $this->forge->dropTable('kta_export_batches', true);
        }

        if ($this->db->tableExists('memberships')) {
            if ($this->db->fieldExists('qr_generated_at', 'memberships')) {
                $this->forge->dropColumn('memberships', 'qr_generated_at');
            }
            if ($this->db->fieldExists('verification_token_selector', 'memberships')) {
                $this->forge->dropColumn('memberships', 'verification_token_selector');
            }
        }
    }
}
