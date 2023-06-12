<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'user_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'images' => ['type' => 'TEXT'],
            'business_id' => ['type' => 'INT', 'constraint' => 25],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);

        $this->forge->addPrimaryKey('product_id');
        $this->forge->addForeignKey('business_id', 'businesses', 'business_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
