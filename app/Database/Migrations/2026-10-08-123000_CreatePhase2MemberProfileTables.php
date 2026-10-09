<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePhase2MemberProfileTables extends Migration
{
    public function up()
    {
        // 1. Create table `event_categories`
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
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
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
        $this->forge->createTable('event_categories', true);

        // 2. Create table `specializations`
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addKey('category_id');
        $this->forge->addKey('is_active');
        $this->forge->addForeignKey('category_id', 'event_categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('specializations', true);

        // 3. Add additive columns to `member_profiles`
        $columnsToAdd = [
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'member_type',
            ],
            'business_description' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'business_name',
            ],
            'years_of_experience' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'business_description',
            ],
            'company_logo_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'photo_path',
            ],
            'youtube' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'linkedin',
            ],
            'show_whatsapp' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'is_public',
            ],
            'show_social' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'show_whatsapp',
            ],
            'show_location' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'show_social',
            ],
        ];
        $this->forge->addColumn('member_profiles', $columnsToAdd);

        // Add foreign key from member_profiles.category_id to event_categories.id
        $this->db->query('ALTER TABLE `member_profiles` ADD CONSTRAINT `fk_member_profiles_category` FOREIGN KEY (`category_id`) REFERENCES `event_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        // 4. Create table `member_specializations` (Many-to-Many)
        $this->forge->addField([
            'member_profile_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'specialization_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey(['member_profile_id', 'specialization_id'], true);
        $this->forge->addForeignKey('member_profile_id', 'member_profiles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('specialization_id', 'specializations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_specializations', true);

        // 5. Create table `member_portfolios`
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_profile_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'project_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'event_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'cover_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'external_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey('member_profile_id');
        $this->forge->addKey('sort_order');
        $this->forge->addForeignKey('member_profile_id', 'member_profiles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('member_portfolios', true);

        // 6. Seed Default Categories
        $defaultCategories = [
            ['name' => 'Event Organizer',           'slug' => 'event-organizer',           'icon' => 'briefcase', 'sort_order' => 1],
            ['name' => 'Wedding Organizer',         'slug' => 'wedding-organizer',         'icon' => 'heart',     'sort_order' => 2],
            ['name' => 'Event Production',          'slug' => 'event-production',          'icon' => 'cog',       'sort_order' => 3],
            ['name' => 'Audio & Sound System',      'slug' => 'audio-sound-system',        'icon' => 'volume-up', 'sort_order' => 4],
            ['name' => 'Lighting',                  'slug' => 'lighting',                  'icon' => 'light-bulb','sort_order' => 5],
            ['name' => 'LED & Multimedia',          'slug' => 'led-multimedia',            'icon' => 'desktop',   'sort_order' => 6],
            ['name' => 'Live Streaming',            'slug' => 'live-streaming',            'icon' => 'video',     'sort_order' => 7],
            ['name' => 'Photography & Videography', 'slug' => 'photography-videography',   'icon' => 'camera',    'sort_order' => 8],
            ['name' => 'Stage & Rigging',           'slug' => 'stage-rigging',             'icon' => 'cube',      'sort_order' => 9],
            ['name' => 'Decoration',                'slug' => 'decoration',                'icon' => 'sparkles',  'sort_order' => 10],
            ['name' => 'Food & Beverage',           'slug' => 'food-beverage',             'icon' => 'cake',      'sort_order' => 11],
            ['name' => 'Talent & Entertainment',    'slug' => 'talent-entertainment',      'icon' => 'music',     'sort_order' => 12],
            ['name' => 'Freelancer',                'slug' => 'freelancer',                'icon' => 'user',      'sort_order' => 13],
            ['name' => 'Other',                     'slug' => 'other',                     'icon' => 'dots',      'sort_order' => 14],
        ];
        $now = date('Y-m-d H:i:s');
        foreach ($defaultCategories as &$cat) {
            $cat['is_active']  = 1;
            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
        }
        $this->db->table('event_categories')->insertBatch($defaultCategories);

        // 7. Seed Default Specializations
        $defaultSpecializations = [
            ['name' => 'vMix Operator',        'slug' => 'vmix-operator',        'sort_order' => 1],
            ['name' => 'VJ Resolume',          'slug' => 'vj-resolume',          'sort_order' => 2],
            ['name' => 'Camera Operator',      'slug' => 'camera-operator',      'sort_order' => 3],
            ['name' => 'Photographer',         'slug' => 'photographer',         'sort_order' => 4],
            ['name' => 'Videographer',         'slug' => 'videographer',         'sort_order' => 5],
            ['name' => 'Lighting Operator',    'slug' => 'lighting-operator',    'sort_order' => 6],
            ['name' => 'Sound Engineer',       'slug' => 'sound-engineer',       'sort_order' => 7],
            ['name' => 'LED Technician',       'slug' => 'led-technician',       'sort_order' => 8],
            ['name' => 'Stage Manager',        'slug' => 'stage-manager',        'sort_order' => 9],
            ['name' => 'Event Planner',        'slug' => 'event-planner',        'sort_order' => 10],
            ['name' => 'Event Crew',           'slug' => 'event-crew',           'sort_order' => 11],
            ['name' => 'Master of Ceremony',   'slug' => 'master-of-ceremony',   'sort_order' => 12],
            ['name' => 'Talent & Performer',   'slug' => 'talent-performer',     'sort_order' => 13],
            ['name' => 'Wedding Planner',      'slug' => 'wedding-planner',      'sort_order' => 14],
            ['name' => 'Show Director',        'slug' => 'show-director',        'sort_order' => 15],
            ['name' => 'Graphic & Motion Designer', 'slug' => 'graphic-motion-designer', 'sort_order' => 16],
            ['name' => 'Livestream Audio Engineer', 'slug' => 'livestream-audio-engineer', 'sort_order' => 17],
            ['name' => 'Technical Director',   'slug' => 'technical-director',   'sort_order' => 18],
        ];
        foreach ($defaultSpecializations as &$spec) {
            $spec['is_active']  = 1;
            $spec['created_at'] = $now;
            $spec['updated_at'] = $now;
        }
        $this->db->table('specializations')->insertBatch($defaultSpecializations);
    }

    public function down()
    {
        $this->forge->dropTable('member_portfolios', true);
        $this->forge->dropTable('member_specializations', true);

        // Remove foreign key and added columns from member_profiles
        if ($this->db->fieldExists('category_id', 'member_profiles')) {
            $this->db->query('ALTER TABLE `member_profiles` DROP FOREIGN KEY `fk_member_profiles_category`');
            $this->forge->dropColumn('member_profiles', [
                'category_id',
                'business_description',
                'years_of_experience',
                'company_logo_path',
                'youtube',
                'show_whatsapp',
                'show_social',
                'show_location',
            ]);
        }

        $this->forge->dropTable('specializations', true);
        $this->forge->dropTable('event_categories', true);
    }
}
