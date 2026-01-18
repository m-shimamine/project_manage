<?php

namespace App\Services;

use App\Models\ProjectModel;
use App\Models\CustomerModel;
use App\Models\MemberModel;

class DashboardService
{
    protected ProjectModel $projectModel;
    protected CustomerModel $customerModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->customerModel = new CustomerModel();
        $this->memberModel = new MemberModel();
    }

    /**
     * ダッシュボードの統計情報を取得
     */
    public function getStats(): array
    {
        $projectStats = $this->projectModel->getStats();
        $memberCount = $this->memberModel->where('is_active', 1)->countAllResults();

        return [
            'projects' => [
                'count'      => $projectStats['in_progress'] + $projectStats['planning'],
                'change'     => '+12%',
                'changeType' => 'positive',
                'label'      => 'アクティブなプロジェクト',
                'detail'     => '先月比 +3',
            ],
            'tasks' => [
                'count'      => 156,
                'change'     => '進行中',
                'changeType' => 'info',
                'label'      => '未完了タスク',
                'detail'     => '今週完了予定: 42',
            ],
            'members' => [
                'count'      => $memberCount,
                'change'     => '+2',
                'changeType' => 'positive',
                'label'      => 'チームメンバー',
                'detail'     => 'オンライン: 12',
            ],
            'progress' => [
                'count'      => '68%',
                'change'     => '良好',
                'changeType' => 'warning',
                'label'      => '全体進捗率',
                'detail'     => '先月比 +5%',
            ],
        ];
    }

    /**
     * アクティブなプロジェクト一覧を取得
     */
    public function getActiveProjects(): array
    {
        $projects = $this->projectModel->getProjects(null, 'in_progress', null);

        $result = [];
        foreach (array_slice($projects, 0, 4) as $project) {
            $result[] = [
                'id'             => $project['id'],
                'name'           => $project['name'],
                'icon'           => 'fa-folder',
                'color'          => ProjectModel::getColor($project['name']),
                'task_count'     => rand(10, 40),
                'assignee'       => $project['project_leader'] ?? '未定',
                'assignee_color' => 'from-blue-400 to-indigo-500',
                'progress'       => rand(30, 90),
                'progress_color' => ProjectModel::getColor($project['name']),
                'deadline'       => $project['end_date'] ?? '-',
                'status'         => ProjectModel::getStatusLabel($project['status']),
                'status_color'   => 'blue',
            ];
        }

        // デモ用のデータが足りない場合
        if (count($result) < 4) {
            $demoProjects = [
                [
                    'id'             => 1,
                    'name'           => 'ECサイトリニューアル',
                    'icon'           => 'fa-shopping-cart',
                    'color'          => 'from-blue-500 to-indigo-600',
                    'task_count'     => 12,
                    'assignee'       => '山田',
                    'assignee_color' => 'from-blue-400 to-indigo-500',
                    'progress'       => 75,
                    'progress_color' => 'from-blue-500 to-indigo-500',
                    'deadline'       => '2024/05/15',
                    'status'         => '進行中',
                    'status_color'   => 'blue',
                ],
                [
                    'id'             => 2,
                    'name'           => '社内業務システム開発',
                    'icon'           => 'fa-building',
                    'color'          => 'from-purple-500 to-pink-600',
                    'task_count'     => 28,
                    'assignee'       => '佐藤',
                    'assignee_color' => 'from-purple-400 to-pink-500',
                    'progress'       => 45,
                    'progress_color' => 'from-purple-500 to-pink-500',
                    'deadline'       => '2024/06/30',
                    'status'         => '進行中',
                    'status_color'   => 'purple',
                ],
                [
                    'id'             => 3,
                    'name'           => 'モバイルアプリ開発',
                    'icon'           => 'fa-mobile-alt',
                    'color'          => 'from-emerald-500 to-teal-600',
                    'task_count'     => 35,
                    'assignee'       => '鈴木',
                    'assignee_color' => 'from-cyan-400 to-blue-500',
                    'progress'       => 60,
                    'progress_color' => 'from-emerald-500 to-teal-500',
                    'deadline'       => '2024/07/20',
                    'status'         => '進行中',
                    'status_color'   => 'emerald',
                ],
                [
                    'id'             => 4,
                    'name'           => 'データ分析プラットフォーム',
                    'icon'           => 'fa-chart-bar',
                    'color'          => 'from-amber-500 to-orange-600',
                    'task_count'     => 18,
                    'assignee'       => '田中',
                    'assignee_color' => 'from-orange-400 to-red-500',
                    'progress'       => 30,
                    'progress_color' => 'from-amber-500 to-orange-500',
                    'deadline'       => '2024/08/10',
                    'status'         => '計画中',
                    'status_color'   => 'amber',
                ],
            ];

            $result = array_merge($result, array_slice($demoProjects, 0, 4 - count($result)));
        }

        return $result;
    }

    /**
     * 最近の活動を取得
     */
    public function getRecentActivities(): array
    {
        $activities = [
            [
                'icon'        => 'fa-check',
                'color'       => 'from-blue-400 to-indigo-500',
                'title'       => 'タスク完了',
                'description' => '山田さんが「要件書作成」を完了',
                'time'        => '2時間前',
            ],
            [
                'icon'        => 'fa-comment',
                'color'       => 'from-purple-400 to-pink-500',
                'title'       => 'コメント追加',
                'description' => '佐藤さんが「ECサイトリニューアル」にコメント',
                'time'        => '5時間前',
            ],
            [
                'icon'        => 'fa-user-plus',
                'color'       => 'from-emerald-400 to-teal-500',
                'title'       => 'メンバー追加',
                'description' => '新メンバー「高橋さん」が追加されました',
                'time'        => '1日前',
            ],
            [
                'icon'        => 'fa-file',
                'color'       => 'from-amber-400 to-orange-500',
                'title'       => 'ファイルアップロード',
                'description' => '鈴木さんが「設計書.pdf」をアップロード',
                'time'        => '2日前',
            ],
        ];

        // is_last フラグを追加
        $count = count($activities);
        foreach ($activities as $index => &$activity) {
            $activity['is_last'] = ($index === $count - 1);
        }

        return $activities;
    }

    /**
     * グラフ用データを取得
     */
    public function getChartData(): array
    {
        return [
            'labels' => ['1月', '2月', '3月', '4月', '5月', '6月'],
            'datasets' => [
                [
                    'label'           => '完了タスク数',
                    'data'            => [12, 19, 15, 25, 22, 30],
                    'borderColor'     => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label'           => '総タスク数',
                    'data'            => [20, 25, 28, 35, 40, 45],
                    'borderColor'     => 'rgb(139, 92, 246)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
        ];
    }
}
