<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'email',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'member',
                'after'      => 'position',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'role',
            ],
        ];

        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', 'phone');
        $this->forge->dropColumn('members', 'role');
        $this->forge->dropColumn('members', 'password');
    }
}
