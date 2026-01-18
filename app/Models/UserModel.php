<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
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
        'name'     => 'required|max_length[255]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => '名前は必須です。',
        ],
        'email' => [
            'required'    => 'メールアドレスは必須です。',
            'valid_email' => '有効なメールアドレスを入力してください。',
            'is_unique'   => 'このメールアドレスは既に登録されています。',
        ],
        'password' => [
            'required'   => 'パスワードは必須です。',
            'min_length' => 'パスワードは8文字以上で入力してください。',
        ],
    ];
    protected $skipValidation = false;

    /**
     * メールアドレスでユーザーを検索
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * パスワードをハッシュ化して保存
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * パスワードを検証
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
