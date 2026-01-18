<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcessMasterModel extends Model
{
    protected $table            = 'process_masters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'status',
        'sort_order',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'   => 'required|max_length[255]',
        'status' => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages = [
        'name' => [
            'required'   => '工程名は必須です。',
            'max_length' => '工程名は255文字以内で入力してください。',
        ],
        'status' => [
            'required' => 'ステータスは必須です。',
            'in_list'  => '無効なステータスです。',
        ],
    ];
    protected $skipValidation = false;

    /**
     * ステータスリスト
     */
    public static function getStatuses(): array
    {
        return [
            'active'   => '有効',
            'inactive' => '無効',
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
            'active'   => 'bg-emerald-50 text-emerald-600',
            'inactive' => 'bg-slate-100 text-slate-500',
            default    => 'bg-slate-100 text-slate-500',
        };
    }

    /**
     * 工程アイコンカラーを取得（名前ベース）
     */
    public static function getIconColor(string $name): string
    {
        $colors = [
            'from-blue-500 to-indigo-600',
            'from-purple-500 to-pink-600',
            'from-cyan-500 to-blue-600',
            'from-emerald-500 to-teal-600',
            'from-amber-500 to-orange-600',
            'from-rose-500 to-pink-600',
            'from-violet-500 to-purple-600',
            'from-slate-500 to-slate-600',
        ];
        $index = abs(crc32($name)) % count($colors);
        return $colors[$index];
    }

    /**
     * 工程アイコンを取得（名前ベース）
     */
    public static function getIcon(string $name): string
    {
        $icons = [
            '要件定義' => 'fa-clipboard-list',
            '基本設計' => 'fa-drafting-compass',
            '詳細設計' => 'fa-file-alt',
            '開発'     => 'fa-code',
            '実装'     => 'fa-code',
            'テスト'   => 'fa-vial',
            '単体テスト' => 'fa-vial',
            '結合テスト' => 'fa-flask',
            'リリース' => 'fa-rocket',
            '保守'     => 'fa-tools',
            '運用'     => 'fa-tools',
        ];

        foreach ($icons as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }
        return 'fa-tasks';
    }

    /**
     * 統計情報を取得
     */
    public function getStats(): array
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults(false);
        $inactive = $this->where('status', 'inactive')->countAllResults();

        $lastUpdated = $this->orderBy('updated_at', 'DESC')->first();
        $lastUpdatedAt = $lastUpdated ? $lastUpdated['updated_at'] : null;

        return [
            'total'           => $total,
            'active'          => $active,
            'inactive'        => $inactive,
            'last_updated_at' => $lastUpdatedAt,
        ];
    }

    /**
     * 相対日時を取得
     */
    public static function getRelativeDate(?string $datetime): string
    {
        if (!$datetime) {
            return '-';
        }

        $now = new \DateTime();
        $date = new \DateTime($datetime);
        $diff = $now->diff($date);

        if ($diff->days === 0) {
            return '今日';
        } elseif ($diff->days === 1) {
            return '昨日';
        } elseif ($diff->days < 7) {
            return $diff->days . '日前';
        } else {
            return $date->format('Y/m/d');
        }
    }

    /**
     * 検索・フィルタ付き一覧取得
     */
    public function getProcessMasters(?string $search = null, ?string $status = null): array
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('name', $search);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $builder->orderBy('sort_order', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * 有効な工程のみ取得
     */
    public function getActiveProcessMasters(): array
    {
        return $this->where('status', 'active')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * 次のsort_orderを取得
     */
    public function getNextSortOrder(): int
    {
        $max = $this->selectMax('sort_order')->first();
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
}
