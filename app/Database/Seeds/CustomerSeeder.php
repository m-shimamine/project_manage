<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name'           => '株式会社ABC商事',
                'status'         => 'active',
                'contact_name'   => '田中 商事',
                'project_leader' => '山田 太郎',
                'members'        => json_encode(['佐藤 花子', '鈴木 一郎', '高橋 二郎']),
                'contacts'       => json_encode([
                    ['name' => '田中 商事', 'phone' => '03-1234-5678', 'email' => 'tanaka@abc-shoji.co.jp'],
                ]),
                'phone'          => '03-1234-5678',
                'email'          => 'tanaka@abc-shoji.co.jp',
                'postal_code'    => '150-0001',
                'city'           => '東京都渋谷区',
                'address'        => '神宮前1-2-3 ABCビル5F',
                'notes'          => '主要取引先。ECサイトの運営を行っている。',
            ],
            [
                'name'           => 'XYZ株式会社',
                'status'         => 'maintenance',
                'contact_name'   => '山本 次郎',
                'project_leader' => '伊藤 健一',
                'members'        => json_encode(['渡辺 美咲', '中村 大輔']),
                'contacts'       => json_encode([
                    ['name' => '山本 次郎', 'phone' => '03-9876-5432', 'email' => 'yamamoto@xyz.co.jp'],
                ]),
                'phone'          => '03-9876-5432',
                'email'          => 'yamamoto@xyz.co.jp',
                'postal_code'    => '100-0001',
                'city'           => '東京都千代田区',
                'address'        => '丸の内1-1-1 XYZタワー10F',
                'notes'          => 'システム開発会社。長期パートナー。',
            ],
            [
                'name'           => 'テックソリューション株式会社',
                'status'         => 'active',
                'contact_name'   => '佐藤 技術',
                'project_leader' => '木村 拓也',
                'members'        => json_encode(['林 直樹', '森 優子', '池田 真一', '清水 恵']),
                'contacts'       => json_encode([
                    ['name' => '佐藤 技術', 'phone' => '03-5555-1234', 'email' => 'sato@techsol.co.jp'],
                ]),
                'phone'          => '03-5555-1234',
                'email'          => 'sato@techsol.co.jp',
                'postal_code'    => '160-0022',
                'city'           => '東京都新宿区',
                'address'        => '新宿3-4-5 テックビル3F',
                'notes'          => 'ソフトウェア開発を専門とする企業。',
            ],
            [
                'name'           => 'グローバルトレード株式会社',
                'status'         => 'inactive',
                'contact_name'   => '高橋 貿易',
                'project_leader' => '小林 誠',
                'members'        => json_encode(['加藤 明']),
                'contacts'       => json_encode([
                    ['name' => '高橋 貿易', 'phone' => '03-7777-8888', 'email' => 'takahashi@globaltrade.co.jp'],
                ]),
                'phone'          => '03-7777-8888',
                'email'          => 'takahashi@globaltrade.co.jp',
                'postal_code'    => '105-0001',
                'city'           => '東京都港区',
                'address'        => '虎ノ門2-3-4 グローバルビル8F',
                'notes'          => '国際貿易・物流を手がける企業。',
            ],
        ];

        foreach ($customers as $customer) {
            $customer['created_at'] = date('Y-m-d H:i:s');
            $customer['updated_at'] = date('Y-m-d H:i:s');

            // 既存チェック
            $existing = $this->db->table('customers')
                ->where('email', $customer['email'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('customers')->insert($customer);
            } else {
                $this->db->table('customers')
                    ->where('email', $customer['email'])
                    ->update($customer);
            }
        }
    }
}
