# スケジュール管理機能 実装計画書

## 1. 概要

プロジェクト管理システムにスケジュール管理（ガントチャート）機能を追加する。
モック（`public/mock/schedule/index.html`）を基に実装を行う。

### 1.1 基本方針

- **Ajax優先**: 表示・データ更新は原則Ajaxで処理
- **Submit対象**: 以下の操作のみフォームsubmitを使用
  1. カレンダーの一括更新（複数タスクの日付変更を保存）
  2. プロジェクト選択（画面遷移を伴う）
  3. タスク切り替え（ガントチャート ⇔ タスクリスト）

---

## 2. データベース設計

### 2.1 設計方針

- スケジュール管理（ガントチャート）とタスク一覧で共通のテーブルを使用
- 営業工数、予定原価、出来高（実績原価）などのコスト関連フィールドを含める
- サブタスクは `parent_id` による自己参照で表現
- 画面名（screen）フィールドで開発タスクの対象画面を管理
- **工程はマスタ管理**: `process_id` で `process_masters` テーブルを参照（文字列ではなくID）

### 2.2 新規テーブル

#### `tasks` テーブル
| カラム名 | 型 | 説明 |
|---------|-----|------|
| id | BIGINT UNSIGNED | 主キー |
| project_id | BIGINT UNSIGNED | プロジェクトID (FK → projects) |
| parent_id | BIGINT UNSIGNED NULL | 親タスクID (自己参照、サブタスク用) |
| process_id | INT UNSIGNED | 工程ID (FK → process_masters) |
| screen_name | VARCHAR(100) NULL | 画面名（開発タスクの対象画面） |
| task_name | VARCHAR(255) | タスク名 |
| sort_order | INT | 表示順 |
| level | TINYINT | 階層レベル (0:工程, 1:親タスク, 2:サブタスク) |
| assignee_id | BIGINT UNSIGNED NULL | 担当者ID (FK → members) |
| status | ENUM('not_started','in_progress','completed','on_hold') | ステータス |
| **--- 予定（計画）---** | | |
| sales_man_days | DECIMAL(10,2) NULL | 営業工数（人日）※顧客折衝等 |
| planned_man_days | DECIMAL(10,2) NULL | 予定工数（人日） |
| planned_start_date | DATE NULL | 予定開始日 |
| planned_end_date | DATE NULL | 予定終了日 |
| planned_cost | INT UNSIGNED NULL | 予定原価（円） |
| **--- 実績 ---** | | |
| actual_man_days | DECIMAL(10,2) NULL | 実績工数（人日） |
| actual_start_date | DATE NULL | 実績開始日 |
| actual_end_date | DATE NULL | 実績終了日 |
| actual_cost | INT UNSIGNED NULL | 出来高・実績原価（円） |
| **--- その他 ---** | | |
| progress | TINYINT UNSIGNED DEFAULT 0 | 進捗率 (0-100) |
| delay_days | INT DEFAULT 0 | 遅延日数（正:遅れ、負:先行） |
| description | TEXT NULL | 説明・備考 |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |
| deleted_at | DATETIME NULL | 削除日時（論理削除） |

**インデックス:**
- `idx_tasks_project_id` (project_id)
- `idx_tasks_parent_id` (parent_id)
- `idx_tasks_process_id` (process_id)
- `idx_tasks_assignee_id` (assignee_id)
- `idx_tasks_status` (status)
- `idx_tasks_planned_dates` (planned_start_date, planned_end_date)

**外部キー:**
- `fk_tasks_project` → projects(id)
- `fk_tasks_parent` → tasks(id) ON DELETE CASCADE
- `fk_tasks_process` → process_masters(id)
- `fk_tasks_assignee` → members(id) ON DELETE SET NULL

#### `task_history` テーブル（変更履歴）
| カラム名 | 型 | 説明 |
|---------|-----|------|
| id | BIGINT UNSIGNED | 主キー |
| task_id | BIGINT UNSIGNED | タスクID (FK → tasks) |
| changed_by | BIGINT UNSIGNED | 変更者ID (FK → users) |
| field_name | VARCHAR(50) | 変更フィールド名 |
| old_value | TEXT NULL | 変更前の値 |
| new_value | TEXT NULL | 変更後の値 |
| created_at | DATETIME | 変更日時 |

