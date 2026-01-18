<?php

namespace App\Services;

use App\Models\TaskModel;
use App\Models\TaskHistoryModel;

class TaskService
{
    protected TaskModel $taskModel;
    protected TaskHistoryModel $historyModel;

    /**
     * 変更履歴を追跡するフィールド
     */
    protected array $trackFields = [
        'task_name',
        'process_id',
        'screen_name',
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
        'description',
    ];

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->historyModel = new TaskHistoryModel();
    }

    /**
     * プロジェクト別タスク統計を取得
     */
    public function getTaskStats(?int $projectId = null): array
    {
        return $this->taskModel->getTaskStats($projectId);
    }

    /**
     * プロジェクト別タスク一覧を取得（階層構造付き）
     */
    public function getTasksByProject(?int $projectId = null): array
    {
        return $this->taskModel->getTasksByProject($projectId);
    }

    /**
     * 全プロジェクト横断でタスク一覧を取得（プロジェクト別グループ）
     */
    public function getAllTasksGroupedByProject(): array
    {
        return $this->taskModel->getAllTasksGroupedByProject();
    }

    /**
     * フラットなタスク一覧を取得
     */
    public function getTasksFlatByProject(int $projectId): array
    {
        return $this->taskModel->getTasksFlatByProject($projectId);
    }

    /**
     * 親タスクのみ取得
     */
    public function getParentTasksByProject(int $projectId): array
    {
        return $this->taskModel->getParentTasksByProject($projectId);
    }

    /**
     * サブタスク取得
     */
    public function getSubtasks(int $parentId): array
    {
        return $this->taskModel->getSubtasks($parentId);
    }

    /**
     * タスク取得
     */
    public function find(int $id): ?array
    {
        return $this->taskModel->find($id);
    }

    /**
     * タスク詳細取得（関連情報付き）
     */
    public function getTaskWithDetails(int $id): ?array
    {
        return $this->taskModel->getTaskWithDetails($id);
    }

    /**
     * タスク作成
     */
    public function createTask(array $data): array
    {
        // sort_order設定
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->taskModel->getNextSortOrder(
                $data['project_id'],
                $data['parent_id'] ?? null
            );
        }

        // level設定
        if (!isset($data['level'])) {
            $data['level'] = isset($data['parent_id']) && $data['parent_id'] ? 2 : 1;
        }

        // バリデーション
        if (!$this->taskModel->validate($data)) {
            return [
                'success' => false,
                'errors'  => $this->taskModel->errors(),
            ];
        }

        $id = $this->taskModel->insert($data);

        if ($id === false) {
            return [
                'success' => false,
                'error'   => 'タスクの作成に失敗しました。',
            ];
        }

        // サブタスクの場合、親タスクの日付を同期
        if (!empty($data['parent_id'])) {
            $this->taskModel->syncParentDates($data['parent_id']);
        }

        return [
            'success' => true,
            'data'    => $this->taskModel->find($id),
        ];
    }

    /**
     * タスク更新
     */
    public function updateTask(int $id, array $data, ?int $changedBy = null): array
    {
        $oldTask = $this->taskModel->find($id);

        if (!$oldTask) {
            return [
                'success' => false,
                'error'   => 'タスクが見つかりません。',
            ];
        }

        // 変更履歴を記録
        if ($changedBy) {
            $this->historyModel->recordChanges($id, $changedBy, $oldTask, $data, $this->trackFields);
        }

        // 更新
        if (!$this->taskModel->update($id, $data)) {
            return [
                'success' => false,
                'errors'  => $this->taskModel->errors(),
            ];
        }

        // サブタスクの場合、親タスクの日付を同期
        if (!empty($oldTask['parent_id'])) {
            $this->taskModel->syncParentDates($oldTask['parent_id']);
        }

        // 遅延日数を再計算
        $this->taskModel->calculateDelayDays($id);

        return [
            'success' => true,
            'data'    => $this->taskModel->find($id),
        ];
    }

    /**
     * タスク削除
     */
    public function deleteTask(int $id): array
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            return [
                'success' => false,
                'error'   => 'タスクが見つかりません。',
            ];
        }

        $parentId = $task['parent_id'];

        // 論理削除（サブタスクも連動で削除される）
        if (!$this->taskModel->delete($id)) {
            return [
                'success' => false,
                'error'   => '削除に失敗しました。',
            ];
        }

        // 親タスクの日付を同期
        if ($parentId) {
            $this->taskModel->syncParentDates($parentId);
        }

        return ['success' => true];
    }

    /**
     * 一括更新（カレンダー一括保存用・タスク一覧一括登録用）
     * 新規タスク（idがnullまたは未設定）は作成、既存タスクは更新
     * deleted_task_idsが渡された場合は該当タスクを削除
     */
    public function bulkUpdateTasks(array $tasks, ?int $changedBy = null, array $deletedTaskIds = []): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $created = [];
        $updated = [];
        $deleted = [];
        $errors = [];

        // 削除対象のタスクを処理
        if (!empty($deletedTaskIds)) {
            foreach ($deletedTaskIds as $deleteId) {
                $result = $this->deleteTask($deleteId);
                if ($result['success']) {
                    $deleted[] = $deleteId;
                } else {
                    $errors[] = $result['error'] ?? "タスク {$deleteId} の削除に失敗しました";
                }
            }
        }

        // 新規親タスクのID対応表（temp_XX -> 実際のID）
        $tempIdMap = [];

        // まず親タスク（parent_id が null または temp_ で始まらないもの）を処理
        foreach ($tasks as $index => $taskData) {
            // サブタスクは後で処理
            $parentId = $taskData['parent_id'] ?? null;
            if ($parentId !== null && (is_string($parentId) && strpos($parentId, 'temp_') === 0)) {
                continue;
            }

            $taskId = $taskData['id'] ?? null;

            if (empty($taskId)) {
                // 新規作成
                $result = $this->createTask($taskData);
                if ($result['success']) {
                    $created[] = $result['data'];
                    // temp_XX のマッピングを記録
                    $tempIdMap['temp_' . $index] = $result['data']['id'];
                } else {
                    $errors[] = $result['errors'] ?? $result['error'] ?? '作成エラー';
                }
            } else {
                // 更新
                $result = $this->updateTask($taskId, $taskData, $changedBy);
                if ($result['success']) {
                    $updated[] = $result['data'];
                } else {
                    $errors[] = $result['error'] ?? '更新エラー';
                }
            }
        }

        // 次にサブタスク（parent_id が temp_ で始まるもの）を処理
        foreach ($tasks as $taskData) {
            $parentId = $taskData['parent_id'] ?? null;
            if ($parentId === null || !is_string($parentId) || strpos($parentId, 'temp_') !== 0) {
                continue;
            }

            // temp_XX を実際のIDに変換
            if (isset($tempIdMap[$parentId])) {
                $taskData['parent_id'] = $tempIdMap[$parentId];
            } else {
                $errors[] = "親タスク {$parentId} が見つかりません。";
                continue;
            }

            $taskId = $taskData['id'] ?? null;

            if (empty($taskId)) {
                // 新規作成
                $result = $this->createTask($taskData);
                if ($result['success']) {
                    $created[] = $result['data'];
                } else {
                    $errors[] = $result['errors'] ?? $result['error'] ?? 'サブタスク作成エラー';
                }
            } else {
                // 更新
                $result = $this->updateTask($taskId, $taskData, $changedBy);
                if ($result['success']) {
                    $updated[] = $result['data'];
                } else {
                    $errors[] = $result['error'] ?? 'サブタスク更新エラー';
                }
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return [
                'success' => false,
                'error'   => '一括更新に失敗しました。',
            ];
        }

        return [
            'success' => empty($errors),
            'created' => $created,
            'updated' => $updated,
            'deleted' => $deleted,
            'errors'  => $errors,
        ];
    }

    /**
     * 進捗率更新
     */
    public function updateProgress(int $id, int $progress, ?int $changedBy = null): array
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            return [
                'success' => false,
                'error'   => 'タスクが見つかりません。',
            ];
        }

        // ステータス自動更新
        $status = 'in_progress';
        if ($progress === 0) {
            $status = 'not_started';
        } elseif ($progress >= 100) {
            $status = 'completed';
            $progress = 100;
        }

        $data = [
            'progress' => $progress,
            'status'   => $status,
        ];

        // 完了時に実績終了日を設定
        if ($status === 'completed' && empty($task['actual_end_date'])) {
            $data['actual_end_date'] = date('Y-m-d');
        }

        return $this->updateTask($id, $data, $changedBy);
    }

    /**
     * 並び替え
     */
    public function reorderTasks(array $order): array
    {
        $orders = [];
        foreach ($order as $index => $taskId) {
            $orders[] = ['id' => $taskId, 'sort_order' => $index + 1];
        }

        if ($this->taskModel->updateSortOrder($orders)) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error'   => '並び替えに失敗しました。',
        ];
    }

    /**
     * サブタスク追加
     */
    public function addSubtask(int $parentId, array $data): array
    {
        $parentTask = $this->taskModel->find($parentId);

        if (!$parentTask) {
            return [
                'success' => false,
                'error'   => '親タスクが見つかりません。',
            ];
        }

        $data['project_id'] = $parentTask['project_id'];
        $data['parent_id'] = $parentId;
        $data['process_id'] = $data['process_id'] ?? $parentTask['process_id'];
        $data['level'] = 2;

        return $this->createTask($data);
    }

    /**
     * タスクコピー
     */
    public function copyTask(int $id, bool $includeSubtasks = true): array
    {
        $task = $this->taskModel->getTaskWithDetails($id);

        if (!$task) {
            return [
                'success' => false,
                'error'   => 'タスクが見つかりません。',
            ];
        }

        // コピー用データを準備
        $copyData = $task;
        unset($copyData['id'], $copyData['created_at'], $copyData['updated_at'], $copyData['deleted_at']);
        unset($copyData['subtasks'], $copyData['assignee_name'], $copyData['process_name'], $copyData['project_name']);
        $copyData['task_name'] = $task['task_name'] . ' (コピー)';
        $copyData['sort_order'] = $this->taskModel->getNextSortOrder($task['project_id'], $task['parent_id']);

        // 実績をリセット
        $copyData['actual_man_days'] = null;
        $copyData['actual_start_date'] = null;
        $copyData['actual_end_date'] = null;
        $copyData['actual_cost'] = null;
        $copyData['progress'] = 0;
        $copyData['status'] = 'not_started';
        $copyData['delay_days'] = 0;

        $result = $this->createTask($copyData);

        if (!$result['success']) {
            return $result;
        }

        $newTaskId = $result['data']['id'];

        // サブタスクもコピー
        if ($includeSubtasks && !empty($task['subtasks'])) {
            foreach ($task['subtasks'] as $subtask) {
                $subtaskData = $subtask;
                unset($subtaskData['id'], $subtaskData['created_at'], $subtaskData['updated_at'], $subtaskData['deleted_at']);
                unset($subtaskData['assignee_name'], $subtaskData['process_name']);
                $subtaskData['parent_id'] = $newTaskId;
                $subtaskData['actual_man_days'] = null;
                $subtaskData['actual_start_date'] = null;
                $subtaskData['actual_end_date'] = null;
                $subtaskData['actual_cost'] = null;
                $subtaskData['progress'] = 0;
                $subtaskData['status'] = 'not_started';
                $subtaskData['delay_days'] = 0;

                $this->createTask($subtaskData);
            }
        }

        return [
            'success' => true,
            'data'    => $this->taskModel->getTaskWithDetails($newTaskId),
        ];
    }

    /**
     * タスクの変更履歴を取得
     */
    public function getTaskHistory(int $taskId, int $limit = 50): array
    {
        return $this->historyModel->getHistoryByTask($taskId, $limit);
    }

    /**
     * プロジェクトの変更履歴を取得
     */
    public function getProjectHistory(int $projectId, int $limit = 100): array
    {
        return $this->historyModel->getHistoryByProject($projectId, $limit);
    }

    /**
     * 一括削除
     */
    public function bulkDeleteTasks(array $taskIds): array
    {
        if (empty($taskIds)) {
            return [
                'success' => false,
                'error'   => 'タスクIDが指定されていません。',
            ];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $deletedCount = 0;
        $errors = [];

        foreach ($taskIds as $taskId) {
            $task = $this->taskModel->find($taskId);

            if (!$task) {
                $errors[] = "タスクID {$taskId} が見つかりません。";
                continue;
            }

            if ($this->taskModel->delete($taskId)) {
                $deletedCount++;

                // 親タスクの日付を同期
                if (!empty($task['parent_id'])) {
                    $this->taskModel->syncParentDates($task['parent_id']);
                }
            } else {
                $errors[] = "タスクID {$taskId} の削除に失敗しました。";
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return [
                'success' => false,
                'error'   => '一括削除に失敗しました。',
            ];
        }

        return [
            'success' => $deletedCount > 0,
            'deleted_count' => $deletedCount,
            'errors'  => $errors,
        ];
    }

    /**
     * 一括編集（タスク一覧用）
     */
    public function bulkEditTasks(array $taskIds, array $updates, ?int $changedBy = null): array
    {
        if (empty($taskIds)) {
            return [
                'success' => false,
                'error'   => 'タスクIDが指定されていません。',
            ];
        }

        if (empty($updates)) {
            return [
                'success' => false,
                'error'   => '更新データが指定されていません。',
            ];
        }

        // 許可されたフィールドのみ抽出
        $allowedFields = [
            'assignee_id',
            'status',
            'progress',
            'process_id',
            'planned_start_date',
            'planned_end_date',
            'planned_man_days',
            'sales_man_days',
        ];

        $filteredUpdates = array_intersect_key($updates, array_flip($allowedFields));

        if (empty($filteredUpdates)) {
            return [
                'success' => false,
                'error'   => '有効な更新フィールドがありません。',
            ];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $updatedCount = 0;
        $errors = [];

        foreach ($taskIds as $taskId) {
            $result = $this->updateTask($taskId, $filteredUpdates, $changedBy);

            if ($result['success']) {
                $updatedCount++;
            } else {
                $errors[] = $result['error'] ?? "タスクID {$taskId} の更新に失敗しました。";
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return [
                'success' => false,
                'error'   => '一括編集に失敗しました。',
            ];
        }

        return [
            'success' => $updatedCount > 0,
            'updated_count' => $updatedCount,
            'errors'  => $errors,
        ];
    }

    /**
     * タスクインポート
     */
    public function importTasks(array $tasks, string $mode = 'add'): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $projectId = $tasks[0]['project_id'] ?? null;

        // replaceモードの場合は既存タスクを削除
        if ($mode === 'replace' && $projectId) {
            $existingTasks = $this->taskModel->where('project_id', $projectId)->findAll();
            foreach ($existingTasks as $task) {
                $this->taskModel->delete($task['id']);
            }
        }

        $imported = 0;
        $errors = [];
        $parentTaskMap = [];

        // まず親タスクを処理（parent_nameがないもの）
        foreach ($tasks as $index => $taskData) {
            if (!empty($taskData['parent_name'])) {
                continue;
            }

            $result = $this->createTask($taskData);
            if ($result['success']) {
                $imported++;
                // タスク名でマッピング
                $parentTaskMap[$taskData['task_name']] = $result['data']['id'];
            } else {
                $errors[] = $result['errors'] ?? $result['error'] ?? "タスク {$index} の作成に失敗しました";
            }
        }

        // 次にサブタスク（parent_nameがあるもの）を処理
        foreach ($tasks as $index => $taskData) {
            if (empty($taskData['parent_name'])) {
                continue;
            }

            $parentName = $taskData['parent_name'];
            unset($taskData['parent_name']);

            // 親タスクIDを取得
            if (isset($parentTaskMap[$parentName])) {
                $taskData['parent_id'] = $parentTaskMap[$parentName];
            } else {
                // 既存の親タスクを検索
                $parentTask = $this->taskModel
                    ->where('project_id', $taskData['project_id'])
                    ->where('task_name', $parentName)
                    ->first();

                if ($parentTask) {
                    $taskData['parent_id'] = $parentTask['id'];
                } else {
                    $errors[] = "サブタスク「{$taskData['task_name']}」の親タスク「{$parentName}」が見つかりません";
                    continue;
                }
            }

            $result = $this->createTask($taskData);
            if ($result['success']) {
                $imported++;
            } else {
                $errors[] = $result['errors'] ?? $result['error'] ?? "サブタスク {$index} の作成に失敗しました";
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return [
                'success' => false,
                'error'   => 'インポートに失敗しました。',
            ];
        }

        return [
            'success' => $imported > 0 || empty($errors),
            'imported' => $imported,
            'errors'  => $errors,
        ];
    }
}
