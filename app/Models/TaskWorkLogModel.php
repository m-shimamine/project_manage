<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskWorkLogModel extends Model
{
    protected $table            = 'task_work_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'task_id',
        'work_date',
        'work_hours',
        'progress',
        'content',
        'created_by',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'task_id'   => 'required|integer',
        'work_date' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'task_id' => [
            'required' => 'タスクIDは必須です',
            'integer'  => 'タスクIDは数値である必要があります',
        ],
        'work_date' => [
            'required'   => '作業日は必須です',
            'valid_date' => '作業日は有効な日付である必要があります',
        ],
    ];

    /**
     * タスクの作業履歴を取得
     */
    public function getLogsByTask(int $taskId, int $limit = 50): array
    {
        return $this->select('task_work_logs.*, u.name as created_by_name')
            ->join('users u', 'u.id = task_work_logs.created_by', 'left')
            ->where('task_work_logs.task_id', $taskId)
            ->orderBy('task_work_logs.work_date', 'DESC')
            ->orderBy('task_work_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * タスクの最新進捗率を取得
     */
    public function getLatestProgress(int $taskId): ?int
    {
        $log = $this->where('task_id', $taskId)
            ->orderBy('work_date', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->first();

        return $log ? (int) $log['progress'] : null;
    }

    /**
     * タスクの合計作業時間を取得
     */
    public function getTotalWorkHours(int $taskId): float
    {
        $result = $this->selectSum('work_hours')
            ->where('task_id', $taskId)
            ->first();

        return (float) ($result['work_hours'] ?? 0);
    }

    /**
     * タスクの実績開始日を取得（最も古い作業日）
     */
    public function getActualStartDate(int $taskId): ?string
    {
        $log = $this->selectMin('work_date')
            ->where('task_id', $taskId)
            ->first();

        return $log['work_date'] ?? null;
    }

    /**
     * タスクの実績終了日を取得（進捗100%の最初の作業日）
     */
    public function getActualEndDate(int $taskId): ?string
    {
        $log = $this->where('task_id', $taskId)
            ->where('progress', 100)
            ->orderBy('work_date', 'ASC')
            ->first();

        return $log ? $log['work_date'] : null;
    }

    /**
     * 作業履歴を追加し、タスクの進捗率を更新
     */
    public function addLogAndUpdateTask(array $data, int $userId): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 作業履歴を追加
            $data['created_by'] = $userId;
            $logId = $this->insert($data);

            if (!$logId) {
                $db->transRollback();
                return [
                    'success' => false,
                    'error'   => 'Failed to create work log',
                    'errors'  => $this->errors(),
                ];
            }

            // タスクの進捗率を更新
            $taskModel = new TaskModel();
            $task = $taskModel->find($data['task_id']);

            if ($task) {
                $updateData = ['progress' => $data['progress']];

                // 実績開始日を更新（まだ設定されていない場合）
                if (empty($task['actual_start_date'])) {
                    $updateData['actual_start_date'] = $this->getActualStartDate($data['task_id']);
                }

                // 進捗100%の場合、実績終了日を更新
                if ((int) $data['progress'] >= 100) {
                    $updateData['actual_end_date'] = $data['work_date'];
                    $updateData['status'] = 'completed';
                } elseif ((int) $data['progress'] > 0 && $task['status'] === 'not_started') {
                    $updateData['status'] = 'in_progress';
                }

                // 実績工数を更新（合計作業時間から計算、1日=8時間として）
                $totalHours = $this->getTotalWorkHours($data['task_id']);
                $updateData['actual_man_days'] = round($totalHours / 8, 2);

                $taskModel->update($data['task_id'], $updateData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'success' => false,
                    'error'   => 'Transaction failed',
                ];
            }

            return [
                'success' => true,
                'data'    => $this->find($logId),
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
