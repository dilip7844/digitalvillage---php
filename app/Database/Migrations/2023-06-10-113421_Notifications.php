<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notifications extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'notification_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR','constraint' => 255],
            'message' => ['type' => 'VARCHAR','constraint' => 500],
            'user_id' => ['type' => 'INT', 'constraint' => 25],
            'extra' => ['type' => 'VARCHAR','constraint' => 500],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);

        $this->forge->addPrimaryKey('notification_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
