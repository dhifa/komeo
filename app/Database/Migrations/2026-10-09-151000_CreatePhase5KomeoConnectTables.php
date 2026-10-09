<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase5KomeoConnectTables extends Migration
{
    public function up()
    {
        // 1. member_documents (CV and Portfolio documents)
        if (! $this->db->tableExists('member_documents')) {
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
                'document_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['cv', 'portfolio_pdf', 'portfolio_external'],
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_size' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'mime_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'external_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                ],
                'external_platform' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'visibility' => [
                    'type'       => 'ENUM',
                    'constraint' => ['public', 'request_only', 'private'],
                    'default'    => 'private',
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
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
            $this->forge->addKey(['user_id', 'document_type']);
            $this->forge->addKey('visibility');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('member_documents', true);
        }

        // 2. member_inquiries (Client-to-member direct inquiries)
        if (! $this->db->tableExists('member_inquiries')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'target_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'client_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'client_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'client_whatsapp' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'client_organization' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'inquiry_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['job_offer', 'cv_request', 'portfolio_request', 'collaboration', 'general'],
                ],
                'subject' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'initial_message' => [
                    'type' => 'LONGTEXT',
                ],
                'event_location' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'event_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'is_email_verified' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['new', 'in_progress', 'replied', 'closed', 'spam'],
                    'default'    => 'new',
                ],
                'client_access_token_selector' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'unique'     => true,
                ],
                'client_access_token_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                ],
                'last_activity_at' => [
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
            $this->forge->addKey(['target_user_id', 'status']);
            $this->forge->addKey('client_email');
            $this->forge->addForeignKey('target_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('member_inquiries', true);
        }

        // 3. inquiry_messages (Threaded conversation messages)
        if (! $this->db->tableExists('inquiry_messages')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'inquiry_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'sender_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['client', 'member'],
                ],
                'sender_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'message' => [
                    'type' => 'LONGTEXT',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('inquiry_id');
            $this->forge->addForeignKey('inquiry_id', 'member_inquiries', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('inquiry_messages', true);
        }

        // 4. document_shares (Cryptographically secure shared document links)
        if (! $this->db->tableExists('document_shares')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'document_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'inquiry_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'shared_by_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'recipient_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'share_token_selector' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'unique'     => true,
                ],
                'share_token_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                ],
                'expires_at' => [
                    'type' => 'DATETIME',
                ],
                'is_revoked' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'revoked_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'access_count' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
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
            $this->forge->addKey('document_id');
            $this->forge->addKey('inquiry_id');
            $this->forge->addKey('recipient_email');
            $this->forge->addForeignKey('document_id', 'member_documents', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('inquiry_id', 'member_inquiries', 'id', 'CASCADE', 'SET NULL');
            $this->forge->addForeignKey('shared_by_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('document_shares', true);
        }

        // 5. document_access_logs
        if (! $this->db->tableExists('document_access_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'document_share_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'accessed_at' => [
                    'type' => 'DATETIME',
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('document_share_id');
            $this->forge->addKey('accessed_at');
            $this->forge->addForeignKey('document_share_id', 'document_shares', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('document_access_logs', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('document_access_logs', true);
        $this->forge->dropTable('document_shares', true);
        $this->forge->dropTable('inquiry_messages', true);
        $this->forge->dropTable('member_inquiries', true);
        $this->forge->dropTable('member_documents', true);
    }
}
