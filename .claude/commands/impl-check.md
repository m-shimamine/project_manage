# /impl-check - 実装漏れチェック（サブエージェント使用）

モック機能仕様書と実装コードを比較し、実装漏れを検出します。
**サブエージェントを使用して並列でチェックを実行します。**

## 使い方
```
/impl-check [機能名]
```

例: `/impl-check task-list`

## 前提条件
- `/mock-spec` で機能仕様書が生成済みであること
- 実装コードが存在すること

## 処理内容

### 1. サブエージェントの起動

以下の3つのサブエージェントを**並列で**起動してください:

```javascript
// 並列でTaskツールを3回呼び出す
Task({
  description: "Check function implementation",
  subagent_type: "Explore",
  prompt: "【関数実装チェック】..."
})

Task({
  description: "Check data structure implementation",
  subagent_type: "Explore",
  prompt: "【データ構造チェック】..."
})

Task({
  description: "Check event handler implementation",
  subagent_type: "Explore",
  prompt: "【イベントハンドラチェック】..."
})
```

### 2. サブエージェント①: 関数実装チェック

プロンプト:
```
モック機能仕様書 .doc/mock-spec/{機能名}-spec.md を読み、
「実装チェックリスト」の各関数が実装されているか確認してください。

確認対象:
- public/js/ 配下の対応するJSファイル
- app/Controllers/ 配下のPHPファイル

出力形式:
| 関数名 | モック | 実装 | 状況 |
|--------|-------|------|------|
| renderTable | ✅ | ✅ | OK |
| addNewRow | ✅ | ❌ | 未実装 |

未実装の関数がある場合、どのファイルに実装すべきか提案してください。
```

### 3. サブエージェント②: データ構造チェック

プロンプト:
```
モック機能仕様書 .doc/mock-spec/{機能名}-spec.md を読み、
「データ構造」セクションの各フィールドが実装で使用されているか確認してください。

確認対象:
- JSファイル内のオブジェクト構造
- PHPのModel/Entity定義
- データベースマイグレーション

出力形式:
| フィールド名 | モック | JS実装 | PHP実装 | DB | 状況 |
|-------------|-------|--------|--------|-----|------|
| id | ✅ | ✅ | ✅ | ✅ | OK |
| progress | ✅ | ✅ | ❌ | ❌ | 部分実装 |

不足しているフィールドがある場合、追加すべき場所を提案してください。
```

### 4. サブエージェント③: イベントハンドラチェック

プロンプト:
```
モック機能仕様書 .doc/mock-spec/{機能名}-spec.md を読み、
「イベントハンドラ」セクションの各イベントが実装されているか確認してください。

確認対象:
- JSファイル内のaddEventListener
- onclick等の属性
- jQueryのon()メソッド

出力形式:
| イベント | 要素 | モック | 実装 | 状況 |
|---------|------|-------|------|------|
| click | #addRowBtn | ✅ | ✅ | OK |
| dblclick | tr.task-row | ✅ | ❌ | 未実装 |

未実装のイベントがある場合、実装コード例を提示してください。
```

### 5. 結果の集約

3つのサブエージェントの結果を集約し、以下の形式でレポートを生成:

```markdown
# {機能名} 実装チェックレポート

チェック日時: YYYY-MM-DD HH:mm
モック仕様書: .doc/mock-spec/{機能名}-spec.md

## サマリー
- 関数実装: ✅ 15/20 (75%)
- データ構造: ✅ 12/15 (80%)
- イベントハンドラ: ✅ 8/10 (80%)
- **総合: 35/45 (78%)**

## 未実装項目

### 関数（5件）
1. `undoChanges()` - TaskUndoManager.js に実装が必要
2. `applyFilter()` - TaskFilterManager.js に実装が必要
...

### データ構造（3件）
1. `delay` フィールド - TaskModel.php に追加が必要
...

### イベントハンドラ（2件）
1. `dblclick` on `tr.task-row` - TaskTableRenderer.js に追加が必要
...

## 推奨アクション
1. Phase 3 の Undo機能を優先実装
2. TaskFilterManager クラスを新規作成
3. データベースマイグレーションで delay カラム追加
```

### 6. 出力先
```
.doc/{機能名}/impl-check-report.md
```

## 注意事項
- サブエージェントは必ず**並列で**起動すること（効率化のため）
- チェック結果は客観的事実のみを記載
- 未実装項目には具体的な実装場所を提案
- 重大な漏れ（必須機能の未実装）は強調表示