<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    /* ガントチャートセル - 1日あたり40px */
    .gantt-cell {
        min-width: 40px;
        width: 40px;
        height: 32px;
    }
    /* タスク行の高さ - ガントと同じ44px */
    .task-row {
        height: 44px;
        min-height: 44px;
    }
    .task-bar {
        height: 28px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .task-bar span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: calc(100% - 20px);
    }
    /* 編集モード時のみホバー効果を有効化 */
    body.edit-mode .task-bar {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: grab;
    }
    body.edit-mode .task-bar:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    /* 表示モード時はホバー効果なし */
    body:not(.edit-mode) .task-bar {
        cursor: default;
    }
    .scroll-sync {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }
    .scroll-sync::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    .scroll-sync::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .scroll-sync::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.6);
        border-radius: 5px;
    }
    .scroll-sync::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.8);
    }
    .weekend-sat {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .weekend-sun {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    .today-marker {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        font-weight: 700;
        color: #92400e;
    }
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
    }
    .toggle-btn-item.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .toggle-btn-item:not(.active):hover {
        background: #f8fafc;
    }
    /* タスク名リンク */
    .task-name-link {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #334155;
        text-decoration: none;
        position: relative;
    }
    .task-name-link::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 0;
        height: 1px;
        background: #3b82f6;
        transition: width 0.2s ease;
    }
    .task-name-link:hover {
        color: #2563eb;
    }
    .task-name-link:hover::after {
        width: 100%;
    }
    .task-name-link i {
        font-size: 10px;
        margin-left: 4px;
        opacity: 0;
        transform: translateX(-4px);
        transition: all 0.15s ease;
        color: #3b82f6;
    }
    .task-name-link:hover i {
        opacity: 1;
        transform: translateX(0);
    }
    .task-name-link.parent {
        font-weight: 600;
        color: #1e293b;
    }
    .task-name-link.parent:hover {
        color: #1d4ed8;
    }
    /* 月ヘッダー行の高さ */
    .month-header-row {
        height: 32px;
    }
    .date-header-row {
        height: 32px;
    }
    .weekday-header-row {
        height: 24px;
    }
    /* 遅れ日数表示 */
    .delay-badge {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .delay-late {
        background: #fef2f2;
        color: #dc2626;
    }
    .delay-ahead {
        background: #f0fdf4;
        color: #16a34a;
    }
    .delay-ontime {
        background: #f8fafc;
        color: #64748b;
    }
    /* 検索パネル */
    .search-panel {
        background: white;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 16px;
        display: none;
    }
    .search-panel.show {
        display: block;
    }
    /* タスクパネルの縦スクロールバー非表示 */
    #task-panel {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    #task-panel::-webkit-scrollbar {
        display: none;
    }
    /* 変更された行の背景色 */
    .task-row-modified {
        background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%) !important;
    }
    .task-row-modified:hover {
        background: linear-gradient(135deg, #fef08a 0%, #fde047 100%) !important;
    }
    /* タスク行アイテム */
    .task-row-item {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.15s;
    }
    .task-row-item:hover {
        background-color: #f8fafc;
    }
    .task-row-item.subtask {
        background-color: #fafbfc;
    }
    /* ステータスバッジ */
    .status-not-started { background: #f1f5f9; color: #64748b; }
    .status-in-progress { background: #dbeafe; color: #2563eb; }
    .status-completed { background: #d1fae5; color: #059669; }
    .status-on-hold { background: #fef3c7; color: #d97706; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden">
    <!-- サブヘッダー -->
    <div class="bg-white border-b border-slate-200 px-6 py-3 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- プロジェクト選択（顧客別グループ） -->
                <form id="project-form" method="get" action="<?= base_url('schedule') ?>">
                    <select id="project-select" name="project_id" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" style="min-width: 280px;" onchange="this.form.submit()">
                        <option value="all" <?= (empty($projectId) || $projectId === 'all') ? 'selected' : '' ?>>📊 全プロジェクト（横断表示）</option>
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
                    <a href="<?= base_url('schedule' . ($projectId ? '?project_id=' . $projectId : '')) ?>" class="toggle-btn-item active">
                        <i class="fas fa-chart-gantt mr-1"></i>ガントチャート
                    </a>
                    <a href="<?= base_url('schedule/tasks' . ($projectId ? '?project_id=' . $projectId : '')) ?>" class="toggle-btn-item">
                        <i class="fas fa-list mr-1"></i>タスク
                    </a>
                </div>

                <!-- 予定/実績切り替え -->
                <div class="toggle-btn-group">
                    <button id="btn-plan" class="toggle-btn-item active" onclick="switchMode('plan')">予定</button>
                    <button id="btn-actual" class="toggle-btn-item" onclick="switchMode('actual')">実績</button>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- 年選択 -->
                <select id="year-select" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" onchange="updateCalendar()">
                    <?php for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++): ?>
                        <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?>年</option>
                    <?php endfor; ?>
                </select>
                <!-- 月選択 -->
                <select id="month-select" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" onchange="scrollToSelectedMonth()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= $m ?>月</option>
                    <?php endfor; ?>
                </select>

                <!-- 今日ボタン -->
                <button onclick="scrollToToday()" class="px-4 py-1.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white">
                    今日
                </button>

                <!-- 絞込ボタン -->
                <button id="search-toggle" onclick="toggleSearchPanel()" class="px-4 py-1.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white flex items-center">
                    <i class="fas fa-filter mr-2"></i>絞込
                </button>

                <!-- 表示/編集モード切り替え -->
                <div class="toggle-btn-group">
                    <button id="btn-view-mode" class="toggle-btn-item active" onclick="switchEditMode('view')">
                        <i class="fas fa-eye mr-1"></i>表示
                    </button>
                    <button id="btn-edit-mode" class="toggle-btn-item" onclick="switchEditMode('edit')">
                        <i class="fas fa-edit mr-1"></i>編集
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 検索パネル -->
    <div id="search-panel" class="search-panel">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-2">
                <label class="text-sm text-slate-600 font-medium">担当者:</label>
                <select id="filter-assignee" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white" onchange="applyFilter()">
                    <option value="">すべて</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['id'] ?>"><?= esc($member['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
            <div class="flex items-center space-x-2">
                <label class="text-sm text-slate-600 font-medium">工程:</label>
                <select id="filter-process" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white" onchange="applyFilter()">
                    <option value="">すべて</option>
                    <?php foreach ($processes as $process): ?>
                        <option value="<?= $process['id'] ?>"><?= esc($process['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button onclick="clearFilter()" class="px-4 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-600 hover:bg-slate-50">クリア</button>
        </div>
    </div>

    <!-- WBS/ガントチャートエリア -->
    <main class="flex-1 overflow-hidden bg-white flex flex-col">
        <!-- ヘッダー固定部分 -->
        <div class="flex flex-shrink-0 border-b-2 border-slate-300">
            <!-- 左側ヘッダー -->
            <div class="flex-shrink-0 border-r-2 border-slate-300 bg-slate-100" style="width: 760px; min-width: 760px;">
                <!-- 月ヘッダー行 -->
                <div class="flex month-header-row items-center border-b border-slate-200 bg-slate-200">
                    <div class="flex-1 px-3 text-sm font-bold text-slate-600 text-center">タスク一覧</div>
                </div>
                <!-- 曜日ヘッダー行 -->
                <div class="flex weekday-header-row items-center border-b border-slate-200 bg-slate-100">
                    <div class="flex-1"></div>
                </div>
                <!-- カラムヘッダー（予定モード） -->
                <div id="header-plan" class="flex date-header-row items-center bg-white">
                    <div class="w-8 px-1 text-center border-r border-slate-200">
                        <input type="checkbox" id="select-all" class="rounded border-slate-300 cursor-pointer" title="全選択">
                    </div>
                    <div class="w-14 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">No</div>
                    <div class="flex-1 px-2 text-xs font-bold text-slate-700 border-r border-slate-200">タスク名</div>
                    <div class="w-16 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">担当者</div>
                    <div class="w-12 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">工数</div>
                    <div class="w-16 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">原価</div>
                    <div class="w-20 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">開始日</div>
                    <div class="w-20 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">終了日</div>
                    <div class="w-12 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">進捗</div>
                    <div class="w-14 px-1 text-xs font-bold text-slate-700 text-center">遅延</div>
                </div>
                <!-- カラムヘッダー（実績モード） -->
                <div id="header-actual" class="flex date-header-row items-center bg-white hidden">
                    <div class="w-8 px-1 text-center border-r border-slate-200">
                        <input type="checkbox" id="select-all-actual" class="rounded border-slate-300 cursor-pointer" title="全選択">
                    </div>
                    <div class="w-14 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">No</div>
                    <div class="flex-1 px-2 text-xs font-bold text-slate-700 border-r border-slate-200">タスク名</div>
                    <div class="w-16 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">担当者</div>
                    <div class="w-14 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">実工数</div>
                    <div class="w-16 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">出来高</div>
                    <div class="w-20 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">実開始</div>
                    <div class="w-20 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">実終了</div>
                    <div class="w-12 px-1 text-xs font-bold text-slate-700 text-center border-r border-slate-200">進捗</div>
                    <div class="w-14 px-1 text-xs font-bold text-slate-700 text-center">遅延</div>
                </div>
            </div>
            <!-- 右側ヘッダー（カレンダー） -->
            <div class="flex-1 overflow-hidden bg-white" id="gantt-header-container">
                <div class="overflow-x-auto scroll-sync" id="gantt-header-scroll" style="overflow-y: hidden;">
                    <div style="min-width: max-content;">
                        <!-- 月ヘッダー -->
                        <div class="flex month-header-row" id="month-header"></div>
                        <!-- 曜日ヘッダー -->
                        <div class="flex weekday-header-row bg-slate-50" id="weekday-header"></div>
                        <!-- 日付行 -->
                        <div class="flex date-header-row bg-white" id="date-header"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 本体スクロール部分 -->
        <div class="flex-1 flex overflow-hidden">
            <!-- 左側：タスク一覧（固定） -->
            <div class="flex-shrink-0 border-r-2 border-slate-300 bg-white overflow-y-auto scroll-sync" style="width: 760px; min-width: 760px;" id="task-panel">
                <div id="task-list">
                    <!-- JavaScriptで動的生成 -->
                </div>
            </div>

            <!-- 右側：ガントチャート（横スクロール可能） -->
            <div class="flex-1 overflow-auto scroll-sync" id="gantt-scroll">
                <div class="relative" id="gantt-body" style="min-width: max-content;">
                    <!-- JavaScriptで生成 -->
                </div>
            </div>
        </div>
    </main>

    <!-- フッター -->
    <footer class="bg-white border-t border-slate-200 px-6 py-3 flex-shrink-0">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-tasks mr-1"></i>全タスク: <strong class="text-slate-700" id="stat-total"><?= $taskStats['total'] ?? 0 ?></strong>件</span>
                <span><i class="fas fa-check-circle mr-1 text-emerald-500"></i>完了: <strong class="text-emerald-600" id="stat-completed"><?= $taskStats['completed'] ?? 0 ?></strong>件</span>
                <span><i class="fas fa-spinner mr-1 text-blue-500"></i>進行中: <strong class="text-blue-600" id="stat-in-progress"><?= $taskStats['in_progress'] ?? 0 ?></strong>件</span>
                <span><i class="fas fa-clock mr-1 text-slate-400"></i>未着手: <strong class="text-slate-600" id="stat-not-started"><?= $taskStats['not_started'] ?? 0 ?></strong>件</span>
            </div>
            <div class="flex items-center space-x-3">
                <!-- モードインジケーター -->
                <span id="view-mode-indicator" class="bg-slate-500 text-white px-3 py-1 rounded text-xs font-medium">
                    <i class="fas fa-eye mr-1"></i>表示モード
                </span>
                <span id="edit-mode-indicator" class="hidden bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">
                    <i class="fas fa-edit mr-1"></i>編集モード
                </span>
                <!-- 編集モード時のボタン群 -->
                <button id="cancel-changes-btn" class="hidden px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-all" onclick="cancelAllChanges()">
                    <i class="fas fa-times mr-1"></i>キャンセル
                </button>
                <button id="save-changes-btn" class="hidden px-4 py-1.5 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-lg text-xs font-semibold hover:from-rose-600 hover:to-red-700 shadow-lg transition-all" onclick="saveAllChanges()">
                    <i class="fas fa-save mr-1"></i>変更を登録
                </button>
            </div>
        </div>
    </footer>
</div>

<!-- タスク編集モーダル -->
<?= $this->include('schedule/partials/task_modal') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// グローバル変数
const projectId = <?= json_encode($projectId) ?>;
const isAllProjects = <?= json_encode($isAllProjects ?? false) ?>;
const tasks = <?= json_encode($tasks) ?>;
const tasksGrouped = <?= json_encode($tasksGrouped ?? []) ?>;
const processes = <?= json_encode($processes) ?>;
const members = <?= json_encode($members) ?>;
let currentMode = 'plan';
let isEditMode = false;
let modifiedTasks = new Map();
let ganttStartDate, ganttEndDate;

// 初期化
document.addEventListener('DOMContentLoaded', function() {
    initGanttChart();
    initScrollSync();
});

// ガントチャート初期化
function initGanttChart() {
    calculateDateRange();
    renderCalendarHeader();
    renderTasks();
    scrollToToday();
}

// 日付範囲を計算
function calculateDateRange() {
    const today = new Date();
    let minDate = new Date(today);
    let maxDate = new Date(today);
    minDate.setMonth(minDate.getMonth() - 1);
    maxDate.setMonth(maxDate.getMonth() + 3);

    tasks.forEach(task => {
        if (task.planned_start_date) {
            const d = new Date(task.planned_start_date);
            if (d < minDate) minDate = new Date(d);
        }
        if (task.planned_end_date) {
            const d = new Date(task.planned_end_date);
            if (d > maxDate) maxDate = new Date(d);
        }
        if (task.subtasks) {
            task.subtasks.forEach(sub => {
                if (sub.planned_start_date) {
                    const d = new Date(sub.planned_start_date);
                    if (d < minDate) minDate = new Date(d);
                }
                if (sub.planned_end_date) {
                    const d = new Date(sub.planned_end_date);
                    if (d > maxDate) maxDate = new Date(d);
                }
            });
        }
    });

    // 月初め・月末に調整
    minDate.setDate(1);
    maxDate.setMonth(maxDate.getMonth() + 1, 0);

    ganttStartDate = minDate;
    ganttEndDate = maxDate;
}

// カレンダーヘッダー描画
function renderCalendarHeader() {
    const monthHeader = document.getElementById('month-header');
    const weekdayHeader = document.getElementById('weekday-header');
    const dateHeader = document.getElementById('date-header');
    if (!monthHeader) return;

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    let monthHtml = '';
    let weekdayHtml = '';
    let dateHtml = '';
    let currentDate = new Date(ganttStartDate);
    let currentMonth = -1;
    let monthDays = 0;

    while (currentDate <= ganttEndDate) {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        const day = currentDate.getDate();
        const dayOfWeek = currentDate.getDay();
        const isToday = currentDate.getTime() === today.getTime();
        const isSat = dayOfWeek === 6;
        const isSun = dayOfWeek === 0;

        // 月が変わったら前の月のヘッダーを出力
        if (month !== currentMonth) {
            if (currentMonth !== -1) {
                monthHtml += `<div class="gantt-cell flex items-center justify-center font-semibold text-xs text-slate-600 bg-slate-200 border-r border-slate-300" style="width: ${monthDays * 40}px;">${currentMonth + 1}月</div>`;
            }
            currentMonth = month;
            monthDays = 0;
        }

        // 曜日・日付セル
        let cellClass = 'gantt-cell flex items-center justify-center border-r border-slate-200';
        if (isToday) cellClass += ' today-marker';
        else if (isSun) cellClass += ' weekend-sun';
        else if (isSat) cellClass += ' weekend-sat';

        weekdayHtml += `<div class="${cellClass} text-xs ${isSun ? 'text-red-500' : isSat ? 'text-blue-500' : 'text-slate-500'}">${weekdays[dayOfWeek]}</div>`;
        dateHtml += `<div class="${cellClass} text-xs font-medium text-slate-700" data-date="${formatDate(currentDate)}">${day}</div>`;

        monthDays++;
        currentDate.setDate(currentDate.getDate() + 1);
    }

    // 最後の月
    if (monthDays > 0) {
        monthHtml += `<div class="gantt-cell flex items-center justify-center font-semibold text-xs text-slate-600 bg-slate-200 border-r border-slate-300" style="width: ${monthDays * 40}px;">${currentMonth + 1}月</div>`;
    }

    monthHeader.innerHTML = monthHtml;
    weekdayHeader.innerHTML = weekdayHtml;
    dateHeader.innerHTML = dateHtml;
}

// タスク一覧描画
function renderTasks() {
    const taskPanel = document.getElementById('task-list');
    const ganttBody = document.getElementById('gantt-body');
    if (!taskPanel) return;

    let taskHtml = '';
    let ganttHtml = '';
    let rowIndex = 0;

    if (isAllProjects && tasksGrouped.length > 0) {
        // 全プロジェクト横断表示：プロジェクト別にグループ化
        tasksGrouped.forEach(group => {
            // プロジェクトヘッダー行
            taskHtml += renderProjectHeaderRow(group, rowIndex);
            ganttHtml += renderProjectHeaderGanttRow(rowIndex);
            rowIndex++;

            // 各プロジェクトのタスク
            group.tasks.forEach(task => {
                taskHtml += renderTaskRow(task, rowIndex, false);
                ganttHtml += renderGanttRow(task, rowIndex);
                rowIndex++;

                // サブタスク
                if (task.subtasks && task.subtasks.length > 0) {
                    task.subtasks.forEach(subtask => {
                        taskHtml += renderTaskRow(subtask, rowIndex, true);
                        ganttHtml += renderGanttRow(subtask, rowIndex);
                        rowIndex++;
                    });
                }
            });
        });
    } else {
        // 単一プロジェクト表示
        tasks.forEach((task, taskIndex) => {
            taskHtml += renderTaskRow(task, rowIndex, false);
            ganttHtml += renderGanttRow(task, rowIndex);
            rowIndex++;

            if (task.subtasks && task.subtasks.length > 0) {
                task.subtasks.forEach(subtask => {
                    taskHtml += renderTaskRow(subtask, rowIndex, true);
                    ganttHtml += renderGanttRow(subtask, rowIndex);
                    rowIndex++;
                });
            }
        });
    }

    taskPanel.innerHTML = taskHtml;
    ganttBody.innerHTML = ganttHtml;

    // ガントボディの幅設定
    const dayCount = Math.ceil((ganttEndDate - ganttStartDate) / (1000 * 60 * 60 * 24)) + 1;
    ganttBody.style.width = (dayCount * 40) + 'px';
}

// プロジェクトヘッダー行描画
function renderProjectHeaderRow(group, rowIndex) {
    return `
        <div class="task-row flex items-center bg-gradient-to-r from-slate-700 to-slate-800 text-white" data-row="${rowIndex}">
            <div class="w-8 px-1 text-center border-r border-slate-600"></div>
            <div class="w-14 px-1 text-center border-r border-slate-600"></div>
            <div class="flex-1 px-3 text-sm font-semibold border-r border-slate-600">
                <i class="fas fa-folder mr-2 text-blue-400"></i>${esc(group.project_name)}
                <span class="ml-2 text-xs text-slate-400">（${esc(group.customer_name)}）</span>
            </div>
            <div class="w-16 border-r border-slate-600"></div>
            <div class="w-12 border-r border-slate-600"></div>
            <div class="w-16 border-r border-slate-600"></div>
            <div class="w-20 border-r border-slate-600"></div>
            <div class="w-20 border-r border-slate-600"></div>
            <div class="w-12 border-r border-slate-600"></div>
            <div class="w-14"></div>
        </div>
    `;
}

// プロジェクトヘッダーのガント行描画
function renderProjectHeaderGanttRow(rowIndex) {
    const dayCount = Math.ceil((ganttEndDate - ganttStartDate) / (1000 * 60 * 60 * 24)) + 1;
    let cellsHtml = '';
    for (let i = 0; i < dayCount; i++) {
        cellsHtml += `<div class="gantt-cell bg-slate-700 border-r border-slate-600"></div>`;
    }
    return `<div class="task-row flex" data-row="${rowIndex}">${cellsHtml}</div>`;
}

// タスク行描画
function renderTaskRow(task, rowIndex, isSubtask) {
    const startDate = currentMode === 'plan' ? task.planned_start_date : task.actual_start_date;
    const endDate = currentMode === 'plan' ? task.planned_end_date : task.actual_end_date;
    const manDays = currentMode === 'plan' ? task.planned_man_days : task.actual_man_days;
    const cost = currentMode === 'plan' ? task.planned_cost : task.actual_cost;
    const delayDays = task.delay_days || 0;
    const indent = isSubtask ? 'pl-6' : '';
    const rowClass = isSubtask ? 'subtask' : '';

    let delayBadge = '';
    if (delayDays > 0) {
        delayBadge = `<span class="delay-badge delay-late">+${delayDays}日</span>`;
    } else if (delayDays < 0) {
        delayBadge = `<span class="delay-badge delay-ahead">${delayDays}日</span>`;
    } else {
        delayBadge = `<span class="delay-badge delay-ontime">0日</span>`;
    }

    return `
        <div class="task-row task-row-item ${rowClass}" data-task-id="${task.id}" data-row="${rowIndex}">
            <div class="w-8 px-1 text-center border-r border-slate-200 flex items-center justify-center">
                <input type="checkbox" class="task-checkbox rounded border-slate-300" data-task-id="${task.id}">
            </div>
            <div class="w-14 px-1 text-xs text-slate-500 text-center border-r border-slate-200 flex items-center justify-center">${rowIndex + 1}</div>
            <div class="flex-1 px-2 text-sm border-r border-slate-200 flex items-center ${indent}">
                <a href="#" onclick="openTaskModal(${task.id}); return false;" class="task-name-link ${isSubtask ? '' : 'parent'}">
                    ${esc(task.task_name)}<i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <div class="w-16 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center">${esc(task.assignee_name || '-')}</div>
            <div class="w-12 px-1 text-xs text-right border-r border-slate-200 flex items-center justify-end">${manDays || '-'}</div>
            <div class="w-16 px-1 text-xs text-right border-r border-slate-200 flex items-center justify-end">${cost ? '¥' + Number(cost).toLocaleString() : '-'}</div>
            <div class="w-20 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center">${formatDisplayDate(startDate)}</div>
            <div class="w-20 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center">${formatDisplayDate(endDate)}</div>
            <div class="w-12 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center">${task.progress || 0}%</div>
            <div class="w-14 px-1 text-xs text-center flex items-center justify-center">${delayBadge}</div>
        </div>
    `;
}

// ガント行描画
function renderGanttRow(task, rowIndex) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const dayCount = Math.ceil((ganttEndDate - ganttStartDate) / (1000 * 60 * 60 * 24)) + 1;

    let cellsHtml = '';
    let currentDate = new Date(ganttStartDate);

    for (let i = 0; i < dayCount; i++) {
        const dayOfWeek = currentDate.getDay();
        const isToday = currentDate.getTime() === today.getTime();
        const isSat = dayOfWeek === 6;
        const isSun = dayOfWeek === 0;

        let cellClass = 'gantt-cell border-r';
        if (isToday) cellClass += ' today-marker';
        else if (isSun) cellClass += ' weekend-sun';
        else if (isSat) cellClass += ' weekend-sat';
        else cellClass += ' border-slate-100';

        cellsHtml += `<div class="${cellClass}"></div>`;
        currentDate.setDate(currentDate.getDate() + 1);
    }

    // タスクバー描画
    let barHtml = '';
    const startDate = currentMode === 'plan' ? task.planned_start_date : task.actual_start_date;
    const endDate = currentMode === 'plan' ? task.planned_end_date : task.actual_end_date;

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const startOffset = Math.floor((start - ganttStartDate) / (1000 * 60 * 60 * 24));
        const duration = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;

        const progress = task.progress || 0;
        const isDelayed = task.delay_days > 0;
        const barColor = isDelayed ? 'from-red-400 to-red-600' : (currentMode === 'plan' ? 'from-blue-400 to-blue-600' : 'from-emerald-400 to-emerald-600');

        barHtml = `
            <div class="task-bar bg-gradient-to-r ${barColor} absolute flex items-center px-2 text-white text-xs font-medium cursor-pointer hover:shadow-lg transition-shadow"
                 style="left: ${startOffset * 40 + 4}px; width: ${duration * 40 - 8}px; top: 8px;"
                 data-task-id="${task.id}"
                 onclick="openTaskModal(${task.id})">
                ${progress > 0 ? `<span>${progress}%</span>` : ''}
                ${duration > 2 ? `<span class="ml-1 truncate">${esc(task.task_name)}</span>` : ''}
            </div>
        `;
    }

    return `
        <div class="task-row flex relative" data-task-id="${task.id}" data-row="${rowIndex}">
            ${cellsHtml}
            ${barHtml}
        </div>
    `;
}

// スクロール同期
function initScrollSync() {
    const taskPanel = document.getElementById('task-panel');
    const ganttScroll = document.getElementById('gantt-scroll');
    const ganttHeaderScroll = document.getElementById('gantt-header-scroll');

    if (taskPanel && ganttScroll) {
        // 縦スクロール同期
        taskPanel.addEventListener('scroll', function() {
            ganttScroll.scrollTop = taskPanel.scrollTop;
        });
        ganttScroll.addEventListener('scroll', function() {
            taskPanel.scrollTop = ganttScroll.scrollTop;
            // 横スクロール同期（ヘッダー）
            if (ganttHeaderScroll) {
                ganttHeaderScroll.scrollLeft = ganttScroll.scrollLeft;
            }
        });
    }

    if (ganttHeaderScroll && ganttScroll) {
        ganttHeaderScroll.addEventListener('scroll', function() {
            ganttScroll.scrollLeft = ganttHeaderScroll.scrollLeft;
        });
    }
}

// 今日にスクロール
function scrollToToday() {
    const todayCell = document.querySelector('[data-date="' + formatDate(new Date()) + '"]');
    if (todayCell) {
        const ganttScroll = document.getElementById('gantt-scroll');
        if (ganttScroll) {
            ganttScroll.scrollLeft = todayCell.offsetLeft - ganttScroll.clientWidth / 2 + 20;
        }
    }
}

// 選択した月にスクロール
function scrollToSelectedMonth() {
    const year = document.getElementById('year-select').value;
    const month = document.getElementById('month-select').value;
    const targetDate = `${year}-${String(month).padStart(2, '0')}-01`;
    const targetCell = document.querySelector(`[data-date="${targetDate}"]`);
    if (targetCell) {
        const ganttScroll = document.getElementById('gantt-scroll');
        if (ganttScroll) {
            ganttScroll.scrollLeft = targetCell.offsetLeft - 20;
        }
    }
}

// モード切り替え（予定/実績）
function switchMode(mode) {
    currentMode = mode;
    document.getElementById('btn-plan').classList.toggle('active', mode === 'plan');
    document.getElementById('btn-actual').classList.toggle('active', mode === 'actual');
    document.getElementById('header-plan').classList.toggle('hidden', mode !== 'plan');
    document.getElementById('header-actual').classList.toggle('hidden', mode !== 'actual');
    renderTasks();
}

// 編集モード切り替え
function switchEditMode(mode) {
    isEditMode = mode === 'edit';
    document.getElementById('btn-view-mode').classList.toggle('active', !isEditMode);
    document.getElementById('btn-edit-mode').classList.toggle('active', isEditMode);
    document.getElementById('view-mode-indicator').classList.toggle('hidden', isEditMode);
    document.getElementById('edit-mode-indicator').classList.toggle('hidden', !isEditMode);
    document.getElementById('cancel-changes-btn').classList.toggle('hidden', !isEditMode);
    document.getElementById('save-changes-btn').classList.toggle('hidden', !isEditMode);
    document.body.classList.toggle('edit-mode', isEditMode);
}

// 検索パネル切り替え
function toggleSearchPanel() {
    document.getElementById('search-panel').classList.toggle('show');
}

// フィルター適用
function applyFilter() {
    // TODO: フィルタリング実装
}

// フィルタークリア
function clearFilter() {
    document.getElementById('filter-assignee').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-process').value = '';
    applyFilter();
}

// 変更キャンセル
function cancelAllChanges() {
    if (modifiedTasks.size > 0) {
        if (!confirm('変更を破棄しますか？')) return;
    }
    modifiedTasks.clear();
    renderTasks();
}

// 変更保存
async function saveAllChanges() {
    if (modifiedTasks.size === 0) {
        showToast('変更がありません', 'info');
        return;
    }

    const tasksToSave = Array.from(modifiedTasks.values());

    try {
        const response = await fetch('<?= base_url('api/tasks/bulk-update') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ tasks: tasksToSave })
        });

        const result = await response.json();

        if (result.success) {
            showToast('保存しました', 'success');
            modifiedTasks.clear();
            location.reload();
        } else {
            showToast(result.error || '保存に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('通信エラーが発生しました', 'error');
    }
}

// ユーティリティ関数
function formatDate(date) {
    return date.toISOString().split('T')[0];
}

function formatDisplayDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return `${d.getMonth() + 1}/${d.getDate()}`;
}

function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// タスクモーダル
function openTaskModal(taskId = null) {
    const modal = document.getElementById('task-modal');
    const form = document.getElementById('task-form');
    const title = document.getElementById('modal-title');

    form.reset();
    document.getElementById('task-id').value = taskId || '';
    document.getElementById('task-project-id').value = projectId;

    if (taskId) {
        title.innerHTML = '<i class="fas fa-tasks mr-2"></i>タスク編集';
        loadTaskData(taskId);
    } else {
        title.innerHTML = '<i class="fas fa-tasks mr-2"></i>新規タスク';
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
            document.getElementById('task-assignee').value = task.assignee_id || '';
            document.getElementById('task-status').value = task.status || 'not_started';
            document.getElementById('task-planned-start').value = task.planned_start_date || '';
            document.getElementById('task-planned-end').value = task.planned_end_date || '';
            document.getElementById('task-planned-man-days').value = task.planned_man_days || '';
            document.getElementById('task-sales-man-days').value = task.sales_man_days || '';
            document.getElementById('task-planned-cost').value = task.planned_cost || '';
            document.getElementById('task-progress').value = task.progress || 0;
            document.getElementById('task-description').value = task.description || '';
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
                'X-Requested-With': 'XMLHttpRequest'
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
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
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
</script>
<?= $this->endSection() ?>
