# プロジェクト管理システム - CodeIgniter 4

## プロジェクト概要
プロジェクトスケジュール、コスト、進捗、チームワークロードを管理するためのシステム。
Laravelから移行したCodeIgniter 4アプリケーション。

## 技術スタック
- **フレームワーク**: CodeIgniter 4
- **PHP**: 8.1+
- **データベース**: MySQL 8.0
- **フロントエンド**: TailwindCSS (CDN), Alpine.js, Jquery, Font Awesome
- **コンテナ**: Docker (PHP, Nginx, MySQL, phpMyAdmin)

## ディレクトリ構造
```
project_manage_ci/
├── app/
│   ├── Config/           # 設定ファイル
│   ├── Controllers/      # コントローラー
│   │   ├── Auth/         # 認証関連
│   │   └── ...
│   ├── Database/
│   │   ├── Migrations/   # マイグレーション
│   │   └── Seeds/        # シーダー
│   ├── Filters/          # フィルター（ミドルウェア相当）
│   ├── Models/           # モデル
│   ├── Services/         # サービス層
│   └── Views/            # ビュー
│       ├── layouts/      # レイアウト
│       ├── auth/         # 認証画面
│       ├── customers/    # 顧客画面
│       └── projects/     # プロジェクト画面
├── docker/               # Docker設定
├── public/               # 公開ディレクトリ
└── writable/             # 書き込み可能ディレクトリ
```

## 開発コマンド

### Docker操作
```bash
# 起動
docker-compose up -d

# 停止
docker-compose down

# コンテナに入る
docker-compose exec ci4_app bash
```

### マイグレーション
```bash
# マイグレーション実行
php spark migrate

# ロールバック
php spark migrate:rollback

# リフレッシュ
php spark migrate:refresh
```

### シーダー
```bash
# 全シーダー実行
php spark db:seed DatabaseSeeder

# 個別実行
php spark db:seed UserSeeder
php spark db:seed MemberSeeder
php spark db:seed CustomerSeeder
```

## コーディング規約

### 命名規則
- **コントローラー**: PascalCase + Controller (例: CustomerController)
- **モデル**: PascalCase + Model (例: CustomerModel)
- **ビュー**: snake_case (例: customers/index.php)
- **メソッド**: camelCase (例: getCustomers)

### バリデーション
- モデル内でバリデーションルールを定義
- コントローラーで追加のバリデーションを実施

### セキュリティ
- CSRFトークンを全フォームで使用: `<?= csrf_field() ?>`
- 出力時は必ずエスケープ: `<?= esc($value) ?>`

### レスポンシブデザイン
- **モバイルファースト**: スマホ画面でもレイアウトが崩れないよう設計
- **ブレークポイント**: TailwindCSSのsm/md/lg/xlを活用
- **スマホ専用デザイン**: 画面幅の関係上PC版と同じ表示が難しい場合は、スマホ用に別途デザインを検討
- **タッチ操作**: ボタンやリンクはタップしやすいサイズ（最小44x44px推奨）
- **テーブル**: スマホではカード形式に変換、または横スクロール対応

## 主要機能

### 認証
- ログイン/ログアウト
- セッション管理

### 顧客管理
- CRUD操作
- 担当者情報管理
- プロジェクト紐付け

### プロジェクト管理
- CRUD操作
- ステータス管理（計画中/進行中/保留中/完了/中止）
- メンバー管理

## アクセスURL
- **アプリケーション**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

## デモアカウント
- **Email**: admin@example.com
- **Password**: password

---

## モック駆動開発ワークフロー

このプロジェクトはモック駆動開発を採用しています。
実装時は以下のワークフローに従ってください。

### ワークフロー概要

```
1. モック作成
   public/mock/{機能名}/ にHTML/JSモックを作成
       ↓
2. /mock-spec {機能名}
   モックから機能仕様書を自動生成
       ↓
3. /impl-plan {機能名}
   実装計画書を生成（クラス設計、フェーズ分け）
       ↓
4. 実装作業
   計画書に従ってコードを実装
       ↓
5. /impl-check {機能名}
   サブエージェントで実装漏れをチェック
       ↓
6. 漏れがあれば修正 → 5に戻る
```

### カスタムコマンド

| コマンド | 説明 |
|---------|------|
| `/mock-spec [機能名]` | モックJSから機能仕様書を生成 |
| `/impl-plan [機能名]` | 実装計画書を生成・更新 |
| `/impl-check [機能名]` | 実装漏れチェック（サブエージェント並列実行） |

### 重要ファイル

実装前に必ず以下を確認すること:

1. **モックファイル**: `public/mock/{機能名}/`
2. **モック機能仕様書**: `.doc/mock-spec/{機能名}-spec.md`
3. **実装計画書**: `.doc/{機能名}/implementation_plan.md`

### ディレクトリ構造（モック関連）

```
project_manage_ci/
├── .claude/
│   └── commands/           # カスタムコマンド定義
│       ├── mock-spec.md    # /mock-spec コマンド
│       ├── impl-plan.md    # /impl-plan コマンド
│       └── impl-check.md   # /impl-check コマンド
├── .doc/
│   ├── mock-spec/          # モック機能仕様書（自動生成）
│   │   └── {機能名}-spec.md
│   ├── {機能名}/           # 実装計画書
│   │   ├── implementation_plan.md
│   │   └── impl-check-report.md
│   └── ...
└── public/
    └── mock/               # HTMLモック・JSモック
        ├── tasks/
        ├── schedule/
        └── ...
```

### 実装時のルール

1. **モック仕様書を必ず読む**: 実装前に `.doc/mock-spec/{機能名}-spec.md` を確認
2. **マッピング表を更新**: 実装完了した関数は実装計画書のマッピング表を更新
3. **実装チェックを実行**: 機能完成後は `/impl-check` で漏れを確認
4. **サブエージェント活用**: チェックは並列サブエージェントで効率化