**インデックス:**
- `idx_task_history_task_id` (task_id)
- `idx_task_history_changed_by` (changed_by)

### 2.3 工程マスタ（既存）

**注意**: 工程マスタは `.doc/process/implementation_plan.md` で別途定義済み。
`tasks.process_id` は `process_masters.id` を参照する。

#### `process_masters` テーブル（参照）
| カラム名 | 型 | 説明 |
|---------|-----|------|
| id | INT UNSIGNED | 主キー |
| name | VARCHAR(255) | 工程名 |
| description | TEXT NULL | 説明 |
| status | ENUM('active','inactive') | ステータス |
| sort_order | INT UNSIGNED | 表示順序 |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

**初期データ（Seeder）:**
| id | name | sort_order |
|----|------|------------|
| 1 | 要件定義 | 1 |
| 2 | 基本設計 | 2 |
| 3 | 詳細設計 | 3 |
| 4 | 開発・実装 | 4 |
| 5 | 単体テスト | 5 |
| 6 | 結合テスト | 6 |
| 7 | 本番リリース | 7 |
| 8 | 保守・運用 | 8 |

### 2.4 ER図（簡易）

```
process_masters (工程マスタ)
    └── 1:N ── tasks
                ├── parent_id → tasks (自己参照・サブタスク)
                ├── project_id → projects
                ├── assignee_id → members
                └── 1:N ── task_history
                            └── changed_by → users

projects (既存)
    ├── 1:N ── tasks
    └── N:M ── members (既存: project_members中間テーブル)
```

### 2.5 データ例

```json
// 親タスク
{
  "id": 1,
  "project_id": 1,
  "parent_id": null,
  "process_id": 1,          // FK → process_masters (要件定義)
  "screen_name": null,
  "task_name": "ヒアリング",
  "sort_order": 1,
  "level": 1,
  "assignee_id": 1,
  "status": "completed",
  "sales_man_days": 1.0,
  "planned_man_days": 2.0,
  "planned_start_date": "2024-01-15",
  "planned_end_date": "2024-01-25",
  "planned_cost": 200000,
  "actual_man_days": 2.0,
  "actual_start_date": "2024-01-15",
  "actual_end_date": "2024-01-24",
  "actual_cost": 200000,
  "progress": 100,
  "delay_days": 0,
  "description": null
}

// サブタスク
{
  "id": 2,
  "project_id": 1,
  "parent_id": 1,
  "process_id": 1,          // FK → process_masters (要件定義)
  "screen_name": null,
  "task_name": "顧客へのヒアリング日程調整",
  "sort_order": 1,
  "level": 2,
  "assignee_id": 2,
  "status": "completed",
  "sales_man_days": 0.5,
  "planned_man_days": 0.5,
  "planned_start_date": "2024-01-15",
  "planned_end_date": "2024-01-16",
  "planned_cost": 50000,
  "actual_man_days": 0.5,
  "actual_start_date": "2024-01-15",
  "actual_end_date": "2024-01-16",
  "actual_cost": 50000,
  "progress": 100,
  "delay_days": 0,
  "description": null
}
```

---

## 3. バックエンド実装

### 3.1 ファイル構成

```
app/
├── Controllers/
│   └── ScheduleController.php      # スケジュール画面コントローラー
├── Controllers/Api/
│   └── TaskApiController.php       # タスクAPI（Ajax用）
├── Models/
│   ├── TaskModel.php               # タスクモデル
│   └── TaskHistoryModel.php        # タスク履歴モデル
├── Services/
│   └── TaskService.php             # タスクサービス
└── Views/
    └── schedule/
        ├── index.php               # ガントチャート画面
        └── partials/
            ├── gantt_header.php    # カレンダーヘッダー
            ├── task_row.php        # タスク行
            └── task_modal.php      # タスク編集モーダル
```

### 3.2 ルーティング（`app/Config/Routes.php`）

