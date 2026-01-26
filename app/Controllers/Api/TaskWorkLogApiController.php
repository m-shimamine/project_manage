<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskWorkLogModel;

class TaskWorkLogApiController extends ResourceController
{
    protected $format = 'json';
    protected TaskWorkLogModel $workLogModel;

    public function __construct()
    {
        $this->workLogModel = new TaskWorkLogModel();
    }

    /**
     * 現在のユーザーIDを取得
     */
    protected function getCurrentUserId(): int
    {
        return session()->get('user_id') ?? 1;
    }

    /**
     * タスクの作業履歴一覧取得
     * GET /api/task-histories?task_id=1
     */
    public function index()
    {
        $taskId = $this->request->getGet('task_id');

        if (!$taskId) {
            return $this->respond([
                'success' => false,
                'error'   => 'task_id is required',
            ], 400);
        }

        $limit = $this->request->getGet('limit') ?? 50;
        $logs = $this->workLogModel->getLogsByTask((int) $taskId, (int) $limit);

        return $this->respond([
            'success' => true,
            'data'    => $logs,
        ]);
    }

    /**
     * 作業履歴詳細取得
     * GET /api/task-histories/{id}
     */
    public function show($id = null)
    {
        $log = $this->workLogModel->find($id);

        if (!$log) {
            return $this->respond([
                'success' => false,
                'error'   => 'Work log not found',
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'data'    => $log,
        ]);
    }

    /**
     * 作業履歴作成
     * POST /api/task-histories
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

        // 必須フィールドチェック
        if (empty($data['task_id'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'task_id is required',
            ], 400);
        }

        if (empty($data['work_date'])) {
            return $this->respond([
                'success' => false,
                'error'   => 'work_date is required',
            ], 400);
        }

        // データを整形
        $logData = [
            'task_id'    => (int) $data['task_id'],
            'work_date'  => $data['work_date'],
            'work_hours' => isset($data['work_hours']) ? (float) $data['work_hours'] : 0,
            'progress'   => isset($data['progress']) ? (int) $data['progress'] : 0,
            'content'    => $data['content'] ?? null,
        ];

        // 進捗率の範囲チェック
        if ($logData['progress'] < 0 || $logData['progress'] > 100) {
            return $this->respond([
                'success' => false,
                'error'   => 'progress must be between 0 and 100',
            ], 400);
        }

        $result = $this->workLogModel->addLogAndUpdateTask($logData, $this->getCurrentUserId());

        if ($result['success']) {
            return $this->respond($result, 201);
        }

        return $this->respond($result, 400);
    }

    /**
     * 作業履歴更新
     * PUT /api/task-histories/{id}
     */
    public function update($id = null)
    {
        $log = $this->workLogModel->find($id);

        if (!$log) {
            return $this->respond([
                'success' => false,
                'error'   => 'Work log not found',
            ], 404);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->respond([
                'success' => false,
                'error'   => 'No data provided',
            ], 400);
        }

        // 更新可能なフィールドのみ抽出
        $updateData = [];
        if (isset($data['work_date'])) {
            $updateData['work_date'] = $data['work_date'];
        }
        if (isset($data['work_hours'])) {
            $updateData['work_hours'] = (float) $data['work_hours'];
        }
        if (isset($data['progress'])) {
            $progress = (int) $data['progress'];
            if ($progress < 0 || $progress > 100) {
                return $this->respond([
                    'success' => false,
                    'error'   => 'progress must be between 0 and 100',
                ], 400);
            }
            $updateData['progress'] = $progress;
        }
        if (array_key_exists('content', $data)) {
            $updateData['content'] = $data['content'];
        }

        if (empty($updateData)) {
            return $this->respond([
                'success' => false,
                'error'   => 'No valid fields to update',
            ], 400);
        }

        if ($this->workLogModel->update($id, $updateData)) {
            return $this->respond([
                'success' => true,
                'data'    => $this->workLogModel->find($id),
            ]);
        }

        return $this->respond([
            'success' => false,
            'error'   => 'Failed to update work log',
            'errors'  => $this->workLogModel->errors(),
        ], 400);
    }

    /**
     * 作業履歴削除
     * DELETE /api/task-histories/{id}
     */
    public function delete($id = null)
    {
        $log = $this->workLogModel->find($id);

        if (!$log) {
            return $this->respond([
                'success' => false,
                'error'   => 'Work log not found',
            ], 404);
        }

        if ($this->workLogModel->delete($id)) {
            return $this->respond([
                'success' => true,
                'message' => 'Work log deleted successfully',
            ]);
        }

        return $this->respond([
            'success' => false,
            'error'   => 'Failed to delete work log',
        ], 400);
    }

    /**
     * タスクの作業統計取得
     * GET /api/task-histories/stats?task_id=1
     */
    public function stats()
    {
        $taskId = $this->request->getGet('task_id');

        if (!$taskId) {
            return $this->respond([
                'success' => false,
                'error'   => 'task_id is required',
            ], 400);
        }

        $taskId = (int) $taskId;

        return $this->respond([
            'success' => true,
            'data'    => [
                'total_work_hours'  => $this->workLogModel->getTotalWorkHours($taskId),
                'latest_progress'   => $this->workLogModel->getLatestProgress($taskId),
                'actual_start_date' => $this->workLogModel->getActualStartDate($taskId),
                'actual_end_date'   => $this->workLogModel->getActualEndDate($taskId),
            ],
        ]);
    }
}
