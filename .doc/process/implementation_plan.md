# 工程マスタ機能 実装計画書

## 1. 概要

プロジェクト管理システムに工程マスタ管理機能を追加する。
モック（`public/mock/settings/process-master.html`）を基に実装を行う。

### 1.1 基本方針

- **Ajax優先**: CRUD操作は全てAjaxで処理（モーダルによる操作）
- **ドラッグ&ドロップ**: 工程の並び替えはHTML5 Drag and Drop APIで実装
- **Submit対象**: 並び替え保存のみ明示的な保存ボタンでAjax送信

---

## 2. データベース設計

### 2.1 新規テーブル

#### `process_masters` テーブル
| カラム名 | 型 | NULL | デフォルト | 説明 |
|---------|-----|------|-----------|------|
| id | INT UNSIGNED | NO | AUTO_INCREMENT | 主キー |
| name | VARCHAR(255) | NO | - | 工程名 |
| description | TEXT | YES | NULL | 説明 |
| status | ENUM('active','inactive') | NO | 'active' | ステータス |
| sort_order | INT UNSIGNED | NO | 0 | 表示順序 |
| created_at | DATETIME | YES | NULL | 作成日時 |
| updated_at | DATETIME | YES | NULL | 更新日時 |

#### インデックス
- PRIMARY KEY (`id`)
- KEY `idx_status` (`status`)
- KEY `idx_sort_order` (`sort_order`)

### 2.2 初期データ（Seeder）

| sort_order | name | description | status |
|------------|------|-------------|--------|
| 1 | 要件定義 | 顧客要件のヒアリングと要件定義書の作成 | active |
| 2 | 基本設計 | システム全体の基本設計と画面設計 | active |
| 3 | 詳細設計 | 機能ごとの詳細設計とDB設計 | active |
| 4 | 開発・実装 | プログラミングとコーディング作業 | active |
| 5 | 単体テスト | 個別のプログラムやモジュールの動作確認 | active |
| 6 | 結合テスト | システム全体の統合テスト | active |
| 7 | 本番リリース | 本番環境へのデプロイと公開 | active |
| 8 | 保守・運用 | システムの保守と運用サポート | inactive |

---

## 3. バックエンド実装

### 3.1 ファイル構成

```
app/
├── Controllers/
│   └── ProcessMasterController.php    # 工程マスタコントローラー
├── Models/
│   └── ProcessMasterModel.php         # 工程マスタモデル
├── Services/
│   └── ProcessMasterService.php       # 工程マスタサービス
├── Database/
│   ├── Migrations/
│   │   └── 2026-01-18-000001_CreateProcessMastersTable.php
│   └── Seeds/
│       └── ProcessMasterSeeder.php
└── Views/
    └── process_masters/
        └── index.php                  # 一覧画面（モーダル含む）
```

### 3.2 ルーティング（`app/Config/Routes.php`）

```php
// 工程マスタ管理
$routes->group('process-masters', function ($routes) {
    $routes->get('/', 'ProcessMasterController::index');           // 一覧表示
    $routes->post('/', 'ProcessMasterController::store');          // 新規作成（Ajax）
    $routes->get('(:num)', 'ProcessMasterController::show/$1');    // 詳細取得（Ajax）
    $routes->put('(:num)', 'ProcessMasterController::update/$1');  // 更新（Ajax）
    $routes->delete('(:num)', 'ProcessMasterController::delete/$1'); // 削除（Ajax）
    $routes->post('reorder', 'ProcessMasterController::reorder');  // 並び替え（Ajax）
});
```

