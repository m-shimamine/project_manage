<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'customer_id',
        'status',
        'description',
        'start_date',
        'end_date',
        'project_leader',
        'members',
        'budget',
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
        'name'        => 'required|max_length[255]',
        'customer_id' => 'required|is_natural_no_zero',
        'status'      => 'required|in_list[planning,in_progress,on_hold,completed,cancelled]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'プロジェクト名は必須です。',
        ],
        'customer_id' => [
            'required' => '顧客は必須です。',
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
        return $row;
    }

    /**
     * ステータスリスト
     */
    public static function getStatuses(): array
    {
        return [
            'planning'    => '企画中',
            'in_progress' => '進行中',
            'on_hold'     => '保留中',
            'completed'   => '完了',
            'cancelled'   => '中止',
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
            'planning'    => 'bg-amber-50 text-amber-600',
            'in_progress' => 'bg-emerald-50 text-emerald-600',
            'on_hold'     => 'bg-slate-100 text-slate-600',
            'completed'   => 'bg-blue-50 text-blue-600',
            'cancelled'   => 'bg-red-50 text-red-600',
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
            'from-blue-400 to-indigo-500',
            'from-emerald-400 to-teal-500',
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
        return [
            'total'       => $this->countAll(),
            'planning'    => $this->where('status', 'planning')->countAllResults(false),
            'in_progress' => $this->where('status', 'in_progress')->countAllResults(false),
            'on_hold'     => $this->where('status', 'on_hold')->countAllResults(false),
            'completed'   => $this->where('status', 'completed')->countAllResults(),
        ];
    }

    /**
     * 検索・フィルタ付き一覧取得（顧客情報込み）
     */
    public function getProjects(?string $search = null, ?string $status = null, ?int $customerId = null, int $perPage = 12): array
    {
        $builder = $this->db->table('projects p')
            ->select('p.*, c.name as customer_name')
            ->join('customers c', 'c.id = p.customer_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('p.name', $search)
                ->orLike('c.name', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('p.status', $status);
        }

        if ($customerId) {
            $builder->where('p.customer_id', $customerId);
        }

        $builder->orderBy('p.created_at', 'DESC');

        $results = $builder->get()->getResultArray();

        // JSONフィールドをデコード
        foreach ($results as &$row) {
            $row = $this->decodeRow($row);
        }

        return $results;
    }

    /**
     * 顧客情報付きでプロジェクトを取得
     */
    public function getProjectWithCustomer(int $id): ?array
    {
        $result = $this->db->table('projects p')
            ->select('p.*, c.name as customer_name')
            ->join('customers c', 'c.id = p.customer_id', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        return $this->decodeRow($result);
    }

    /**
     * 顧客IDでプロジェクトを取得
     */
    public function getProjectsByCustomerId(int $customerId): array
    {
        $results = $this->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $results;
    }
}