```php
// スケジュール画面（ページ表示）
$routes->get('schedule', 'ScheduleController::index');
$routes->get('schedule/tasks', 'ScheduleController::taskList');  // タスクリスト表示

// タスクAPI（Ajax）
$routes->group('api/tasks', function($routes) {
    $routes->get('/', 'Api\TaskApiController::index');           // タスク一覧取得
    $routes->get('(:num)', 'Api\TaskApiController::show/$1');    // タスク詳細取得
    $routes->post('/', 'Api\TaskApiController::create');         // タスク作成
    $routes->put('(:num)', 'Api\TaskApiController::update/$1');  // タスク更新
    $routes->delete('(:num)', 'Api\TaskApiController::delete/$1'); // タスク削除
    $routes->post('bulk-update', 'Api\TaskApiController::bulkUpdate'); // 一括更新
    $routes->post('(:num)/progress', 'Api\TaskApiController::updateProgress/$1'); // 進捗更新
    $routes->post('reorder', 'Api\TaskApiController::reorder');  // 並び替え
});
```

### 3.3 ScheduleController（画面表示）

```php
<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Models\ProjectModel;

class ScheduleController extends BaseController
{
    protected TaskService $taskService;
    protected ProjectModel $projectModel;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->projectModel = new ProjectModel();
    }

    /**
     * ガントチャート画面表示
     * - プロジェクト選択変更時はsubmitでページ遷移
     */
    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $view = $this->request->getGet('view') ?? 'gantt'; // gantt or task

        $projects = $this->projectModel->getProjectsGroupedByCustomer();
        $selectedProject = $projectId ? $this->projectModel->find($projectId) : null;

        // 初期表示用タスクデータ（Ajax以外での初回ロード用）
        $tasks = $projectId
            ? $this->taskService->getTasksByProject($projectId)
            : [];

        return view('schedule/index', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'projectId' => $projectId,
            'tasks' => $tasks,
            'view' => $view,
        ]);
    }

    /**
     * タスクリスト表示（タスク切り替え時）
     */
    public function taskList()
    {
        $projectId = $this->request->getGet('project_id');
        // タスクビューへリダイレクト
        return redirect()->to('/schedule?project_id=' . $projectId . '&view=task');
    }
}
```

### 3.4 TaskApiController（Ajax処理）

```php
<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\TaskService;

class TaskApiController extends ResourceController
{
    protected $format = 'json';
    protected TaskService $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    /**
     * タスク一覧取得（Ajax）
     * GET /api/tasks?project_id=1
     */
    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $mode = $this->request->getGet('mode') ?? 'plan'; // plan or actual

        if (!$projectId) {
            return $this->respond(['error' => 'project_id is required'], 400);
        }

        $tasks = $this->taskService->getTasksByProject($projectId);

        return $this->respond([
            'success' => true,
            'data' => $tasks,
            'mode' => $mode,
        ]);
    }

    /**
     * タスク詳細取得（Ajax）
     * GET /api/tasks/{id}
     */
    public function show($id = null)
    {
        $task = $this->taskService->getTaskWithDetails($id);

        if (!$task) {
            return $this->respond(['error' => 'Task not found'], 404);
        }

        return $this->respond([
            'success' => true,
            'data' => $task,
        ]);
    }

    /**
     * タスク作成（Ajax）
     * POST /api/tasks
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        $result = $this->taskService->createTask($data);

        if ($result['success']) {
            return $this->respond($result, 201);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスク更新（Ajax）
     * PUT /api/tasks/{id}
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $result = $this->taskService->updateTask($id, $data);

        if ($result['success']) {
            return $this->respond($result);
        }

        return $this->respond($result, 400);
    }

    /**
     * タスク削除（Ajax）
     * DELETE /api/tasks/{id}
     */
    public function delete($id = null)
    {
        $result = $this->taskService->deleteTask($id);

        if ($result['success']) {
            return $this->respond($result);
        }

        return $this->respond($result, 400);
    }

    /**
     * 一括更新（Submit - カレンダー一括更新用）
     * POST /api/tasks/bulk-update
     */
    public function bulkUpdate()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['tasks'])) {
            return $this->respond(['error' => 'No tasks provided'], 400);
        }

        $result = $this->taskService->bulkUpdateTasks($data['tasks']);

        return $this->respond($result);
    }

    /**
     * 進捗率更新（Ajax）
     * POST /api/tasks/{id}/progress
     */
    public function updateProgress($id = null)
    {
        $data = $this->request->getJSON(true);
        $progress = $data['progress'] ?? null;

        if ($progress === null || $progress < 0 || $progress > 100) {
            return $this->respond(['error' => 'Invalid progress value'], 400);
        }

        $result = $this->taskService->updateProgress($id, $progress);

        return $this->respond($result);
    }

    /**
     * タスク並び替え（Ajax）
     * POST /api/tasks/reorder
     */
    public function reorder()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['order'])) {
            return $this->respond(['error' => 'No order provided'], 400);
        }

        $result = $this->taskService->reorderTasks($data['order']);

        return $this->respond($result);
    }
}
```

