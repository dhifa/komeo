<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase5ExtensionIdentityVerificationTables extends Migration
{
    public function up()
    {
        // 1. Table member_verifications
        if (! $this->db->tableExists('member_verifications')) {
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
                ],
                'verification_subject_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['individual', 'business'],
                    'default'    => 'individual',
                ],
                'verification_method' => [
                    'type'       => 'ENUM',
                    'constraint' => ['ktp_selfie', 'pic_only', 'nib_pic'],
                    'default'    => 'ktp_selfie',
                ],
                'verification_level' => [
                    'type'       => 'ENUM',
                    'constraint' => ['none', 'identity_verified', 'pic_verified', 'business_verified'],
                    'default'    => 'none',
                ],
                'business_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'pic_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'nib_document_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'ktp_document_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'selfie_document_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'verification_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['unverified', 'pending', 'approved', 'rejected', 'revoked'],
                    'default'    => 'unverified',
                ],
                'rejection_reason' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'admin_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'consent_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'submitted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'reviewed_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'reviewed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'revoked_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'revoked_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'documents_deleted_at' => [
                    'type' => 'DATETIME',
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
            $this->forge->addUniqueKey('user_id');
            $this->forge->addKey('verification_status');
            $this->forge->addKey('verification_level');
            $this->forge->addKey('verification_method');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->addForeignKey('revoked_by', 'users', 'id', 'SET NULL', 'CASCADE');
            $this->forge->createTable('member_verifications', true);
        }

        // 2. Table member_verification_history
        if (! $this->db->tableExists('member_verification_history')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'verification_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'action' => [
                    'type'       => 'ENUM',
                    'constraint' => ['submitted', 'resubmitted', 'approved', 'rejected', 'revoked', 'documents_deleted'],
                ],
                'previous_status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'new_status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'verification_level' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'actor_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'actor_role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('verification_id');
            $this->forge->addKey('user_id');
            $this->forge->addForeignKey('verification_id', 'member_verifications', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('member_verification_history', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('member_verification_history')) {
            $this->forge->dropTable('member_verification_history', true);
        }
        if ($this->db->tableExists('member_verifications')) {
            $this->forge->dropTable('member_verifications', true);
        }
    }
}
