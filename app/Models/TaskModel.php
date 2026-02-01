<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table            = 'tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'parent_id',
        'process_id',
        'screen_name',
        'task_name',
        'sort_order',
        'level',
        'assignee_id',
        'status',
        'sales_man_days',
        'planned_man_days',
        'planned_start_date',
        'planned_end_date',
        'planned_cost',
        'actual_man_days',
        'actual_start_date',
        'actual_end_date',
        'actual_cost',
        'progress',
        'delay_days',
        'description',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'project_id' => 'required|is_natural_no_zero',
        'process_id' => 'required|is_natural_no_zero',
        'task_name'  => 'required|max_length[255]',
        'status'     => 'required|in_list[not_started,in_progress,completed,on_hold]',
        'progress'   => 'permit_empty|is_natural|less_than_equal_to[100]',
    ];
    protected $validationMessages = [
        'project_id' => [
            'required' => 'プロジェクトは必須です。',
        ],
        'process_id' => [
            'required' => '工程は必須です。',
        ],
        'task_name' => [
            'required'   => 'タスク名は必須です。',
            'max_length' => 'タスク名は255文字以内で入力してください。',
        ],
        'status' => [
            'required' => 'ステータスは必須です。',
            'in_list'  => '無効なステータスです。',
        ],
        'progress' => [
            'less_than_equal_to' => '進捗率は0〜100の範囲で入力してください。',
        ],
    ];
    protected $skipValidation = false;

    /**
     * ステータスリスト
     */
    public static function getStatuses(): array
    {
        return [
            'not_started' => '未着手',
            'in_progress' => '進行中',
            'completed'   => '完了',
            'on_hold'     => '保留',
        ];
    }

    /**
     * ステータスラベルを取得
     */
    public static function getStatusLabel(string $status): string
    {
        $statuses = self::getStatuses();
        return $statuses[$status] ?? $status;
    }

    /**
     * ステータスカラーを取得
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'not_started' => 'bg-slate-100 text-slate-600',
            'in_progress' => 'bg-blue-50 text-blue-600',
            'completed'   => 'bg-emerald-50 text-emerald-600',
            'on_hold'     => 'bg-amber-50 text-amber-600',
            default       => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * ステータスバッジクラスを取得
     */
    public static function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'not_started' => 'status-not-started',
            'in_progress' => 'status-in-progress',
            'completed'   => 'status-completed',
            'on_hold'     => 'status-delayed',
            default       => 'status-not-started',
        };
    }

    /**
     * プロジェクト別タスク統計を取得（projectIdがnullの場合は全プロジェクト）
     */
    public function getTaskStats(?int $projectId = null): array
    {
        $builder = $this->where('deleted_at IS NULL');

        if ($projectId !== null) {
            $builder->where('project_id', $projectId);
        }

        return [
            'total'       => (clone $builder)->countAllResults(false),
            'not_started' => (clone $builder)->where('status', 'not_started')->countAllResults(false),
            'in_progress' => (clone $builder)->where('status', 'in_progress')->countAllResults(false),
            'completed'   => (clone $builder)->where('status', 'completed')->countAllResults(false),
            'on_hold'     => (clone $builder)->where('status', 'on_hold')->countAllResults(),
        ];
    }

    /**
     * プロジェクト別タスク一覧を取得（階層構造付き、projectIdがnullの場合は全プロジェクト）
     */
    public function getTasksByProject(?int $projectId = null): array
    {
        $builder = $this->select('tasks.*, m.name as assignee_name, pm.name as process_name, p.name as project_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->join('projects p', 'p.id = tasks.project_id', 'left')
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('tasks.project_id', 'ASC')
            ->orderBy('tasks.sort_order', 'ASC');

        if ($projectId !== null) {
            $builder->where('tasks.project_id', $projectId);
        }

        $tasks = $builder->findAll();

        // 画面表示用に遅延日数を本日基準で再計算
        $tasks = $this->recalculateDelayDaysForDisplay($tasks);

        return $this->buildHierarchy($tasks);
    }

    /**
     * 全プロジェクト横断でタスク一覧を取得（プロジェクト別グループ）
     */
    public function getAllTasksGroupedByProject(): array
    {
        $tasks = $this->select('tasks.*, m.name as assignee_name, pm.name as process_name, p.name as project_name, c.name as customer_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->join('projects p', 'p.id = tasks.project_id', 'left')
            ->join('customers c', 'c.id = p.customer_id', 'left')
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('c.name', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->orderBy('tasks.sort_order', 'ASC')
            ->findAll();

        // 画面表示用に遅延日数を本日基準で再計算
        $tasks = $this->recalculateDelayDaysForDisplay($tasks);

        // プロジェクト別にグループ化
        $grouped = [];
        foreach ($tasks as $task) {
            $projectId = $task['project_id'];
            if (!isset($grouped[$projectId])) {
                $grouped[$projectId] = [
                    'project_id'    => $projectId,
                    'project_name'  => $task['project_name'],
                    'customer_name' => $task['customer_name'],
                    'tasks'         => [],
                ];
            }
            $grouped[$projectId]['tasks'][] = $task;
        }

        // 各プロジェクトのタスクを階層化
        foreach ($grouped as &$group) {
            $group['tasks'] = $this->buildHierarchy($group['tasks']);
        }

        return array_values($grouped);
    }

    /**
     * フラットなタスク一覧を取得（サブタスク含む）
     */
    public function getTasksFlatByProject(int $projectId): array
    {
        return $this->select('tasks.*, m.name as assignee_name, pm.name as process_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->where('tasks.project_id', $projectId)
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('tasks.sort_order', 'ASC')
            ->findAll();
    }

    /**
     * 親タスクのみ取得
     */
    public function getParentTasksByProject(int $projectId): array
    {
        return $this->select('tasks.*, m.name as assignee_name, pm.name as process_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->where('tasks.project_id', $projectId)
            ->where('tasks.parent_id IS NULL')
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('tasks.sort_order', 'ASC')
            ->findAll();
    }

    /**
     * サブタスク取得
     */
    public function getSubtasks(int $parentId): array
    {
        return $this->select('tasks.*, m.name as assignee_name, pm.name as process_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->where('tasks.parent_id', $parentId)
            ->where('tasks.deleted_at IS NULL')
            ->orderBy('tasks.sort_order', 'ASC')
            ->findAll();
    }

    /**
     * タスク詳細取得（関連情報付き）
     */
    public function getTaskWithDetails(int $id): ?array
    {
        $task = $this->select('tasks.*, m.name as assignee_name, pm.name as process_name, p.name as project_name')
            ->join('members m', 'm.id = tasks.assignee_id', 'left')
            ->join('process_masters pm', 'pm.id = tasks.process_id', 'left')
            ->join('projects p', 'p.id = tasks.project_id', 'left')
            ->where('tasks.id', $id)
            ->first();

        if ($task) {
            $task['subtasks'] = $this->getSubtasks($id);
        }

        return $task;
    }

    /**
     * 階層構造を構築
     */
    protected function buildHierarchy(array $tasks): array
    {
        $parentTasks = [];
        $subtaskMap = [];

        // 親タスクとサブタスクを分離
        foreach ($tasks as $task) {
            if ($task['parent_id'] === null) {
                $task['subtasks'] = [];
                $parentTasks[$task['id']] = $task;
            } else {
                $subtaskMap[$task['parent_id']][] = $task;
            }
        }

        // サブタスクを親タスクに紐付け
        foreach ($subtaskMap as $parentId => $subtasks) {
            if (isset($parentTasks[$parentId])) {
                $parentTasks[$parentId]['subtasks'] = $subtasks;
            }
        }

        return array_values($parentTasks);
    }

    /**
     * 次のsort_orderを取得
     */
    public function getNextSortOrder(int $projectId, ?int $parentId = null): int
    {
        $builder = $this->where('project_id', $projectId);

        if ($parentId !== null) {
            $builder->where('parent_id', $parentId);
        } else {
            $builder->where('parent_id IS NULL');
        }

        $max = $builder->selectMax('sort_order')->first();

        return ($max['sort_order'] ?? 0) + 1;
    }

    /**
     * 並び替え順序を一括更新
     */
    public function updateSortOrder(array $orders): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($orders as $order) {
            $this->update($order['id'], ['sort_order' => $order['sort_order']]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * 親タスクの日付をサブタスクに合わせて更新
     */
    public function syncParentDates(int $parentId): bool
    {
        $subtasks = $this->where('parent_id', $parentId)
            ->where('deleted_at IS NULL')
            ->findAll();

        if (empty($subtasks)) {
            return false;
        }

        $minStartDate = null;
        $maxEndDate = null;

        foreach ($subtasks as $subtask) {
            if ($subtask['planned_start_date']) {
                if (!$minStartDate || $subtask['planned_start_date'] < $minStartDate) {
                    $minStartDate = $subtask['planned_start_date'];
                }
            }
            if ($subtask['planned_end_date']) {
                if (!$maxEndDate || $subtask['planned_end_date'] > $maxEndDate) {
                    $maxEndDate = $subtask['planned_end_date'];
                }
            }
        }

        $updateData = [];
        if ($minStartDate) {
            $updateData['planned_start_date'] = $minStartDate;
        }
        if ($maxEndDate) {
            $updateData['planned_end_date'] = $maxEndDate;
        }

        if (!empty($updateData)) {
            return $this->update($parentId, $updateData);
        }

        return false;
    }

    /**
     * 画面表示用に遅延日数を本日基準で再計算（DBは更新しない）
     */
    public function recalculateDelayDaysForDisplay(array $tasks): array
    {
        $today = date('Y-m-d');

        foreach ($tasks as &$task) {
            if (empty($task['planned_end_date'])) {
                $task['delay_days'] = 0;
                continue;
            }

            $plannedEnd = $task['planned_end_date'];
            $actualEnd = $task['actual_end_date'] ?? null;
            $status = $task['status'] ?? '';
            $progress = (int)($task['progress'] ?? 0);

            // 完了している場合は実績終了日と予定終了日を比較
            if (($status === 'completed' || $progress === 100) && $actualEnd) {
                $delay = (strtotime($actualEnd) - strtotime($plannedEnd)) / 86400;
            } else {
                // 未完了の場合は今日と予定終了日を比較
                $delay = (strtotime($today) - strtotime($plannedEnd)) / 86400;
            }

            $task['delay_days'] = (int) round($delay);
        }

        return $tasks;
    }

    /**
     * 遅延日数を計算して更新
     */
    public function calculateDelayDays(int $taskId): int
    {
        $task = $this->find($taskId);

        if (!$task || !$task['planned_end_date']) {
            return 0;
        }

        $today = date('Y-m-d');
        $plannedEnd = $task['planned_end_date'];
        $actualEnd = $task['actual_end_date'];

        // 完了している場合は実績終了日と予定終了日を比較
        if ($task['status'] === 'completed' && $actualEnd) {
            $delay = (strtotime($actualEnd) - strtotime($plannedEnd)) / 86400;
        } else {
            // 未完了の場合は今日と予定終了日を比較
            $delay = (strtotime($today) - strtotime($plannedEnd)) / 86400;
            $delay = max(0, $delay); // 未来の場合は0
        }

        $delayDays = (int) $delay;
        $this->update($taskId, ['delay_days' => $delayDays]);

        return $delayDays;
    }
}
