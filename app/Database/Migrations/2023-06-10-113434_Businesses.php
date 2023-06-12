<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Businesses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'business_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'outlet_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'address' => ['type' => 'MEDIUMTEXT'],
            'owners' =>  ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 200],
            'contact_number' => ['type' => 'VARCHAR', 'constraint' => 10],
            'about' => ['type' => 'TEXT'],
            'user_id' => ['type' => 'INT', 'constraint' => 25],
            'business_category_id' => ['type' => 'INT', 'constraint' => 25],
            'extra' => ['type' => 'VARCHAR', 'constraint' => 500],
            'is_visible' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'Yes',
                'null' => FALSE,
            ],
            'opening_time' => ['type' => 'VARCHAR', 'constraint' => 100],
            'closing_time' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);

        $this->forge->addPrimaryKey('business_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('business_category_id', 'business_categories', 'business_category_id','CASCADE', 'CASCADE');
        $this->forge->createTable('businesses');
    }

    public function down()
    {
        $this->forge->dropTable('businesses');
    }
}