### 3.5 TaskService

```php
<?php

namespace App\Services;

use App\Models\TaskModel;
use App\Models\TaskHistoryModel;

class TaskService
{
    protected TaskModel $taskModel;
    protected TaskHistoryModel $historyModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->historyModel = new TaskHistoryModel();
    }

    /**
     * プロジェクトのタスク一覧を取得（階層構造）
     */
    public function getTasksByProject(int $projectId): array
    {
        return $this->taskModel
            ->where('project_id', $projectId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * タスク詳細取得（担当者情報含む）
     */
    public function getTaskWithDetails(int $id): ?array
    {
        return $this->taskModel
            ->select('tasks.*, members.name as assignee_name')
            ->join('members', 'members.id = tasks.assignee_id', 'left')
            ->find($id);
    }

    /**
     * タスク作成
     */
    public function createTask(array $data): array
    {
        // バリデーション
        if (!$this->taskModel->validate($data)) {
            return [
                'success' => false,
                'errors' => $this->taskModel->errors(),
            ];
        }

        // sort_order設定
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->getNextSortOrder($data['project_id'], $data['parent_id'] ?? null);
        }

        $id = $this->taskModel->insert($data);

        return [
            'success' => true,
            'data' => $this->taskModel->find($id),
        ];
    }

    /**
     * タスク更新
     */
    public function updateTask(int $id, array $data): array
    {
        $oldTask = $this->taskModel->find($id);

        if (!$oldTask) {
            return ['success' => false, 'error' => 'Task not found'];
        }

        // 変更履歴を記録
        $this->recordHistory($id, $oldTask, $data);

        $this->taskModel->update($id, $data);

        return [
            'success' => true,
            'data' => $this->taskModel->find($id),
        ];
    }

    /**
     * 一括更新（カレンダー一括保存用）
     */
    public function bulkUpdateTasks(array $tasks): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $updated = [];
        $errors = [];

        foreach ($tasks as $taskData) {
            if (!isset($taskData['id'])) {
                $errors[] = 'Task ID is required';
                continue;
            }

            $result = $this->updateTask($taskData['id'], $taskData);

            if ($result['success']) {
                $updated[] = $result['data'];
            } else {
                $errors[] = $result['error'] ?? 'Unknown error';
            }
        }

        $db->transComplete();

        return [
            'success' => empty($errors),
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * 進捗率更新
     */
    public function updateProgress(int $id, int $progress): array
    {
        $this->taskModel->update($id, ['progress' => $progress]);

        // ステータス自動更新
        $status = 'in_progress';
        if ($progress === 0) {
            $status = 'not_started';
        } elseif ($progress === 100) {
            $status = 'completed';
        }

        $this->taskModel->update($id, ['status' => $status]);

        return [
            'success' => true,
            'data' => $this->taskModel->find($id),
        ];
    }

    /**
     * タスク削除
     */
    public function deleteTask(int $id): array
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            return ['success' => false, 'error' => 'Task not found'];
        }

        // 子タスクも削除
        $this->taskModel->where('parent_id', $id)->delete();
        $this->taskModel->delete($id);

        return ['success' => true];
    }

    /**
     * 並び替え
     */
    public function reorderTasks(array $order): array
    {
        foreach ($order as $index => $taskId) {
            $this->taskModel->update($taskId, ['sort_order' => $index]);
        }

        return ['success' => true];
    }

    /**
     * 変更履歴を記録
     */
    protected function recordHistory(int $taskId, array $oldTask, array $newData): void
    {
        $trackFields = [
            'planned_start_date', 'planned_end_date',
            'actual_start_date', 'actual_end_date',
            'progress', 'status', 'assignee_id',
        ];

        $userId = session()->get('user_id') ?? 1;

        foreach ($trackFields as $field) {
            if (isset($newData[$field]) && ($oldTask[$field] ?? null) !== $newData[$field]) {
                $this->historyModel->insert([
                    'task_id' => $taskId,
                    'changed_by' => $userId,
                    'field_name' => $field,
                    'old_value' => $oldTask[$field] ?? null,
                    'new_value' => $newData[$field],
                ]);
            }
        }
    }

    /**
     * 次のsort_orderを取得
     */
    protected function getNextSortOrder(int $projectId, ?int $parentId): int
    {
        $query = $this->taskModel->where('project_id', $projectId);

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->where('parent_id IS NULL');
        }

        $max = $query->selectMax('sort_order')->first();

        return ($max['sort_order'] ?? 0) + 1;
    }
}
```

