<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // プロジェクトIDを取得
        $projectId = $this->db->table('projects')->select('id')->limit(1)->get()->getRowArray();

        if (!$projectId) {
            echo "プロジェクトが存在しません。先にプロジェクトを作成してください。\n";
            return;
        }

        $projectId = $projectId['id'];

        // 担当者IDを取得
        $members = $this->db->table('members')->select('id, name')->get()->getResultArray();
        $memberMap = [];
        foreach ($members as $member) {
            $memberMap[$member['name']] = $member['id'];
        }

        // 工程IDを取得
        $processes = $this->db->table('process_masters')->select('id, name')->get()->getResultArray();
        $processMap = [];
        foreach ($processes as $process) {
            $processMap[$process['name']] = $process['id'];
        }

        // デフォルト値
        $defaultMemberId = !empty($memberMap) ? reset($memberMap) : 1;
        $defaultProcessId = !empty($processMap) ? reset($processMap) : 1;

        $tasks = [
            // 要件定義
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['要件定義'] ?? $defaultProcessId,
                'task_name' => 'ヒアリング',
                'sort_order' => 1,
                'level' => 1,
                'assignee_id' => $memberMap['山田太郎'] ?? $defaultMemberId,
                'status' => 'completed',
                'sales_man_days' => 1.0,
                'planned_man_days' => 2.0,
                'planned_start_date' => '2024-01-15',
                'planned_end_date' => '2024-01-25',
                'planned_cost' => 200000,
                'actual_man_days' => 2.0,
                'actual_start_date' => '2024-01-15',
                'actual_end_date' => '2024-01-24',
                'actual_cost' => 200000,
                'progress' => 100,
                'delay_days' => 0,
                'subtasks' => [
                    [
                        'task_name' => '顧客へのヒアリング日程調整',
                        'assignee_id' => $memberMap['佐藤花子'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'sales_man_days' => 0.5,
                        'planned_man_days' => 0.5,
                        'planned_start_date' => '2024-01-15',
                        'planned_end_date' => '2024-01-16',
                        'planned_cost' => 50000,
                        'actual_man_days' => 0.5,
                        'actual_start_date' => '2024-01-15',
                        'actual_end_date' => '2024-01-16',
                        'actual_cost' => 50000,
                        'progress' => 100,
                    ],
                    [
                        'task_name' => 'ヒアリングシート作成',
                        'assignee_id' => $memberMap['山田太郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'sales_man_days' => 0.5,
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-01-17',
                        'planned_end_date' => '2024-01-20',
                        'planned_cost' => 100000,
                        'actual_man_days' => 1.0,
                        'actual_start_date' => '2024-01-17',
                        'actual_end_date' => '2024-01-20',
                        'actual_cost' => 100000,
                        'progress' => 100,
                    ],
                    [
                        'task_name' => '議事録作成',
                        'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'sales_man_days' => 0,
                        'planned_man_days' => 0.5,
                        'planned_start_date' => '2024-01-21',
                        'planned_end_date' => '2024-01-25',
                        'planned_cost' => 50000,
                        'actual_man_days' => 0.5,
                        'actual_start_date' => '2024-01-21',
                        'actual_end_date' => '2024-01-24',
                        'actual_cost' => 50000,
                        'progress' => 100,
                    ],
                ],
            ],
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['要件定義'] ?? $defaultProcessId,
                'task_name' => '要件書作成',
                'sort_order' => 2,
                'level' => 1,
                'assignee_id' => $memberMap['山田太郎'] ?? $defaultMemberId,
                'status' => 'completed',
                'sales_man_days' => 0.5,
                'planned_man_days' => 3.0,
                'planned_start_date' => '2024-01-26',
                'planned_end_date' => '2024-02-15',
                'planned_cost' => 300000,
                'actual_man_days' => 3.0,
                'actual_start_date' => '2024-01-25',
                'actual_end_date' => '2024-02-14',
                'actual_cost' => 300000,
                'progress' => 100,
                'delay_days' => 0,
                'subtasks' => [
                    [
                        'task_name' => '要件一覧の整理',
                        'assignee_id' => $memberMap['山田太郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'sales_man_days' => 0.5,
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-01-26',
                        'planned_end_date' => '2024-01-31',
                        'planned_cost' => 100000,
                        'actual_man_days' => 1.0,
                        'actual_start_date' => '2024-01-25',
                        'actual_end_date' => '2024-01-30',
                        'actual_cost' => 100000,
                        'progress' => 100,
                    ],
                ],
            ],
            // 基本設計
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['基本設計'] ?? $defaultProcessId,
                'task_name' => '画面設計',
                'sort_order' => 3,
                'level' => 1,
                'assignee_id' => $memberMap['佐藤花子'] ?? $defaultMemberId,
                'status' => 'completed',
                'sales_man_days' => 0,
                'planned_man_days' => 4.0,
                'planned_start_date' => '2024-02-16',
                'planned_end_date' => '2024-03-10',
                'planned_cost' => 400000,
                'actual_man_days' => 5.0,
                'actual_start_date' => '2024-02-15',
                'actual_end_date' => '2024-03-12',
                'actual_cost' => 500000,
                'progress' => 100,
                'delay_days' => 2,
                'description' => '仕様変更対応',
                'subtasks' => [
                    [
                        'task_name' => 'ワイヤーフレーム作成',
                        'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'planned_man_days' => 2.0,
                        'planned_start_date' => '2024-02-16',
                        'planned_end_date' => '2024-02-25',
                        'planned_cost' => 200000,
                        'actual_man_days' => 2.0,
                        'actual_start_date' => '2024-02-15',
                        'actual_end_date' => '2024-02-25',
                        'actual_cost' => 200000,
                        'progress' => 100,
                    ],
                ],
            ],
            // 開発
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['開発・実装'] ?? $defaultProcessId,
                'screen_name' => 'ログイン画面',
                'task_name' => 'ログイン画面実装',
                'sort_order' => 4,
                'level' => 1,
                'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                'status' => 'completed',
                'sales_man_days' => 0,
                'planned_man_days' => 2.0,
                'planned_start_date' => '2024-04-01',
                'planned_end_date' => '2024-04-05',
                'planned_cost' => 200000,
                'actual_man_days' => 2.0,
                'actual_start_date' => '2024-04-01',
                'actual_end_date' => '2024-04-05',
                'actual_cost' => 200000,
                'progress' => 100,
                'delay_days' => 0,
                'subtasks' => [
                    [
                        'task_name' => 'ログインフォームUI実装',
                        'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-04-01',
                        'planned_end_date' => '2024-04-03',
                        'planned_cost' => 100000,
                        'actual_man_days' => 1.0,
                        'actual_start_date' => '2024-04-01',
                        'actual_end_date' => '2024-04-03',
                        'actual_cost' => 100000,
                        'progress' => 100,
                    ],
                    [
                        'task_name' => '入力バリデーション',
                        'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-04-04',
                        'planned_end_date' => '2024-04-05',
                        'planned_cost' => 100000,
                        'actual_man_days' => 1.0,
                        'actual_start_date' => '2024-04-04',
                        'actual_end_date' => '2024-04-05',
                        'actual_cost' => 100000,
                        'progress' => 100,
                    ],
                ],
            ],
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['開発・実装'] ?? $defaultProcessId,
                'screen_name' => '一覧画面',
                'task_name' => '一覧画面実装',
                'sort_order' => 5,
                'level' => 1,
                'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                'status' => 'in_progress',
                'sales_man_days' => 0,
                'planned_man_days' => 3.0,
                'planned_start_date' => '2024-04-13',
                'planned_end_date' => '2024-04-20',
                'planned_cost' => 300000,
                'actual_man_days' => null,
                'actual_start_date' => '2024-04-15',
                'actual_end_date' => null,
                'actual_cost' => 150000,
                'progress' => 50,
                'delay_days' => 2,
                'subtasks' => [
                    [
                        'task_name' => 'テーブルヘッダー実装',
                        'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-04-13',
                        'planned_end_date' => '2024-04-15',
                        'planned_cost' => 100000,
                        'actual_man_days' => 1.0,
                        'actual_start_date' => '2024-04-15',
                        'actual_end_date' => '2024-04-17',
                        'actual_cost' => 100000,
                        'progress' => 100,
                    ],
                    [
                        'task_name' => '行クリックイベント',
                        'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                        'status' => 'in_progress',
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-04-16',
                        'planned_end_date' => '2024-04-18',
                        'planned_cost' => 100000,
                        'actual_start_date' => '2024-04-18',
                        'actual_cost' => 50000,
                        'progress' => 50,
                    ],
                    [
                        'task_name' => 'ソート機能',
                        'assignee_id' => $memberMap['佐藤花子'] ?? $defaultMemberId,
                        'status' => 'not_started',
                        'planned_man_days' => 1.0,
                        'planned_start_date' => '2024-04-19',
                        'planned_end_date' => '2024-04-20',
                        'planned_cost' => 100000,
                        'progress' => 0,
                    ],
                ],
            ],
            // テスト
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['単体テスト'] ?? $defaultProcessId,
                'task_name' => '単体テスト',
                'sort_order' => 6,
                'level' => 1,
                'assignee_id' => $memberMap['高橋三郎'] ?? $defaultMemberId,
                'status' => 'in_progress',
                'sales_man_days' => 0,
                'planned_man_days' => 5.0,
                'planned_start_date' => '2024-04-15',
                'planned_end_date' => '2024-05-20',
                'planned_cost' => 500000,
                'actual_start_date' => '2024-04-17',
                'actual_cost' => 150000,
                'progress' => 30,
                'delay_days' => 2,
                'subtasks' => [
                    [
                        'task_name' => 'テストケース作成',
                        'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                        'status' => 'completed',
                        'planned_man_days' => 2.0,
                        'planned_start_date' => '2024-04-15',
                        'planned_end_date' => '2024-04-25',
                        'planned_cost' => 200000,
                        'actual_man_days' => 2.0,
                        'actual_start_date' => '2024-04-17',
                        'actual_end_date' => '2024-04-27',
                        'actual_cost' => 200000,
                        'progress' => 100,
                    ],
                    [
                        'task_name' => 'ユニットテスト実行',
                        'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                        'status' => 'in_progress',
                        'planned_man_days' => 2.0,
                        'planned_start_date' => '2024-04-26',
                        'planned_end_date' => '2024-05-10',
                        'planned_cost' => 200000,
                        'actual_start_date' => '2024-04-28',
                        'actual_cost' => 100000,
                        'progress' => 50,
                    ],
                ],
            ],
            // リリース
            [
                'project_id' => $projectId,
                'parent_id' => null,
                'process_id' => $processMap['本番リリース'] ?? $defaultProcessId,
                'task_name' => 'デプロイ・公開',
                'sort_order' => 7,
                'level' => 1,
                'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                'status' => 'not_started',
                'sales_man_days' => 0,
                'planned_man_days' => 1.0,
                'planned_start_date' => '2024-06-26',
                'planned_end_date' => '2024-06-30',
                'planned_cost' => 100000,
                'progress' => 0,
                'delay_days' => 0,
                'subtasks' => [
                    [
                        'task_name' => 'デプロイ作業',
                        'assignee_id' => $memberMap['田中次郎'] ?? $defaultMemberId,
                        'status' => 'not_started',
                        'planned_man_days' => 0.5,
                        'planned_start_date' => '2024-06-26',
                        'planned_end_date' => '2024-06-27',
                        'planned_cost' => 50000,
                        'progress' => 0,
                    ],
                    [
                        'task_name' => '動作確認',
                        'assignee_id' => $memberMap['鈴木一郎'] ?? $defaultMemberId,
                        'status' => 'not_started',
                        'planned_man_days' => 0.5,
                        'planned_start_date' => '2024-06-28',
                        'planned_end_date' => '2024-06-30',
                        'planned_cost' => 50000,
                        'progress' => 0,
                    ],
                ],
            ],
        ];

        $now = date('Y-m-d H:i:s');

        foreach ($tasks as $taskData) {
            $subtasks = $taskData['subtasks'] ?? [];
            unset($taskData['subtasks']);

            $taskData['created_at'] = $now;
            $taskData['updated_at'] = $now;

            // 親タスクを挿入
            $this->db->table('tasks')->insert($taskData);
            $parentId = $this->db->insertID();

            // サブタスクを挿入
            $subtaskOrder = 1;
            foreach ($subtasks as $subtask) {
                $subtask['project_id'] = $projectId;
                $subtask['parent_id'] = $parentId;
                $subtask['process_id'] = $taskData['process_id'];
                $subtask['sort_order'] = $subtaskOrder++;
                $subtask['level'] = 2;
                $subtask['delay_days'] = $subtask['delay_days'] ?? 0;
                $subtask['created_at'] = $now;
                $subtask['updated_at'] = $now;

                $this->db->table('tasks')->insert($subtask);
            }
        }

        echo "タスクデータを挿入しました。\n";
    }
}
