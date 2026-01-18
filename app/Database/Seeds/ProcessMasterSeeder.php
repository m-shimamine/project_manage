<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProcessMasterSeeder extends Seeder
{
    public function run()
    {
        $processes = [
            [
                'name'        => '要件定義',
                'description' => '顧客要件のヒアリングと要件定義書の作成',
                'status'      => 'active',
                'sort_order'  => 1,
            ],
            [
                'name'        => '基本設計',
                'description' => 'システム全体の基本設計と画面設計',
                'status'      => 'active',
                'sort_order'  => 2,
            ],
            [
                'name'        => '詳細設計',
                'description' => '機能ごとの詳細設計とDB設計',
                'status'      => 'active',
                'sort_order'  => 3,
            ],
            [
                'name'        => '開発・実装',
                'description' => 'プログラミングとコーディング作業',
                'status'      => 'active',
                'sort_order'  => 4,
            ],
            [
                'name'        => '単体テスト',
                'description' => '個別のプログラムやモジュールの動作確認',
                'status'      => 'active',
                'sort_order'  => 5,
            ],
            [
                'name'        => '結合テスト',
                'description' => 'システム全体の統合テスト',
                'status'      => 'active',
                'sort_order'  => 6,
            ],
            [
                'name'        => 'システムテスト',
                'description' => '本番環境を想定した総合的なテスト',
                'status'      => 'active',
                'sort_order'  => 7,
            ],
            [
                'name'        => '本番リリース',
                'description' => '本番環境へのデプロイと公開',
                'status'      => 'active',
                'sort_order'  => 8,
            ],
            [
                'name'        => '保守・運用',
                'description' => 'システムの保守と運用サポート',
                'status'      => 'inactive',
                'sort_order'  => 9,
            ],
        ];

        foreach ($processes as $process) {
            $process['created_at'] = date('Y-m-d H:i:s');
            $process['updated_at'] = date('Y-m-d H:i:s');

            // 既存チェック
            $existing = $this->db->table('process_masters')
                ->where('name', $process['name'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('process_masters')->insert($process);
            } else {
                $this->db->table('process_masters')
                    ->where('name', $process['name'])
                    ->update($process);
            }
        }
    }
}
