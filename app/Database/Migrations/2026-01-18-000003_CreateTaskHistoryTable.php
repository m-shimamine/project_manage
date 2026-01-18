<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'task_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'comment'    => 'タスクID',
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => '変更者ID',
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '変更フィールド名',
            ],
            'old_value' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '変更前の値',
            ],
            'new_value' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '変更後の値',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id', false, false, 'idx_task_history_task_id');
        $this->forge->addKey('changed_by', false, false, 'idx_task_history_changed_by');

        $this->forge->addForeignKey('task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('task_history');
    }

    public function down()
    {
        $this->forge->dropTable('task_history');
    }
}
