<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
  /* ガントチャートセル - 1日あたり40px */
  .gantt-cell {
    min-width: 40px;
    width: 40px;
    height: 100%;
  }

  /* ガント本体（タスク行）のセル高さ */
  #gantt-body .gantt-cell,
  #gantt-scroll .task-row .gantt-cell {
    min-height: 44px;
  }

  /* ヘッダーセルは親の高さに合わせる */
  .weekday-header-row .gantt-cell,
  .date-header-row .gantt-cell,
  .month-header-row .gantt-cell {
    height: 100%;
    min-height: auto;
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

  .task-bar {
    overflow: hidden;
  }

  .task-bar span.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    flex: 1;
  }

  /* 編集モード時のみホバー効果を有効化 */
  body.edit-mode .task-bar {
    transition: all 0.2s ease;
    cursor: grab;
  }

  body.edit-mode .task-bar:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }

  body.edit-mode .task-bar:active {
    cursor: grabbing;
  }

  /* 表示モード時はホバー効果なし */
  body:not(.edit-mode) .task-bar {
    cursor: pointer;
  }

  /* タスクバーのリサイズハンドル */
  .task-bar .resize-handle {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 8px;
    cursor: ew-resize;
    background: transparent;
    opacity: 0;
    transition: opacity 0.2s;
  }

  body.edit-mode .task-bar:hover .resize-handle {
    opacity: 1;
    background: rgba(255, 255, 255, 0.3);
  }

  .task-bar .resize-left {
    left: 0;
    border-radius: 6px 0 0 6px;
  }

  .task-bar .resize-right {
    right: 0;
    border-radius: 0 6px 6px 0;
  }

  /* タスクバー選択状態 */
  .task-bar-selected {
    outline: 3px solid #f59e0b !important;
    outline-offset: 1px;
    z-index: 30 !important;
  }

  /* タスク行選択状態 */
  .task-row-selected {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
  }

  /* 選択ツールバー */
  #selection-toolbar {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 100;
    display: none;
  }

  #selection-toolbar.show {
    display: flex;
    align-items: center;
    gap: 16px;
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
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
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
  .status-not-started {
    background: #f1f5f9;
    color: #64748b;
  }

  .status-in-progress {
    background: #dbeafe;
    color: #2563eb;
  }

  .status-completed {
    background: #d1fae5;
    color: #059669;
  }

  .status-on-hold {
    background: #fef3c7;
    color: #d97706;
  }

  /* トースト通知 */
  #toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .toast {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease;
    min-width: 280px;
  }

  .toast.hiding {
    animation: slideOut 0.3s ease forwards;
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }

    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }

  .toast-icon {
    font-size: 18px;
  }

  .toast-message {
    flex: 1;
    font-size: 14px;
    color: #334155;
  }

  .toast-close {
    cursor: pointer;
    color: #94a3b8;
  }

  .toast-close:hover {
    color: #64748b;
  }

  .toast-success {
    border-left: 4px solid #10b981;
  }

  .toast-success .toast-icon {
    color: #10b981;
  }

  .toast-error {
    border-left: 4px solid #ef4444;
  }

  .toast-error .toast-icon {
    color: #ef4444;
  }

  .toast-info {
    border-left: 4px solid #3b82f6;
  }

  .toast-info .toast-icon {
    color: #3b82f6;
  }

  .toast-warning {
    border-left: 4px solid #f59e0b;
  }

  .toast-warning .toast-icon {
    color: #f59e0b;
  }

  /* ローディングオーバーレイ */
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(2px);
    z-index: 10000;
    display: none;
    justify-content: center;
    align-items: center;
  }

  .loading-overlay.show {
    display: flex;
  }

  .loading-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    padding: 40px 50px;
    border-radius: 20px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    animation: cardPopIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  @keyframes cardPopIn {
    from {
      opacity: 0;
      transform: scale(0.8) translateY(20px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  .loading-icon-wrapper {
    position: relative;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .loading-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 4px solid #e2e8f0;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .loading-icon {
    font-size: 28px;
    color: #3b82f6;
    animation: iconPulse 1.5s ease-in-out infinite;
  }

  @keyframes iconPulse {
    0%, 100% {
      transform: scale(1);
      opacity: 1;
    }
    50% {
      transform: scale(1.1);
      opacity: 0.8;
    }
  }

  #loading-message {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    letter-spacing: 0.5px;
  }

  .loading-dots {
    display: flex;
    gap: 8px;
  }

  .loading-dots span {
    width: 10px;
    height: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 50%;
    animation: dotBounce 1.4s ease-in-out infinite;
  }

  .loading-dots span:nth-child(1) {
    animation-delay: 0s;
  }

  .loading-dots span:nth-child(2) {
    animation-delay: 0.2s;
  }

  .loading-dots span:nth-child(3) {
    animation-delay: 0.4s;
  }

  @keyframes dotBounce {
    0%, 80%, 100% {
      transform: scale(0.6);
      opacity: 0.5;
    }
    40% {
      transform: scale(1);
      opacity: 1;
    }
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
        <!-- プロジェクト選択（顧客別グループ） -->
        <form id="project-form" method="get" action="<?= base_url('schedule') ?>">
          <select id="project-select" name="project_id"
            class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
            style="min-width: 280px;" onchange="this.form.submit()">
            <option value="all" <?= (empty($projectId) || $projectId === 'all') ? 'selected' : '' ?>>📊 全プロジェクト（横断表示）
            </option>
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
          <a href="<?= base_url('schedule' . ($projectId ? '?project_id=' . $projectId : '')) ?>"
            class="toggle-btn-item active">
            <i class="fas fa-chart-gantt mr-1"></i>ガントチャート
          </a>
          <a href="<?= base_url('schedule/tasks' . ($projectId ? '?project_id=' . $projectId : '')) ?>"
            class="toggle-btn-item">
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
        <select id="year-select"
          class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
          onchange="updateCalendar()">
          <?php for ($y = date('Y') - 3; $y <= date('Y') + 3; $y++): ?>
            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?>年</option>
          <?php endfor; ?>
        </select>
        <!-- 月選択 -->
        <select id="month-select"
          class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
          onchange="updateCalendar()">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= $m ?>月</option>
          <?php endfor; ?>
        </select>

        <!-- 今日ボタン -->
        <button onclick="scrollToToday()"
          class="px-4 py-1.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white">
          今日
        </button>

        <!-- 絞込ボタン -->
        <button id="search-toggle"
          class="px-4 py-1.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm bg-white flex items-center">
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
        <select id="filter-assignee" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white"
          onchange="applyFilter()">
          <option value="">すべて</option>
          <?php foreach ($members as $member): ?>
            <option value="<?= $member['id'] ?>"><?= esc($member['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex items-center space-x-2">
        <label class="text-sm text-slate-600 font-medium">ステータス:</label>
        <select id="filter-status" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white"
          onchange="applyFilter()">
          <option value="">すべて</option>
          <option value="not_started">未着手</option>
          <option value="in_progress">進行中</option>
          <option value="completed">完了</option>
          <option value="on_hold">保留</option>
        </select>
      </div>
      <div class="flex items-center space-x-2">
        <label class="text-sm text-slate-600 font-medium">工程:</label>
        <select id="filter-process" class="border border-slate-300 rounded px-2 py-1.5 text-sm bg-white"
          onchange="applyFilter()">
          <option value="">すべて</option>
          <?php foreach ($processes as $process): ?>
            <option value="<?= $process['id'] ?>"><?= esc($process['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button onclick="clearFilter()"
        class="px-4 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-600 hover:bg-slate-50">クリア</button>
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
      <div class="flex-shrink-0 border-r-2 border-slate-300 bg-white overflow-y-auto scroll-sync"
        style="width: 760px; min-width: 760px;" id="task-panel">
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
        <span><i class="fas fa-tasks mr-1"></i>全タスク: <strong class="text-slate-700"
            id="stat-total"><?= $taskStats['total'] ?? 0 ?></strong>件</span>
        <span><i class="fas fa-check-circle mr-1 text-emerald-500"></i>完了: <strong class="text-emerald-600"
            id="stat-completed"><?= $taskStats['completed'] ?? 0 ?></strong>件</span>
        <span><i class="fas fa-spinner mr-1 text-blue-500"></i>進行中: <strong class="text-blue-600"
            id="stat-in-progress"><?= $taskStats['in_progress'] ?? 0 ?></strong>件</span>
        <span><i class="fas fa-clock mr-1 text-slate-400"></i>未着手: <strong class="text-slate-600"
            id="stat-not-started"><?= $taskStats['not_started'] ?? 0 ?></strong>件</span>
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
        <button id="undo-btn"
          class="hidden px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-all"
          onclick="undoLastChange()">
          <i class="fas fa-undo mr-1"></i>元に戻す
        </button>
        <button id="cancel-changes-btn"
          class="hidden px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition-all"
          onclick="cancelAllChanges()">
          <i class="fas fa-times mr-1"></i>キャンセル
        </button>
        <button id="save-changes-btn"
          class="hidden px-4 py-1.5 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-lg text-xs font-semibold hover:from-rose-600 hover:to-red-700 shadow-lg transition-all"
          onclick="saveAllChanges()">
          <i class="fas fa-save mr-1"></i>変更を登録
        </button>
      </div>
    </div>
  </footer>
</div>

<!-- 選択ツールバー -->
<div id="selection-toolbar">
  <span id="selection-count" class="text-sm font-medium">0件選択中</span>
  <div class="flex items-center gap-2">
    <button onclick="bulkMoveSelected(-1)" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
      title="1日前へ移動">
      <i class="fas fa-chevron-left"></i>
    </button>
    <button onclick="bulkMoveSelected(1)" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
      title="1日後へ移動">
      <i class="fas fa-chevron-right"></i>
    </button>
    <button onclick="clearTaskSelection()" class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
      title="選択解除">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>

<!-- トースト通知コンテナ -->
<div id="toast-container"></div>

<!-- ローディングオーバーレイ -->
<div id="loading-overlay" class="loading-overlay">
  <div class="loading-card">
    <div class="loading-icon-wrapper">
      <div class="loading-ring"></div>
      <i class="fas fa-save loading-icon"></i>
    </div>
    <span id="loading-message">保存中...</span>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>
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
  let changeHistory = []; // 変更履歴（元に戻す用）
  let ganttStartDate, ganttEndDate;
  const CELL_WIDTH = 40; // 1日あたりのセル幅
  let selectedTasks = new Set(); // 選択されたタスクIDのセット

  // 初期化
  document.addEventListener('DOMContentLoaded', function () {
    initGanttChart();
    initScrollSync();

    // 検索パネル切り替え
    const searchToggle = document.getElementById('search-toggle');
    if (searchToggle) {
      searchToggle.addEventListener('click', function () {
        document.getElementById('search-panel').classList.toggle('show');
      });
    }
  });

  // ガントチャート初期化
  function initGanttChart() {
    calculateDateRange();
    renderCalendarHeader();
    renderTasks();
    // 選択されている月（デフォルトは今月）にスクロール
    scrollToSelectedMonth();
  }

  // 日付範囲を計算（選択した年月を中心に前後1年）
  function calculateDateRange() {
    const selectedYear = parseInt(document.getElementById('year-select').value);
    const selectedMonth = parseInt(document.getElementById('month-select').value) - 1; // 0-indexed

    // 選択した年月を中心に前後1年を表示
    const centerDate = new Date(selectedYear, selectedMonth, 1);
    let minDate = new Date(centerDate);
    let maxDate = new Date(centerDate);
    minDate.setFullYear(minDate.getFullYear() - 1);
    maxDate.setFullYear(maxDate.getFullYear() + 1);

    // 月初め・月末に調整
    minDate.setDate(1);
    maxDate.setMonth(maxDate.getMonth() + 1, 0);

    ganttStartDate = minDate;
    ganttEndDate = maxDate;
  }

  // カレンダーを更新（年月選択時）
  function updateCalendar() {
    calculateDateRange();
    renderCalendarHeader();
    renderTasks();
    scrollToSelectedMonth();
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
    let currentYear = -1;
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
      if (month !== currentMonth || year !== currentYear) {
        if (currentMonth !== -1) {
          monthHtml += `<div class="gantt-cell flex items-center justify-center font-bold text-sm text-slate-700 bg-slate-200 border-r border-slate-300" style="width: ${monthDays * 40}px;" data-year="${currentYear}" data-month="${currentMonth + 1}">${currentYear}年${currentMonth + 1}月</div>`;
        }
        currentMonth = month;
        currentYear = year;
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
      monthHtml += `<div class="gantt-cell flex items-center justify-center font-bold text-sm text-slate-700 bg-slate-200 border-r border-slate-300" style="width: ${monthDays * 40}px;" data-year="${currentYear}" data-month="${currentMonth + 1}">${currentYear}年${currentMonth + 1}月</div>`;
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
    ganttBody.style.width = (dayCount * CELL_WIDTH) + 'px';

    // タスクバーのドラッグ機能を設定
    setupTaskBarDrag();
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

  // 担当者カラー設定
  const ASSIGNEE_COLORS = {};
  const colorPalette = [
    'from-blue-400 to-indigo-500',
    'from-purple-400 to-pink-500',
    'from-cyan-400 to-blue-500',
    'from-orange-400 to-red-500',
    'from-teal-400 to-cyan-500',
    'from-emerald-400 to-green-500',
    'from-rose-400 to-pink-500',
    'from-amber-400 to-orange-500'
  ];
  let colorIndex = 0;

  function getAssigneeColor(assigneeName) {
    if (!assigneeName || assigneeName === '-') return 'from-gray-400 to-gray-500';
    if (!ASSIGNEE_COLORS[assigneeName]) {
      ASSIGNEE_COLORS[assigneeName] = colorPalette[colorIndex % colorPalette.length];
      colorIndex++;
    }
    return ASSIGNEE_COLORS[assigneeName];
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

    // タスクアイコン（親: clipboard-list、サブタスク: caret-right）
    const taskIcon = isSubtask
      ? '<i class="fas fa-caret-right text-blue-500 mr-2"></i>'
      : '<i class="fas fa-clipboard-list text-blue-500 mr-2"></i>';

    // 遅延バッジ
    let delayBadge = '';
    if (delayDays > 0) {
      delayBadge = `<span class="delay-badge delay-late">${delayDays}日遅れ</span>`;
    } else if (delayDays < 0) {
      delayBadge = `<span class="delay-badge delay-ahead">${Math.abs(delayDays)}日先行</span>`;
    } else {
      delayBadge = `<span class="delay-badge delay-ontime">予定通り</span>`;
    }

    // 担当者表示（カラー付き丸アイコン）
    const assigneeName = task.assignee_name || '-';
    const assigneeColor = getAssigneeColor(assigneeName);
    const assigneeHtml = assigneeName === '-'
      ? '<span class="text-slate-500 text-xs">-</span>'
      : `<div class="flex items-center justify-center">
            <div class="w-5 h-5 rounded-full bg-gradient-to-br ${assigneeColor} mr-1 flex-shrink-0"></div>
            <span class="text-xs text-slate-600 truncate">${esc(assigneeName)}</span>
           </div>`;

    // 進捗率の色
    const progress = task.progress || 0;
    const progressColorClass = progress === 100 ? 'text-emerald-600 font-semibold'
      : (progress > 0 ? 'text-blue-600' : 'text-slate-400');

    return `
        <div class="task-row task-row-item ${rowClass}" data-task-id="${task.id}" data-row="${rowIndex}">
            <div class="w-8 px-1 text-center border-r border-slate-200 flex items-center justify-center">
                <input type="checkbox" class="task-checkbox rounded border-slate-300" data-task-id="${task.id}">
            </div>
            <div class="w-14 px-1 text-xs text-slate-500 text-center border-r border-slate-200 flex items-center justify-center">${rowIndex + 1}</div>
            <div class="flex-1 px-2 text-sm border-r border-slate-200 flex items-center ${indent}">
                ${taskIcon}
                <a href="#" onclick="openTaskModal(${task.id}); return false;" class="task-name-link ${isSubtask ? '' : 'parent'}">
                    ${esc(task.task_name)}<i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            <div class="w-16 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center">${assigneeHtml}</div>
            <div class="w-12 px-1 text-xs text-right border-r border-slate-200 flex items-center justify-end" data-field="man-days">${manDays || '-'}</div>
            <div class="w-16 px-1 text-xs text-right border-r border-slate-200 flex items-center justify-end" data-field="cost">${cost ? '¥' + Number(cost).toLocaleString() : '-'}</div>
            <div class="w-20 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center" data-field="start-date">${formatDisplayDate(startDate)}</div>
            <div class="w-20 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center" data-field="end-date">${formatDisplayDate(endDate)}</div>
            <div class="w-12 px-1 text-xs text-center border-r border-slate-200 flex items-center justify-center ${progressColorClass}" data-field="progress">${progress}%</div>
            <div class="w-14 px-1 text-xs text-center flex items-center justify-center" data-field="delay">${delayBadge}</div>
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
            <div class="task-bar bg-gradient-to-r ${barColor} absolute flex items-center text-white text-xs font-medium"
                 style="left: ${startOffset * CELL_WIDTH + 4}px; width: ${duration * CELL_WIDTH - 8}px; top: 8px;"
                 data-task-id="${task.id}"
                 data-parent-id="${task.parent_id || ''}"
                 data-start-date="${startDate}"
                 data-end-date="${endDate}"
                 title="${esc(task.task_name)}${progress > 0 ? ' (' + progress + '%)' : ''}">
                <div class="resize-handle resize-left"></div>
                <span class="ml-2 truncate pointer-events-none">${esc(task.task_name)}</span>
                ${progress > 0 ? `<span class="ml-1 pointer-events-none flex-shrink-0">${progress}%</span>` : ''}
                <div class="resize-handle resize-right"></div>
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
      taskPanel.addEventListener('scroll', function () {
        ganttScroll.scrollTop = taskPanel.scrollTop;
      });
      ganttScroll.addEventListener('scroll', function () {
        taskPanel.scrollTop = ganttScroll.scrollTop;
        // 横スクロール同期（ヘッダー）
        if (ganttHeaderScroll) {
          ganttHeaderScroll.scrollLeft = ganttScroll.scrollLeft;
        }
      });
    }

    if (ganttHeaderScroll && ganttScroll) {
      ganttHeaderScroll.addEventListener('scroll', function () {
        ganttScroll.scrollLeft = ganttHeaderScroll.scrollLeft;
      });
    }

    // ドラッグスクロール初期化
    initDragScroll(ganttScroll);
  }

  // ドラッグスクロール機能
  function initDragScroll(element) {
    if (!element) return;

    let isDown = false;
    let startX;
    let startY;
    let scrollLeft;
    let scrollTop;

    element.addEventListener('mousedown', (e) => {
      // タスクバーやボタンのクリックは除外
      if (e.target.closest('.task-bar') || e.target.closest('button') || e.target.closest('input')) return;

      isDown = true;
      element.style.cursor = 'grabbing';
      element.style.userSelect = 'none';
      startX = e.pageX - element.offsetLeft;
      startY = e.pageY - element.offsetTop;
      scrollLeft = element.scrollLeft;
      scrollTop = element.scrollTop;
    });

    element.addEventListener('mouseleave', () => {
      if (isDown) {
        isDown = false;
        element.style.cursor = 'grab';
        element.style.userSelect = '';
      }
    });

    element.addEventListener('mouseup', () => {
      isDown = false;
      element.style.cursor = 'grab';
      element.style.userSelect = '';
    });

    element.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - element.offsetLeft;
      const y = e.pageY - element.offsetTop;
      const walkX = (x - startX) * 1.5; // スクロール速度調整
      const walkY = (y - startY) * 1.5;
      element.scrollLeft = scrollLeft - walkX;
      element.scrollTop = scrollTop - walkY;
    });

    // 初期カーソル設定
    element.style.cursor = 'grab';
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
    const year = parseInt(document.getElementById('year-select').value);
    const month = parseInt(document.getElementById('month-select').value);

    // 少し遅延させてDOMが更新された後にスクロール
    setTimeout(() => {
      const ganttScroll = document.getElementById('gantt-scroll');
      const ganttHeaderScroll = document.getElementById('gantt-header-scroll');

      if (ganttScroll) {
        // 選択された年月1日の位置を計算（ganttStartDateからの日数）
        const targetFirstDay = new Date(year, month - 1, 1); // monthは1-indexed
        const dayOffset = Math.floor((targetFirstDay - ganttStartDate) / (1000 * 60 * 60 * 24));

        // 1日を画面中央に配置
        const scrollPos = (dayOffset * CELL_WIDTH) - (ganttScroll.clientWidth / 2) + (CELL_WIDTH / 2);
        ganttScroll.scrollLeft = Math.max(0, scrollPos);
        if (ganttHeaderScroll) {
          ganttHeaderScroll.scrollLeft = Math.max(0, scrollPos);
        }
      }
    }, 100);
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

  // 編集モード切り替え - モーダルと双方向連動
  function switchEditMode(mode) {
    isEditMode = mode === 'edit';
    document.getElementById('btn-view-mode').classList.toggle('active', !isEditMode);
    document.getElementById('btn-edit-mode').classList.toggle('active', isEditMode);
    document.getElementById('view-mode-indicator').classList.toggle('hidden', isEditMode);
    document.getElementById('edit-mode-indicator').classList.toggle('hidden', !isEditMode);
    document.getElementById('cancel-changes-btn').classList.toggle('hidden', !isEditMode);
    document.getElementById('save-changes-btn').classList.toggle('hidden', !isEditMode);
    document.body.classList.toggle('edit-mode', isEditMode);

    // 表示モード切り替え時は選択をクリア
    if (!isEditMode) {
      clearTaskSelection();
    }

    // モーダルが開いている場合、モーダル側のモードも連動
    const taskModal = document.getElementById('task-modal');
    if (taskModal && taskModal.classList.contains('show')) {
      const modalMode = isEditMode ? 'edit' : 'view';
      // switchModalModeにsyncFromCalendar=trueを渡してループ防止
      if (typeof switchModalMode === 'function') {
        switchModalMode(modalMode, true);
      }
    }
  }

  // ========== タスクバードラッグ機能 ==========

  // ドラッグ状態をグローバルで管理
  let dragState = {
    isDragging: false,
    isResizing: false,
    hasMoved: false,
    resizeDirection: '',
    startX: 0,
    startLeft: 0,
    startWidth: 0,
    activeBar: null,
    activeTaskId: null
  };

  // タスクバードラッグのセットアップ
  function setupTaskBarDrag() {
    // 各タスクバーにmousedownイベントを設定
    document.querySelectorAll('.task-bar').forEach(bar => {
      bar.addEventListener('mousedown', handleBarMouseDown);
      bar.addEventListener('click', handleBarClick);
    });

    // ガントスクロール領域のクリックで選択解除
    const ganttScroll = document.getElementById('gantt-scroll');
    if (ganttScroll) {
      ganttScroll.addEventListener('click', function (e) {
        if (e.target.closest('.task-bar')) return;
        if (isEditMode && !e.ctrlKey && !e.metaKey) {
          clearTaskSelection();
        }
      });
    }
  }

  // mousedownハンドラ
  function handleBarMouseDown(e) {
    // 表示モードの場合はドラッグ無効
    if (!isEditMode) return;

    const bar = e.currentTarget;
    const target = e.target;

    // 状態をリセット
    dragState.hasMoved = false;
    dragState.activeBar = bar;
    dragState.activeTaskId = bar.dataset.taskId;
    dragState.startX = e.clientX;
    dragState.startLeft = parseInt(bar.style.left) || 0;
    dragState.startWidth = parseInt(bar.style.width) || 0;

    if (target.classList.contains('resize-handle')) {
      dragState.isResizing = true;
      dragState.isDragging = false;
      dragState.resizeDirection = target.classList.contains('resize-left') ? 'left' : 'right';
    } else {
      dragState.isDragging = true;
      dragState.isResizing = false;
    }

    e.stopPropagation();
    e.preventDefault();
  }

  // clickハンドラ
  function handleBarClick(e) {
    if (dragState.hasMoved) return; // ドラッグ中は無視

    // 表示モードの場合は何もしない
    if (!isEditMode) return;

    const bar = e.currentTarget;
    const taskId = bar.dataset.taskId;

    // 編集モード: 選択
    if (e.ctrlKey || e.metaKey) {
      // Ctrl+クリック: 複数選択トグル
      toggleTaskSelection(taskId, bar);
    } else {
      // 通常クリック: 選択をクリアして単一選択
      clearTaskSelection();
      selectTask(taskId, bar);
    }
    e.stopPropagation();
  }

  // documentレベルのイベントリスナー（一度だけ登録）
  document.addEventListener('mousemove', function (e) {
    if (!dragState.isDragging && !dragState.isResizing) return;
    if (!dragState.activeBar) return;

    const diff = e.clientX - dragState.startX;
    if (Math.abs(diff) > 3) dragState.hasMoved = true;

    const bar = dragState.activeBar;
    const taskId = dragState.activeTaskId;

    if (dragState.isDragging) {
      let newLeft = dragState.startLeft + diff;
      newLeft = Math.round(newLeft / CELL_WIDTH) * CELL_WIDTH + 4; // グリッドにスナップ
      newLeft = Math.max(4, newLeft);
      const daysMoved = Math.round(diff / CELL_WIDTH);

      // 選択されたタスクがある場合は全て一緒に移動
      if (selectedTasks.size > 0 && selectedTasks.has(taskId)) {
        selectedTasks.forEach(id => {
          const selectedBar = document.querySelector(`.task-bar[data-task-id="${id}"]`);
          if (selectedBar) {
            let origLeft = selectedBar.dataset.dragStartLeft;
            if (origLeft === undefined) {
              origLeft = parseInt(selectedBar.style.left) || 0;
              selectedBar.dataset.dragStartLeft = origLeft;
            }
            let selNewLeft = parseInt(origLeft) + daysMoved * CELL_WIDTH;
            selNewLeft = Math.max(4, selNewLeft);
            selectedBar.style.left = selNewLeft + 'px';
          }
        });
      } else {
        bar.style.left = newLeft + 'px';
      }
    } else if (dragState.isResizing) {
      const daysChange = Math.round(diff / CELL_WIDTH);

      if (selectedTasks.size > 0 && selectedTasks.has(taskId)) {
        // 複数選択時は全て一括でリサイズ
        selectedTasks.forEach(id => {
          const selectedBar = document.querySelector(`.task-bar[data-task-id="${id}"]`);
          if (selectedBar) {
            let origLeft = selectedBar.dataset.resizeStartLeft;
            let origWidth = selectedBar.dataset.resizeStartWidth;
            if (origLeft === undefined) {
              origLeft = parseInt(selectedBar.style.left) || 0;
              origWidth = parseInt(selectedBar.style.width) || 0;
              selectedBar.dataset.resizeStartLeft = origLeft;
              selectedBar.dataset.resizeStartWidth = origWidth;
            }

            if (dragState.resizeDirection === 'right') {
              let selNewWidth = parseInt(origWidth) + daysChange * CELL_WIDTH;
              selNewWidth = Math.max(CELL_WIDTH - 8, selNewWidth);
              selectedBar.style.width = selNewWidth + 'px';
            } else {
              let selNewLeft = parseInt(origLeft) + daysChange * CELL_WIDTH;
              let selNewWidth = parseInt(origWidth) - daysChange * CELL_WIDTH;
              selNewWidth = Math.max(CELL_WIDTH - 8, selNewWidth);
              selNewLeft = Math.max(4, selNewLeft);
              selectedBar.style.left = selNewLeft + 'px';
              selectedBar.style.width = selNewWidth + 'px';
            }
          }
        });
      } else {
        // 単一タスクのリサイズ
        if (dragState.resizeDirection === 'right') {
          let newWidth = dragState.startWidth + diff;
          newWidth = Math.round(newWidth / CELL_WIDTH) * CELL_WIDTH - 8;
          newWidth = Math.max(CELL_WIDTH - 8, newWidth);
          bar.style.width = newWidth + 'px';
        } else {
          let newLeft = dragState.startLeft + diff;
          let newWidth = dragState.startWidth - diff;
          newLeft = Math.round(newLeft / CELL_WIDTH) * CELL_WIDTH + 4;
          newWidth = Math.round(newWidth / CELL_WIDTH) * CELL_WIDTH - 8;
          newWidth = Math.max(CELL_WIDTH - 8, newWidth);
          newLeft = Math.max(4, newLeft);
          bar.style.left = newLeft + 'px';
          bar.style.width = newWidth + 'px';
        }
      }
    }
  });

  document.addEventListener('mouseup', function () {
    if (!dragState.isDragging && !dragState.isResizing) return;

    const bar = dragState.activeBar;
    const taskId = dragState.activeTaskId;

    if (bar && dragState.hasMoved) {
      // 複数選択されている場合
      if (selectedTasks.size > 0 && selectedTasks.has(taskId)) {
        selectedTasks.forEach(id => {
          const selectedBar = document.querySelector(`.task-bar[data-task-id="${id}"]`);
          if (selectedBar) {
            // 元の位置を取得（履歴用）
            const origLeft = parseInt(selectedBar.dataset.dragStartLeft) || parseInt(selectedBar.dataset.resizeStartLeft) || dragState.startLeft;
            const origWidth = parseInt(selectedBar.dataset.resizeStartWidth) || dragState.startWidth;
            updateTaskDatesFromBar(selectedBar, id, origLeft, origWidth);
            // データをクリア
            delete selectedBar.dataset.dragStartLeft;
            delete selectedBar.dataset.resizeStartLeft;
            delete selectedBar.dataset.resizeStartWidth;
          }
        });
      } else {
        // 元の位置を渡して履歴に保存
        updateTaskDatesFromBar(bar, taskId, dragState.startLeft, dragState.startWidth);
        // ドラッグ終了後、タスクを選択状態にする
        clearTaskSelection();
        selectTask(taskId, bar);
      }
    }

    // 状態をリセット
    dragState.isDragging = false;
    dragState.isResizing = false;
    dragState.hasMoved = false;
    dragState.activeBar = null;
    dragState.activeTaskId = null;
  });

  // タスクバーの位置から日付を計算して更新
  function updateTaskDatesFromBar(bar, taskId, originalLeft = null, originalWidth = null) {
    const left = parseInt(bar.style.left) || 0;
    const width = parseInt(bar.style.width) || 0;

    // 元の位置が渡されていない場合は現在の位置を使用（履歴に保存しない）
    const saveHistory = originalLeft !== null && originalWidth !== null;

    // 元の日付を計算（履歴用）
    if (saveHistory) {
      const origStartDayOffset = Math.round((originalLeft - 4) / CELL_WIDTH);
      const origDuration = Math.round((originalWidth + 8) / CELL_WIDTH);
      const origStartDate = new Date(ganttStartDate);
      origStartDate.setDate(origStartDate.getDate() + origStartDayOffset);
      const origEndDate = new Date(origStartDate);
      origEndDate.setDate(origEndDate.getDate() + origDuration - 1);

      // 履歴に追加
      changeHistory.push({
        taskId: taskId,
        originalLeft: originalLeft,
        originalWidth: originalWidth,
        originalStartDate: formatDate(origStartDate),
        originalEndDate: formatDate(origEndDate)
      });
    }

    const startDayOffset = Math.round((left - 4) / CELL_WIDTH);
    const duration = Math.round((width + 8) / CELL_WIDTH);

    const newStartDate = new Date(ganttStartDate);
    newStartDate.setDate(newStartDate.getDate() + startDayOffset);

    const newEndDate = new Date(newStartDate);
    newEndDate.setDate(newEndDate.getDate() + duration - 1);

    // modifiedTasksに追加
    const taskData = modifiedTasks.get(taskId) || { id: taskId };
    if (currentMode === 'plan') {
      taskData.planned_start_date = formatDate(newStartDate);
      taskData.planned_end_date = formatDate(newEndDate);
    } else {
      taskData.actual_start_date = formatDate(newStartDate);
      taskData.actual_end_date = formatDate(newEndDate);
    }
    modifiedTasks.set(taskId, taskData);

    // 一覧側の開始日・終了日・遅延を更新
    updateTaskListRow(taskId, newStartDate, newEndDate);

    // 親タスクの日付を連動して更新（空文字列でないことを確認）
    const parentId = bar.dataset.parentId;
    if (parentId && parentId !== '' && parentId !== 'null' && parentId !== 'undefined') {
      updateParentTaskDates(parentId, newStartDate, newEndDate);
    }

    // ボタンを表示
    updateEditButtons();
  }

  // 編集ボタンの表示状態を更新
  function updateEditButtons() {
    const hasChanges = modifiedTasks.size > 0;
    const hasHistory = changeHistory.length > 0;

    const saveBtn = document.getElementById('save-changes-btn');
    const cancelBtn = document.getElementById('cancel-changes-btn');
    const undoBtn = document.getElementById('undo-btn');

    if (saveBtn) saveBtn.classList.toggle('hidden', !hasChanges);
    if (cancelBtn) cancelBtn.classList.toggle('hidden', !hasChanges);
    if (undoBtn) undoBtn.classList.toggle('hidden', !hasHistory);

    console.log('updateEditButtons:', { hasChanges, hasHistory, historyLength: changeHistory.length });
  }

  // 最後の変更を元に戻す
  function undoLastChange() {
    if (changeHistory.length === 0) {
      showToast('元に戻す変更はありません', 'info');
      return;
    }

    const lastChange = changeHistory.pop();

    // タスクバーの位置を元に戻す
    const bar = document.querySelector(`.task-bar[data-task-id="${lastChange.taskId}"]`);
    if (bar) {
      bar.style.left = lastChange.originalLeft + 'px';
      bar.style.width = lastChange.originalWidth + 'px';

      // 一覧側の日付も元に戻す
      const origStartDate = new Date(lastChange.originalStartDate);
      const origEndDate = new Date(lastChange.originalEndDate);
      updateTaskListRow(lastChange.taskId, origStartDate, origEndDate);

      // タスク行の変更マークを外す
      const taskRow = document.querySelector(`.task-row-item[data-task-id="${lastChange.taskId}"]`);
      if (taskRow) {
        taskRow.classList.remove('task-row-modified');
      }
    }

    // modifiedTasksから削除
    modifiedTasks.delete(lastChange.taskId);

    // ボタン表示を更新
    updateEditButtons();

    showToast('変更を元に戻しました', 'info');
  }

  // 親タスクの日付を子タスクに合わせて更新
  function updateParentTaskDates(parentTaskId, childStartDate, childEndDate) {
    const parentBar = document.querySelector(`.task-bar[data-task-id="${parentTaskId}"]`);
    if (!parentBar) return;

    // 親タスクの現在の日付を取得
    const parentLeft = parseInt(parentBar.style.left) || 0;
    const parentWidth = parseInt(parentBar.style.width) || 0;
    const parentStartOffset = Math.round((parentLeft - 4) / CELL_WIDTH);
    const parentDuration = Math.round((parentWidth + 8) / CELL_WIDTH);

    const parentStartDate = new Date(ganttStartDate);
    parentStartDate.setDate(parentStartDate.getDate() + parentStartOffset);
    const parentEndDate = new Date(parentStartDate);
    parentEndDate.setDate(parentEndDate.getDate() + parentDuration - 1);

    let needsUpdate = false;
    let newParentStartDate = new Date(parentStartDate);
    let newParentEndDate = new Date(parentEndDate);

    // 子の開始日が親の開始日より前の場合
    if (childStartDate < parentStartDate) {
      newParentStartDate = new Date(childStartDate);
      needsUpdate = true;
    }

    // 子の終了日が親の終了日より後の場合
    if (childEndDate > parentEndDate) {
      newParentEndDate = new Date(childEndDate);
      needsUpdate = true;
    }

    if (needsUpdate) {
      // 親タスクバーの位置とサイズを更新
      const newParentStartOffset = Math.floor((newParentStartDate - ganttStartDate) / (1000 * 60 * 60 * 24));
      const newParentDuration = Math.floor((newParentEndDate - newParentStartDate) / (1000 * 60 * 60 * 24)) + 1;

      parentBar.style.left = (newParentStartOffset * CELL_WIDTH + 4) + 'px';
      parentBar.style.width = (newParentDuration * CELL_WIDTH - 8) + 'px';

      // modifiedTasksに親タスクも追加
      const parentData = modifiedTasks.get(parentTaskId) || { id: parentTaskId };
      if (currentMode === 'plan') {
        parentData.planned_start_date = formatDate(newParentStartDate);
        parentData.planned_end_date = formatDate(newParentEndDate);
      } else {
        parentData.actual_start_date = formatDate(newParentStartDate);
        parentData.actual_end_date = formatDate(newParentEndDate);
      }
      modifiedTasks.set(parentTaskId, parentData);

      // 親タスクの一覧行も更新
      updateTaskListRow(parentTaskId, newParentStartDate, newParentEndDate);

      // 親タスクにさらに親がいる場合は再帰的に更新
      const grandParentId = parentBar.dataset.parentId;
      if (grandParentId && grandParentId !== '' && grandParentId !== 'null' && grandParentId !== 'undefined') {
        updateParentTaskDates(grandParentId, newParentStartDate, newParentEndDate);
      }
    }
  }

  // タスク一覧の行を更新（開始日・終了日・遅延）
  function updateTaskListRow(taskId, startDate, endDate) {
    const taskRow = document.querySelector(`.task-row-item[data-task-id="${taskId}"]`);
    if (!taskRow) return;

    // data-field属性でセルを特定
    const startDateCell = taskRow.querySelector('[data-field="start-date"]');
    const endDateCell = taskRow.querySelector('[data-field="end-date"]');
    const delayCell = taskRow.querySelector('[data-field="delay"]');

    if (startDateCell) {
      startDateCell.textContent = formatDisplayDate(formatDate(startDate));
    }
    if (endDateCell) {
      endDateCell.textContent = formatDisplayDate(formatDate(endDate));
    }

    // 遅延日数を計算して更新
    if (delayCell) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const delayDays = calculateDelayDays(endDate, today);
      delayCell.innerHTML = renderDelayBadge(delayDays);
    }

    // 変更された行をハイライト
    taskRow.classList.add('task-row-modified');
  }

  // 遅延日数を計算
  function calculateDelayDays(plannedEndDate, today) {
    const endDate = new Date(plannedEndDate);
    endDate.setHours(0, 0, 0, 0);

    // 完了していない場合、今日との差分
    const diffTime = today - endDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
  }

  // 遅延バッジをレンダリング
  function renderDelayBadge(delayDays) {
    if (delayDays > 0) {
      return `<span class="delay-badge delay-late">${delayDays}日遅れ</span>`;
    } else if (delayDays < 0) {
      return `<span class="delay-badge delay-ahead">${Math.abs(delayDays)}日先行</span>`;
    } else {
      return `<span class="delay-badge delay-ontime">予定通り</span>`;
    }
  }

  // タスク選択
  function selectTask(taskId, bar) {
    selectedTasks.add(taskId);
    bar.classList.add('task-bar-selected');
    // タスク一覧側も選択表示
    const taskRow = document.querySelector(`.task-row-item[data-task-id="${taskId}"]`);
    if (taskRow) taskRow.classList.add('task-row-selected');
    updateSelectionUI();
  }

  // タスク選択解除
  function deselectTask(taskId, bar) {
    selectedTasks.delete(taskId);
    bar.classList.remove('task-bar-selected');
    const taskRow = document.querySelector(`.task-row-item[data-task-id="${taskId}"]`);
    if (taskRow) taskRow.classList.remove('task-row-selected');
    updateSelectionUI();
  }

  // タスク選択トグル
  function toggleTaskSelection(taskId, bar) {
    if (selectedTasks.has(taskId)) {
      deselectTask(taskId, bar);
    } else {
      selectTask(taskId, bar);
    }
  }

  // 全選択クリア
  function clearTaskSelection() {
    selectedTasks.forEach(taskId => {
      const bar = document.querySelector(`.task-bar[data-task-id="${taskId}"]`);
      if (bar) bar.classList.remove('task-bar-selected');
      const taskRow = document.querySelector(`.task-row-item[data-task-id="${taskId}"]`);
      if (taskRow) taskRow.classList.remove('task-row-selected');
    });
    selectedTasks.clear();
    updateSelectionUI();
  }

  // 選択UIの更新
  function updateSelectionUI() {
    const count = selectedTasks.size;
    const toolbar = document.getElementById('selection-toolbar');
    if (count > 0 && isEditMode) {
      toolbar.classList.add('show');
      document.getElementById('selection-count').textContent = count + '件選択中';
    } else {
      toolbar.classList.remove('show');
    }
  }

  // 選択されたタスクを一括移動
  function bulkMoveSelected(days) {
    if (selectedTasks.size === 0) return;

    selectedTasks.forEach(taskId => {
      const bar = document.querySelector(`.task-bar[data-task-id="${taskId}"]`);
      if (bar) {
        const currentLeft = parseInt(bar.style.left) || 0;
        const newLeft = Math.max(4, currentLeft + (days * CELL_WIDTH));
        bar.style.left = newLeft + 'px';
        updateTaskDatesFromBar(bar, taskId);
      }
    });
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
    if (modifiedTasks.size === 0 && changeHistory.length === 0) {
      showToast('変更はありません', 'info');
      return;
    }

    if (!confirm('全ての変更をキャンセルしますか？')) return;

    // 履歴を逆順にたどって全ての変更を元に戻す
    while (changeHistory.length > 0) {
      const change = changeHistory.pop();
      const bar = document.querySelector(`.task-bar[data-task-id="${change.taskId}"]`);
      if (bar) {
        bar.style.left = change.originalLeft + 'px';
        bar.style.width = change.originalWidth + 'px';

        // 一覧側の日付も元に戻す
        const origStartDate = new Date(change.originalStartDate);
        const origEndDate = new Date(change.originalEndDate);
        updateTaskListRow(change.taskId, origStartDate, origEndDate);

        // タスク行の変更マークを外す
        const taskRow = document.querySelector(`.task-row-item[data-task-id="${change.taskId}"]`);
        if (taskRow) {
          taskRow.classList.remove('task-row-modified');
        }
      }
    }

    // 変更をクリア
    modifiedTasks.clear();
    changeHistory = [];
    updateEditButtons();

    showToast('全ての変更をキャンセルしました', 'info');
  }

  // ローディングオーバーレイ表示
  function showLoading(message = '保存中...') {
    document.getElementById('loading-message').textContent = message;
    document.getElementById('loading-overlay').classList.add('show');
  }

  // ローディングオーバーレイ非表示
  function hideLoading() {
    document.getElementById('loading-overlay').classList.remove('show');
  }

  // 変更保存
  async function saveAllChanges() {
    if (modifiedTasks.size === 0) {
      showToast('変更がありません', 'info');
      return;
    }

    const tasksToSave = Array.from(modifiedTasks.values());

    // ローディング表示
    showLoading('保存中...');

    // ブラウザに再描画の時間を与える
    await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));

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
        hideLoading();
        showToast('保存しました（' + tasksToSave.length + '件）', 'success');
        modifiedTasks.clear();
        changeHistory = []; // 履歴もクリア
        // トーストを表示してからリロード
        setTimeout(() => location.reload(), 1500);
      } else {
        hideLoading();
        showToast(result.error || '保存に失敗しました', 'error');
      }
    } catch (error) {
      console.error('Error:', error);
      hideLoading();
      showToast('通信エラーが発生しました', 'error');
    }
  }

  // ユーティリティ関数
  function formatDate(date) {
    // ローカル日付を使用（toISOString()はUTC変換で日付ずれの原因になる）
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function formatDisplayDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const yy = String(d.getFullYear()).slice(-2);
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yy}/${mm}/${dd}`;
  }

  function esc(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // モーダル関連の関数は task_modal.php に定義

  // トースト通知
  function showToast(message, type = 'info') {
    // 空のメッセージは表示しない
    if (!message || message.trim() === '') {
      console.warn('showToast called with empty message');
      return;
    }

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

  // グローバルスコープに関数を公開
  window.saveAllChanges = saveAllChanges;
  window.cancelAllChanges = cancelAllChanges;
  window.showLoading = showLoading;
  window.hideLoading = hideLoading;
</script>
<?= $this->endSection() ?>