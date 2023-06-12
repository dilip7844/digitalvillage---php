<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Posts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'post_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'post' => ['type' => 'TEXT'],
            'images' => ['type' => 'TEXT'],
            'liked_by' => ['type' => 'TEXT'],
            'disliked_by' => ['type' => 'TEXT'],
            'user_id' => ['type' => 'INT', 'constraint' => 25],
            'business_id' => ['type' => 'INT', 'constraint' => 25],
            'is_visible' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'Yes',
                'null' => FALSE,
            ],
            'is_business_post' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'No',
                'null' => FALSE,
            ],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);

        $this->forge->addPrimaryKey('post_id');
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('business_id', 'businesses', 'business_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('posts');
     }

    public function down()
    {
        $this->forge->dropTable('posts');
    }
}