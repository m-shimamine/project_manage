<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskHistoryModel extends Model
{
    protected $table            = 'task_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'task_id',
        'changed_by',
        'field_name',
        'old_value',
        'new_value',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * フィールド名の日本語ラベル
     */
    public static function getFieldLabels(): array
    {
        return [
            'task_name'          => 'タスク名',
            'process_id'         => '工程',
            'screen_name'        => '画面名',
            'assignee_id'        => '担当者',
            'status'             => 'ステータス',
            'sales_man_days'     => '営業工数',
            'planned_man_days'   => '予定工数',
            'planned_start_date' => '予定開始日',
            'planned_end_date'   => '予定終了日',
            'planned_cost'       => '予定原価',
            'actual_man_days'    => '実績工数',
            'actual_start_date'  => '実績開始日',
            'actual_end_date'    => '実績終了日',
            'actual_cost'        => '出来高',
            'progress'           => '進捗率',
            'delay_days'         => '遅延日数',
            'description'        => '備考',
        ];
    }

    /**
     * フィールド名のラベルを取得
     */
    public static function getFieldLabel(string $fieldName): string
    {
        $labels = self::getFieldLabels();
        return $labels[$fieldName] ?? $fieldName;
    }

    /**
     * タスクの変更履歴を取得
     */
    public function getHistoryByTask(int $taskId, int $limit = 50): array
    {
        return $this->select('task_history.*, u.name as changed_by_name')
            ->join('users u', 'u.id = task_history.changed_by', 'left')
            ->where('task_history.task_id', $taskId)
            ->orderBy('task_history.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * プロジェクトの変更履歴を取得
     */
    public function getHistoryByProject(int $projectId, int $limit = 100): array
    {
        return $this->select('task_history.*, u.name as changed_by_name, t.task_name')
            ->join('users u', 'u.id = task_history.changed_by', 'left')
            ->join('tasks t', 't.id = task_history.task_id', 'left')
            ->where('t.project_id', $projectId)
            ->orderBy('task_history.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * 変更を記録
     */
    public function recordChange(int $taskId, int $changedBy, string $fieldName, $oldValue, $newValue): bool
    {
        return $this->insert([
            'task_id'    => $taskId,
            'changed_by' => $changedBy,
            'field_name' => $fieldName,
            'old_value'  => $oldValue !== null ? (string) $oldValue : null,
            'new_value'  => $newValue !== null ? (string) $newValue : null,
        ]) !== false;
    }

    /**
     * 複数のフィールド変更を記録
     */
    public function recordChanges(int $taskId, int $changedBy, array $oldData, array $newData, array $trackFields): int
    {
        $count = 0;

        foreach ($trackFields as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;

            // 値が変更されている場合のみ記録
            if ($oldValue !== $newValue) {
                $this->recordChange($taskId, $changedBy, $field, $oldValue, $newValue);
                $count++;
            }
        }

        return $count;
    }
}
