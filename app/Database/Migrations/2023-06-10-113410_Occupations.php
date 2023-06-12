<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Occupations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'occupation_id' => ['type' => 'INT', 'usigned' => true, 'constraint' => 5, 'auto_increment' => true],
            'occupation_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'occupation_name_marathi' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);

        $this->forge->addPrimaryKey('occupation_id');
        $this->forge->createTable('occupations');
    }

    public function down()
    {
        $this->forge->dropTable('occupations');
    }
}
