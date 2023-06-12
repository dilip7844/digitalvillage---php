<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Events extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'event_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'event' => ['type' => 'TEXT'],
            'description' => ['type' => 'TEXT'],
            'images' => ['type' => 'TEXT'],
            'venue' => ['type' => 'TEXT'],
            'start_datetime' => ['type' => 'VARCHAR', 'constraint' => 100],
            'end_datetime' => ['type' => 'VARCHAR', 'constraint' => 100],
            'going' => ['type' => 'TEXT'],
            'user_id' => ['type' => 'INT', 'constraint' => 25],
            'event_category_id' => ['type' => 'INT', 'constraint' => 25],
            'business_id' => ['type' => 'INT', 'constraint' => 25],
            'is_visible' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'Yes',
                'null' => FALSE,
            ],
            'is_business_event' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'No',
                'null' => FALSE,
            ],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);

        $this->forge->addPrimaryKey('event_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('event_category_id', 'event_categories', 'event_category_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('business_id', 'businesses', 'business_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('events');
     }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}
