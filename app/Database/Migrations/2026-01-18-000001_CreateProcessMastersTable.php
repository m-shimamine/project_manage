<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProcessMastersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => '工程名',
            ],
            'description' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '説明',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'comment'    => 'ステータス',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => '表示順序',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->addKey('sort_order', false, false, 'idx_sort_order');
        $this->forge->createTable('process_masters');
    }

    public function down()
    {
        $this->forge->dropTable('process_masters');
    }
}