### 3.3 ProcessMasterModel

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcessMasterModel extends Model
{
    protected $table            = 'process_masters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'status',
        'sort_order',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'   => 'required|max_length[255]',
        'status' => 'required|in_list[active,inactive]',
    ];
    protected $validationMessages = [
        'name' => [
            'required'   => '工程名は必須です。',
            'max_length' => '工程名は255文字以内で入力してください。',
        ],
        'status' => [
            'required' => 'ステータスは必須です。',
            'in_list'  => '無効なステータスです。',
        ],
    ];

    /**
     * ステータスリスト
     */
    public static function getStatuses(): array
    {
        return [
            'active'   => '有効',
            'inactive' => '無効',
        ];
    }

    /**
     * ステータスラベルを取得
     */
    public static function getStatusLabel(string $status): string
    {
        $statuses = self::getStatuses();
        return $statuses[$status] ?? $status;
    }

    /**
     * ステータスカラーを取得
     */
    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'active'   => 'bg-emerald-50 text-emerald-600',
            'inactive' => 'bg-slate-100 text-slate-500',
            default    => 'bg-slate-100 text-slate-500',
        };
    }

    /**
     * 工程アイコンカラーを取得（名前ベース）
     */
    public static function getIconColor(string $name): string
    {
        $colors = [
            'from-blue-500 to-indigo-600',
            'from-purple-500 to-pink-600',
            'from-cyan-500 to-blue-600',
            'from-emerald-500 to-teal-600',
            'from-amber-500 to-orange-600',
            'from-rose-500 to-pink-600',
            'from-violet-500 to-purple-600',
            'from-slate-500 to-slate-600',
        ];
        $index = abs(crc32($name)) % count($colors);
        return $colors[$index];
    }

    /**
     * 工程アイコンを取得（名前ベース）
     */
    public static function getIcon(string $name): string
    {
        $icons = [
            '要件定義' => 'fa-clipboard-list',
            '基本設計' => 'fa-drafting-compass',
            '詳細設計' => 'fa-file-alt',
            '開発' => 'fa-code',
            '実装' => 'fa-code',
            'テスト' => 'fa-vial',
            '単体テスト' => 'fa-vial',
            '結合テスト' => 'fa-flask',
            'リリース' => 'fa-rocket',
            '保守' => 'fa-tools',
            '運用' => 'fa-tools',
        ];

        foreach ($icons as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }
        return 'fa-tasks';
    }

    /**
     * 統計情報を取得
     */
    public function getStats(): array
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults(false);
        $inactive = $this->where('status', 'inactive')->countAllResults();

        $lastUpdated = $this->orderBy('updated_at', 'DESC')->first();
        $lastUpdatedAt = $lastUpdated ? $lastUpdated['updated_at'] : null;

        return [
            'total'           => $total,
            'active'          => $active,
            'inactive'        => $inactive,
            'last_updated_at' => $lastUpdatedAt,
        ];
    }

    /**
     * 検索・フィルタ付き一覧取得
     */
    public function getProcessMasters(?string $search = null, ?string $status = null): array
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->like('name', $search);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $builder->orderBy('sort_order', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * 有効な工程のみ取得
     */
    public function getActiveProcessMasters(): array
    {
        return $this->where('status', 'active')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * 次のsort_orderを取得
     */
    public function getNextSortOrder(): int
    {
        $max = $this->selectMax('sort_order')->first();
        return ($max['sort_order'] ?? 0) + 1;
    }

    /**
     * 並び替え順序を一括更新
     */
    public function updateSortOrder(array $orders): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($orders as $order) {
            $this->update($order['id'], ['sort_order' => $order['sort_order']]);
        }

        $db->transComplete();
        return $db->transStatus();
    }
}
```

### 3.4 ProcessMasterService

```php
<?php

namespace App\Services;

use App\Models\ProcessMasterModel;

class ProcessMasterService
{
    protected ProcessMasterModel $model;

    public function __construct()
    {
        $this->model = new ProcessMasterModel();
    }

    public function getStats(): array
    {
        return $this->model->getStats();
    }

    public function getProcessMasters(?string $search = null, ?string $status = null): array
    {
        return $this->model->getProcessMasters($search, $status);
    }

    public function getActiveProcessMasters(): array
    {
        return $this->model->getActiveProcessMasters();
    }

    public function findById(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function create(array $data): array
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->model->getNextSortOrder();
        }

        $this->model->insert($data);
        return $this->model->find($this->model->getInsertID());
    }

    public function update(int $id, array $data): ?array
    {
        $this->model->update($id, $data);
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function updateSortOrder(array $orders): bool
    {
        return $this->model->updateSortOrder($orders);
    }
}
```

### 3.5 ProcessMasterController

```php
<?php

