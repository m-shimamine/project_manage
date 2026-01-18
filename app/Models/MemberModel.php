<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'phone',
        'department',
        'position',
        'role',
        'password',
        'is_active',
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
        'name' => 'required|max_length[255]',
    ];
    protected $validationMessages = [
        'name' => [
            'required'   => 'メンバー名は必須です。',
            'max_length' => 'メンバー名は255文字以内で入力してください。',
        ],
    ];
    protected $skipValidation = false;

    /**
     * 権限リストを取得
     */
    public static function getRoles(): array
    {
        return [
            'admin'  => '管理者',
            'leader' => 'リーダー',
            'member' => 'メンバー',
        ];
    }

    /**
     * 権限ラベルを取得
     */
    public static function getRoleLabel(string $role): string
    {
        $roles = self::getRoles();
        return $roles[$role] ?? $role;
    }

    /**
     * 権限の色を取得
     */
    public static function getRoleColor(string $role): string
    {
        return match ($role) {
            'admin'  => 'role-admin',
            'leader' => 'role-leader',
            'member' => 'role-member',
            default  => 'bg-slate-100 text-slate-800',
        };
    }

    /**
     * 部署リストを取得
     */
    public static function getDepartments(): array
    {
        return [
            'development' => '開発部',
            'sales'       => '営業部',
            'design'      => 'デザイン部',
            'management'  => '管理部',
            'support'     => 'サポート部',
        ];
    }

    /**
     * 部署ラベルを取得
     */
    public static function getDepartmentLabel(string $department): string
    {
        $departments = self::getDepartments();
        return $departments[$department] ?? $department;
    }

    /**
     * 部署の色を取得
     */
    public static function getDepartmentColor(string $department): string
    {
        return match ($department) {
            'development' => 'bg-blue-100 text-blue-800',
            'sales'       => 'bg-green-100 text-green-800',
            'design'      => 'bg-purple-100 text-purple-800',
            'management'  => 'bg-amber-100 text-amber-800',
            'support'     => 'bg-cyan-100 text-cyan-800',
            default       => 'bg-slate-100 text-slate-800',
        };
    }

    /**
     * ステータスラベルを取得
     */
    public static function getStatusLabel(bool $isActive): string
    {
        return $isActive ? '有効' : '無効';
    }

    /**
     * ステータスの色を取得
     */
    public static function getStatusColor(bool $isActive): string
    {
        return $isActive
            ? 'bg-emerald-100 text-emerald-800'
            : 'bg-slate-100 text-slate-600';
    }

    /**
     * イニシャルを取得
     */
    public static function getInitial(string $name): string
    {
        return mb_substr($name, 0, 1, 'UTF-8');
    }

    /**
     * 名前からカラーを生成
     */
    public static function getColor(string $name): string
    {
        $colors = [
            'from-blue-400 to-blue-600',
            'from-emerald-400 to-emerald-600',
            'from-violet-400 to-violet-600',
            'from-rose-400 to-rose-600',
            'from-amber-400 to-amber-600',
            'from-cyan-400 to-cyan-600',
            'from-indigo-400 to-indigo-600',
            'from-pink-400 to-pink-600',
        ];

        $index = abs(crc32($name)) % count($colors);
        return $colors[$index];
    }

    /**
     * アクティブなメンバーのみ取得
     */
    public function getActiveMembers(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * メンバー一覧を取得（検索・フィルター対応）
     */
    public function getMembers(?string $search = null, ?string $department = null, ?string $status = null): array
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('position', $search)
                    ->groupEnd();
        }

        if (!empty($department)) {
            $builder->where('department', $department);
        }

        if ($status !== null && $status !== '') {
            $builder->where('is_active', $status === 'active' ? 1 : 0);
        }

        return $builder->orderBy('name', 'ASC')->get()->getResultArray();
    }

    /**
     * 統計情報を取得
     */
    public function getStats(): array
    {
        $total = $this->countAll();
        $active = $this->where('is_active', 1)->countAllResults(false);
        $inactive = $this->where('is_active', 0)->countAllResults(false);

        // 部署別カウント
        $departmentCounts = [];
        foreach (self::getDepartments() as $key => $label) {
            $departmentCounts[$key] = $this->where('department', $key)->countAllResults(false);
        }

        return [
            'total'       => $total,
            'active'      => $active,
            'inactive'    => $inactive,
            'departments' => $departmentCounts,
        ];
    }

    /**
     * メールアドレスで検索または作成
     */
    public function findOrCreate(array $data): array
    {
        if (!empty($data['email'])) {
            $existing = $this->where('email', $data['email'])->first();
            if ($existing) {
                $this->update($existing['id'], $data);
                return $this->find($existing['id']);
            }
        }

        $this->insert($data);
        return $this->find($this->getInsertID());
    }
}
