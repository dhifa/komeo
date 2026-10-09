<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase5SharedAndEventTables extends Migration
{
    public function up()
    {
        // 1. verification_challenges (Shared email & token verification for guests and inquiries)
        if (! $this->db->tableExists('verification_challenges')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'token_selector' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                ],
                'token_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                ],
                'context' => [
                    'type'       => 'ENUM',
                    'constraint' => ['event_registration', 'client_inquiry'],
                ],
                'reference_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'attempts' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'default'    => 0,
                ],
                'max_attempts' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'default'    => 5,
                ],
                'expires_at' => [
                    'type' => 'DATETIME',
                ],
                'verified_at' => [
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
            $this->forge->addKey('token_selector');
            $this->forge->addKey(['context', 'reference_id']);
            $this->forge->addKey('email');
            $this->forge->createTable('verification_challenges', true);
        }

        // 2. audit_logs (Shared audit & activity logging)
        if (! $this->db->tableExists('audit_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'action' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'entity_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'entity_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
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
                'details' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('action');
            $this->forge->addKey(['entity_type', 'entity_id']);
            $this->forge->createTable('audit_logs', true);
        }

        // 3. email_logs (Shared email tracking & status)
        if (! $this->db->tableExists('email_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'recipient_email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'subject' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'template' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['sent', 'failed', 'queued'],
                    'default'    => 'sent',
                ],
                'error_message' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('recipient_email');
            $this->forge->addKey('status');
            $this->forge->createTable('email_logs', true);
        }

        // 4. events table
        if (! $this->db->tableExists('events')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'slug' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'unique'     => true,
                ],
                'description' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'banner_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'venue_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'start_date' => [
                    'type' => 'DATETIME',
                ],
                'end_date' => [
                    'type' => 'DATETIME',
                ],
                'timezone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'Asia/Jakarta',
                ],
                'event_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['internal', 'external', 'hybrid'],
                    'default'    => 'hybrid',
                ],
                'reg_start_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'reg_end_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'checkin_start_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'checkin_end_date' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'total_quota' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'member_quota' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'external_quota' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'requires_approval' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'is_registration_open' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'allow_kta_checkin' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draft', 'published', 'cancelled', 'completed'],
                    'default'    => 'draft',
                ],
                'form_config' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'confirmation_message' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'privacy_consent_text' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
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
            $this->forge->addKey('status');
            $this->forge->addKey('event_type');
            $this->forge->addKey('start_date');
            $this->forge->createTable('events', true);
        }

        // 5. event_guests table (external participants without user accounts)
        if (! $this->db->tableExists('event_guests')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'whatsapp' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                ],
                'company' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'job_title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'profession_category' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'province' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'is_email_verified' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'verified_at' => [
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
            $this->forge->addKey('email');
            $this->forge->createTable('event_guests', true);
        }

        // 6. event_registrations
        if (! $this->db->tableExists('event_registrations')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'registration_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'unique'     => true,
                ],
                'participant_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['member', 'external'],
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'guest_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending_verification', 'pending_approval', 'confirmed', 'rejected', 'cancelled'],
                    'default'    => 'pending_verification',
                ],
                'ticket_token_selector' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'unique'     => true,
                ],
                'ticket_token_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                ],
                'custom_data' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'registered_at' => [
                    'type' => 'DATETIME',
                ],
                'confirmed_at' => [
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
            $this->forge->addKey('event_id');
            $this->forge->addKey('user_id');
            $this->forge->addKey('guest_id');
            $this->forge->addKey('status');
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
            $this->forge->addForeignKey('guest_id', 'event_guests', 'id', 'CASCADE', 'SET NULL');
            $this->forge->createTable('event_registrations', true);
        }

        // 7. event_attendance
        if (! $this->db->tableExists('event_attendance')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'registration_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'unique'     => true,
                ],
                'checkin_method' => [
                    'type'       => 'ENUM',
                    'constraint' => ['qr_ticket', 'kta_qr', 'manual'],
                ],
                'checked_in_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'checked_in_at' => [
                    'type' => 'DATETIME',
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('event_id');
            $this->forge->addKey('checked_in_at');
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('registration_id', 'event_registrations', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('event_attendance', true);
        }

        // 8. event_staff_assignments
        if (! $this->db->tableExists('event_staff_assignments')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'assigned_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['event_id', 'user_id']);
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('event_staff_assignments', true);
        }

        // 9. event_checkin_logs
        if (! $this->db->tableExists('event_checkin_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'registration_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'scanned_token_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['event_ticket', 'kta_qr', 'manual'],
                ],
                'scanned_identifier' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['success', 'failed', 'duplicate', 'invalid'],
                ],
                'reason' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'staff_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['event_id', 'status']);
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('event_checkin_logs', true);
        }

        // 10. membership_verification_logs (KTA verification audit log)
        if (! $this->db->tableExists('membership_verification_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'membership_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'verification_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['public_qr', 'admin_scan', 'event_checkin'],
                ],
                'verified_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
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
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['valid', 'invalid', 'suspended'],
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['membership_id', 'verification_type']);
            $this->forge->addKey('created_at');
            $this->forge->addForeignKey('membership_id', 'memberships', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('membership_verification_logs', true);
        }
    }

    public function down()
    {
        $this->forge->dropTable('membership_verification_logs', true);
        $this->forge->dropTable('event_checkin_logs', true);
        $this->forge->dropTable('event_staff_assignments', true);
        $this->forge->dropTable('event_attendance', true);
        $this->forge->dropTable('event_registrations', true);
        $this->forge->dropTable('event_guests', true);
        $this->forge->dropTable('events', true);
        $this->forge->dropTable('email_logs', true);
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('verification_challenges', true);
    }
}