namespace App\Controllers;

use App\Models\ProcessMasterModel;
use App\Services\ProcessMasterService;

class ProcessMasterController extends BaseController
{
    protected ProcessMasterModel $processModel;
    protected ProcessMasterService $processService;

    public function __construct()
    {
        $this->processModel = new ProcessMasterModel();
        $this->processService = new ProcessMasterService();
    }

    /**
     * 一覧表示
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $data = [
            'pageTitle'  => '工程マスタ',
            'processes'  => $this->processService->getProcessMasters($search, $status),
            'stats'      => $this->processService->getStats(),
            'search'     => $search,
            'status'     => $status,
        ];

        return view('process_masters/index', $data);
    }

    /**
     * 詳細取得（Ajax）
     */
    public function show(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $process,
        ]);
    }

    /**
     * 新規作成（Ajax）
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '工程名は必須です。',
                'max_length' => '工程名は255文字以内で入力してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '入力エラーがあります。',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $process = $this->processService->create($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を登録しました。',
            'data'    => $process,
        ]);
    }

    /**
     * 更新（Ajax）
     */
    public function update(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '工程名は必須です。',
                'max_length' => '工程名は255文字以内で入力してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '入力エラーがあります。',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $updated = $this->processService->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を更新しました。',
            'data'    => $updated,
        ]);
    }

    /**
     * 削除（Ajax）
     */
    public function delete(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        $this->processService->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を削除しました。',
        ]);
    }

    /**
     * 並び替え保存（Ajax）
     */
    public function reorder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $orders = $this->request->getPost('orders');
        if (empty($orders) || !is_array($orders)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '並び順データが不正です。',
            ]);
        }

        $result = $this->processService->updateSortOrder($orders);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => '並び順を保存しました。',
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => '保存に失敗しました。',
        ]);
    }
}
```

---

## 4. フロントエンド実装

### 4.1 View構成

`app/Views/process_masters/index.php` にモックHTMLを移植。

**主要セクション:**
1. ヘッダー（パンくずリスト、設定に戻るリンク）
2. サブヘッダー（検索、フィルター、追加ボタン）
3. 統計カード（総工程数、有効、無効、最終更新）
4. 工程テーブル（ドラッグ&ドロップ対応）
5. 追加/編集モーダル
6. JavaScript（Ajax、ドラッグ&ドロップ）

### 4.2 JavaScript処理

```javascript
/**
 * 工程マスタ管理JS
 */
class ProcessMasterManager {
    constructor() {
        this.baseUrl = '/process-masters';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.isOrderChanged = false;
        this.draggedRow = null;
    }

    /**
     * 初期化
     */
    init() {
        this.bindEvents();
    }

