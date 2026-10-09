<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlacklistCaseCategoriesTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('blacklist_case_categories')) {
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
                    'constraint' => 120,
                    'null'       => true,
                ],
                'description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
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
            $this->forge->createTable('blacklist_case_categories', true);

            // Seed default case categories
            $now = date('Y-m-d H:i:s');
            $defaultCategories = [
                [
                    'name'        => 'Gagal Bayar / Pembayaran Macet',
                    'slug'        => 'gagal-bayar',
                    'description' => 'Tidak melunasi kewajiban pembayaran fee vendor, crew, atau penyewaan sesuai kesepakatan.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Bawa Kabur DP / Penggelapan Dana',
                    'slug'        => 'bawa-kabur-dp',
                    'description' => 'Menerima pembayaran uang muka (DP) namun tidak mengerjakan kewajiban atau melarikan diri.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Wanprestasi Kontrak / SPK',
                    'slug'        => 'wanprestasi-kontrak',
                    'description' => 'Melanggar klausul perjanjian kerjasama kerja, ingkar janji spesifikasi teknis, atau membatalkan sepihak.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Barang Rental Rusak / Hilang',
                    'slug'        => 'barang-rusak-hilang',
                    'description' => 'Merusak atau menghilangkan aset peralatan rental/event tanpa ada itikad baik pertanggungjawaban/ganti rugi.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Tidak Hadir Tanpa Kabar (No Show / Ghosting)',
                    'slug'        => 'no-show-ghosting',
                    'description' => 'Tidak hadir pada hari H acara tanpa pemberitahuan atau alasan darurat sah, menyebabkan acara terkendala.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Penipuan Identitas & Portofolio Palsu',
                    'slug'        => 'penipuan-portofolio',
                    'description' => 'Mengklaim karya atau kepemilikan alat orang lain untuk mengelabui klien atau sesama vendor.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Pelanggaran Etika & Disiplin Event',
                    'slug'        => 'pelanggaran-etika',
                    'description' => 'Tindakan indisipliner berat yang mencemarkan nama baik penyelenggara atau mitra event.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
                [
                    'name'        => 'Lainnya',
                    'slug'        => 'lainnya',
                    'description' => 'Kasus pelanggaran lain yang telah diverifikasi dan memiliki bukti sah.',
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ],
            ];

            $this->db->table('blacklist_case_categories')->insertBatch($defaultCategories);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('blacklist_case_categories')) {
            $this->forge->dropTable('blacklist_case_categories', true);
        }
    }
}
