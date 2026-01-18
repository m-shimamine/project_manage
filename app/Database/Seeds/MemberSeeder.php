<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $members = [
            ['name' => '山田 太郎', 'email' => 'yamada@example.com', 'department' => '開発部', 'position' => 'マネージャー'],
            ['name' => '佐藤 花子', 'email' => 'sato@example.com', 'department' => '開発部', 'position' => 'リーダー'],
            ['name' => '鈴木 一郎', 'email' => 'suzuki@example.com', 'department' => '開発部', 'position' => 'エンジニア'],
            ['name' => '高橋 二郎', 'email' => 'takahashi@example.com', 'department' => '開発部', 'position' => 'エンジニア'],
            ['name' => '伊藤 健一', 'email' => 'ito@example.com', 'department' => '営業部', 'position' => 'マネージャー'],
            ['name' => '渡辺 美咲', 'email' => 'watanabe@example.com', 'department' => '営業部', 'position' => 'スタッフ'],
            ['name' => '中村 大輔', 'email' => 'nakamura@example.com', 'department' => 'デザイン部', 'position' => 'デザイナー'],
            ['name' => '木村 拓也', 'email' => 'kimura@example.com', 'department' => '開発部', 'position' => 'リーダー'],
            ['name' => '林 直樹', 'email' => 'hayashi@example.com', 'department' => '開発部', 'position' => 'エンジニア'],
            ['name' => '森 優子', 'email' => 'mori@example.com', 'department' => 'デザイン部', 'position' => 'デザイナー'],
            ['name' => '池田 真一', 'email' => 'ikeda@example.com', 'department' => '開発部', 'position' => 'エンジニア'],
            ['name' => '清水 恵', 'email' => 'shimizu@example.com', 'department' => '営業部', 'position' => 'スタッフ'],
            ['name' => '小林 誠', 'email' => 'kobayashi@example.com', 'department' => '開発部', 'position' => 'マネージャー'],
            ['name' => '加藤 明', 'email' => 'kato@example.com', 'department' => '開発部', 'position' => 'エンジニア'],
            ['name' => '田中 商事', 'email' => 'tanaka@example.com', 'department' => '営業部', 'position' => 'スタッフ'],
        ];

        foreach ($members as $member) {
            $member['is_active'] = 1;
            $member['created_at'] = date('Y-m-d H:i:s');
            $member['updated_at'] = date('Y-m-d H:i:s');

            // 既存チェック
            $existing = $this->db->table('members')
                ->where('email', $member['email'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('members')->insert($member);
            } else {
                $this->db->table('members')
                    ->where('email', $member['email'])
                    ->update($member);
            }
        }
    }
}