    /**
     * イベントバインド
     */
    bindEvents() {
        // 検索・フィルター
        document.getElementById('search-input')?.addEventListener('input', debounce(() => {
            this.filterProcesses();
        }, 300));

        document.getElementById('status-filter')?.addEventListener('change', () => {
            this.filterProcesses();
        });

        // モーダル
        document.getElementById('btn-add')?.addEventListener('click', () => {
            this.openModal('add');
        });

        document.getElementById('process-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // 並び替え保存
        document.getElementById('btn-save-reorder')?.addEventListener('click', () => {
            this.saveReorder();
        });
    }

    /**
     * Ajax共通ヘッダー
     */
    getHeaders() {
        return {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
        };
    }

    /**
     * 工程追加/編集モーダルを開く
     */
    openModal(mode, id = null) {
        const modal = document.getElementById('process-modal');
        const form = document.getElementById('process-form');
        const title = document.getElementById('modal-title');
        const submitBtn = document.getElementById('submit-button-text');

        form.reset();
        document.getElementById('process-id').value = id || '';

        if (mode === 'add') {
            title.textContent = '工程追加';
            submitBtn.textContent = '登録する';
        } else {
            title.textContent = '工程編集';
            submitBtn.textContent = '更新する';
            this.loadProcessData(id);
        }

        modal.classList.remove('hidden');
        modal.classList.add('show');
    }

    /**
     * モーダルを閉じる
     */
    closeModal() {
        const modal = document.getElementById('process-modal');
        modal.classList.remove('show');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    /**
     * 工程データを読み込む（編集用）
     */
    async loadProcessData(id) {
        try {
            const response = await fetch(`${this.baseUrl}/${id}`, {
                headers: this.getHeaders(),
            });
            const result = await response.json();

            if (result.success) {
                document.getElementById('process-name').value = result.data.name;
                document.getElementById('process-description').value = result.data.description || '';
                document.querySelector(`input[name="status"][value="${result.data.status}"]`).checked = true;
            }
        } catch (error) {
            console.error('Error loading process:', error);
        }
    }

    /**
     * フォーム送信
     */
    async handleSubmit() {
        const form = document.getElementById('process-form');
        const id = document.getElementById('process-id').value;
        const formData = new FormData(form);

        const url = id ? `${this.baseUrl}/${id}` : this.baseUrl;
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: this.getHeaders(),
                body: new URLSearchParams(formData),
            });
            const result = await response.json();

            if (result.success) {
                this.showToast(result.message, 'success');
                this.closeModal();
                location.reload();
            } else {
                this.showToast(result.message || '保存に失敗しました', 'error');
            }
        } catch (error) {
            console.error('Error saving process:', error);
            this.showToast('通信エラーが発生しました', 'error');
        }
    }

    /**
     * 削除確認
     */
    async confirmDelete(id, name) {
        if (!confirm(`「${name}」を削除してもよろしいですか？`)) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'DELETE',
                headers: this.getHeaders(),
            });
            const result = await response.json();

            if (result.success) {
                this.showToast(result.message, 'success');
                location.reload();
            } else {
                this.showToast(result.message || '削除に失敗しました', 'error');
            }
        } catch (error) {
            console.error('Error deleting process:', error);
            this.showToast('通信エラーが発生しました', 'error');
        }
    }

    /**
     * ドラッグ開始
     */
    dragStart(e) {
        this.draggedRow = e.target.closest('tr');
        e.dataTransfer.effectAllowed = 'move';
        this.draggedRow.style.opacity = '0.5';
    }

    /**
     * ドラッグオーバー
     */
    dragOver(e) {
        e.preventDefault();
        const targetRow = e.target.closest('tr');
        if (targetRow && targetRow !== this.draggedRow) {
            const tbody = targetRow.parentNode;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const draggedIndex = rows.indexOf(this.draggedRow);
            const targetIndex = rows.indexOf(targetRow);

            if (draggedIndex < targetIndex) {
                tbody.insertBefore(this.draggedRow, targetRow.nextSibling);
            } else {
                tbody.insertBefore(this.draggedRow, targetRow);
            }
            this.isOrderChanged = true;
        }
    }

    /**
     * ドラッグ終了
     */
    dragEnd(e) {
        if (this.draggedRow) {
            this.draggedRow.style.opacity = '';
            this.draggedRow = null;
        }
        if (this.isOrderChanged) {
            this.showSaveReorderButton();
        }
    }

    /**
     * 並び替え保存ボタン表示
     */
    showSaveReorderButton() {
        document.getElementById('save-reorder-container').classList.remove('hidden');
    }

    /**
     * 並び替え保存
     */
    async saveReorder() {
        const rows = document.querySelectorAll('.process-table tbody tr');
        const orders = Array.from(rows).map((row, index) => ({
            id: parseInt(row.dataset.id),
            sort_order: index + 1,
        }));

        try {
            const response = await fetch(`${this.baseUrl}/reorder`, {
                method: 'POST',
                headers: this.getHeaders(),
                body: new URLSearchParams({ orders: JSON.stringify(orders) }),
            });
            const result = await response.json();

            if (result.success) {
                this.showToast(result.message, 'success');
                this.isOrderChanged = false;
                document.getElementById('save-reorder-container').classList.add('hidden');
                this.updateRowNumbers();
            } else {
                this.showToast(result.message || '保存に失敗しました', 'error');
            }
        } catch (error) {
            console.error('Error saving reorder:', error);
            this.showToast('通信エラーが発生しました', 'error');
        }
    }

    /**
     * 行番号更新
     */
    updateRowNumbers() {
        document.querySelectorAll('.process-table tbody tr').forEach((row, index) => {
            const noCell = row.querySelector('.row-no');
            if (noCell) noCell.textContent = index + 1;
        });
    }

    /**
     * 検索・フィルター適用
     */
    filterProcesses() {
        const search = document.getElementById('search-input')?.value || '';
        const status = document.getElementById('status-filter')?.value || '';
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        window.location.href = `${this.baseUrl}?${params.toString()}`;
    }

    /**
     * トースト表示
     */
    showToast(message, type = 'info') {
        // 実装：トースト通知
        alert(message);
    }
}

