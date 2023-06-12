<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EventCategories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'event_category_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'event_category_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'event_category_name_marathi' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);

        $this->forge->addPrimaryKey('event_category_id');
        $this->forge->createTable('event_categories');
    }

    public function down()
    {
        $this->forge->dropTable('event_categories');
    }
}
