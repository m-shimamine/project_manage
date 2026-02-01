<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    /* 予定/実績切り替えボタン */
    .toggle-btn-group {
        display: flex;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    .toggle-btn-item {
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: white;
        text-decoration: none;
        color: #475569;
    }
    .toggle-btn-item.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .toggle-btn-item:not(.active):hover {
        background: #f8fafc;
    }

    /* チェックボックスを大きく */
    .row-checkbox, #select-all {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    /* 編集可能セル */
    .editable-cell { transition: all 0.2s; }
    .editable-cell:hover { background: #f0f9ff; }
    .edit-mode .editable-cell { background: #fffbeb; cursor: text; }
    .edit-mode .editable-cell:focus-within { background: #fef3c7; box-shadow: inset 0 0 0 2px #f59e0b; }
    .edit-input { width: 100%; height: 100%; border: none; background: transparent; padding: 0; font-size: inherit; color: inherit; }
    .edit-input:focus { outline: none; }

    /* 行選択 */
    .task-row.selected, .task-row.selected td { background: #eff6ff !important; }
    .task-row:hover, .task-row:hover td { background: #f8fafc !important; }

    /* ステータスバッジ */
    .status-badge { font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; }
    .status-not-started { background: #f1f5f9; color: #64748b; }
    .status-in-progress { background: #dbeafe; color: #2563eb; }
    .status-completed { background: #dcfce7; color: #16a34a; }
    .status-on-hold { background: #fef3c7; color: #d97706; }

    /* 遅延バッジ */
    .delay-badge { font-size: 10px; padding: 2px 6px; border-radius: 8px; font-weight: 600; }
    .delay-late { background: #fef2f2; color: #dc2626; }
    .delay-early { background: #f0fdf4; color: #16a34a; }
    .delay-ontime { background: #f8fafc; color: #64748b; }

    /* テーブルスクロール */
    .table-container { scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent; }
    .table-container::-webkit-scrollbar { width: 8px; height: 8px; }
    .table-container::-webkit-scrollbar-track { background: #f1f5f9; }
    .table-container::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.6); border-radius: 4px; }

    /* 右クリックメニュー */
    .context-menu { position: fixed; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 1000; min-width: 180px; padding: 4px 0; display: none; }
    .context-menu.show { display: block; }
    .context-menu-item { padding: 8px 16px; cursor: pointer; display: flex; align-items: center; font-size: 14px; color: #475569; }
    .context-menu-item:hover { background: #f1f5f9; }
    .context-menu-item i { width: 20px; margin-right: 8px; color: #94a3b8; }
    .context-menu-separator { height: 1px; background: #e2e8f0; margin: 4px 0; }

    /* 予定ヘッダー背景 */
    .planned-header { background: #e0f2fe; }

    /* 実績ヘッダー背景 */
    .actual-header { background: #fef3c7; }

    /* ドラッグハンドル */
    .drag-handle { cursor: grab; color: #94a3b8; padding: 4px; }
    .drag-handle:hover { color: #3b82f6; }
    .drag-handle:active { cursor: grabbing; }
    .task-row.dragging, .task-row.dragging td { opacity: 0.5; background: #dbeafe !important; }
    .task-row.drag-over { border-top: 2px solid #3b82f6; }
    .task-row.copied, .task-row.copied td { background: #ecfdf5 !important; }
    .task-row.date-edited, .task-row.date-edited td,
    .subtask-row.date-edited, .subtask-row.date-edited td { background: #fef9c3 !important; }
    .task-row.completed-row, .task-row.completed-row td { background: #f0fdf4 !important; }
    .task-row.completed-row:hover, .task-row.completed-row:hover td { background: #dcfce7 !important; }

    /* サブタスク行 */
    .subtask-row { background: #f8fafc; }
    .subtask-row:hover { background: #f1f5f9; }
    .subtask-row td { color: #334155; }
    .subtask-row .text-slate-400 { color: #64748b !important; }
    .subtask-row .text-slate-500 { color: #475569 !important; }
    .subtask-row .text-slate-600 { color: #334155 !important; }

    /* 表示モード時：チェックボックスと並び替えを非表示 */
    #table-container:not(.edit-mode) .row-checkbox,
    #table-container:not(.edit-mode) .drag-handle { visibility: hidden; }
    #table-container:not(.edit-mode) #select-all { visibility: hidden; }

    /* 進捗バー */
    .progress-bar-container { width: 50px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
    .progress-bar { height: 100%; border-radius: 3px; transition: width 0.3s ease; }
    .progress-bar.low { background: linear-gradient(90deg, #ef4444, #f87171); }
    .progress-bar.medium { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .progress-bar.high { background: linear-gradient(90deg, #10b981, #34d399); }

    /* ヘッダーフィルター */
    .header-filter { position: relative; cursor: pointer; user-select: none; }
    .header-filter:hover { background: #e2e8f0; }
    .header-filter.active { background: #dbeafe; }
    .filter-icon { margin-left: 4px; font-size: 10px; color: #94a3b8; transition: color 0.2s; }
    .header-filter:hover .filter-icon { color: #3b82f6; }
    .header-filter.has-filter .filter-icon { color: #2563eb; }

    /* フィルタードロップダウン */
    .filter-dropdown { position: absolute; top: 100%; left: 0; min-width: 180px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 100; display: none; padding: 8px 0; }
    .filter-dropdown.show { display: block; }
    .filter-dropdown-item { padding: 8px 12px; cursor: pointer; font-size: 13px; color: #475569; display: flex; align-items: center; }
    .filter-dropdown-item:hover { background: #f1f5f9; }
    .filter-dropdown-item.selected { background: #eff6ff; color: #2563eb; }
    .filter-dropdown-item i { width: 16px; margin-right: 8px; }
    .filter-dropdown-search { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }
    .filter-dropdown-search input { width: 100%; padding: 6px 8px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 12px; }
    .filter-dropdown-header { padding: 6px 12px; font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }

    /* テキスト入力フィルタ */
    .filter-dropdown-input { padding: 8px; }
    .filter-text-input { width: 100%; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; margin-bottom: 8px; }
    .filter-text-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
    .filter-dropdown-input .filter-dropdown-item { border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 8px; }

    /* 検索可能ドロップダウン */
    .filter-dropdown-searchable { min-width: 160px; }
    .filter-dropdown-searchable .filter-dropdown-search { padding: 8px; border-bottom: 1px solid #e2e8f0; }
    .filter-dropdown-searchable .filter-dropdown-search input { width: 100%; padding: 6px 8px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 12px; }
    .filter-dropdown-searchable .filter-dropdown-search input:focus { outline: none; border-color: #3b82f6; }
    .filter-options-container { max-height: 200px; overflow-y: auto; }
    .filter-dropdown-item.hidden { display: none; }

    /* 検索パネル */
    .search-panel { background: white; border-bottom: 1px solid #e2e8f0; padding: 12px 16px; display: none; }
    .search-panel.show { display: block; }

    /* 編集可能セル */
    .editable-cell { transition: all 0.2s; }
    .editable-cell:hover { background: #f0f9ff; }
    .edit-mode .editable-cell { background: #fffbeb; cursor: text; }
    .edit-mode .editable-cell:focus-within { background: #fef3c7; box-shadow: inset 0 0 0 2px #f59e0b; }

    /* 編集モードのインラインフィールド（枠なしスタイリッシュデザイン） */
    .edit-input {
        width: 100%;
        height: 100%;
        border: none;
        background: transparent;
        padding: 0;
        font-size: inherit;
        color: inherit;
    }
    .edit-input:focus {
        outline: none;
    }
    .edit-input[type="number"] {
        text-align: center;
    }
    select.edit-input {
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        padding-right: 16px;
        background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M2 4l4 4 4-4'/%3E%3C/svg%3E") no-repeat right center;
    }

    /* 編集モード時のテーブルコンテナ */
    #table-container.edit-mode .task-row td {
        padding: 4px 6px;
    }
    #table-container.edit-mode .task-row:hover td {
        background: #fffbeb;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('mainClass') ?>overflow-hidden<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="flex flex-col h-full overflow-hidden">
    <!-- サブヘッダー -->
    <div class="bg-white border-b border-slate-200 px-6 py-3 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- プロジェクト選択 -->
                <form id="project-form" method="get" action="<?= base_url('schedule/tasks') ?>">
                    <select name="project_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" style="min-width: 280px;" onchange="this.form.submit()">
                        <option value="">-- プロジェクトを選択 --</option>
                        <?php foreach ($projectsByCustomer as $group): ?>
                            <optgroup label="<?= esc($group['customer_name']) ?>">
                                <?php foreach ($group['projects'] as $project): ?>
                                    <option value="<?= $project['id'] ?>" <?= ($projectId == $project['id']) ? 'selected' : '' ?>>
                                        <?= esc($project['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </form>

                <!-- ガントチャート/タスク切り替え -->
                <div class="toggle-btn-group">
                    <a href="<?= base_url('schedule?project_id=' . $projectId) ?>" class="toggle-btn-item">
                        <i class="fas fa-chart-gantt mr-1"></i>ガントチャート
                    </a>
                    <span class="toggle-btn-item active">
                        <i class="fas fa-list mr-1"></i>タスク
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- 絞込ボタン（スケジュールと同様） -->
                <button id="search-toggle" onclick="toggleSearchPanel()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white flex items-center">
                    <i class="fas fa-filter mr-2"></i>絞込
                </button>
                <!-- インポート -->
                <button onclick="openImportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white">
                    <i class="fas fa-file-import mr-2"></i>インポート
                </button>
                <!-- エクスポート -->
                <button onclick="openExportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white">
                    <i class="fas fa-file-export mr-2"></i>エクスポート
                </button>
                <!-- 表示/編集モード切り替え -->
                <div class="toggle-btn-group">
                    <button id="btn-view-mode" class="toggle-btn-item active" onclick="switchViewMode('view')">
                        <i class="fas fa-eye mr-1"></i>表示
                    </button>
                    <button id="btn-edit-mode" class="toggle-btn-item" onclick="switchViewMode('edit')">
                        <i class="fas fa-edit mr-1"></i>編集
                    </button>
                </div>
                <!-- タスク追加 -->
                <button onclick="openTaskModal()" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-indigo-700 shadow-lg" <?= !$projectId ? 'disabled' : '' ?>>
                    <i class="fas fa-plus mr-2"></i>タスク追加
                </button>
            </div>
        </div>
    </div>

    <!-- 検索パネル -->
    <div id="search-panel" class="search-panel">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-2">
                <label class="text-sm text-slate-600 font-medium">ステータス:</label>
                <select id="filter-status" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white" onchange="applyFilter()">
                    <option value="">すべて</option>
                    <option value="not_started">未着手</option>
                    <option value="in_progress">進行中</option>
                    <option value="completed">完了</option>
                    <option value="on_hold">保留</option>
                </select>
            </div>
            <button onclick="clearFilter()" class="px-4 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-600 hover:bg-slate-50">クリア</button>
        </div>
    </div>

    <!-- 選択時アクションバー -->
    <div id="selection-action-bar" class="bg-blue-50 border-b border-blue-200 px-6 py-2 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-sm text-blue-700"><strong id="selected-count">0</strong>件選択中</span>
                <button onclick="openBulkEditModal()" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-edit mr-1"></i>一括編集
                </button>
                <button onclick="bulkDelete()" class="px-3 py-1.5 border border-rose-300 text-rose-600 rounded-lg text-sm font-medium hover:bg-rose-50">
                    <i class="fas fa-trash mr-1"></i>一括削除
                </button>
            </div>
            <button onclick="clearSelection()" class="text-sm text-slate-500 hover:text-slate-700">
                <i class="fas fa-times mr-1"></i>選択解除
            </button>
        </div>
    </div>

    <!-- クリップボードステータス -->
    <div id="clipboard-status" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 hidden">
        <span class="text-sm text-emerald-700 bg-emerald-100 px-4 py-2 rounded-full shadow-lg">
            <i class="fas fa-clipboard-check mr-1"></i>行をコピーしました（Ctrl+Vで貼り付け）
        </span>
    </div>

    <!-- タスク一覧テーブル -->
    <?php if ($projectId && $selectedProject): ?>
    <main class="flex-1 overflow-hidden bg-white">
        <div class="h-full overflow-auto table-container" id="table-container">
            <table class="w-full border-collapse min-w-max" id="task-table">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-slate-100 border-b-2 border-slate-300">
                        <th class="w-10 px-2 py-3 text-center border-r border-slate-200" rowspan="2">
                            <input type="checkbox" id="select-all" class="row-checkbox rounded border-slate-300">
                        </th>
                        <th class="w-10 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200" rowspan="2" title="ドラッグで並替">並替</th>
                        <th class="w-12 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200" rowspan="2">No</th>
                        <!-- 工程（フィルター付き） -->
                        <th class="w-20 px-2 py-3 text-xs font-bold text-slate-600 text-left border-r border-slate-200 header-filter" rowspan="2" data-filter-type="process">
                            工程<i class="fas fa-filter filter-icon"></i>
                            <div id="filter-dropdown-process" class="filter-dropdown">
                                <div class="filter-dropdown-item" data-value=""><i class="fas fa-check"></i>すべて</div>
                                <?php foreach ($processes as $process): ?>
                                    <div class="filter-dropdown-item" data-value="<?= $process['id'] ?>"><?= esc($process['name']) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </th>
                        <!-- タスク名（フィルター付き） -->
                        <th class="min-w-40 px-2 py-3 text-xs font-bold text-slate-600 text-left border-r border-slate-200 header-filter" rowspan="2" data-filter-type="taskName">
                            タスク名<i class="fas fa-filter filter-icon"></i>
                            <div id="filter-dropdown-taskName" class="filter-dropdown filter-dropdown-input">
                                <input type="text" id="filter-input-taskName" class="filter-text-input" placeholder="部分一致検索" onclick="event.stopPropagation()" oninput="applyHeaderTextFilter('taskName')">
                                <div class="filter-dropdown-item" data-value="" onclick="clearHeaderFilter('taskName')"><i class="fas fa-times"></i>クリア</div>
                            </div>
                        </th>
                        <!-- 担当者（フィルター付き） -->
                        <th class="w-20 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200 header-filter" rowspan="2" data-filter-type="assigneeName">
                            担当者<i class="fas fa-filter filter-icon"></i>
                            <div id="filter-dropdown-assigneeName" class="filter-dropdown filter-dropdown-searchable">
                                <div class="filter-dropdown-search">
                                    <input type="text" id="filter-search-assigneeName" placeholder="検索..." onclick="event.stopPropagation()" oninput="filterAssigneeOptions(this.value)">
                                </div>
                                <div id="assignee-options-container" class="filter-options-container">
                                    <div class="filter-dropdown-item" data-value=""><i class="fas fa-check"></i>すべて</div>
                                    <?php foreach ($members as $member): ?>
                                        <div class="filter-dropdown-item assignee-option" data-value="<?= esc($member['name']) ?>"><?= esc($member['name']) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </th>
                        <!-- ステータス（フィルター付き） -->
                        <th class="w-20 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200 header-filter" rowspan="2" data-filter-type="status">
                            ステータス<i class="fas fa-filter filter-icon"></i>
                            <div id="filter-dropdown-status" class="filter-dropdown">
                                <div class="filter-dropdown-item" data-value=""><i class="fas fa-check"></i>すべて</div>
                                <div class="filter-dropdown-item" data-value="not_started">未着手</div>
                                <div class="filter-dropdown-item" data-value="in_progress">進行中</div>
                                <div class="filter-dropdown-item" data-value="completed">完了</div>
                                <div class="filter-dropdown-item" data-value="on_hold">保留</div>
                            </div>
                        </th>
                        <!-- 予定 -->
                        <th class="px-2 py-1 text-xs font-bold text-sky-800 text-center border-r border-slate-200 planned-header" colspan="5">予定</th>
                        <!-- 実績 -->
                        <th class="px-2 py-1 text-xs font-bold text-amber-800 text-center border-r border-slate-200 actual-header" colspan="4">実績</th>
                        <th class="w-14 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200" rowspan="2">進捗</th>
                        <!-- 遅延（フィルター付き） -->
                        <th class="w-16 px-2 py-3 text-xs font-bold text-slate-600 text-center border-r border-slate-200 header-filter" rowspan="2" data-filter-type="delay">
                            遅延<i class="fas fa-filter filter-icon"></i>
                            <div id="filter-dropdown-delay" class="filter-dropdown">
                                <div class="filter-dropdown-item" data-value=""><i class="fas fa-check"></i>すべて</div>
                                <div class="filter-dropdown-item" data-value="delayed">遅延あり</div>
                                <div class="filter-dropdown-item" data-value="on-time">予定通り</div>
                            </div>
                        </th>
                        <th class="min-w-24 px-2 py-3 text-xs font-bold text-slate-600 text-left" rowspan="2">備考</th>
                    </tr>
                    <tr class="bg-slate-50 border-b border-slate-300">
                        <!-- 予定サブヘッダー -->
                        <th class="w-14 px-1 py-2 text-xs font-semibold text-sky-700 text-center border-r border-slate-200 planned-header">営業工数</th>
                        <th class="w-14 px-1 py-2 text-xs font-semibold text-sky-700 text-center border-r border-slate-200 planned-header">工数</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-sky-700 text-center border-r border-slate-200 planned-header">開始日</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-sky-700 text-center border-r border-slate-200 planned-header">終了日</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-sky-700 text-center border-r border-slate-200 planned-header">原価</th>
                        <!-- 実績サブヘッダー -->
                        <th class="w-14 px-1 py-2 text-xs font-semibold text-amber-700 text-center border-r border-slate-200 actual-header">実工数</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-amber-700 text-center border-r border-slate-200 actual-header">実開始</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-amber-700 text-center border-r border-slate-200 actual-header">実終了</th>
                        <th class="w-20 px-1 py-2 text-xs font-semibold text-amber-700 text-center border-r border-slate-200 actual-header">出来高</th>
                    </tr>
                </thead>
                <tbody id="task-tbody">
                    <?php
                    $rowNo = 1;
                    foreach ($tasks as $task):
                        $statusClass = \App\Models\TaskModel::getStatusBadgeClass($task['status']);
                        $statusLabel = \App\Models\TaskModel::getStatusLabel($task['status']);
                        $isCompleted = $task['status'] === 'completed';
                    ?>
                    <tr class="task-row <?= $isCompleted ? 'completed-row' : '' ?> border-b border-slate-200 hover:bg-slate-50" data-task-id="<?= $task['id'] ?>" data-index="<?= $rowNo - 1 ?>">
                        <td class="px-2 py-2 text-center border-r border-slate-100">
                            <input type="checkbox" class="row-checkbox task-checkbox rounded border-slate-300" data-task-id="<?= $task['id'] ?>">
                        </td>
                        <td class="px-2 py-2 text-center border-r border-slate-100">
                            <span class="drag-handle text-slate-400 hover:text-blue-500 cursor-grab" title="ドラッグで並び替え">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                        </td>
                        <td class="px-2 py-2 text-center text-xs text-slate-500 border-r border-slate-100"><?= $rowNo++ ?></td>
                        <td class="px-2 py-2 text-xs text-slate-700 font-medium border-r border-slate-100"><?= esc($task['process_name'] ?? '') ?: '-' ?></td>
                        <td class="px-2 py-2 border-r border-slate-100">
                            <a href="javascript:void(0)" onclick="openTaskModal(<?= $task['id'] ?>)" class="text-sm text-slate-800 hover:text-blue-600 font-medium">
                                <?= esc($task['task_name']) ?>
                            </a>
                        </td>
                        <td class="px-2 py-2 text-center text-xs border-r border-slate-100"><?= esc($task['assignee_name'] ?? '-') ?></td>
                        <td class="px-2 py-2 text-center border-r border-slate-100">
                            <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                        </td>
                        <!-- 予定 -->
                        <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $task['sales_man_days'] ?? '-' ?></td>
                        <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $task['planned_man_days'] ?? '-' ?></td>
                        <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30"><?= $task['planned_start_date'] ? date('n/j', strtotime($task['planned_start_date'])) : '-' ?></td>
                        <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30"><?= $task['planned_end_date'] ? date('n/j', strtotime($task['planned_end_date'])) : '-' ?></td>
                        <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $task['planned_cost'] ? '¥' . number_format($task['planned_cost']) : '-' ?></td>
                        <!-- 実績 -->
                        <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30"><?= $task['actual_man_days'] ?? '-' ?></td>
                        <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30"><?= $task['actual_start_date'] ? date('n/j', strtotime($task['actual_start_date'])) : '-' ?></td>
                        <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30"><?= $task['actual_end_date'] ? date('n/j', strtotime($task['actual_end_date'])) : '-' ?></td>
                        <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30"><?= $task['actual_cost'] ? '¥' . number_format($task['actual_cost']) : '-' ?></td>
                        <!-- 進捗 -->
                        <td class="px-2 py-2 border-r border-slate-100">
                            <div class="flex items-center gap-1">
                                <div class="progress-bar-container">
                                    <div class="progress-bar <?= $task['progress'] < 30 ? 'low' : ($task['progress'] < 70 ? 'medium' : 'high') ?>" style="width: <?= $task['progress'] ?>%"></div>
                                </div>
                                <span class="text-xs text-slate-600"><?= $task['progress'] ?>%</span>
                            </div>
                        </td>
                        <!-- 遅延 -->
                        <td class="px-2 py-2 text-center border-r border-slate-100">
                            <?php if (isset($task['delay_days']) && $task['delay_days'] > 0): ?>
                                <span class="delay-badge delay-late"><?= $task['delay_days'] ?>日遅</span>
                            <?php elseif (isset($task['delay_days']) && $task['delay_days'] < 0): ?>
                                <span class="delay-badge delay-early"><?= abs($task['delay_days']) ?>日先</span>
                            <?php else: ?>
                                <span class="text-slate-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-2 py-2 text-xs text-slate-500"><?= esc($task['description'] ?? '') ?></td>
                    </tr>

                    <?php if (!empty($task['subtasks'])): ?>
                        <?php foreach ($task['subtasks'] as $subtask):
                            $subStatusClass = \App\Models\TaskModel::getStatusBadgeClass($subtask['status']);
                            $subStatusLabel = \App\Models\TaskModel::getStatusLabel($subtask['status']);
                            $subIsCompleted = $subtask['status'] === 'completed';
                        ?>
                        <tr class="task-row subtask-row <?= $subIsCompleted ? 'completed-row' : '' ?> border-b border-slate-200" data-task-id="<?= $subtask['id'] ?>" data-parent-id="<?= $task['id'] ?>" data-index="<?= $rowNo - 1 ?>">
                            <td class="px-2 py-2 text-center border-r border-slate-100">
                                <input type="checkbox" class="row-checkbox task-checkbox rounded border-slate-300" data-task-id="<?= $subtask['id'] ?>">
                            </td>
                            <td class="px-2 py-2 text-center border-r border-slate-100">
                                <span class="drag-handle text-slate-400 hover:text-blue-500 cursor-grab" title="ドラッグで並び替え">
                                    <i class="fas fa-grip-vertical"></i>
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center text-xs text-slate-600 border-r border-slate-100"><?= $rowNo++ ?></td>
                            <td class="px-2 py-2 text-xs text-slate-700 font-medium border-r border-slate-100"><?= esc($subtask['process_name'] ?? '') ?: '-' ?></td>
                            <td class="px-2 py-2 border-r border-slate-100">
                                <div class="flex items-center pl-4">
                                    <span class="text-slate-500 mr-1 flex-shrink-0">└</span>
                                    <a href="javascript:void(0)" onclick="openTaskModal(<?= $subtask['id'] ?>)" class="text-sm text-slate-700 hover:text-blue-600 font-medium">
                                        <?= esc($subtask['task_name']) ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-center text-xs border-r border-slate-100"><?= esc($subtask['assignee_name'] ?? '-') ?></td>
                            <td class="px-2 py-2 text-center border-r border-slate-100">
                                <span class="status-badge <?= $subStatusClass ?>"><?= $subStatusLabel ?></span>
                            </td>
                            <!-- 予定 -->
                            <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $subtask['sales_man_days'] ?? '-' ?></td>
                            <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $subtask['planned_man_days'] ?? '-' ?></td>
                            <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30"><?= $subtask['planned_start_date'] ? date('n/j', strtotime($subtask['planned_start_date'])) : '-' ?></td>
                            <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30"><?= $subtask['planned_end_date'] ? date('n/j', strtotime($subtask['planned_end_date'])) : '-' ?></td>
                            <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30"><?= $subtask['planned_cost'] ? '¥' . number_format($subtask['planned_cost']) : '-' ?></td>
                            <!-- 実績 -->
                            <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30"><?= $subtask['actual_man_days'] ?? '-' ?></td>
                            <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30"><?= $subtask['actual_start_date'] ? date('n/j', strtotime($subtask['actual_start_date'])) : '-' ?></td>
                            <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30"><?= $subtask['actual_end_date'] ? date('n/j', strtotime($subtask['actual_end_date'])) : '-' ?></td>
                            <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30"><?= $subtask['actual_cost'] ? '¥' . number_format($subtask['actual_cost']) : '-' ?></td>
                            <!-- 進捗 -->
                            <td class="px-2 py-2 border-r border-slate-100">
                                <div class="flex items-center gap-1">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar <?= $subtask['progress'] < 30 ? 'low' : ($subtask['progress'] < 70 ? 'medium' : 'high') ?>" style="width: <?= $subtask['progress'] ?>%"></div>
                                    </div>
                                    <span class="text-xs text-slate-600"><?= $subtask['progress'] ?>%</span>
                                </div>
                            </td>
                            <!-- 遅延 -->
                            <td class="px-2 py-2 text-center border-r border-slate-100">
                                <?php if (isset($subtask['delay_days']) && $subtask['delay_days'] > 0): ?>
                                    <span class="delay-badge delay-late"><?= $subtask['delay_days'] ?>日遅</span>
                                <?php elseif (isset($subtask['delay_days']) && $subtask['delay_days'] < 0): ?>
                                    <span class="delay-badge delay-early"><?= abs($subtask['delay_days']) ?>日先</span>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-2 text-xs text-slate-600"><?= esc($subtask['description'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php else: ?>
    <main class="flex-1 flex items-center justify-center bg-slate-50">
        <div class="text-center">
            <div class="text-6xl text-slate-300 mb-4"><i class="fas fa-tasks"></i></div>
            <h3 class="text-xl font-semibold text-slate-600 mb-2">プロジェクトを選択してください</h3>
            <p class="text-slate-500">上部のドロップダウンからプロジェクトを選択すると、タスク一覧が表示されます。</p>
        </div>
    </main>
    <?php endif; ?>

    <!-- フッター -->
    <footer class="bg-white border-t border-slate-200 px-6 py-3 flex-shrink-0">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-tasks mr-1"></i>全タスク: <strong class="text-slate-700" id="total-count"><?= $taskStats['total'] ?? 0 ?>件</strong></span>
                <span><i class="fas fa-check-circle mr-1 text-emerald-500"></i>完了: <strong class="text-emerald-600" id="completed-count"><?= $taskStats['completed'] ?? 0 ?>件</strong></span>
                <span><i class="fas fa-spinner mr-1 text-blue-500"></i>進行中: <strong class="text-blue-600" id="progress-count"><?= $taskStats['in_progress'] ?? 0 ?>件</strong></span>
                <span><i class="fas fa-clock mr-1 text-slate-400"></i>未着手: <strong class="text-slate-600" id="notstarted-count"><?= $taskStats['not_started'] ?? 0 ?>件</strong></span>
                <span class="border-l border-slate-300 pl-4"><i class="fas fa-exclamation-triangle mr-1 text-rose-500"></i>遅延: <strong class="text-rose-600" id="delayed-count"><?= $taskStats['delayed'] ?? 0 ?>件</strong></span>
                <?php if (!empty($selectedProject)): ?>
                <span class="border-l border-slate-300 pl-4"><i class="fas fa-yen-sign mr-1 text-green-600"></i>予算: <strong class="text-green-700" id="budget-amount"><?= number_format($selectedProject['budget'] ?? 0) ?>円</strong></span>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-3">
                <!-- 編集モード時のボタン群 -->
                <!-- 原価割り振りボタン（チェック時に表示） -->
                <?php if (!empty($selectedProject)): ?>
                <button id="cost-allocation-btn" onclick="openCostAllocationModal()" class="hidden px-3 py-1.5 border border-green-400 rounded-lg text-xs font-medium text-green-700 hover:bg-green-50 bg-green-50">
                    <i class="fas fa-calculator mr-1"></i>原価割り振り
                </button>
                <?php endif; ?>
                <button id="bulk-date-btn" onclick="openBulkDateModal()" class="hidden px-3 py-1.5 border border-amber-400 rounded-lg text-xs font-medium text-amber-700 hover:bg-amber-50 bg-amber-50">
                    <i class="fas fa-calendar-alt mr-1"></i>一括日付更新
                </button>
                <button onclick="undoChanges()" id="undo-btn" class="hidden px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled title="元に戻す (Ctrl+Z)">
                    <i class="fas fa-undo mr-1"></i>元に戻す
                </button>
                <button onclick="cancelEdit()" id="cancel-edit-btn" class="hidden px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-all">
                    <i class="fas fa-times mr-1"></i>キャンセル
                </button>
                <button onclick="saveAllTasks()" id="save-all-btn" class="hidden px-4 py-1.5 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-lg text-xs font-semibold hover:from-rose-600 hover:to-red-700 shadow-lg transition-all">
                    <i class="fas fa-save mr-1"></i>変更を登録
                </button>
                <!-- モードインジケーター（右端） -->
                <span class="border-l border-slate-300 h-6"></span>
                <span id="view-mode-indicator" class="bg-slate-500 text-white px-3 py-1 rounded text-xs font-medium">
                    <i class="fas fa-eye mr-1"></i>表示モード
                </span>
                <span id="edit-mode-indicator" class="hidden bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">
                    <i class="fas fa-edit mr-1"></i>編集モード
                </span>
            </div>
        </div>
    </footer>
</div>

<!-- 右クリックメニュー（タスク用） -->
<div id="context-menu" class="context-menu">
    <div class="context-menu-item" onclick="addRowAbove()"><i class="fas fa-arrow-up"></i>上に行追加</div>
    <div class="context-menu-item" onclick="addRowBelow()"><i class="fas fa-arrow-down"></i>下に行追加</div>
    <div class="context-menu-item" onclick="addSubtaskFromMenu()"><i class="fas fa-level-up-alt fa-rotate-90"></i>サブタスク追加</div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="copyTaskAbove()"><i class="fas fa-level-up-alt"></i>コピーして上に追加</div>
    <div class="context-menu-item" onclick="copyTaskBelow()"><i class="fas fa-level-down-alt"></i>コピーして下に追加</div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="deleteTaskFromMenu()" style="color: #dc2626;"><i class="fas fa-trash" style="color: #dc2626;"></i>削除</div>
</div>

<!-- 右クリックメニュー（サブタスク用） -->
<div id="subtask-context-menu" class="context-menu">
    <div class="context-menu-item" onclick="addSubtaskAbove()"><i class="fas fa-arrow-up"></i>上にサブタスク追加</div>
    <div class="context-menu-item" onclick="addSubtaskBelow()"><i class="fas fa-arrow-down"></i>下にサブタスク追加</div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="copySubtaskAbove()"><i class="fas fa-level-up-alt"></i>コピーして上に追加</div>
    <div class="context-menu-item" onclick="copySubtaskBelow()"><i class="fas fa-level-down-alt"></i>コピーして下に追加</div>
    <div class="context-menu-separator"></div>
    <div class="context-menu-item" onclick="deleteSubtaskFromMenu()" style="color: #dc2626;"><i class="fas fa-trash" style="color: #dc2626;"></i>削除</div>
</div>

<!-- 一括編集モーダル -->
<div id="bulk-edit-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBulkEditModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-edit mr-2"></i>一括編集</h3>
                <button onclick="closeBulkEditModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4"><strong id="bulk-edit-count">0</strong>件のタスクを一括編集します。変更しない項目は空欄のままにしてください。</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">担当者</label>
                    <select id="bulk-assignee" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">-- 変更しない --</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member['id'] ?>"><?= esc($member['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ステータス</label>
                    <select id="bulk-status" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">-- 変更しない --</option>
                        <option value="not_started">未着手</option>
                        <option value="in_progress">進行中</option>
                        <option value="completed">完了</option>
                        <option value="on_hold">保留</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">進捗</label>
                    <select id="bulk-progress" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">-- 変更しない --</option>
                        <option value="0">0%</option>
                        <option value="25">25%</option>
                        <option value="50">50%</option>
                        <option value="75">75%</option>
                        <option value="100">100%</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">工程</label>
                    <select id="bulk-process" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">-- 変更しない --</option>
                        <?php foreach ($processes as $process): ?>
                            <option value="<?= $process['id'] ?>"><?= esc($process['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="border-t border-slate-200 pt-4 mt-4">
                    <p class="text-sm font-semibold text-slate-700 mb-2">日付の一括更新</p>
                    <p class="text-xs text-slate-500 mb-3">チェックした行を起点として日付を更新できます</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">予定開始日</label>
                            <input type="date" id="bulk-planned-start" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">予定終了日</label>
                            <input type="date" id="bulk-planned-end" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">実績開始日</label>
                            <input type="date" id="bulk-actual-start" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">実績終了日</label>
                            <input type="date" id="bulk-actual-end" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <label class="flex items-start text-sm text-amber-800 cursor-pointer">
                            <input type="checkbox" id="bulk-update-subsequent" class="mt-0.5 mr-2 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                            <span>
                                <strong>チェックした行から下のタスクもすべて更新</strong>
                                <br><span class="text-xs text-amber-600">※選択した行を起点に、それ以降のタスクの日付を一括更新します</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                <button onclick="closeBulkEditModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                    キャンセル
                </button>
                <button onclick="applyBulkEdit()" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-sm font-semibold hover:from-indigo-700 hover:to-purple-700">
                    適用
                </button>
            </div>
        </div>
    </div>
</div>

<!-- エクスポートモーダル -->
<div id="export-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeExportModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-file-export mr-2"></i>エクスポート</h3>
                <button onclick="closeExportModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4">タスク一覧をファイルにエクスポートします。</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ファイル形式</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="export-format" value="csv" checked class="mr-2">
                            <span class="text-sm text-slate-700">CSV</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="export-format" value="excel" class="mr-2">
                            <span class="text-sm text-slate-700">Excel</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">エクスポート対象</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="export-target" value="all" checked class="mr-2">
                            <span class="text-sm text-slate-700">すべてのタスク</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="export-target" value="filtered" class="mr-2">
                            <span class="text-sm text-slate-700">フィルター適用中のタスクのみ</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="export-include-subtasks" checked class="mr-2">
                        <span class="text-sm text-slate-700">サブタスクを含める</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                <button onclick="closeExportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                    キャンセル
                </button>
                <button onclick="executeExport()" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg text-sm font-semibold hover:from-emerald-700 hover:to-teal-700">
                    <i class="fas fa-download mr-2"></i>エクスポート
                </button>
            </div>
        </div>
    </div>
</div>

<!-- インポートモーダル -->
<div id="import-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeImportModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-file-import mr-2"></i>インポート</h3>
                <button onclick="closeImportModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4">CSVファイルからタスクをインポート、またはテキストを貼り付けます。</p>
            <div class="space-y-4">
                <!-- タブ切り替え -->
                <div class="flex border-b border-slate-200">
                    <button type="button" id="import-tab-file" class="import-tab active px-4 py-2 text-sm font-medium border-b-2 border-orange-500 text-orange-600" data-tab="file" onclick="switchImportTab('file')">
                        <i class="fas fa-file-csv mr-1"></i>ファイル
                    </button>
                    <button type="button" id="import-tab-paste" class="import-tab px-4 py-2 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700" data-tab="paste" onclick="switchImportTab('paste')">
                        <i class="fas fa-paste mr-1"></i>貼り付け
                    </button>
                </div>

                <!-- ファイル選択 -->
                <div id="import-file-section">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">CSVファイル</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors cursor-pointer" onclick="document.getElementById('import-file').click()">
                        <input type="file" id="import-file" accept=".csv" class="hidden" onchange="handleImportFileSelect(this)">
                        <div id="import-file-preview" class="text-slate-500">
                            <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-slate-400"></i>
                            <p class="text-sm">クリックしてファイルを選択<br>または、ドラッグ＆ドロップ</p>
                        </div>
                    </div>
                </div>

                <!-- 貼り付け -->
                <div id="import-paste-section" class="hidden">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">CSVデータを貼り付け（Ctrl+V）</label>
                    <textarea id="paste-data" class="w-full h-40 border border-slate-300 rounded-lg p-3 text-sm font-mono focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="CSVデータをここに貼り付けてください&#10;例: タスク名,工程,担当者,ステータス,進捗率,予定開始日,予定終了日,予定工数..." oninput="handlePasteInput(this)"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">インポートモード</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="import-mode" value="add" checked class="mr-2">
                            <span class="text-sm text-slate-700">追加（既存のタスクは残す）</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="import-mode" value="replace" class="mr-2">
                            <span class="text-sm text-slate-700">置換（既存のタスクを削除して新規追加）</span>
                        </label>
                    </div>
                </div>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>CSVフォーマット:</strong> No,工程,タスク名,担当者,ステータス,営業工数,予定工数,予定開始日,予定終了日,予定原価,実績工数,実績開始日,実績終了日,出来高,進捗率,備考,親タスクNo<br>
                        <a href="javascript:downloadTemplate()" class="text-blue-600 underline hover:text-blue-800">テンプレートをダウンロード</a>
                    </p>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                <button onclick="closeImportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                    キャンセル
                </button>
                <button onclick="executeImport()" id="import-execute-btn" disabled class="px-6 py-2 bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-lg text-sm font-semibold hover:from-orange-600 hover:to-amber-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>インポート
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 一括日付更新モーダル -->
<div id="bulk-date-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBulkDateModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-calendar-alt mr-2"></i>一括日付更新</h3>
                <button onclick="closeBulkDateModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-sm font-medium text-amber-800 mb-1"><i class="fas fa-check-square mr-1"></i>開始タスク（チェックした行）</p>
                <p id="bulk-start-task-display" class="text-sm text-amber-700 font-medium">-</p>
                <input type="hidden" id="bulk-start-task" value="">
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-medium text-blue-800 mb-1"><i class="fas fa-stop mr-1"></i>終了タスク</p>
                <p id="bulk-end-task-display" class="text-sm text-blue-700">指定なし（最後まで）</p>
                <input type="hidden" id="bulk-end-task" value="">
                <button onclick="startEndTaskSelection()" class="mt-2 px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    <i class="fas fa-mouse-pointer mr-1"></i>一覧から選択
                </button>
                <button onclick="clearEndTaskSelection()" class="mt-2 ml-2 px-3 py-1 text-xs border border-slate-300 text-slate-600 rounded hover:bg-slate-100 transition-colors">
                    <i class="fas fa-times mr-1"></i>クリア
                </button>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="bulk-exclude-weekends" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="bulk-exclude-weekends" class="ml-2 text-sm text-slate-700">土日を除外（営業日ベース）</label>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-600 mb-2"><i class="fas fa-info-circle mr-1 text-blue-500"></i>処理内容</p>
                <ul class="text-xs text-slate-500 space-y-1 ml-4 list-disc">
                    <li>開始タスクの日付を起点に、後続タスクの日付を連続割り当て</li>
                    <li>メンバー → 工程 → タスク順でソート</li>
                    <li>メンバーが変わると開始タスクの日付にリセット</li>
                    <li>サブタスクは親タスクの開始日から連続割り当て</li>
                    <li>親タスクの終了日はサブタスクの最終日に合わせる</li>
                    <li>ステータスが「完了」のタスクは除外</li>
                </ul>
            </div>
            <div id="bulk-date-preview" class="hidden p-3 bg-blue-50 rounded-lg max-h-40 overflow-y-auto">
                <p class="text-xs text-blue-700 font-medium mb-2"><i class="fas fa-eye mr-1"></i>対象タスク</p>
                <div id="bulk-date-preview-content" class="text-xs text-slate-600"></div>
            </div>
        </div>
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <button onclick="previewBulkDate()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                <i class="fas fa-eye mr-1"></i>プレビュー
            </button>
            <div class="flex space-x-3">
                <button onclick="closeBulkDateModal()" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">キャンセル</button>
                <button onclick="executeBulkDateUpdate()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-indigo-700 shadow-lg transition-all">
                    <i class="fas fa-check mr-2"></i>更新実行
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 原価割り振りモーダル -->
<div id="cost-allocation-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCostAllocationModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-calculator mr-2"></i>原価割り振り</h3>
                <button onclick="closeCostAllocationModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="grid grid-cols-3 gap-4 text-sm mb-3">
                    <div>
                        <span class="text-slate-600">プロジェクト予算:</span>
                        <span class="font-bold text-green-700 ml-2" id="modal-budget"><?= isset($selectedProject['budget']) ? number_format($selectedProject['budget']) : 0 ?>円</span>
                    </div>
                    <div>
                        <span class="text-slate-600">完了分:</span>
                        <span class="font-bold text-slate-500 ml-2" id="modal-completed-cost">0円</span>
                    </div>
                    <div>
                        <span class="text-slate-600">確定分:</span>
                        <span class="font-bold text-orange-600 ml-2" id="modal-fixed-cost">0円</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm pt-3 border-t border-green-200">
                    <div>
                        <span class="text-slate-600">残予算:</span>
                        <span class="font-bold text-emerald-700 ml-2" id="modal-remaining-budget">0円</span>
                    </div>
                    <div>
                        <span class="text-slate-600">総予定工数:</span>
                        <span class="font-bold text-blue-700 ml-2" id="modal-total-man-days">0日</span>
                    </div>
                    <div>
                        <span class="text-slate-600">工数単価:</span>
                        <span class="font-bold text-amber-700 ml-2" id="modal-unit-cost">0円/日</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-sm text-slate-600 mb-2">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    予算から完了分・確定分を差し引いた残予算を、チェックしたタスクの工数で按分します。
                </p>
                <p class="text-sm text-amber-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    完了分=完了済みタスク、確定分=チェックしていない未完了タスク
                </p>
            </div>
            <div class="mb-4 max-h-60 overflow-y-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-slate-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-slate-600">タスク名</th>
                            <th class="px-3 py-2 text-center text-slate-600">予定工数</th>
                            <th class="px-3 py-2 text-center text-slate-600">現在原価</th>
                            <th class="px-3 py-2 text-center text-slate-600">→ 新原価</th>
                        </tr>
                    </thead>
                    <tbody id="cost-allocation-preview"></tbody>
                </table>
            </div>
        </div>
        <div class="border-t border-slate-200 px-6 py-4 flex justify-end space-x-3 bg-slate-50">
            <button onclick="closeCostAllocationModal()" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">キャンセル</button>
            <button onclick="executeCostAllocation()" class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg text-sm font-semibold hover:from-green-700 hover:to-emerald-700 shadow-lg transition-all">
                <i class="fas fa-check mr-2"></i>割り振り実行
            </button>
        </div>
    </div>
</div>

<!-- 終了タスク選択モーダル -->
<div id="end-task-select-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEndTaskSelectModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-stop mr-2"></i>終了タスクを選択</h3>
                <button onclick="closeEndTaskSelectModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-4 max-h-[60vh] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-slate-100">
                    <tr>
                        <th class="px-3 py-2 text-left text-slate-600">No</th>
                        <th class="px-3 py-2 text-left text-slate-600">工程</th>
                        <th class="px-3 py-2 text-left text-slate-600">タスク名</th>
                        <th class="px-3 py-2 text-left text-slate-600">担当者</th>
                        <th class="px-3 py-2 text-left text-slate-600">ステータス</th>
                    </tr>
                </thead>
                <tbody id="end-task-select-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- タスク編集モーダル -->
<?= $this->include('schedule/partials/task_modal') ?>

<!-- ローディングオーバーレイ -->
<div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 flex items-center space-x-4">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
        <span id="loading-message" class="text-slate-700 font-medium">処理中...</span>
    </div>
</div>

<style>
#loading-overlay.show {
    opacity: 1;
    pointer-events: auto;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SheetJS for Excel export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<!-- タスク一覧用JavaScriptクラス -->
<?php $jsVer = '?v=' . time(); ?>
<script src="<?= base_url('js/schedule/task-list/TaskDataManager.js') . $jsVer ?>"></script>
<script src="<?= base_url('js/schedule/task-list/TaskTableRenderer.js') . $jsVer ?>"></script>
<script src="<?= base_url('js/schedule/task-list/TaskDragDropHandler.js') . $jsVer ?>"></script>
<script src="<?= base_url('js/schedule/task-list/TaskContextMenu.js') . $jsVer ?>"></script>
<script src="<?= base_url('js/schedule/task-list/TaskKeyboardHandler.js') . $jsVer ?>"></script>
<script src="<?= base_url('js/schedule/task-list/TaskListApp.js') . $jsVer ?>"></script>

<script>
// 初期データ
const initialData = {
    projectId: <?= json_encode($projectId) ?>,
    tasks: <?= json_encode($tasks) ?>,
    processes: <?= json_encode($processes) ?>,
    members: <?= json_encode($members) ?>,
    apiBaseUrl: '<?= base_url('api/tasks') ?>',
    csrfToken: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>'
};

// TaskListAppインスタンス
let taskListApp = null;

// アプリ初期化
document.addEventListener('DOMContentLoaded', function() {
    if (initialData.projectId) {
        taskListApp = new TaskListApp(initialData);
    }

    // ヘッダーフィルターのクリックイベント
    document.querySelectorAll('.header-filter').forEach(header => {
        header.addEventListener('click', function(e) {
            const filterType = this.dataset.filterType;
            if (filterType) {
                toggleHeaderFilter(e, filterType);
            }
        });
    });

    // フィルタードロップダウンアイテムのクリックイベント
    document.querySelectorAll('.filter-dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.filter-dropdown');
            const filterType = dropdown.id.replace('filter-dropdown-', '');
            const value = this.dataset.value || '';
            // 担当者フィルタは専用関数を使用
            if (filterType === 'assigneeName') {
                selectAssigneeFilter(value);
            } else {
                selectHeaderFilter(e, filterType, value);
            }
        });
    });

    // ドキュメントクリックでフィルタードロップダウンを閉じる
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.header-filter')) {
            document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.remove('show'));
        }
    });
});

// グローバル関数（既存のテンプレートとの互換性のため）
function switchViewMode(mode) {
    if (taskListApp) taskListApp.switchViewMode(mode);
}

function toggleSearchPanel() {
    if (taskListApp) taskListApp.toggleSearchPanel();
}

function toggleHeaderFilter(event, filterType) {
    event.stopPropagation();
    document.querySelectorAll('.filter-dropdown').forEach(d => {
        if (d.id !== 'filter-dropdown-' + filterType) d.classList.remove('show');
    });
    const dropdown = document.getElementById('filter-dropdown-' + filterType);
    if (dropdown) dropdown.classList.toggle('show');
}

function selectHeaderFilter(event, filterType, value) {
    event.stopPropagation();
    if (taskListApp) {
        taskListApp.activeFilters[filterType] = value;
        const dropdown = document.getElementById('filter-dropdown-' + filterType);
        if (dropdown) {
            dropdown.classList.remove('show');
            // 選択状態のスタイルを更新
            dropdown.querySelectorAll('.filter-dropdown-item').forEach(item => {
                item.classList.toggle('selected', item.dataset.value === value);
            });
        }
        const header = document.querySelector(`th[data-filter-type="${filterType}"]`);
        if (header) header.classList.toggle('has-filter', !!value);
        const panelSelect = document.getElementById('filter-' + filterType);
        if (panelSelect) panelSelect.value = value;
        taskListApp.applyFilters();
    }
}

function clearHeaderFilter(filterType) {
    const input = document.getElementById('filter-input-' + filterType);
    if (input) input.value = '';
    if (taskListApp) {
        taskListApp.activeFilters[filterType] = '';
        document.getElementById('filter-dropdown-' + filterType)?.classList.remove('show');
        const header = document.querySelector(`th[data-filter-type="${filterType}"]`);
        if (header) header.classList.remove('has-filter');
        taskListApp.applyFilters();
    }
}

function applyHeaderTextFilter(filterType) {
    const input = document.getElementById('filter-input-' + filterType);
    if (input && taskListApp) {
        taskListApp.activeFilters[filterType] = input.value;
        const header = document.querySelector(`th[data-filter-type="${filterType}"]`);
        if (header) header.classList.toggle('has-filter', !!input.value);
        taskListApp.applyFilters();
    }
}

// 担当者オプションをフィルタリング
function filterAssigneeOptions(searchText) {
    const options = document.querySelectorAll('#assignee-options-container .assignee-option');
    const searchLower = searchText.toLowerCase();
    options.forEach(option => {
        const text = option.textContent.toLowerCase();
        option.classList.toggle('hidden', !text.includes(searchLower));
    });
}

// 担当者フィルタを選択
function selectAssigneeFilter(value) {
    if (taskListApp) {
        taskListApp.activeFilters.assigneeName = value;
        const dropdown = document.getElementById('filter-dropdown-assigneeName');
        if (dropdown) {
            dropdown.classList.remove('show');
            // 選択状態のスタイルを更新
            dropdown.querySelectorAll('.filter-dropdown-item').forEach(item => {
                item.classList.toggle('selected', item.dataset.value === value);
            });
        }
        const header = document.querySelector('th[data-filter-type="assigneeName"]');
        if (header) header.classList.toggle('has-filter', !!value);
        // 検索ボックスをクリア
        const searchInput = document.getElementById('filter-search-assigneeName');
        if (searchInput) searchInput.value = '';
        filterAssigneeOptions('');
        taskListApp.applyFilters();
    }
}

function filterBySearch(searchText) {
    if (taskListApp) {
        taskListApp.activeFilters.search = searchText.toLowerCase();
        taskListApp.applyFilters();
    }
}

function applyFilter() {
    if (taskListApp) taskListApp.applyPanelFilters();
}

function clearFilter() {
    if (taskListApp) taskListApp.clearFilter();
}

function clearSelection() {
    if (taskListApp) taskListApp.clearSelection();
}

function updateSelectionBar() {
    if (taskListApp) taskListApp.updateSelectionBar();
}

function openBulkEditModal() {
    if (taskListApp) taskListApp.openBulkEditModal();
}

function closeBulkEditModal() {
    if (taskListApp) taskListApp.closeBulkEditModal();
}

function applyBulkEdit() {
    if (taskListApp) taskListApp.applyBulkEdit();
}

function bulkDelete() {
    if (taskListApp) taskListApp.bulkDelete();
}

function undoChanges() {
    if (taskListApp) taskListApp.undo();
}

function cancelEdit() {
    if (taskListApp) taskListApp.cancelEdit();
}

function saveAllTasks() {
    if (taskListApp) taskListApp.saveAllTasks();
}

// 右クリックメニュー操作
function addRowAbove() { if (taskListApp) taskListApp.addRowAbove(); }
function addRowBelow() { if (taskListApp) taskListApp.addRowBelow(); }
function addSubtaskFromMenu() { if (taskListApp) taskListApp.addSubtaskFromMenu(); }
function copyTaskAbove() { if (taskListApp) taskListApp.copyTaskAbove(); }
function copyTaskBelow() { if (taskListApp) taskListApp.copyTaskBelow(); }
function deleteTaskFromMenu() { if (taskListApp) taskListApp.deleteTaskFromMenu(); }
function addSubtaskAbove() { if (taskListApp) taskListApp.addRowAbove(); }
function addSubtaskBelow() { if (taskListApp) taskListApp.addRowBelow(); }
function copySubtaskAbove() { if (taskListApp) taskListApp.copyTaskAbove(); }
function copySubtaskBelow() { if (taskListApp) taskListApp.copyTaskBelow(); }
function deleteSubtaskFromMenu() { if (taskListApp) taskListApp.deleteTaskFromMenu(); }
function hideContextMenus() { document.querySelectorAll('.context-menu').forEach(m => m.classList.remove('show')); }

// ========================================
// インポート/エクスポート機能
// ========================================

let importFileData = null;
let importMode = 'file'; // 'file' or 'paste'

function openExportModal() {
    document.getElementById('export-modal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('export-modal').classList.add('hidden');
}

function openImportModal() {
    const modal = document.getElementById('import-modal');
    modal.classList.remove('hidden');
    resetImportForm();
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
    resetImportForm();
}

function resetImportForm() {
    document.getElementById('import-file').value = '';
    document.getElementById('import-file-preview').innerHTML = `
        <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-slate-400"></i>
        <p class="text-sm">クリックしてファイルを選択<br>または、ドラッグ＆ドロップ</p>
    `;
    const pasteArea = document.getElementById('paste-data');
    if (pasteArea) pasteArea.value = '';
    document.getElementById('import-execute-btn').disabled = true;
    importFileData = null;
    importMode = 'file';
    switchImportTab('file');
}

function switchImportTab(tab) {
    importMode = tab;
    const fileTab = document.getElementById('import-tab-file');
    const pasteTab = document.getElementById('import-tab-paste');
    const fileSection = document.getElementById('import-file-section');
    const pasteSection = document.getElementById('import-paste-section');

    if (tab === 'file') {
        fileTab.classList.add('border-orange-500', 'text-orange-600', 'active');
        fileTab.classList.remove('border-transparent', 'text-slate-500');
        pasteTab.classList.remove('border-orange-500', 'text-orange-600', 'active');
        pasteTab.classList.add('border-transparent', 'text-slate-500');
        fileSection.classList.remove('hidden');
        pasteSection.classList.add('hidden');
        // ファイルが選択されていればボタン有効化
        document.getElementById('import-execute-btn').disabled = !importFileData;
    } else {
        pasteTab.classList.add('border-orange-500', 'text-orange-600', 'active');
        pasteTab.classList.remove('border-transparent', 'text-slate-500');
        fileTab.classList.remove('border-orange-500', 'text-orange-600', 'active');
        fileTab.classList.add('border-transparent', 'text-slate-500');
        pasteSection.classList.remove('hidden');
        fileSection.classList.add('hidden');
        // テキストがあればボタン有効化
        const pasteArea = document.getElementById('paste-data');
        document.getElementById('import-execute-btn').disabled = !pasteArea.value.trim();
    }
}

function handlePasteInput(textarea) {
    const hasContent = textarea.value.trim().length > 0;
    document.getElementById('import-execute-btn').disabled = !hasContent;
}

function handleImportFileSelect(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('import-file-preview').innerHTML = `
            <i class="fas fa-file-csv text-3xl mb-2 text-emerald-500"></i>
            <p class="text-sm font-medium text-slate-700">${file.name}</p>
            <p class="text-xs text-slate-500">${(file.size / 1024).toFixed(1)} KB</p>
        `;
        document.getElementById('import-execute-btn').disabled = false;

        const reader = new FileReader();
        reader.onload = function(e) {
            importFileData = e.target.result;
        };
        reader.readAsText(file, 'UTF-8');
    }
}

// エクスポート実行
function executeExport() {
    if (!taskListApp) return;

    const format = document.querySelector('input[name="export-format"]:checked').value;
    const target = document.querySelector('input[name="export-target"]:checked').value;
    const includeSubtasks = document.getElementById('export-include-subtasks').checked;

    let tasks;
    if (target === 'filtered' && taskListApp.filteredTasks) {
        tasks = taskListApp.filteredTasks;
    } else {
        tasks = taskListApp.dataManager.getTasks();
    }

    // CSVデータ作成（一覧の列順に合わせる）
    const headers = [
        'No', '工程', 'タスク名', '担当者', 'ステータス',
        '営業工数', '予定工数', '予定開始日', '予定終了日', '予定原価',
        '実績工数', '実績開始日', '実績終了日', '出来高',
        '進捗率', '備考', '親タスクNo'
    ];
    let rows = [headers];

    const processMap = {};
    initialData.processes.forEach(p => processMap[p.id] = p.name);

    const memberMap = {};
    initialData.members.forEach(m => memberMap[m.id] = m.name);

    const statusMap = {
        'not_started': '未着手',
        'in_progress': '進行中',
        'completed': '完了',
        'on_hold': '保留'
    };

    let rowNo = 1;
    tasks.forEach(task => {
        const parentNo = rowNo;
        rows.push([
            rowNo++,
            processMap[task.process_id] || '',
            task.task_name || '',
            memberMap[task.assignee_id] || '',
            statusMap[task.status] || task.status || '',
            task.sales_man_days || '',
            task.planned_man_days || '',
            task.planned_start_date || '',
            task.planned_end_date || '',
            task.planned_cost || '',
            task.actual_man_days || '',
            task.actual_start_date || '',
            task.actual_end_date || '',
            task.actual_cost || '',
            task.progress || '0',
            task.description || '',
            ''
        ]);

        if (includeSubtasks && task.subtasks) {
            task.subtasks.forEach(subtask => {
                rows.push([
                    rowNo++,
                    processMap[subtask.process_id] || '',
                    subtask.task_name || '',
                    memberMap[subtask.assignee_id] || '',
                    statusMap[subtask.status] || subtask.status || '',
                    subtask.sales_man_days || '',
                    subtask.planned_man_days || '',
                    subtask.planned_start_date || '',
                    subtask.planned_end_date || '',
                    subtask.planned_cost || '',
                    subtask.actual_man_days || '',
                    subtask.actual_start_date || '',
                    subtask.actual_end_date || '',
                    subtask.actual_cost || '',
                    subtask.progress || '0',
                    subtask.description || '',
                    parentNo
                ]);
            });
        }
    });

    if (format === 'csv') {
        downloadCSV(rows, `tasks_${formatDateString(new Date())}.csv`);
    } else {
        downloadExcel(rows, `tasks_${formatDateString(new Date())}.xlsx`);
    }

    closeExportModal();
    showToast(`${rows.length - 1}件のタスクをエクスポートしました`, 'success');
}

// CSVダウンロード
function downloadCSV(rows, filename) {
    const bom = '\uFEFF';
    const csv = rows.map(row =>
        row.map(cell => {
            const cellStr = String(cell);
            if (cellStr.includes(',') || cellStr.includes('"') || cellStr.includes('\n')) {
                return '"' + cellStr.replace(/"/g, '""') + '"';
            }
            return cellStr;
        }).join(',')
    ).join('\n');

    const blob = new Blob([bom + csv], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Excel形式ダウンロード（SheetJSを使用して正しいxlsx形式で出力）
function downloadExcel(rows, filename) {
    // SheetJSを使用してワークブックを作成
    const ws = XLSX.utils.aoa_to_sheet(rows);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'タスク一覧');

    // xlsxファイルとしてダウンロード
    XLSX.writeFile(wb, filename);
}

// テンプレートダウンロード
function downloadTemplate() {
    const headers = [
        'No', '工程', 'タスク名', '担当者', 'ステータス',
        '営業工数', '予定工数', '予定開始日', '予定終了日', '予定原価',
        '実績工数', '実績開始日', '実績終了日', '出来高',
        '進捗率', '備考', '親タスクNo'
    ];
    const example1 = [
        '1', '設計', 'サンプル親タスク', '山田太郎', '未着手',
        '5', '3', '2025-01-20', '2025-01-25', '100000',
        '', '', '', '',
        '0', 'タスクの説明文', ''
    ];
    const example2 = [
        '2', '設計', 'サンプル子タスク', '山田太郎', '未着手',
        '2', '1', '2025-01-20', '2025-01-22', '50000',
        '', '', '', '',
        '0', '親タスクの子タスク', '1'
    ];
    downloadCSV([headers, example1, example2], 'task_import_template.csv');
}

// インポート実行
async function executeImport() {
    console.log('[executeImport] 開始');
    // ファイルモードかペーストモードかを判定
    const importTab = document.querySelector('.import-tab.active');
    const isPasteMode = importTab && importTab.dataset.tab === 'paste';
    console.log('[executeImport] importTab:', importTab);
    console.log('[executeImport] isPasteMode:', isPasteMode);

    let dataToImport = '';

    if (isPasteMode) {
        // ペーストモード
        const pasteArea = document.getElementById('paste-data');
        console.log('[executeImport] pasteArea:', pasteArea);
        console.log('[executeImport] pasteArea.value:', pasteArea ? pasteArea.value : 'null');
        dataToImport = pasteArea ? pasteArea.value : '';
        if (!dataToImport || !dataToImport.trim()) {
            showToast('データを貼り付けてください', 'warning');
            return;
        }
    } else {
        // ファイルモード
        console.log('[executeImport] importFileData:', importFileData);
        if (!importFileData) {
            showToast('ファイルを選択してください', 'warning');
            return;
        }
        dataToImport = importFileData;
    }
    console.log('[executeImport] dataToImport length:', dataToImport.length);

    if (!taskListApp) {
        showToast('アプリケーションが初期化されていません', 'error');
        return;
    }

    const mode = document.querySelector('input[name="import-mode"]:checked').value;

    // CSVまたはTSVパース（タブ区切りも対応）
    const lines = dataToImport.split('\n').filter(line => line.trim());
    if (lines.length < 1) {
        showToast('有効なデータがありません', 'error');
        return;
    }

    // タブ区切りかカンマ区切りかを判定
    const firstLine = lines[0];
    const isTabSeparated = firstLine.includes('\t');

    // ヘッダー行の判定（最初の列が「No」「工程」等のヘッダーかどうか）
    const headerKeywords = ['No', 'no', 'NO', '工程', '工程名', 'タスク名', 'タスク', 'task', 'Task', 'TASK', '名前', '名称', 'process'];
    const firstCol = isTabSeparated ? firstLine.split('\t')[0] : parseCSVLine(firstLine)[0];
    const hasHeader = headerKeywords.some(keyword => firstCol.trim().toLowerCase() === keyword.toLowerCase());

    // ヘッダー行がある場合はスキップ
    const dataLines = hasHeader ? lines.slice(1) : lines;

    if (dataLines.length === 0) {
        showToast('インポートするデータがありません', 'error');
        return;
    }

    // 工程・担当者の逆引きマップ作成
    const processNameMap = {};
    initialData.processes.forEach(p => processNameMap[p.name] = p.id);

    const memberNameMap = {};
    initialData.members.forEach(m => memberNameMap[m.name] = m.id);

    const statusNameMap = {
        '未着手': 'not_started',
        '進行中': 'in_progress',
        '完了': 'completed',
        '保留': 'on_hold'
    };

    // タスクデータ作成
    const tasksToImport = [];
    const parentTaskMap = {};

    dataLines.forEach((line, index) => {
        const cols = isTabSeparated ? line.split('\t') : parseCSVLine(line);
        if (cols.length < 3 || !cols[2]?.trim()) return; // タスク名（3列目）が必須

        // 全17列に対応（テーブル列順序: No, 工程, タスク名, 担当者, ステータス, 営業工数, 予定工数, 予定開始日, 予定終了日, 予定原価, 実績工数, 実績開始日, 実績終了日, 出来高, 進捗率, 備考, 親タスクNo）
        const rowNo = cols[0]?.trim() || '';
        const processName = cols[1]?.trim() || '';
        const taskName = cols[2]?.trim() || '';
        const assigneeName = cols[3]?.trim() || '';
        const statusName = cols[4]?.trim() || '';
        const salesManDays = cols[5]?.trim() || null;
        const plannedManDays = cols[6]?.trim() || null;
        const plannedStart = cols[7]?.trim() || null;
        const plannedEnd = cols[8]?.trim() || null;
        const plannedCost = cols[9]?.trim() || null;
        const actualManDays = cols[10]?.trim() || null;
        const actualStart = cols[11]?.trim() || null;
        const actualEnd = cols[12]?.trim() || null;
        const actualCost = cols[13]?.trim() || null;
        const progress = cols[14]?.trim() || '0';
        const description = cols[15]?.trim() || '';
        const parentNo = cols[16]?.trim() || '';

        const taskData = {
            project_id: initialData.projectId,
            task_name: taskName,
            process_id: processNameMap[processName] || null,
            assignee_id: memberNameMap[assigneeName] || null,
            status: statusNameMap[statusName] || 'not_started',
            progress: parseInt(progress) || 0,
            planned_start_date: plannedStart || null,
            planned_end_date: plannedEnd || null,
            planned_man_days: plannedManDays ? parseFloat(plannedManDays) : null,
            sales_man_days: salesManDays ? parseFloat(salesManDays) : null,
            planned_cost: plannedCost ? parseFloat(plannedCost) : null,
            actual_start_date: actualStart || null,
            actual_end_date: actualEnd || null,
            actual_man_days: actualManDays ? parseFloat(actualManDays) : null,
            actual_cost: actualCost ? parseFloat(actualCost) : null,
            description: description || null
        };

        if (parentNo) {
            // サブタスク - 親タスクNoで参照
            taskData.parent_no = parentNo;
        }

        // NoをキーにしてマップにIndexを保存
        if (rowNo) {
            parentTaskMap[rowNo] = tasksToImport.length;
        }

        tasksToImport.push(taskData);
    });

    // APIに送信
    try {
        const payload = {
            tasks: tasksToImport,
            mode: mode
        };

        const response = await fetch('<?= base_url('api/tasks/import') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            showToast(`${result.imported || tasksToImport.length}件のタスクをインポートしました`, 'success');
            closeImportModal();
            location.reload();
        } else {
            showToast(result.error || 'インポートに失敗しました', 'error');
        }
    } catch (error) {
        console.error('Import error:', error);
        showToast('インポート中にエラーが発生しました', 'error');
    }
}

// CSV行パース（ダブルクォート対応）
function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;

    for (let i = 0; i < line.length; i++) {
        const char = line[i];

        if (inQuotes) {
            if (char === '"') {
                if (line[i + 1] === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = false;
                }
            } else {
                current += char;
            }
        } else {
            if (char === '"') {
                inQuotes = true;
            } else if (char === ',') {
                result.push(current);
                current = '';
            } else {
                current += char;
            }
        }
    }
    result.push(current);

    return result;
}

// トースト表示
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle', warning: 'exclamation-triangle' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon"><i class="fas fa-${icons[type] || icons.info}"></i></div>
        <div class="toast-message">${message}</div>
        <div class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></div>
    `;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.add('hiding'); setTimeout(() => toast.remove(), 300); }, 5000);
}

// タスクモーダル関連
function openTaskModal(taskId = null) {
    const modal = document.getElementById('task-modal');
    const form = document.getElementById('task-form');
    const title = document.getElementById('modal-title');

    form.reset();
    document.getElementById('task-id').value = taskId || '';
    document.getElementById('task-project-id').value = initialData.projectId;

    if (taskId) {
        title.textContent = 'タスク編集';
        loadTaskData(taskId);
    } else {
        title.textContent = '新規タスク';
        document.getElementById('btn-delete-task').classList.add('hidden');
    }

    modal.classList.remove('hidden');
}

function closeTaskModal() {
    document.getElementById('task-modal').classList.add('hidden');
}

async function loadTaskData(taskId) {
    try {
        const response = await fetch(`<?= base_url('api/tasks') ?>/${taskId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        if (result.success) {
            const task = result.data;
            document.getElementById('task-name').value = task.task_name || '';
            document.getElementById('task-process').value = task.process_id || '';

            // 担当者をセット（セレクトボックスにオプションがない場合は追加）
            const assigneeSelect = document.getElementById('task-assignee');
            if (task.assignee_id) {
                const optionExists = Array.from(assigneeSelect.options).some(opt => opt.value == task.assignee_id);
                if (!optionExists && task.assignee_name) {
                    const newOption = document.createElement('option');
                    newOption.value = task.assignee_id;
                    newOption.textContent = task.assignee_name + ' (プロジェクト外)';
                    assigneeSelect.appendChild(newOption);
                }
            }
            assigneeSelect.value = task.assignee_id || '';
            document.getElementById('task-status').value = task.status || 'not_started';
            document.getElementById('task-planned-start').value = task.planned_start_date || '';
            document.getElementById('task-planned-end').value = task.planned_end_date || '';
            document.getElementById('task-planned-man-days').value = task.planned_man_days || '';
            document.getElementById('task-sales-man-days').value = task.sales_man_days || '';
            document.getElementById('task-planned-cost').value = task.planned_cost || '';
            document.getElementById('task-progress').value = task.progress || 0;
            document.getElementById('task-description').value = task.description || '';
            document.getElementById('btn-delete-task').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function saveTask() {
    const taskId = document.getElementById('task-id').value;
    const data = {
        project_id: document.getElementById('task-project-id').value,
        task_name: document.getElementById('task-name').value,
        process_id: document.getElementById('task-process').value,
        assignee_id: document.getElementById('task-assignee').value || null,
        status: document.getElementById('task-status').value,
        planned_start_date: document.getElementById('task-planned-start').value || null,
        planned_end_date: document.getElementById('task-planned-end').value || null,
        planned_man_days: document.getElementById('task-planned-man-days').value || null,
        sales_man_days: document.getElementById('task-sales-man-days').value || null,
        planned_cost: document.getElementById('task-planned-cost').value || null,
        progress: document.getElementById('task-progress').value || 0,
        description: document.getElementById('task-description').value || null
    };

    const url = taskId ? `<?= base_url('api/tasks') ?>/${taskId}` : '<?= base_url('api/tasks') ?>';
    const method = taskId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast(taskId ? '更新しました' : '作成しました', 'success');
            closeTaskModal();
            location.reload();
        } else {
            const errors = result.errors ? Object.values(result.errors).join('\n') : result.error;
            showToast(errors || '保存に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('通信エラーが発生しました', 'error');
    }
}

async function deleteTask() {
    const taskId = document.getElementById('task-id').value;
    if (!taskId) return;

    if (!confirm('このタスクを削除しますか？サブタスクも削除されます。')) return;

    try {
        const response = await fetch(`<?= base_url('api/tasks') ?>/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast('削除しました', 'success');
            closeTaskModal();
            location.reload();
        } else {
            showToast(result.error || '削除に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('通信エラーが発生しました', 'error');
    }
}

// キーボードショートカット
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.context-menu').forEach(m => m.classList.remove('show'));
        closeBulkEditModal();
        closeTaskModal();
        closeBulkDateModal();
        closeEndTaskSelectModal();
        closeExportModal();
        closeImportModal();
    }
});

// ========================================
// 一括日付更新機能
// ========================================

// 工程の順序マップ
const processOrder = {};
initialData.processes.forEach((p, i) => { processOrder[p.id] = i + 1; });

// 日付編集状態
let dateEditedTaskId = null;
let dateEditedSubtaskId = null;
let dateEditedRows = new Set();
let dateEditedField = 'planned_start_date'; // 編集された日付フィールド（デフォルトは開始日）
let selectedEndTaskId = null;
let selectedEndSubtaskId = null;

// 日付編集時の処理（TaskTableRendererから呼び出し）
function onDateEdited(taskId, subtaskId = null, fieldName = null) {
    dateEditedTaskId = taskId;
    dateEditedSubtaskId = subtaskId;
    if (fieldName && (fieldName === 'planned_start_date' || fieldName === 'planned_end_date')) {
        dateEditedField = fieldName;
    }

    // ハイライト表示
    dateEditedRows.add(`task-${taskId}`);
    if (subtaskId !== null) {
        dateEditedRows.add(`subtask-${subtaskId}`);
    }
    applyDateEditHighlight();
    // 一括日付ボタンはチェックボックス選択時のみ表示（updateSelectionBarで制御）
}

// ハイライトを適用
function applyDateEditHighlight() {
    document.querySelectorAll('.task-row, .subtask-row').forEach(row => {
        const taskId = row.dataset.taskId;
        if (dateEditedRows.has(`task-${taskId}`) || dateEditedRows.has(`subtask-${taskId}`)) {
            row.classList.add('date-edited');
        } else {
            row.classList.remove('date-edited');
        }
    });
}

// ハイライトとボタンをリセット
function clearDateEditState() {
    dateEditedTaskId = null;
    dateEditedSubtaskId = null;
    dateEditedRows.clear();
    dateEditedField = 'planned_start_date'; // デフォルトにリセット
    document.querySelectorAll('.date-edited').forEach(el => el.classList.remove('date-edited'));
    document.getElementById('bulk-date-btn').classList.add('hidden');
}

function openBulkDateModal() {
    const modal = document.getElementById('bulk-date-modal');
    modal.classList.remove('hidden');

    // モーダルを開く前に、全ての日付入力値をDataManagerに同期
    // これにより、編集中の値が確実に反映される
    syncAllDateValuesToDataManager();

    // 開始タスクを表示（チェックした行の中で最も上にある行）
    const startTaskId = window.selectedStartTaskId || dateEditedTaskId;

    if (startTaskId !== null && taskListApp) {
        const task = taskListApp.dataManager.getTaskById(startTaskId);
        if (task) {
            // DOM入力フィールドから変更後の日付を取得（編集中の値を優先）
            let startDate = getDateFromDom(startTaskId, 'planned_start_date') || task.planned_start_date || '未設定';
            let endDate = getDateFromDom(startTaskId, 'planned_end_date') || task.planned_end_date || '未設定';

            // 編集されたフィールドの日付を基準日として使用
            const baseFieldLabel = dateEditedField === 'planned_end_date' ? '終了日' : '開始日';
            const baseDate = dateEditedField === 'planned_end_date' ? endDate : startDate;

            console.log('[openBulkDateModal] タスクID:', startTaskId);
            console.log('[openBulkDateModal] 編集されたフィールド:', dateEditedField);
            console.log('[openBulkDateModal] 基準日:', baseDate);

            let displayText = `${task.task_name}`;
            displayText += `\n<strong>基準日（${baseFieldLabel}）: ${baseDate}</strong>`;
            displayText += `\n開始日: ${startDate} / 終了日: ${endDate}`;

            // 選択した行数を表示
            const selectedCount = window.selectedTaskIds ? window.selectedTaskIds.length : 1;
            if (selectedCount > 1) {
                displayText += ` [${selectedCount}件選択中]`;
            }

            document.getElementById('bulk-start-task-display').innerHTML = displayText.replace(/\n/g, '<br>');
            document.getElementById('bulk-start-task').value = startTaskId;
        }
    } else {
        document.getElementById('bulk-start-task-display').textContent = '行をチェックして選択してください';
        document.getElementById('bulk-start-task').value = '';
    }

    // 終了タスクをクリア
    clearEndTaskSelection();

    // プレビューを非表示に
    document.getElementById('bulk-date-preview').classList.add('hidden');
}

function closeBulkDateModal() {
    document.getElementById('bulk-date-modal').classList.add('hidden');
}

// HTMLエスケープ
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ローディング表示
function showLoading(message = '処理中...') {
    document.getElementById('loading-message').textContent = message;
    document.getElementById('loading-overlay').classList.add('show');
}

// ローディング非表示
function hideLoading() {
    document.getElementById('loading-overlay').classList.remove('show');
}

// 原価割り振りモーダルを開く
function openCostAllocationModal() {
    const modal = document.getElementById('cost-allocation-modal');
    if (!modal) return;

    const budget = <?= $selectedProject['budget'] ?? 0 ?>;
    const tasks = taskListApp.dataManager.getTasks();

    // チェックされたタスクIDを取得
    const selectedIds = taskListApp.tableRenderer.getSelectedTaskIds();
    if (selectedIds.length === 0) {
        showToast('対象タスクをチェックしてください', 'warning');
        return;
    }

    // 予算が0の場合は警告
    if (!budget || budget <= 0) {
        showToast('プロジェクトに予算が設定されていません', 'warning');
        return;
    }

    // 完了タスクの原価を集計（予算から差し引く分）
    let completedCost = 0;
    // チェックされていない未完了タスクの原価を集計（確定分として予算から差し引く）
    let fixedCost = 0;

    tasks.forEach(task => {
        const isSelected = selectedIds.includes(String(task.id));
        const isCompleted = task.progress >= 100 || task.status === 'completed';

        if (isCompleted) {
            // 完了タスク
            completedCost += parseFloat(task.planned_cost) || 0;
        } else if (!isSelected) {
            // チェックされていない未完了タスク（確定分）
            fixedCost += parseFloat(task.planned_cost) || 0;
        }

        // サブタスクもチェック
        if (task.subtasks) {
            task.subtasks.forEach(subtask => {
                const isSubSelected = selectedIds.includes(String(subtask.id));
                const isSubCompleted = subtask.progress >= 100 || subtask.status === 'completed';

                if (isSubCompleted) {
                    completedCost += parseFloat(subtask.planned_cost) || 0;
                } else if (!isSubSelected) {
                    fixedCost += parseFloat(subtask.planned_cost) || 0;
                }
            });
        }
    });

    // 残予算を計算（完了分と確定分を差し引く）
    const remainingBudget = Math.max(0, budget - completedCost - fixedCost);

    // チェックされたタスク（進捗100%以外かつ完了ステータス以外）を抽出
    let targetTasks = [];
    tasks.forEach(task => {
        // 親タスクがチェックされている場合
        if (selectedIds.includes(String(task.id))) {
            if (task.progress < 100 && task.status !== 'completed' && task.planned_man_days > 0) {
                targetTasks.push({
                    id: task.id,
                    name: task.task_name,
                    manDays: parseFloat(task.planned_man_days) || 0,
                    currentCost: parseFloat(task.planned_cost) || 0,
                    isSubtask: false
                });
            }
        }
        // サブタスクもチェック
        if (task.subtasks) {
            task.subtasks.forEach(subtask => {
                if (selectedIds.includes(String(subtask.id))) {
                    if (subtask.progress < 100 && subtask.status !== 'completed' && subtask.planned_man_days > 0) {
                        targetTasks.push({
                            id: subtask.id,
                            name: '└ ' + subtask.task_name,
                            manDays: parseFloat(subtask.planned_man_days) || 0,
                            currentCost: parseFloat(subtask.planned_cost) || 0,
                            isSubtask: true
                        });
                    }
                }
            });
        }
    });

    // 総工数計算（残予算を使用）
    const totalManDays = targetTasks.reduce((sum, t) => sum + t.manDays, 0);
    const unitCost = totalManDays > 0 ? Math.floor(remainingBudget / totalManDays) : 0;

    // 表示を更新
    document.getElementById('modal-completed-cost').textContent = completedCost.toLocaleString() + '円';
    document.getElementById('modal-fixed-cost').textContent = fixedCost.toLocaleString() + '円';
    document.getElementById('modal-remaining-budget').textContent = remainingBudget.toLocaleString() + '円';
    document.getElementById('modal-total-man-days').textContent = totalManDays.toFixed(1) + '日';
    document.getElementById('modal-unit-cost').textContent = unitCost.toLocaleString() + '円/日';

    // プレビュー生成
    const previewBody = document.getElementById('cost-allocation-preview');
    previewBody.innerHTML = '';

    if (targetTasks.length === 0) {
        previewBody.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">対象タスクがありません（完了済みか工数未設定）</td></tr>';
    } else {
        targetTasks.forEach(task => {
            const newCost = Math.floor(task.manDays * unitCost);
            const row = document.createElement('tr');
            row.className = 'border-b border-slate-100';
            row.innerHTML = `
                <td class="px-3 py-2 ${task.isSubtask ? 'text-slate-500' : 'text-slate-700'}">${escapeHtml(task.name)}</td>
                <td class="px-3 py-2 text-center">${task.manDays}日</td>
                <td class="px-3 py-2 text-center text-slate-500">${task.currentCost.toLocaleString()}円</td>
                <td class="px-3 py-2 text-center font-medium text-green-700">${newCost.toLocaleString()}円</td>
            `;
            previewBody.appendChild(row);
        });
    }

    // モーダルにデータを保存
    modal.dataset.targetTasks = JSON.stringify(targetTasks);
    modal.dataset.unitCost = unitCost;

    modal.classList.remove('hidden');
}

function closeCostAllocationModal() {
    document.getElementById('cost-allocation-modal').classList.add('hidden');
}

// 原価割り振り実行
async function executeCostAllocation() {
    const modal = document.getElementById('cost-allocation-modal');
    const targetTasks = JSON.parse(modal.dataset.targetTasks || '[]');
    const unitCost = parseFloat(modal.dataset.unitCost) || 0;

    if (targetTasks.length === 0) {
        showToast('対象タスクがありません', 'warning');
        return;
    }

    showLoading('原価を更新中...');

    try {
        // 各タスクの原価を更新
        const updates = targetTasks.map(task => ({
            id: task.id,
            planned_cost: Math.floor(task.manDays * unitCost)
        }));

        const response = await fetch('<?= base_url('api/tasks/bulk-update') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ tasks: updates })
        });

        const result = await response.json();

        if (result.success) {
            hideLoading();
            closeCostAllocationModal();
            showToast(`${updates.length}件のタスクの原価を更新しました`, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            hideLoading();
            showToast(result.error || '更新に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        hideLoading();
        showToast('通信エラーが発生しました', 'error');
    }
}

// 終了タスク選択モーダルを開く
function startEndTaskSelection() {
    document.getElementById('bulk-date-modal').classList.add('hidden');
    document.getElementById('end-task-select-modal').classList.remove('hidden');
    renderEndTaskSelectList();
}

// 終了タスク選択モーダルを閉じる
function closeEndTaskSelectModal() {
    document.getElementById('end-task-select-modal').classList.add('hidden');
    document.getElementById('bulk-date-modal').classList.remove('hidden');
}

// 終了タスク選択一覧を生成
function renderEndTaskSelectList() {
    const tbody = document.getElementById('end-task-select-tbody');
    tbody.innerHTML = '';

    if (!taskListApp) return;

    const tasks = taskListApp.dataManager.getTasks();
    const startTaskId = dateEditedTaskId;

    tasks.forEach((task, index) => {
        let statusClass = 'text-slate-500';
        if (task.status === 'in_progress') statusClass = 'text-blue-600';
        else if (task.status === 'completed') statusClass = 'text-emerald-600';

        const isStartTask = (startTaskId === task.id && dateEditedSubtaskId === null);
        const highlightClass = isStartTask ? 'bg-amber-100' : '';
        const statusLabel = { 'not_started': '未着手', 'in_progress': '進行中', 'completed': '完了', 'on_hold': '保留' }[task.status] || task.status;

        const row = document.createElement('tr');
        row.className = `end-task-select-row border-b border-slate-100 hover:bg-slate-50 cursor-pointer ${highlightClass}`;
        row.innerHTML = `
            <td class="px-3 py-2 text-slate-600">${index + 1}</td>
            <td class="px-3 py-2 text-slate-600">${task.process_name || '-'}</td>
            <td class="px-3 py-2 font-medium text-slate-800">
                ${isStartTask ? '<i class="fas fa-play text-amber-500 mr-1"></i>' : ''}${task.task_name}
            </td>
            <td class="px-3 py-2 text-slate-600">${task.assignee_name || '-'}</td>
            <td class="px-3 py-2 ${statusClass}">${statusLabel}</td>
        `;
        row.addEventListener('click', () => selectEndTask(task.id, null, task));
        tbody.appendChild(row);

        // サブタスクも表示
        if (task.subtasks && task.subtasks.length > 0) {
            task.subtasks.forEach((subtask, subIndex) => {
                let subStatusClass = 'text-slate-500';
                if (subtask.status === 'in_progress') subStatusClass = 'text-blue-600';
                else if (subtask.status === 'completed') subStatusClass = 'text-emerald-600';

                const isStartSubtask = (startTaskId === task.id && dateEditedSubtaskId === subtask.id);
                const subHighlightClass = isStartSubtask ? 'bg-amber-100' : '';
                const subStatusLabel = { 'not_started': '未着手', 'in_progress': '進行中', 'completed': '完了', 'on_hold': '保留' }[subtask.status] || subtask.status;

                const subRow = document.createElement('tr');
                subRow.className = `end-task-select-row border-b border-slate-50 bg-slate-50 hover:bg-slate-100 cursor-pointer ${subHighlightClass}`;
                subRow.innerHTML = `
                    <td class="px-3 py-2 text-slate-400 text-sm">${index + 1}-${subIndex + 1}</td>
                    <td class="px-3 py-2 text-slate-500 text-sm">${subtask.process_name || '-'}</td>
                    <td class="px-3 py-2 text-slate-700 text-sm pl-6">
                        <i class="fas fa-level-up-alt fa-rotate-90 text-slate-300 mr-1"></i>
                        ${isStartSubtask ? '<i class="fas fa-play text-amber-500 mr-1"></i>' : ''}${subtask.task_name}
                    </td>
                    <td class="px-3 py-2 text-slate-500 text-sm">${subtask.assignee_name || '-'}</td>
                    <td class="px-3 py-2 text-sm ${subStatusClass}">${subStatusLabel}</td>
                `;
                subRow.addEventListener('click', () => selectEndTask(task.id, subtask.id, task, subtask));
                tbody.appendChild(subRow);
            });
        }
    });
}

// 終了タスク選択完了
function selectEndTask(taskId, subtaskId, task, subtask = null) {
    selectedEndTaskId = taskId;
    selectedEndSubtaskId = subtaskId;

    let displayText = '';
    if (subtaskId !== null && subtask) {
        displayText = `${task.task_name} > ${subtask.task_name} (${subtask.assignee_name || '未割当'})`;
        document.getElementById('bulk-end-task').value = `${taskId}-${subtaskId}`;
    } else {
        displayText = `${task.task_name} (${task.assignee_name || '未割当'})`;
        document.getElementById('bulk-end-task').value = taskId;
    }
    document.getElementById('bulk-end-task-display').textContent = displayText;

    closeEndTaskSelectModal();
}

// 終了タスク選択クリア
function clearEndTaskSelection() {
    selectedEndTaskId = null;
    selectedEndSubtaskId = null;
    document.getElementById('bulk-end-task').value = '';
    document.getElementById('bulk-end-task-display').textContent = '指定なし（最後まで）';
}

// 営業日を計算（土日を除外）
function addBusinessDays(startDate, days, excludeWeekends) {
    const date = new Date(startDate);
    let addedDays = 0;

    while (addedDays < days) {
        date.setDate(date.getDate() + 1);
        if (!excludeWeekends || (date.getDay() !== 0 && date.getDay() !== 6)) {
            addedDays++;
        }
    }

    return date;
}

// 開始日が営業日でなければ次の営業日に調整
function adjustToBusinessDay(date, excludeWeekends) {
    const d = new Date(date);
    if (excludeWeekends) {
        while (d.getDay() === 0 || d.getDay() === 6) {
            d.setDate(d.getDate() + 1);
        }
    }
    return d;
}

// 工数から終了日を計算
function calculateEndDate(startDate, manDays, excludeWeekends) {
    if (!manDays || manDays <= 0) {
        return startDate;
    }
    const daysToAdd = Math.ceil(manDays) - 1;
    if (daysToAdd <= 0) {
        return startDate;
    }
    return addBusinessDays(startDate, daysToAdd, excludeWeekends);
}

// 終了日から開始日を逆算（工数から開始日を計算）
function calculateStartDateFromEnd(endDate, manDays, excludeWeekends) {
    if (!manDays || manDays <= 0) {
        return endDate;
    }
    const daysToSubtract = Math.ceil(manDays) - 1;
    if (daysToSubtract <= 0) {
        return endDate;
    }
    return subtractBusinessDays(endDate, daysToSubtract, excludeWeekends);
}

// 営業日を減算（土日を除外）
function subtractBusinessDays(endDate, days, excludeWeekends) {
    const date = new Date(endDate);
    let subtractedDays = 0;

    while (subtractedDays < days) {
        date.setDate(date.getDate() - 1);
        if (!excludeWeekends || (date.getDay() !== 0 && date.getDay() !== 6)) {
            subtractedDays++;
        }
    }

    return date;
}

// 日付を文字列に変換
function formatDateString(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = ('0' + (d.getMonth() + 1)).slice(-2);
    const day = ('0' + d.getDate()).slice(-2);
    return `${year}-${month}-${day}`;
}

// 対象タスクを取得（範囲指定・完了除外）
function getTargetTasks() {
    if (!taskListApp) return [];

    const tasks = taskListApp.dataManager.getTasks();
    const startTaskId = document.getElementById('bulk-start-task').value;
    const endTaskId = document.getElementById('bulk-end-task').value;

    let targetTasks = [];
    let started = false;
    let ended = false;

    for (let i = 0; i < tasks.length; i++) {
        const task = tasks[i];

        // 開始タスクを見つけたらフラグを立てる
        if (task.id == startTaskId) {
            started = true;
        }

        if (started && !ended) {
            // 完了タスクは除外
            if (task.status !== 'completed') {
                targetTasks.push({
                    originalIndex: i,
                    task: task
                });
            }
        }

        // 終了タスクを見つけたら終了
        if (endTaskId && task.id == endTaskId) {
            ended = true;
        }
    }

    return targetTasks;
}

// タスクをソート（メンバー → 工程 → タスク順）
function sortTasksForScheduling(targetTasks) {
    return targetTasks.sort((a, b) => {
        // 1. 担当者でソート
        const assigneeA = a.task.assignee_name || '';
        const assigneeB = b.task.assignee_name || '';
        if (assigneeA !== assigneeB) {
            return assigneeA.localeCompare(assigneeB, 'ja');
        }

        // 2. 工程でソート
        const processA = processOrder[a.task.process_id] || 99;
        const processB = processOrder[b.task.process_id] || 99;
        if (processA !== processB) {
            return processA - processB;
        }

        // 3. 元の順序を維持
        return a.originalIndex - b.originalIndex;
    });
}

// DOM入力フィールドから現在の日付値を取得する関数
function getDateFromDom(taskId, fieldName) {
    const taskIdStr = String(taskId);

    // 方法1: 全ての該当フィールドの入力を検索してタスクIDで絞り込む
    const allInputs = document.querySelectorAll(`input[data-field="${fieldName}"]`);
    for (const input of allInputs) {
        if (String(input.dataset.taskId) === taskIdStr) {
            console.log(`[getDateFromDom] 方法1で発見 - taskId: ${taskIdStr}, field: ${fieldName}, value: "${input.value}"`);
            if (input.value) {
                return input.value;
            }
        }
    }

    // 方法2: 対象の行を探してから入力を探す
    const row = document.querySelector(`tr[data-task-id="${taskIdStr}"]`);
    if (row) {
        const input = row.querySelector(`input[data-field="${fieldName}"]`);
        if (input) {
            console.log(`[getDateFromDom] 方法2で発見 - taskId: ${taskIdStr}, field: ${fieldName}, value: "${input.value}"`);
            if (input.value) {
                return input.value;
            }
        }
    }

    console.log(`[getDateFromDom] 見つからず - taskId: ${taskIdStr}, field: ${fieldName}`);
    return null;
}

// 全ての日付入力値をDataManagerに同期
function syncAllDateValuesToDataManager() {
    if (!taskListApp || !taskListApp.dataManager) return;

    const dateFields = ['planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date'];
    const rows = document.querySelectorAll('#task-tbody .task-row');

    rows.forEach(row => {
        dateFields.forEach(fieldName => {
            const input = row.querySelector(`input[data-field="${fieldName}"]`);
            if (input && input.value) {
                const inputTaskId = input.dataset.taskId;
                if (inputTaskId) {
                    taskListApp.dataManager.updateTaskField(inputTaskId, fieldName, input.value);
                }
            }
        });
    });
}

// プレビュー
function previewBulkDate() {
    const startTaskId = document.getElementById('bulk-start-task').value;
    if (!startTaskId || !taskListApp) {
        alert('日付を編集したタスクがありません。');
        return;
    }

    // 最新の日付値をDataManagerに同期
    syncAllDateValuesToDataManager();

    // 編集されたフィールドの日付を基準日として使用
    const baseField = dateEditedField || 'planned_start_date';
    const baseFieldLabel = baseField === 'planned_end_date' ? '終了日' : '開始日';

    // DOM入力フィールドから変更後の日付を取得（編集中の値を優先）
    let baseDate = getDateFromDom(startTaskId, baseField);
    if (!baseDate) {
        // DOMに入力がない場合はdataManagerから取得
        const startTask = taskListApp.dataManager.getTaskById(startTaskId);
        baseDate = startTask?.[baseField];
    }
    if (!baseDate) {
        alert(`開始タスクの${baseFieldLabel}が設定されていません。`);
        return;
    }

    const targetTasks = getTargetTasks();
    if (targetTasks.length === 0) {
        alert('対象となるタスクがありません（完了タスクは除外されます）。');
        return;
    }

    const sortedTasks = sortTasksForScheduling(targetTasks);

    let previewHtml = `<p class="mb-2">対象: ${sortedTasks.length}件のタスク（メンバー→工程→タスク順）</p>`;
    previewHtml += `<p class="mb-2 text-amber-700">基準日（${baseFieldLabel}）: ${baseDate}</p>`;
    previewHtml += '<ul class="space-y-1">';

    let currentAssignee = null;
    sortedTasks.forEach(item => {
        const isNewAssignee = item.task.assignee_name !== currentAssignee;
        if (isNewAssignee) {
            currentAssignee = item.task.assignee_name;
            previewHtml += `<li class="font-medium text-blue-700 mt-2">【${currentAssignee || '未割当'}】</li>`;
        }
        previewHtml += `<li class="ml-4">・${item.task.task_name} (工数: ${item.task.planned_man_days || 0}日)</li>`;

        // サブタスクも表示
        if (item.task.subtasks && item.task.subtasks.length > 0) {
            const nonCompletedSubtasks = item.task.subtasks.filter(st => st.status !== 'completed');
            if (nonCompletedSubtasks.length > 0) {
                nonCompletedSubtasks.forEach(st => {
                    previewHtml += `<li class="ml-8 text-slate-500">└ ${st.task_name} (${st.assignee_name || '未割当'}, 工数: ${st.planned_man_days || 0}日)</li>`;
                });
            }
        }
    });

    previewHtml += '</ul>';

    document.getElementById('bulk-date-preview-content').innerHTML = previewHtml;
    document.getElementById('bulk-date-preview').classList.remove('hidden');
}

// 一括日付更新実行
function executeBulkDateUpdate() {
    const startTaskId = document.getElementById('bulk-start-task').value;
    if (!startTaskId || !taskListApp) {
        alert('日付を編集したタスクがありません。');
        return;
    }

    // 最新の日付値をDataManagerに同期
    syncAllDateValuesToDataManager();

    // 編集されたフィールドの日付を基準日として使用
    const baseField = dateEditedField || 'planned_start_date';
    const baseFieldLabel = baseField === 'planned_end_date' ? '終了日' : '開始日';

    // DOM入力フィールドから変更後の日付を取得（編集中の値を優先）
    let baseDate = getDateFromDom(startTaskId, baseField);
    console.log(`[executeBulkDateUpdate] 基準フィールド: ${baseField}`);
    console.log(`[executeBulkDateUpdate] DOM ${baseFieldLabel}:`, baseDate);

    if (!baseDate) {
        // DOMに入力がない場合はdataManagerから取得
        const startTask = taskListApp.dataManager.getTaskById(startTaskId);
        baseDate = startTask?.[baseField];
        console.log(`[executeBulkDateUpdate] DataManager ${baseFieldLabel}:`, baseDate);
    }
    if (!baseDate) {
        alert(`開始タスクの${baseFieldLabel}が設定されていません。`);
        return;
    }

    console.log('[executeBulkDateUpdate] 使用する基準日:', baseDate);

    const excludeWeekends = document.getElementById('bulk-exclude-weekends').checked;
    const targetTasks = getTargetTasks();

    if (targetTasks.length === 0) {
        alert('対象となるタスクがありません（完了タスクは除外されます）。');
        return;
    }

    const sortedTasks = sortTasksForScheduling(targetTasks);

    // メンバーごとの現在の日付を管理
    let memberCurrentDate = {};
    const baseDateObj = adjustToBusinessDay(new Date(baseDate), excludeWeekends);
    const isEndDateBase = (baseField === 'planned_end_date');
    let isFirstTask = true;

    sortedTasks.forEach(item => {
        const task = item.task;
        const assignee = task.assignee_id || '_unassigned_';

        // メンバーが変わったら基準日にリセット
        if (!memberCurrentDate[assignee]) {
            // 終了日基準の場合、最初のタスクは終了日を固定し、翌日から次のタスクを開始
            if (isEndDateBase && isFirstTask) {
                // 最初のタスク：基準日を終了日として使用し、開始日を逆算
                const endDate = new Date(baseDateObj);
                const startDate = calculateStartDateFromEnd(endDate, task.planned_man_days, excludeWeekends);

                taskListApp.dataManager.updateTask(task.id, {
                    planned_start_date: formatDateString(startDate),
                    planned_end_date: formatDateString(endDate)
                });

                // 次のタスクは終了日の翌日から開始
                const nextDate = new Date(endDate);
                nextDate.setDate(nextDate.getDate() + 1);
                memberCurrentDate[assignee] = nextDate;
                isFirstTask = false;

                // ハイライト対象に追加
                dateEditedRows.add(`task-${task.id}`);
                return; // このタスクの処理は完了
            } else {
                memberCurrentDate[assignee] = new Date(baseDateObj);
            }
        }

        let currentDate = memberCurrentDate[assignee];
        currentDate = adjustToBusinessDay(currentDate, excludeWeekends);

        // サブタスクがある場合
        if (task.subtasks && task.subtasks.length > 0) {
            const nonCompletedSubtasks = task.subtasks.filter(st => st.status !== 'completed');

            if (nonCompletedSubtasks.length > 0) {
                // 親タスクの開始日を設定
                taskListApp.dataManager.updateTask(task.id, { planned_start_date: formatDateString(currentDate) });

                // サブタスク用のメンバーごとの日付管理
                let subtaskMemberDate = {};
                let maxEndDate = currentDate;

                nonCompletedSubtasks.forEach(subtask => {
                    const stAssignee = subtask.assignee_id || '_unassigned_';

                    if (!subtaskMemberDate[stAssignee]) {
                        subtaskMemberDate[stAssignee] = new Date(currentDate);
                    }

                    let stCurrentDate = subtaskMemberDate[stAssignee];
                    stCurrentDate = adjustToBusinessDay(stCurrentDate, excludeWeekends);

                    // サブタスクの開始日・終了日を設定
                    const stEndDate = calculateEndDate(stCurrentDate, subtask.planned_man_days, excludeWeekends);
                    taskListApp.dataManager.updateSubtask(task.id, subtask.id, {
                        planned_start_date: formatDateString(stCurrentDate),
                        planned_end_date: formatDateString(stEndDate)
                    });

                    // 次のサブタスク用に日付を更新
                    const nextDate = new Date(stEndDate);
                    nextDate.setDate(nextDate.getDate() + 1);
                    subtaskMemberDate[stAssignee] = nextDate;

                    // 最大終了日を更新
                    if (stEndDate > maxEndDate) {
                        maxEndDate = stEndDate;
                    }
                });

                // 親タスクの終了日をサブタスクの最終日に合わせる
                taskListApp.dataManager.updateTask(task.id, { planned_end_date: formatDateString(maxEndDate) });

                // 次のタスク用に日付を更新
                const nextDate = new Date(maxEndDate);
                nextDate.setDate(nextDate.getDate() + 1);
                memberCurrentDate[assignee] = nextDate;

            } else {
                // 完了していないサブタスクがない場合は親タスクの工数で計算
                const endDate = calculateEndDate(currentDate, task.planned_man_days, excludeWeekends);
                taskListApp.dataManager.updateTask(task.id, {
                    planned_start_date: formatDateString(currentDate),
                    planned_end_date: formatDateString(endDate)
                });

                const nextDate = new Date(endDate);
                nextDate.setDate(nextDate.getDate() + 1);
                memberCurrentDate[assignee] = nextDate;
            }
        } else {
            // サブタスクがない場合
            const endDate = calculateEndDate(currentDate, task.planned_man_days, excludeWeekends);
            taskListApp.dataManager.updateTask(task.id, {
                planned_start_date: formatDateString(currentDate),
                planned_end_date: formatDateString(endDate)
            });

            const nextDate = new Date(endDate);
            nextDate.setDate(nextDate.getDate() + 1);
            memberCurrentDate[assignee] = nextDate;
        }

        // ハイライト対象に追加
        dateEditedRows.add(`task-${task.id}`);
        if (task.subtasks) {
            task.subtasks.forEach(st => {
                if (st.status !== 'completed') {
                    dateEditedRows.add(`subtask-${st.id}`);
                }
            });
        }
    });

    // テーブルを再描画
    taskListApp.tableRenderer.render(taskListApp.dataManager.getTasks());
    applyDateEditHighlight();

    closeBulkDateModal();
    showToast(`${sortedTasks.length}件のタスクの日付を更新しました`, 'success');
}

// グローバルに公開
window.onDateEdited = onDateEdited;
window.clearDateEditState = clearDateEditState;
</script>
<?= $this->endSection() ?>