---

## 4. フロントエンド実装

### 4.1 JavaScript構成

```
public/js/
└── schedule/
    ├── main.js              # メイン初期化・イベント
    ├── gantt.js             # ガントチャート描画
    ├── task-api.js          # Ajax通信
    ├── drag-handler.js      # ドラッグ&ドロップ
    └── utils.js             # ユーティリティ
```

### 4.2 Ajax通信クラス（task-api.js）

```javascript
/**
 * タスクAPI通信クラス
 */
class TaskApi {
    constructor() {
        this.baseUrl = '/api/tasks';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }

    /**
     * 共通リクエストヘッダー
     */
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': this.csrfToken,
        };
    }

    /**
     * タスク一覧取得
     */
    async getTasks(projectId, mode = 'plan') {
        const response = await fetch(`${this.baseUrl}?project_id=${projectId}&mode=${mode}`, {
            headers: this.getHeaders(),
        });
        return response.json();
    }

    /**
     * タスク詳細取得
     */
    async getTask(id) {
        const response = await fetch(`${this.baseUrl}/${id}`, {
            headers: this.getHeaders(),
        });
        return response.json();
    }

    /**
     * タスク作成
     */
    async createTask(data) {
        const response = await fetch(this.baseUrl, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });
        return response.json();
    }

    /**
     * タスク更新
     */
    async updateTask(id, data) {
        const response = await fetch(`${this.baseUrl}/${id}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });
        return response.json();
    }

    /**
     * タスク削除
     */
    async deleteTask(id) {
        const response = await fetch(`${this.baseUrl}/${id}`, {
            method: 'DELETE',
            headers: this.getHeaders(),
        });
        return response.json();
    }

    /**
     * 一括更新（カレンダー保存用）
     */
    async bulkUpdate(tasks) {
        const response = await fetch(`${this.baseUrl}/bulk-update`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify({ tasks }),
        });
        return response.json();
    }

    /**
     * 進捗更新
     */
    async updateProgress(id, progress) {
        const response = await fetch(`${this.baseUrl}/${id}/progress`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify({ progress }),
        });
        return response.json();
    }

    /**
     * 並び替え
     */
    async reorder(order) {
        const response = await fetch(`${this.baseUrl}/reorder`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify({ order }),
        });
        return response.json();
    }
}

// グローバルインスタンス
window.taskApi = new TaskApi();
```

### 4.3 メインJavaScript（main.js）

```javascript
/**
 * スケジュール管理メインJS
 */
document.addEventListener('DOMContentLoaded', function() {
    const scheduleManager = new ScheduleManager();
    scheduleManager.init();
});

