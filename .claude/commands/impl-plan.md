# /impl-plan - 実装計画を生成

モック機能仕様書を基に、実装計画を生成・更新します。

## 使い方
```
/impl-plan [機能名]
```

例: `/impl-plan task-list`

## 前提条件
- `/mock-spec` で機能仕様書が生成済みであること
- `.doc/mock-spec/{機能名}-spec.md` が存在すること

## 処理内容

### 1. 必要ファイルの読み込み

以下のファイルを読み込んでください:

1. **モック機能仕様書**: `.doc/mock-spec/{機能名}-spec.md`
2. **既存の実装計画書**（あれば）: `.doc/{機能名}/implementation_plan.md`
3. **既存の実装コード**（あれば）:
   - `public/js/{対応パス}/*.js`
   - `app/Controllers/*Controller.php`
   - `app/Services/*Service.php`
   - `app/Models/*Model.php`

### 2. 実装クラス設計

モック機能仕様書の関数を、以下の観点でクラスに分割:

```markdown
## クラス設計

| クラス名 | 責務 | 対応するモック関数 |
|---------|------|-------------------|
| TaskListApp | メイン制御 | init(), switchViewMode() |
| TaskTableRenderer | テーブル描画 | renderTable(), renderRow() |
| TaskDataManager | データ管理 | saveAllTasks(), loadTasks() |
```

### 3. 実装フェーズの設計

機能を依存関係に基づいてフェーズ分け:

```markdown
## 実装フェーズ

### Phase 1: 基盤（必須・最優先）
- [ ] TaskListApp.js - 初期化処理
- [ ] TaskTableRenderer.js - テーブル描画

### Phase 2: CRUD操作
- [ ] TaskDataManager.js - データ保存・読み込み
- [ ] 新規行追加、削除機能

### Phase 3: 編集機能
- [ ] 編集モード切替
- [ ] インライン編集

### Phase 4: 高度な機能
- [ ] ドラッグ&ドロップ
- [ ] 一括編集
- [ ] フィルター
```

### 4. モック関数マッピング表の生成

```markdown
## モック関数 → 実装マッピング

| モック関数 | 実装クラス | メソッド名 | 実装状況 |
|-----------|-----------|-----------|---------|
| renderTable() | TaskTableRenderer | render() | ⬜ 未実装 |
| addNewRow() | TaskDataManager | addTask() | ⬜ 未実装 |
| saveAllTasks() | TaskDataManager | saveAll() | ⬜ 未実装 |
```

### 5. 出力先

#### 新規作成の場合
```
.doc/{機能名}/implementation_plan.md
```

#### 既存計画書がある場合
計画書の末尾に「モック関数マッピング」セクションを追加

### 6. 出力フォーマット

```markdown
# {機能名} 実装計画書

更新日時: YYYY-MM-DD HH:mm
モック仕様書: .doc/mock-spec/{機能名}-spec.md

## 概要
(機能の概要)

## クラス設計
(上記のクラス設計テーブル)

## 実装フェーズ
(上記のフェーズ別チェックリスト)

## モック関数マッピング
(上記のマッピング表)

## API設計
(必要なAPIエンドポイント)

## データベース設計
(必要なテーブル・カラム)
```

## 注意事項
- 既存の実装計画書がある場合は内容を尊重し、マッピング表を追記
- 実装状況は既存コードをスキャンして自動判定
- フェーズは依存関係を考慮して設計