// 初期化
document.addEventListener('DOMContentLoaded', () => {
    window.processMaster = new ProcessMasterManager();
    window.processMaster.init();
});

// ユーティリティ
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
```

---

## 5. 操作別処理方式まとめ

| 操作 | 方式 | 理由 |
|------|------|------|
| **一覧表示** | ページ表示 | 初期ロード |
| **検索・フィルター** | Submit (GET) | URLパラメータでブックマーク可能 |
| **工程追加** | Ajax (POST) | モーダルで操作、即座に反映 |
| **工程編集** | Ajax (PUT) | モーダルで操作、即座に反映 |
| **工程削除** | Ajax (DELETE) | 確認ダイアログ後に即座に反映 |
| **ドラッグ&ドロップ** | JavaScript | クライアントサイドで並び替え |
| **並び替え保存** | Ajax (POST) | 一括でサーバーに保存 |

---

## 6. 実装フェーズ

### Phase 1: データベース基盤
1. Migration作成（`CreateProcessMastersTable`）
2. Seeder作成（`ProcessMasterSeeder`）
3. マイグレーション実行

### Phase 2: バックエンド実装
4. Model作成（`ProcessMasterModel`）
5. Service作成（`ProcessMasterService`）
6. Controller作成（`ProcessMasterController`）
7. Routes追加

### Phase 3: フロントエンド実装
8. View作成（`process_masters/index.php`）
9. JavaScript実装（Ajax、ドラッグ&ドロップ）

### Phase 4: テスト・調整
10. 動作確認（CRUD、並び替え）
11. バリデーションエラー確認
12. UI調整

---

## 7. 検証手順

### 7.1 データベース確認
```bash
php spark migrate
php spark db:seed ProcessMasterSeeder
```

### 7.2 動作確認項目

| No | 項目 | 確認内容 |
|----|------|---------|
| 1 | 一覧表示 | `/process-masters` にアクセスし、8件の工程が表示されること |
| 2 | 統計カード | 総工程数=8、有効=7、無効=1 と表示されること |
| 3 | 検索 | 工程名で検索できること |
| 4 | フィルター | ステータスでフィルターできること |
| 5 | 工程追加 | モーダルから工程を追加できること |
| 6 | 工程編集 | モーダルから工程を編集できること |
| 7 | 工程削除 | 確認後に工程を削除できること |
| 8 | 並び替え | ドラッグ&ドロップで並び替えできること |
| 9 | 並び替え保存 | 並び替え後、保存ボタンで順序が保存されること |
| 10 | バリデーション | 工程名未入力時にエラーが表示されること |

---

## 8. 注意事項

### セキュリティ
- 全AjaxリクエストにCSRFトークンを含める
- 入力値のバリデーション（サーバーサイド必須）
- 出力エスケープ（`esc()`関数使用）

### UI/UX
- モーダルのアニメーション
- 操作後のフィードバック（トースト通知）
- 並び替え変更時の視覚的フィードバック
- ローディング状態の表示

### 将来の拡張性
- 工程マスタは `tasks` テーブルの `process_name` と連携予定
- プロジェクトごとに利用可能な工程を設定する機能（将来）
