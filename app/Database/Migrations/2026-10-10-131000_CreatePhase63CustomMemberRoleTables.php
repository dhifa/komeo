<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase63CustomMemberRoleTables extends Migration
{
    public function up()
    {
        // 1. komeo_member_roles Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'role_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'user',
            ],
            'background_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '#4F46E5',
            ],
            'text_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '#FFFFFF',
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
            'is_public' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
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
        $this->forge->addUniqueKey('role_key');
        $this->forge->addKey('sort_order');
        $this->forge->addKey('is_active');
        $this->forge->addKey('is_default');
        $this->forge->createTable('komeo_member_roles', true);

        // 2. komeo_member_role_assignments Table
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
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'assigned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revoked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revoked_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'internal_note' => [
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('role_id');
        $this->forge->addKey('is_primary');
        $this->forge->addKey('expires_at');
        $this->forge->addKey('revoked_at');
        $this->forge->createTable('komeo_member_role_assignments', true);

        // Seed default community roles if empty
        $db = \Config\Database::connect();
        $builder = $db->table('komeo_member_roles');
        if ($builder->countAllResults() === 0) {
            $defaultRoles = [
                [
                    'role_key'         => 'anggota-reguler',
                    'name'             => 'Anggota Reguler',
                    'description'      => 'Anggota resmi komunitas KOMEO yang telah melalui verifikasi keanggotaan.',
                    'icon'             => 'user-check',
                    'background_color' => '#4F46E5',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 1,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 1,
                ],
                [
                    'role_key'         => 'pengurus-komeo',
                    'name'             => 'Pengurus KOMEO',
                    'description'      => 'Pengurus inti dan penggerak operasional komunitas KOMEO Indonesia.',
                    'icon'             => 'shield',
                    'background_color' => '#7C3AED',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 2,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'koordinator-daerah',
                    'name'             => 'Koordinator Daerah',
                    'description'      => 'Koordinator wilayah dan perwakilan resmi komunitas KOMEO di daerah.',
                    'icon'             => 'map-pin',
                    'background_color' => '#0284C7',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 3,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'komeo-ambassador',
                    'name'             => 'KOMEO Ambassador',
                    'description'      => 'Duta perwakilan komunitas KOMEO untuk kolaborasi dan sosialisasi industri event.',
                    'icon'             => 'star',
                    'background_color' => '#EA580C',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 4,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'community-partner',
                    'name'             => 'Community Partner',
                    'description'      => 'Mitra kolaboratif komunitas dan asosiasi pendukung ekosistem KOMEO.',
                    'icon'             => 'handshake',
                    'background_color' => '#0D9488',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 5,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'vendor-partner',
                    'name'             => 'Vendor Partner',
                    'description'      => 'Mitra strategis vendor penyedia layanan dan perlengkapan event terpercaya.',
                    'icon'             => 'briefcase',
                    'background_color' => '#2563EB',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 6,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'anggota-kehormatan',
                    'name'             => 'Anggota Kehormatan',
                    'description'      => 'Tokoh senior dan penasihat kehormatan dalam industri event Indonesia.',
                    'icon'             => 'award',
                    'background_color' => '#059669',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 7,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'member-vip',
                    'name'             => 'Member VIP',
                    'description'      => 'Anggota prioritas dengan kontribusi istimewa dalam jejaring KOMEO.',
                    'icon'             => 'crown',
                    'background_color' => '#D97706',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 8,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
                [
                    'role_key'         => 'kontributor-komunitas',
                    'name'             => 'Kontributor Komunitas',
                    'description'      => 'Kontributor aktif materi edukasi, wawasan, dan kegiatan komunitas.',
                    'icon'             => 'users',
                    'background_color' => '#4B5563',
                    'text_color'       => '#FFFFFF',
                    'sort_order'       => 9,
                    'is_active'        => 1,
                    'is_public'        => 1,
                    'is_default'       => 0,
                ],
            ];
            $now = date('Y-m-d H:i:s');
            foreach ($defaultRoles as $r) {
                $r['created_at'] = $now;
                $r['updated_at'] = $now;
                $builder->insert($r);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('komeo_member_role_assignments', true);
        $this->forge->dropTable('komeo_member_roles', true);
    }
}
