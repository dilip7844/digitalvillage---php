<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 500],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 10],
            'email' => ['type' => 'VARCHAR', 'constraint' => 200],
            'dob' => ['type' => 'VARCHAR', 'constraint' => 100],
            'address' => ['type' => 'VARCHAR', 'constraint' => 500],
            'profile_pic' => ['type' => 'MEDIUMTEXT'],
            'fcm_token' => ['type' => 'MEDIUMTEXT'],
            'occupation_id' => ['type' => 'INT', 'constraint' => 25],
            'business_id' => ['type' => 'INT', 'constraint' => 25],
            'service' => ['type' => 'INT', 'constraint' => 25],
            'gender' => [
                'type' => 'ENUM("Male","Female","Other")',
                'default' => 'Male',
                'null' => FALSE,
            ],
            'is_authority' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'No',
                'null' => FALSE,
            ],
            'is_verified' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'No',
                'null' => FALSE,
            ],
            'is_active' => [
                'type' => 'ENUM("Yes","No")',
                'default' => 'Yes',
                'null' => FALSE,
            ],
            'created_on' => ['type' => 'VARCHAR', 'constraint' => 100],
            'timestamp' => ['type' => 'VARCHAR', 'constraint' => 100]
        ]);
        $this->forge->addPrimaryKey('user_id');
        $this->forge->addForeignKey('occupation_id', 'occupations', 'occupation_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        //
        $this->forge->dropTable('users');

    }
}