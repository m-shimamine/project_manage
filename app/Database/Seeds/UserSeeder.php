<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'       => '山田太郎',
                'email'      => 'admin@example.com',
                'password'   => password_hash('password', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => '佐藤花子',
                'email'      => 'user@example.com',
                'password'   => password_hash('password', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $user) {
            // 既存チェック
            $existing = $this->db->table('users')
                ->where('email', $user['email'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('users')->insert($user);
            }
        }
    }
}
