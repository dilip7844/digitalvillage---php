<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BusinessCategories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'business_category_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'business_category_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'business_category_name_marathi' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);

        $this->forge->addPrimaryKey('business_category_id');
        $this->forge->createTable('business_categories');
    }

    public function down()
    {
        $this->forge->dropTable('business_categories');
    }
}