class ScheduleManager {
    constructor() {
        this.projectId = null;
        this.currentMode = 'plan';      // plan or actual
        this.currentView = 'gantt';      // gantt or task
        this.isEditMode = false;
        this.modifiedTasks = new Map();  // 変更されたタスク
        this.gantt = null;
    }

    init() {
        this.projectId = document.getElementById('project-select')?.value;
        this.bindEvents();
        this.loadTasks();
    }

    bindEvents() {
        // プロジェクト選択（Submit）
        document.getElementById('project-select')?.addEventListener('change', (e) => {
            this.onProjectChange(e.target.value);
        });

        // ガント/タスク切り替え（Submit）
        document.getElementById('btn-gantt')?.addEventListener('click', () => {
            this.switchView('gantt');
        });
        document.getElementById('btn-task')?.addEventListener('click', () => {
            this.switchView('task');
        });

        // 予定/実績切り替え（Ajax）
        document.getElementById('btn-plan')?.addEventListener('click', () => {
            this.switchMode('plan');
        });
        document.getElementById('btn-actual')?.addEventListener('click', () => {
            this.switchMode('actual');
        });

        // 表示/編集モード切り替え（Ajax）
        document.getElementById('btn-view-mode')?.addEventListener('click', () => {
            this.switchEditMode(false);
        });
        document.getElementById('btn-edit-mode')?.addEventListener('click', () => {
            this.switchEditMode(true);
        });

        // 一括保存ボタン（Submit）
        document.getElementById('btn-save-all')?.addEventListener('click', () => {
            this.saveAllChanges();
        });
    }

    /**
     * プロジェクト変更（Submit - ページ遷移）
     */
    onProjectChange(projectId) {
        if (this.hasUnsavedChanges()) {
            if (!confirm('保存されていない変更があります。破棄しますか？')) {
                return;
            }
        }
        // フォームSubmitでページ遷移
        window.location.href = `/schedule?project_id=${projectId}&view=${this.currentView}`;
    }

    /**
     * ガント/タスクビュー切り替え（Submit - ページ遷移）
     */
    switchView(view) {
        if (this.hasUnsavedChanges()) {
            if (!confirm('保存されていない変更があります。破棄しますか？')) {
                return;
            }
        }
        window.location.href = `/schedule?project_id=${this.projectId}&view=${view}`;
    }

    /**
     * 予定/実績モード切り替え（Ajax）
     */
    async switchMode(mode) {
        this.currentMode = mode;
        this.updateModeButtons(mode);
        await this.loadTasks();
    }

    /**
     * 編集モード切り替え（Ajax）
     */
    switchEditMode(isEdit) {
        this.isEditMode = isEdit;
        document.body.classList.toggle('edit-mode', isEdit);
        this.updateEditModeButtons(isEdit);

        if (this.gantt) {
            this.gantt.setEditMode(isEdit);
        }
    }

