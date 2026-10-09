<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHomepageBenefitsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'users',
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
        $this->forge->addKey('sort_order');
        $this->forge->addKey('is_active');

        $this->forge->createTable('homepage_benefits', true);

        // Seed initial default benefits to preserve existing homepage content
        $now = date('Y-m-d H:i:s');
        $this->db->table('homepage_benefits')->insertBatch([
            [
                'title'       => 'Jejaring Lintas Kota',
                'description' => 'Terhubung dengan ribuan praktisi event dari berbagai kota di Indonesia. Lebih mudah mencari rekanan ketika menggarap event di luar kota asal Anda.',
                'icon'        => 'users',
                'sort_order'  => 1,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'title'       => 'Promosi Portofolio',
                'description' => 'Tampilkan keahlian, legalitas bisnis, dan hasil karya Anda kepada calon klien, wedding planner, dan EO yang secara aktif mencari penyedia jasa terverifikasi.',
                'icon'        => 'briefcase',
                'sort_order'  => 2,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'title'       => 'KTA Digital Resmi',
                'description' => 'Dapatkan Kartu Tanda Anggota (KTA) digital dengan QR code verifikasi instan sebagai bukti keanggotaan dan kredibilitas profesional Anda di industri.',
                'icon'        => 'badge-check',
                'sort_order'  => 3,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('homepage_benefits', true);
    }
}
