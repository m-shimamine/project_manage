<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'status',
        'contact_name',
        'contacts',
        'project_leader',
        'members',
        'phone',
        'email',
        'postal_code',
        'city',
        'address',
        'notes',
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
        'status' => 'required|in_list[active,maintenance,inactive]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => '会社名は必須です。',
        ],
        'status' => [
            'required' => 'ステータスは必須です。',
            'in_list'  => '無効なステータスです。',
        ],
    ];
    protected $skipValidation = false;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeJsonFields'];
    protected $beforeUpdate   = ['encodeJsonFields'];
    protected $afterFind      = ['decodeJsonFields'];

    /**
     * JSON フィールドをエンコード
     */
    protected function encodeJsonFields(array $data): array
    {
        if (isset($data['data']['members']) && is_array($data['data']['members'])) {
            $data['data']['members'] = json_encode($data['data']['members']);
        }
        if (isset($data['data']['contacts']) && is_array($data['data']['contacts'])) {
            $data['data']['contacts'] = json_encode($data['data']['contacts']);
        }
        return $data;
    }

    /**
     * JSON フィールドをデコード
     */
    protected function decodeJsonFields(array $data): array
    {
        if ($data['singleton']) {
            $data['data'] = $this->decodeRow($data['data']);
        } else {
            foreach ($data['data'] as &$row) {
                $row = $this->decodeRow($row);
            }
        }
        return $data;
    }

    /**
     * 行のJSONフィールドをデコード
     */
    private function decodeRow(?array $row): ?array
    {
        if ($row === null) {
            return null;
        }
        if (isset($row['members']) && is_string($row['members'])) {
            $row['members'] = json_decode($row['members'], true) ?? [];
        }
        if (isset($row['contacts']) && is_string($row['contacts'])) {
            $row['contacts'] = json_decode($row['contacts'], true) ?? [];
        }
        return $row;
    }

    /**
     * ステータスリスト
     */
    public static function getStatuses(): array
    {
        return [
            'active'      => '進行中',
            'maintenance' => '保守',
            'inactive'    => '取引停止',
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
            'active'      => 'bg-emerald-50 text-emerald-600',
            'maintenance' => 'bg-blue-50 text-blue-600',
            'inactive'    => 'bg-slate-100 text-slate-600',
            default       => 'bg-slate-100 text-slate-600',
        };
    }

    /**
     * イニシャルを取得
     */
    public static function getInitial(string $name): string
    {
        return mb_substr($name, 0, 1);
    }

    /**
     * カラーを取得（名前のハッシュから生成）
     */
    public static function getColor(string $name): string
    {
        $colors = [
            'from-emerald-400 to-teal-500',
            'from-blue-400 to-indigo-500',
            'from-amber-400 to-orange-500',
            'from-violet-400 to-purple-500',
            'from-rose-400 to-pink-500',
            'from-cyan-400 to-blue-500',
        ];

        $hash = crc32($name);
        return $colors[abs($hash) % count($colors)];
    }

    /**
     * 統計情報を取得
     */
    public function getStats(): array
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults(false);
        $maintenance = $this->where('status', 'maintenance')->countAllResults(false);
        $inactive = $this->where('status', 'inactive')->countAllResults();

        return [
            'total'       => $total,
            'active'      => $active,
            'maintenance' => $maintenance,
            'inactive'    => $inactive,
        ];
    }

    /**
     * 検索・フィルタ付き一覧取得
     */
    public function getCustomers(?string $search = null, ?string $status = null): array
    {
        $builder = $this->builder();

        if ($search) {
            $builder->like('name', $search);
        }

        if ($status) {
            $builder->where('status', $status);
        }

        $builder->orderBy('created_at', 'DESC');

        $results = $builder->get()->getResultArray();

        // JSONフィールドをデコード
        foreach ($results as &$row) {
            $row = $this->decodeRow($row);
        }

        return $results;
    }
}
