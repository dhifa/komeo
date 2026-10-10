<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase63LiveTransactionTables extends Migration
{
    public function up()
    {
        // 1. komeo_transaction_categories Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'briefcase',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('sort_order');
        $this->forge->addKey('is_active');
        $this->forge->createTable('komeo_transaction_categories', true);

        // 2. komeo_transactions Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'public_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'internal_client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'internal_project_ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'IDR',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'agreed_dp_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'agreed_dp_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'dp_due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'final_due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'current_work_stage' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Transaksi Masuk',
            ],
            'payment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'UNPAID',
            ],
            'is_overdue' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'contract_issue_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'none',
            ],
            'amount_visibility' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'hide',
            ],
            'is_published' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'transaction_date' => [
                'type' => 'DATE',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('transaction_code');
        $this->forge->addKey('category_id');
        $this->forge->addKey('current_work_stage');
        $this->forge->addKey('payment_status');
        $this->forge->addKey('contract_issue_status');
        $this->forge->addKey('is_published');
        $this->forge->addKey('transaction_date');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('komeo_transactions', true);

        // 3. komeo_transaction_updates Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'update_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'work_stage',
            ],
            'previous_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'new_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'public_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'internal_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'visible_to_members' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('update_type');
        $this->forge->addKey('visible_to_members');
        $this->forge->createTable('komeo_transaction_updates', true);

        // 4. komeo_transaction_payments Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'payment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'payment_date' => [
                'type' => 'DATE',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'internal_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'recorded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'voided_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'voided_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'void_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('payment_type');
        $this->forge->addKey('payment_date');
        $this->forge->addKey('voided_at');
        $this->forge->createTable('komeo_transaction_payments', true);

        // 5. komeo_transaction_issues Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'issue_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'under_review',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'public_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Dalam Penanganan',
            ],
            'resolution_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reported_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'recorded_by' => [
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
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('status');
        $this->forge->createTable('komeo_transaction_issues', true);

        // 6. komeo_transaction_audit_logs Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'previous_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'new_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'admin_user_id' => [
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('action');
        $this->forge->addKey('admin_user_id');
        $this->forge->createTable('komeo_transaction_audit_logs', true);

        // Seed default transaction categories if empty
        $db = \Config\Database::connect();
        $builder = $db->table('komeo_transaction_categories');
        if ($builder->countAllResults() === 0) {
            $defaultCategories = [
                ['name' => 'Event Production', 'slug' => 'event-production', 'icon' => 'calendar', 'sort_order' => 1],
                ['name' => 'Live Streaming', 'slug' => 'live-streaming', 'icon' => 'video', 'sort_order' => 2],
                ['name' => 'Multimedia System', 'slug' => 'multimedia-system', 'icon' => 'tv', 'sort_order' => 3],
                ['name' => 'LED Screen', 'slug' => 'led-screen', 'icon' => 'monitor', 'sort_order' => 4],
                ['name' => 'Event Organizer', 'slug' => 'event-organizer', 'icon' => 'users', 'sort_order' => 5],
                ['name' => 'Freelancer Crew', 'slug' => 'freelancer-crew', 'icon' => 'user-check', 'sort_order' => 6],
                ['name' => 'Sponsorship', 'slug' => 'sponsorship', 'icon' => 'award', 'sort_order' => 7],
                ['name' => 'Vendor Collaboration', 'slug' => 'vendor-collaboration', 'icon' => 'handshake', 'sort_order' => 8],
                ['name' => 'Lainnya', 'slug' => 'lainnya', 'icon' => 'more-horizontal', 'sort_order' => 9],
            ];
            $now = date('Y-m-d H:i:s');
            foreach ($defaultCategories as $cat) {
                $cat['is_active'] = 1;
                $cat['created_at'] = $now;
                $cat['updated_at'] = $now;
                $builder->insert($cat);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('komeo_transaction_audit_logs', true);
        $this->forge->dropTable('komeo_transaction_issues', true);
        $this->forge->dropTable('komeo_transaction_payments', true);
        $this->forge->dropTable('komeo_transaction_updates', true);
        $this->forge->dropTable('komeo_transactions', true);
        $this->forge->dropTable('komeo_transaction_categories', true);
    }
}
