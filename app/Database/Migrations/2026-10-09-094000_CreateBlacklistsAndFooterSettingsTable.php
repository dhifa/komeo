<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlacklistsAndFooterSettingsTable extends Migration
{
    public function up()
    {
        // 1. Create event_blacklists table
        if (! $this->db->tableExists('event_blacklists')) {
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
                'entity_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'vendor',
                    'comment'    => 'vendor, eo, freelance, lainnya',
                ],
                'city' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'case_category' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'Wanprestasi',
                    'comment'    => 'Wanprestasi, Gagal Bayar, Penipuan DP, dsb',
                ],
                'incident_date' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'description' => [
                    'type' => 'TEXT',
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'blacklisted',
                    'comment'    => 'blacklisted, monitoring, resolved',
                ],
                'evidence_notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'is_public' => [
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
            $this->forge->addKey('entity_type');
            $this->forge->addKey('status');
            $this->forge->addKey('is_public');
            $this->forge->createTable('event_blacklists', true);

            // Seed initial verified blacklist data
            $now = date('Y-m-d H:i:s');
            $this->db->table('event_blacklists')->insertBatch([
                [
                    'name'           => 'CV Cahaya Megah Production (Fiktif/Ex-Vendor)',
                    'entity_type'    => 'vendor',
                    'city'           => 'Surabaya',
                    'case_category'  => 'Gagal Bayar Vendor Subkon',
                    'incident_date'  => 'Agustus 2026',
                    'description'    => 'Tidak menyelesaikan pembayaran sisa pelunasan rental lighting & LED panggung pada 3 event beruntun tanpa ada itikad baik.',
                    'status'         => 'blacklisted',
                    'evidence_notes' => 'Surat somasi resmi ke-3 dan bukti invoice SPK bermaterai.',
                    'is_public'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'name'           => 'Bintang Organizer Project',
                    'entity_type'    => 'eo',
                    'city'           => 'Bandung',
                    'case_category'  => 'Wanprestasi & Pembatalan Sepihak',
                    'incident_date'  => 'Juli 2026',
                    'description'    => 'Membatalkan job kru multimedia H-1 tanpa kompensasi dan membawa lari dana operasional produksi talent.',
                    'status'         => 'blacklisted',
                    'evidence_notes' => 'Laporan kolektif 8 freelancer kru dan bukti chat kesepakatan kerja.',
                    'is_public'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
                [
                    'name'           => 'Rian "VJ Swift" (Nama Panggung)',
                    'entity_type'    => 'freelance',
                    'city'           => 'Jakarta Barat',
                    'case_category'  => 'Indisipliner Fatal & Kerusakan Alat',
                    'incident_date'  => 'September 2026',
                    'description'    => 'Meninggalkan booth visual saat konser berlangsung serta merusak server resolume milik rental tanpa ganti rugi.',
                    'status'         => 'monitoring',
                    'evidence_notes' => 'Berita acara kerusakan teknis dan rekaman CCTV venue.',
                    'is_public'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ],
            ]);
        }

        // 2. Insert default settings for Footer and Homepage Blacklist if not exists
        $settingsTable = $this->db->table('settings');
        $defaultSettings = [
            'App.footer_description'              => "Satu Komunitas, Ribuan Peluang Kolaborasi.\nWadah resmi kolaborasi profesional ekosistem industri event di Indonesia: Event Organizer, Wedding Organizer, Vendor Teknis, Kreatif, dan Talenta.",
            'App.footer_subtext'                  => 'Dibangun untuk kemajuan, etika, dan transparansi ekosistem event Indonesia.',
            'App.copyright_text'                  => '© ' . date('Y') . ' KOMEO.ID. Hak Cipta Dilindungi Undang-Undang.',
            'Homepage.section_blacklist_visible' => '1',
            'Homepage.blacklist_title'           => 'Daftar Peringatan & Blacklist Industri Event',
            'Homepage.blacklist_subtitle'        => 'Informasi perlindungan komunitas: daftar entitas vendor, EO, dan freelance yang terbukti wanprestasi fatal, gagal bayar, atau melanggar kode etik kerja resmi.',
        ];

        foreach ($defaultSettings as $key => $val) {
            $exists = $settingsTable->where('key', $key)->countAllResults();
            if ($exists === 0) {
                $settingsTable->insert([
                    'class'      => str_starts_with($key, 'App.') ? 'App' : 'Homepage',
                    'key'        => $key,
                    'value'      => $val,
                    'type'       => 'string',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('event_blacklists', true);
    }
}
