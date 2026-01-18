<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\TaskService;

class TaskApiController extends ResourceController
{
    protected $format = 'json';
    protected TaskService $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    /**
     * 現在のユーザーIDを取得
     */
    protected function getCurrentUserId(): int
    {
        return session()->get('user_id') ?? 1;
    }

    /**
     * タスク一覧取得（Ajax）
     * GET /api/tasks?project_id=1
     */
    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $flat = $this->request->getGet('flat') === 'true';

        if (!$projectId) {
            return $this->respond([
                'success' => false,
                'error'   => 'project_id is required',
            ], 400);
        }

        $tasks = $flat
            ? $this->taskService->getTasksFlatByProject($projectId)
            : $this->taskService->getTasksByProject($projectId);

        $stats = $this->taskService->getTaskStats($projectId);

        return $this->respond([
            'success' => true,
            'data'    => $tasks,
            'stats'   => $stats,
        ]);
    }

    /**
     * タスク詳細取得（Ajax）
     * GET /api/tasks/{id}
     */
    public function show($id = null)
    {
        $task = $this->taskService->getTaskWithDetails($id);

        if (!$task) {
            return $this->respond([
                'success' => false,
                'error'   => 'Task not found',
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'data'    => $task,
        ]);
    }

    /**
     * タスク作成（Ajax）
     * POST /api/tasks
     */
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->respond([
                'success' => false,
                'error'   => 'No data provided',
            ], 400);
        }

        $result = $this->taskService->createTask($data);

        if ($result['success']) {
            return $this->respond($result, 201);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスク更新（Ajax）
     * PUT /api/tasks/{id}
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->respond([
                'success' => false,
                'error'   => 'No data provided',
            ], 400);
        }

        $result = $this->taskService->updateTask($id, $data, $this->getCurrentUserId());

        if ($result['success']) {
            return $this->respond($result);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスク削除（Ajax）
     * DELETE /api/tasks/{id}
     */
    public function delete($id = null)
    {
        $result = $this->taskService->deleteTask($id);

        if ($result['success']) {
            return $this->respond($result);
        }

        return $this->respond($result, 400);
    }

    /**
     * 一括更新（カレンダー一括保存用・タスク一覧一括登録用）
     * POST /api/tasks/bulk-update
     */
    public function bulkUpdate()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['tasks']) && empty($data['deleted_task_ids'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'No tasks provided',
            ], 400);
        }

        $tasks = $data['tasks'] ?? [];
        $deletedTaskIds = $data['deleted_task_ids'] ?? [];

        $result = $this->taskService->bulkUpdateTasks($tasks, $this->getCurrentUserId(), $deletedTaskIds);

        return $this->respond($result);
    }

    /**
     * 進捗率更新（Ajax）
     * POST /api/tasks/{id}/progress
     */
    public function updateProgress($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $progress = $data['progress'] ?? null;

        if ($progress === null || $progress < 0 || $progress > 100) {
            return $this->respond([
                'success' => false,
                'error'   => 'Invalid progress value (0-100)',
            ], 400);
        }

        $result = $this->taskService->updateProgress($id, (int) $progress, $this->getCurrentUserId());

        return $this->respond($result);
    }

    /**
     * タスク並び替え（Ajax）
     * POST /api/tasks/reorder
     */
    public function reorder()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['order'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'No order provided',
            ], 400);
        }

        $result = $this->taskService->reorderTasks($data['order']);

        return $this->respond($result);
    }

    /**
     * サブタスク追加（Ajax）
     * POST /api/tasks/{id}/subtasks
     */
    public function addSubtask($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->respond([
                'success' => false,
                'error'   => 'No data provided',
            ], 400);
        }

        $result = $this->taskService->addSubtask($id, $data);

        if ($result['success']) {
            return $this->respond($result, 201);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスクコピー（Ajax）
     * POST /api/tasks/{id}/copy
     */
    public function copy($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $includeSubtasks = ($data['include_subtasks'] ?? true) !== false;

        $result = $this->taskService->copyTask($id, $includeSubtasks);

        if ($result['success']) {
            return $this->respond($result, 201);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスク履歴取得（Ajax）
     * GET /api/tasks/{id}/history
     */
    public function history($id = null)
    {
        $limit = $this->request->getGet('limit') ?? 50;

        $history = $this->taskService->getTaskHistory($id, (int) $limit);

        return $this->respond([
            'success' => true,
            'data'    => $history,
        ]);
    }

    /**
     * サブタスク一覧取得（Ajax）
     * GET /api/tasks/{id}/subtasks
     */
    public function subtasks($id = null)
    {
        $subtasks = $this->taskService->getSubtasks($id);

        return $this->respond([
            'success' => true,
            'data'    => $subtasks,
        ]);
    }

    /**
     * 一括削除（Ajax）
     * POST /api/tasks/bulk-delete
     */
    public function bulkDelete()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['task_ids']) || !is_array($data['task_ids'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'task_ids is required and must be an array',
            ], 400);
        }

        $result = $this->taskService->bulkDeleteTasks($data['task_ids']);

        return $this->respond($result);
    }

    /**
     * 統計情報取得（Ajax）
     * GET /api/tasks/stats?project_id=1
     */
    public function stats()
    {
        $projectId = $this->request->getGet('project_id');

        if (!$projectId) {
            return $this->respond([
                'success' => false,
                'error'   => 'project_id is required',
            ], 400);
        }

        $stats = $this->taskService->getTaskStats($projectId);

        return $this->respond([
            'success' => true,
            'data'    => $stats,
        ]);
    }

    /**
     * 一括編集（タスク一覧用）
     * POST /api/tasks/bulk-edit
     */
    public function bulkEdit()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['task_ids']) || !is_array($data['task_ids'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'task_ids is required and must be an array',
            ], 400);
        }

        if (empty($data['updates'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'updates is required',
            ], 400);
        }

        $result = $this->taskService->bulkEditTasks(
            $data['task_ids'],
            $data['updates'],
            $this->getCurrentUserId()
        );

        return $this->respond($result);
    }

    /**
     * タスクインポート（Ajax）
     * POST /api/tasks/import
     */
    public function import()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['tasks']) || !is_array($data['tasks'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'tasks is required and must be an array',
            ], 400);
        }

        $mode = $data['mode'] ?? 'add';
        $result = $this->taskService->importTasks($data['tasks'], $mode);

        return $this->respond($result);
    }
}
