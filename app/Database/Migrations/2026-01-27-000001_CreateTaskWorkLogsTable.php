<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskWorkLogsTable extends Migration
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
            'work_date' => [
                'type'    => 'DATE',
                'comment' => '作業日',
            ],
            'work_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
                'comment'    => '作業時間（時間）',
            ],
            'progress' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => '進捗率（0-100）',
            ],
            'content' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '作業内容・備考',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => '作成者ID',
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
        $this->forge->addKey('task_id', false, false, 'idx_task_work_logs_task_id');
        $this->forge->addKey('work_date', false, false, 'idx_task_work_logs_work_date');
        $this->forge->addKey('created_by', false, false, 'idx_task_work_logs_created_by');

        $this->forge->addForeignKey('task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('task_work_logs');
    }

    public function down()
    {
        $this->forge->dropTable('task_work_logs');
    }
}
