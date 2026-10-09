<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemberFieldsToEventBlacklists extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('event_blacklists')) {
            $fields = [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'id',
                ],
                'membership_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'user_id',
                ],
                'member_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'membership_id',
                ],
            ];

            // Only add fields that don't already exist
            $existing = $this->db->getFieldNames('event_blacklists');
            $toAdd = [];
            foreach ($fields as $col => $attr) {
                if (! in_array($col, $existing, true)) {
                    $toAdd[$col] = $attr;
                }
            }

            if (! empty($toAdd)) {
                $this->forge->addColumn('event_blacklists', $toAdd);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('event_blacklists')) {
            $existing = $this->db->getFieldNames('event_blacklists');
            $toDrop = array_intersect(['user_id', 'membership_id', 'member_number'], $existing);
            if (! empty($toDrop)) {
                $this->forge->dropColumn('event_blacklists', $toDrop);
            }
        }
    }
}