    /**
     * タスク読み込み（Ajax）
     */
    async loadTasks() {
        if (!this.projectId || this.projectId === 'all') {
            return;
        }

        try {
            const result = await taskApi.getTasks(this.projectId, this.currentMode);

            if (result.success) {
                this.renderTasks(result.data);
            } else {
                this.showError('タスクの読み込みに失敗しました');
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
            this.showError('通信エラーが発生しました');
        }
    }

    /**
     * タスク描画
     */
    renderTasks(tasks) {
        if (this.currentView === 'gantt') {
            if (!this.gantt) {
                this.gantt = new GanttChart('#gantt-container', {
                    onTaskUpdate: (taskId, data) => this.onTaskUpdate(taskId, data),
                    onTaskClick: (taskId) => this.onTaskClick(taskId),
                });
            }
            this.gantt.render(tasks, this.currentMode);
        } else {
            this.renderTaskList(tasks);
        }
    }

    /**
     * タスク更新（Ajax - 個別）
     */
    async onTaskUpdate(taskId, data) {
        // 変更を追跡
        this.modifiedTasks.set(taskId, { ...this.modifiedTasks.get(taskId), ...data });
        this.updateSaveButton();

        // リアルタイム更新（オプション）
        // await taskApi.updateTask(taskId, data);
    }

    /**
     * タスククリック（Ajax - モーダル表示）
     */
    async onTaskClick(taskId) {
        try {
            const result = await taskApi.getTask(taskId);

            if (result.success) {
                this.showTaskModal(result.data);
            }
        } catch (error) {
            console.error('Error loading task:', error);
        }
    }

    /**
     * 一括保存（Submit）
     */
    async saveAllChanges() {
        if (this.modifiedTasks.size === 0) {
            return;
        }

        const tasks = Array.from(this.modifiedTasks.entries()).map(([id, data]) => ({
            id,
            ...data,
        }));

        try {
            const result = await taskApi.bulkUpdate(tasks);

            if (result.success) {
                this.modifiedTasks.clear();
                this.updateSaveButton();
                this.showSuccess('保存しました');
                await this.loadTasks();
            } else {
                this.showError('保存に失敗しました');
            }
        } catch (error) {
            console.error('Error saving tasks:', error);
            this.showError('通信エラーが発生しました');
        }
    }

    /**
     * 未保存の変更があるか
     */
    hasUnsavedChanges() {
        return this.modifiedTasks.size > 0;
    }

    // UI更新メソッド
    updateModeButtons(mode) { /* ... */ }
    updateEditModeButtons(isEdit) { /* ... */ }
    updateSaveButton() { /* ... */ }
    showTaskModal(task) { /* ... */ }
    showSuccess(message) { /* ... */ }
    showError(message) { /* ... */ }
    renderTaskList(tasks) { /* ... */ }
}
```

---

## 5. 操作別処理方式まとめ

| 操作 | 方式 | 理由 |
|------|------|------|
| **プロジェクト選択** | Submit (ページ遷移) | 表示データが大きく変わるため |
| **ガント/タスク切り替え** | Submit (ページ遷移) | UIが大きく変わるため |
| **予定/実績切り替え** | Ajax | 同じUI内でデータ切り替え |
| **表示/編集モード切り替え** | Ajax | UI状態変更のみ |
| **タスクバードラッグ** | Ajax (追跡のみ) | 即座に保存せず変更追跡 |
| **タスク詳細表示** | Ajax | モーダル表示 |
| **タスク編集（モーダル）** | Ajax | 個別更新 |
| **進捗更新** | Ajax | 個別更新 |
| **カレンダー一括保存** | Submit (Ajax POST) | 複数変更を一括保存 |
| **タスク並び替え** | Ajax | 即座に反映 |
| **タスク追加/削除** | Ajax | 即座に反映 |

---

## 6. 実装フェーズ

### Phase 1: 基盤構築
1. データベースマイグレーション作成
2. Model作成（TaskModel, TaskHistoryModel）
3. Service作成（TaskService）
4. シーダー作成（デモデータ）

### Phase 2: API実装
1. TaskApiController実装
2. ルーティング設定
3. APIテスト

### Phase 3: 画面実装
1. ScheduleController実装
2. View作成（レイアウト統合）
3. JavaScript基盤（task-api.js, main.js）

### Phase 4: ガントチャート実装
1. カレンダー描画
2. タスクバー描画
3. ドラッグ&ドロップ
4. 同期スクロール

### Phase 5: タスク管理機能
1. タスク詳細モーダル
2. タスク追加/編集/削除
3. 進捗管理

### Phase 6: 仕上げ
1. 検索・フィルター機能
2. エクスポート機能
3. レスポンシブ対応
4. テスト・バグ修正

---

## 7. 注意事項

### セキュリティ
- 全AjaxリクエストにCSRFトークンを含める
- プロジェクトへのアクセス権限チェック
- 入力値のバリデーション・サニタイズ

### パフォーマンス
- タスク数が多い場合の遅延読み込み検討
- カレンダー描画の最適化（仮想スクロール検討）
- 変更追跡による不要なAPI呼び出し削減

### UX
- 保存前の変更を視覚的に表示（黄色背景）
- 未保存変更がある場合の画面遷移警告
- 操作のUndo/Redo機能
