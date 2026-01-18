<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
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
            'project_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'プロジェクトID',
            ],
            'parent_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => '親タスクID（サブタスク用）',
            ],
            'process_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => '工程ID',
            ],
            'screen_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => '画面名',
            ],
            'task_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'タスク名',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => '表示順',
            ],
            'level' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => '階層レベル（1:親タスク, 2:サブタスク）',
            ],
            'assignee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => '担当者ID',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['not_started', 'in_progress', 'completed', 'on_hold'],
                'default'    => 'not_started',
                'comment'    => 'ステータス',
            ],
            // 予定（計画）
            'sales_man_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => '営業工数（人日）',
            ],
            'planned_man_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => '予定工数（人日）',
            ],
            'planned_start_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => '予定開始日',
            ],
            'planned_end_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => '予定終了日',
            ],
            'planned_cost' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => '予定原価（円）',
            ],
            // 実績
            'actual_man_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'comment'    => '実績工数（人日）',
            ],
            'actual_start_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => '実績開始日',
            ],
            'actual_end_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => '実績終了日',
            ],
            'actual_cost' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => '出来高・実績原価（円）',
            ],
            // その他
            'progress' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => '進捗率（0-100）',
            ],
            'delay_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => '遅延日数（正:遅れ、負:先行）',
            ],
            'description' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '説明・備考',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => '削除日時（論理削除）',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('project_id', false, false, 'idx_tasks_project_id');
        $this->forge->addKey('parent_id', false, false, 'idx_tasks_parent_id');
        $this->forge->addKey('process_id', false, false, 'idx_tasks_process_id');
        $this->forge->addKey('assignee_id', false, false, 'idx_tasks_assignee_id');
        $this->forge->addKey('status', false, false, 'idx_tasks_status');
        $this->forge->addKey(['planned_start_date', 'planned_end_date'], false, false, 'idx_tasks_planned_dates');

        $this->forge->addForeignKey('project_id', 'projects', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('process_id', 'process_masters', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('assignee_id', 'members', 'id', 'SET NULL', 'SET NULL');

        $this->forge->createTable('tasks');

        // 自己参照の外部キーは別途追加
        $this->db->query('ALTER TABLE tasks ADD CONSTRAINT fk_tasks_parent FOREIGN KEY (parent_id) REFERENCES tasks(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // 外部キー制約を先に削除
        $this->db->query('ALTER TABLE tasks DROP FOREIGN KEY fk_tasks_parent');
        $this->forge->dropTable('tasks');
    }
}
