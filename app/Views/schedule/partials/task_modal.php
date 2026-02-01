<!-- タスクモーダル用CSS -->
<style>
    /* タスクモーダル（フルスクリーン） */
    .task-modal-fullscreen {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
    }
    .task-modal-fullscreen.show {
        display: block;
    }
    .task-modal-content {
        width: 100%;
        height: 100%;
        background: white;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    /* 表示モード時のスタイル */
    .modal-view-mode input:not([type="checkbox"]):not([type="range"]),
    .modal-view-mode select,
    .modal-view-mode textarea {
        background-color: #f8fafc !important;
        border-color: transparent !important;
        pointer-events: none;
        cursor: default;
        color: #334155;
    }
    .modal-view-mode input:focus,
    .modal-view-mode select:focus,
    .modal-view-mode textarea:focus {
        box-shadow: none !important;
        ring: none !important;
    }
    .modal-view-mode input[type="range"] {
        pointer-events: none;
    }
    .modal-view-mode #modal-header {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
    }
    .modal-view-mode #history-add-btn {
        display: none !important;
    }
    .modal-view-mode #btn-delete-task {
        display: none !important;
    }
</style>

<!-- タスク編集/追加モーダル -->
<div id="task-modal" class="task-modal-fullscreen">
    <!-- モーダル本体（全画面） -->
    <div class="task-modal-content">
        <!-- ヘッダー -->
        <div id="modal-header" class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <h3 id="modal-title" class="text-lg font-bold text-white">タスク編集</h3>
                    <span id="modal-task-id-display" class="ml-3 px-2 py-0.5 bg-white/20 rounded text-sm text-white/90"></span>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- 表示/編集モード切替ボタン -->
                    <div id="modal-mode-toggle" class="toggle-btn-group hidden">
                        <button type="button" id="modal-view-btn" onclick="switchModalMode('view')" class="toggle-btn-item">
                            <i class="fas fa-eye mr-1"></i>表示
                        </button>
                        <button type="button" id="modal-edit-btn" onclick="switchModalMode('edit')" class="toggle-btn-item active">
                            <i class="fas fa-edit mr-1"></i>編集
                        </button>
                    </div>
                    <button type="button" onclick="closeTaskModal()" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- メインコンテンツ（2カラム） -->
        <div class="flex-1 flex overflow-hidden">
            <!-- 左側：タスク基本情報 -->
            <div class="w-1/2 border-r border-slate-200 overflow-y-auto bg-white">
                <div class="p-6">
                    <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>タスク基本情報
                    </h4>
                    <form id="task-form" onsubmit="return false;">
                        <input type="hidden" id="task-id" name="id" value="">
                        <input type="hidden" id="task-project-id" name="project_id" value="">

                        <!-- 工程選択 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                工程 <span class="text-rose-500">*</span>
                            </label>
                            <select id="task-process" name="process_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">工程を選択してください</option>
                                <?php foreach ($processes as $process): ?>
                                    <option value="<?= $process['id'] ?>"><?= esc($process['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- タスク名 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                タスク名 <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="task-name" name="task_name" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="タスク名を入力">
                        </div>

                        <!-- 担当者 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                担当者
                            </label>
                            <select id="task-assignee" name="assignee_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">担当者を選択</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= $member['id'] ?>"><?= esc($member['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- ステータス -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ステータス</label>
                            <select id="task-status" name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="not_started">未着手</option>
                                <option value="in_progress">進行中</option>
                                <option value="completed">完了</option>
                                <option value="on_hold">保留</option>
                            </select>
                        </div>

                        <!-- 予定：日付と工数 -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs font-bold text-blue-700 mb-2"><i class="fas fa-calendar mr-1"></i>予定</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">開始日</label>
                                    <input type="date" id="task-planned-start" name="planned_start_date" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">終了日</label>
                                    <input type="date" id="task-planned-end" name="planned_end_date" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">工数（日）</label>
                                    <input type="number" id="task-planned-man-days" name="planned_man_days" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" min="0" step="0.5">
                                </div>
                            </div>
                        </div>

                        <!-- 実績サマリー（自動計算） -->
                        <div id="modal-actual-section" class="mb-4 p-3 bg-emerald-50 rounded-lg">
                            <p class="text-xs font-bold text-emerald-700 mb-2"><i class="fas fa-check-circle mr-1"></i>実績</p>
                            <div class="grid grid-cols-4 gap-3 text-center">
                                <div>
                                    <p class="text-xs text-slate-500">実開始日</p>
                                    <p id="modal-actual-start" class="text-sm font-semibold text-slate-700">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">実終了日</p>
                                    <p id="modal-actual-end" class="text-sm font-semibold text-slate-700">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">実工数</p>
                                    <p id="modal-actual-days" class="text-sm font-semibold text-slate-700">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">進捗</p>
                                    <p id="modal-actual-progress" class="text-sm font-semibold text-emerald-600">0%</p>
                                </div>
                            </div>
                        </div>

                        <!-- 進捗率 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">進捗率</label>
                            <div class="flex items-center space-x-3">
                                <input type="range" id="task-progress-range" class="flex-1 h-2 bg-slate-200 rounded-lg" min="0" max="100" value="0" oninput="syncProgress(this.value, 'range')">
                                <input type="number" id="task-progress" name="progress" class="w-16 border border-slate-300 rounded px-2 py-1 text-sm text-center" min="0" max="100" value="0" oninput="syncProgress(this.value, 'number')">
                                <span class="text-xs text-slate-500">%</span>
                            </div>
                        </div>

                        <!-- 原価 -->
                        <div class="mb-4 p-3 bg-amber-50 rounded-lg">
                            <p class="text-xs font-bold text-amber-700 mb-2"><i class="fas fa-yen-sign mr-1"></i>原価</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">予定原価（円）</label>
                                    <input type="number" id="task-planned-cost" name="planned_cost" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="0" min="0" step="1000">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">実績原価（進捗換算）</label>
                                    <p id="modal-cost-actual" class="text-sm font-semibold text-amber-700 py-1.5">¥0</p>
                                </div>
                            </div>
                        </div>

                        <!-- 営業工数 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">営業工数（人日）</label>
                            <input type="number" id="task-sales-man-days" name="sales_man_days" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="0" min="0" step="0.5">
                        </div>

                        <!-- 備考 -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                備考
                            </label>
                            <textarea id="task-description" name="description" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" rows="2" placeholder="備考を入力（任意）"></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 右側：タスク履歴 -->
            <div class="w-1/2 overflow-hidden bg-slate-50 flex flex-col">
                <!-- 履歴ヘッダー -->
                <div class="p-4 border-b border-slate-200 bg-white flex items-center justify-between flex-shrink-0">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center">
                        <i class="fas fa-history mr-2 text-purple-500"></i>作業履歴
                    </h4>
                    <button type="button" id="history-add-btn" onclick="openHistoryEntryForm()" class="px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i>履歴追加
                    </button>
                </div>

                <!-- 履歴入力フォーム（非表示、追加時に表示） -->
                <div id="history-entry-form" class="p-4 border-b border-slate-200 bg-purple-50 hidden flex-shrink-0">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-bold text-purple-700"><i class="fas fa-edit mr-1"></i>履歴入力</p>
                        <button type="button" onclick="closeHistoryEntryForm()" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">作業日 <span class="text-rose-500">*</span></label>
                            <input type="date" id="history-date" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">作業時間（時間）</label>
                            <input type="number" id="history-hours" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm" placeholder="0" min="0" step="0.5">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-slate-600 mb-1">進捗率（%）</label>
                        <div class="flex items-center space-x-2">
                            <input type="range" id="history-progress" class="flex-1 h-2 bg-slate-200 rounded-lg" min="0" max="100" value="0" oninput="syncHistoryProgress(this.value, 'range')">
                            <input type="number" id="history-progress-num" class="w-16 border border-slate-300 rounded px-2 py-1 text-sm text-center" min="0" max="100" value="0" oninput="syncHistoryProgress(this.value, 'number')">
                            <span class="text-xs text-slate-500">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs text-slate-600 mb-1">内容・備考</label>
                        <textarea id="history-content" class="w-full border border-slate-300 rounded px-2 py-1.5 text-sm resize-none" rows="3" placeholder="作業内容、問題点、注意事項、報告事項など"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeHistoryEntryForm()" class="px-3 py-1.5 border border-slate-300 rounded text-xs text-slate-600 hover:bg-slate-100">キャンセル</button>
                        <button type="button" onclick="saveHistoryEntry()" class="px-3 py-1.5 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700">
                            <i class="fas fa-save mr-1"></i>保存
                        </button>
                    </div>
                </div>

                <!-- 履歴一覧 -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div id="history-list" class="space-y-3">
                        <!-- 履歴がJavaScriptで動的に生成される -->
                        <p id="no-history-msg" class="text-center text-sm text-slate-400 py-8">履歴はありません</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- フッター -->
        <div class="bg-white px-6 py-4 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-3">
                <button type="button" onclick="deleteTask()" id="btn-delete-task" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors hidden">
                    <i class="fas fa-trash mr-1"></i>削除
                </button>
                <span id="modal-created-info" class="text-sm text-slate-500"></span>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" id="modal-cancel-btn" onclick="closeTaskModal()" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                    キャンセル
                </button>
                <button type="button" id="modal-save-btn" onclick="saveTask(); return false;" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-indigo-700 shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i>保存
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// モーダルモード
let modalMode = 'edit'; // 'edit' or 'view'

// タスクモーダルを開く
function openTaskModal(taskId = null) {
    const modal = document.getElementById('task-modal');
    const form = document.getElementById('task-form');
    const title = document.getElementById('modal-title');
    const deleteBtn = document.getElementById('btn-delete-task');
    const modeToggle = document.getElementById('modal-mode-toggle');
    const taskIdDisplay = document.getElementById('modal-task-id-display');

    form.reset();
    document.getElementById('task-id').value = taskId || '';
    document.getElementById('task-project-id').value = projectId;
    document.getElementById('task-progress-range').value = 0;

    // 履歴をクリア
    document.getElementById('history-list').innerHTML = '<p id="no-history-msg" class="text-center text-sm text-slate-400 py-8">履歴はありません</p>';
    closeHistoryEntryForm();

    if (taskId) {
        taskIdDisplay.textContent = 'ID: ' + taskId;
        modeToggle.classList.remove('hidden');
        loadTaskData(taskId);
    } else {
        title.innerHTML = '<i class="fas fa-tasks mr-2"></i>新規タスク';
        taskIdDisplay.textContent = '';
        deleteBtn.classList.add('hidden');
        modeToggle.classList.add('hidden');
        // 実績セクションをリセット
        document.getElementById('modal-actual-start').textContent = '-';
        document.getElementById('modal-actual-end').textContent = '-';
        document.getElementById('modal-actual-days').textContent = '-';
        document.getElementById('modal-actual-progress').textContent = '0%';
        document.getElementById('modal-cost-actual').textContent = '¥0';
    }

    // カレンダー側のモードと連動（isEditModeはindex.phpで定義）
    // 編集モードなら編集モード、表示モードなら表示モードで開く
    const calendarIsEditMode = typeof isEditMode !== 'undefined' ? isEditMode : true;
    if (taskId) {
        // 既存タスクの場合はカレンダーのモードに合わせる（syncFromCalendar=trueでループ防止）
        switchModalMode(calendarIsEditMode ? 'edit' : 'view', true);
        deleteBtn.classList.toggle('hidden', !calendarIsEditMode);
    } else {
        // 新規タスクは常に編集モード（syncFromCalendar=trueでループ防止）
        switchModalMode('edit', true);
    }

    modal.classList.add('show');
}

// タスクモーダルを閉じる
function closeTaskModal() {
    document.getElementById('task-modal').classList.remove('show');
}

// モーダルモード切替（表示/編集）- カレンダー側と双方向連動
function switchModalMode(mode, syncFromCalendar = false) {
    modalMode = mode;
    const content = document.querySelector('.task-modal-content');
    const viewBtn = document.getElementById('modal-view-btn');
    const editBtn = document.getElementById('modal-edit-btn');
    const title = document.getElementById('modal-title');
    const saveBtn = document.getElementById('modal-save-btn');
    const historyAddBtn = document.getElementById('history-add-btn');
    const deleteBtn = document.getElementById('btn-delete-task');

    if (mode === 'view') {
        content.classList.add('modal-view-mode');
        viewBtn.classList.add('active');
        editBtn.classList.remove('active');
        title.innerHTML = '<i class="fas fa-tasks mr-2"></i>タスク詳細';
        saveBtn.classList.add('hidden');
        if (historyAddBtn) historyAddBtn.classList.add('hidden');
        if (deleteBtn) deleteBtn.classList.add('hidden');
    } else {
        content.classList.remove('modal-view-mode');
        viewBtn.classList.remove('active');
        editBtn.classList.add('active');
        title.innerHTML = '<i class="fas fa-tasks mr-2"></i>タスク編集';
        saveBtn.classList.remove('hidden');
        if (historyAddBtn) historyAddBtn.classList.remove('hidden');
        if (deleteBtn && document.getElementById('task-id').value) deleteBtn.classList.remove('hidden');
    }

    // カレンダー側のモードも連動（カレンダーからの同期でない場合のみ）
    if (!syncFromCalendar && typeof switchEditMode === 'function') {
        const calendarMode = mode === 'edit' ? 'edit' : 'view';
        // カレンダー側の現在のモードと異なる場合のみ切り替え
        if ((calendarMode === 'edit' && !isEditMode) || (calendarMode === 'view' && isEditMode)) {
            switchEditMode(calendarMode);
        }
    }
}

// 進捗率同期
function syncProgress(value, source) {
    const rangeInput = document.getElementById('task-progress-range');
    const numberInput = document.getElementById('task-progress');
    value = Math.max(0, Math.min(100, parseInt(value) || 0));
    rangeInput.value = value;
    numberInput.value = value;
    updateCostActual();
}

// 履歴進捗率同期
function syncHistoryProgress(value, source) {
    const rangeInput = document.getElementById('history-progress');
    const numberInput = document.getElementById('history-progress-num');
    value = Math.max(0, Math.min(100, parseInt(value) || 0));
    rangeInput.value = value;
    numberInput.value = value;
}

// 実績原価を更新
function updateCostActual() {
    const cost = parseFloat(document.getElementById('task-planned-cost').value) || 0;
    const progress = parseInt(document.getElementById('task-progress').value) || 0;
    const actualCost = Math.round(cost * progress / 100);
    document.getElementById('modal-cost-actual').textContent = '¥' + actualCost.toLocaleString();
}

// 履歴入力フォームを開く
function openHistoryEntryForm() {
    const form = document.getElementById('history-entry-form');
    form.classList.remove('hidden');
    // 今日の日付をセット
    document.getElementById('history-date').value = new Date().toISOString().split('T')[0];
    // 現在の進捗率をセット
    const currentProgress = document.getElementById('task-progress').value || 0;
    document.getElementById('history-progress').value = currentProgress;
    document.getElementById('history-progress-num').value = currentProgress;
}

// 履歴入力フォームを閉じる
function closeHistoryEntryForm() {
    const form = document.getElementById('history-entry-form');
    form.classList.add('hidden');
    document.getElementById('history-date').value = '';
    document.getElementById('history-hours').value = '';
    document.getElementById('history-progress').value = 0;
    document.getElementById('history-progress-num').value = 0;
    document.getElementById('history-content').value = '';
}

// 履歴エントリを保存
async function saveHistoryEntry() {
    const taskId = document.getElementById('task-id').value;
    if (!taskId) {
        showToast('タスクを先に保存してください', 'warning');
        return;
    }

    const date = document.getElementById('history-date').value;
    if (!date) {
        showToast('作業日を入力してください', 'warning');
        return;
    }

    const data = {
        task_id: taskId,
        work_date: date,
        work_hours: parseFloat(document.getElementById('history-hours').value) || 0,
        progress: parseInt(document.getElementById('history-progress-num').value) || 0,
        content: document.getElementById('history-content').value || ''
    };

    // 保存ボタンを取得してローディング表示
    const saveBtn = document.querySelector('#history-entry-form button[onclick="saveHistoryEntry()"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>保存中...';
    }

    try {
        const response = await fetch('<?= base_url('api/task-histories') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast('作業履歴を追加しました', 'success');
            closeHistoryEntryForm();
            loadTaskHistories(taskId);
            // 進捗率を更新
            document.getElementById('task-progress').value = data.progress;
            document.getElementById('task-progress-range').value = data.progress;
            updateCostActual();
        } else {
            showToast(result.error || '履歴の追加に失敗しました', 'error');
            // ボタンを元に戻す
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>保存';
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('通信エラーが発生しました', 'error');
        // ボタンを元に戻す
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>保存';
        }
    }
}

// タスク履歴を読み込む
async function loadTaskHistories(taskId) {
    try {
        const response = await fetch(`<?= base_url('api/task-histories') ?>?task_id=${taskId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        const historyList = document.getElementById('history-list');

        if (result.success && result.data && result.data.length > 0) {
            historyList.innerHTML = result.data.map(h => `
                <div class="bg-white rounded-lg p-3 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-purple-600">
                            <i class="fas fa-calendar mr-1"></i>${formatDisplayDate(h.work_date)}
                        </span>
                        <div class="flex items-center space-x-2">
                            ${h.work_hours ? `<span class="text-xs text-slate-500"><i class="fas fa-clock mr-1"></i>${h.work_hours}h</span>` : ''}
                            <span class="text-xs font-semibold ${h.progress >= 100 ? 'text-emerald-600' : 'text-blue-600'}">${h.progress}%</span>
                        </div>
                    </div>
                    ${h.content ? `<p class="text-sm text-slate-600">${escapeHtml(h.content)}</p>` : ''}
                    <div class="text-xs text-slate-400 mt-2">
                        ${h.created_by_name || ''}
                    </div>
                </div>
            `).join('');
        } else {
            historyList.innerHTML = '<p id="no-history-msg" class="text-center text-sm text-slate-400 py-8">履歴はありません</p>';
        }
    } catch (error) {
        console.error('Error loading histories:', error);
    }
}

// タスクデータを読み込む
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
            document.getElementById('task-progress-range').value = task.progress || 0;
            document.getElementById('task-description').value = task.description || '';

            // 実績セクションを更新
            document.getElementById('modal-actual-start').textContent = task.actual_start_date ? formatDisplayDate(task.actual_start_date) : '-';
            document.getElementById('modal-actual-end').textContent = task.actual_end_date ? formatDisplayDate(task.actual_end_date) : '-';
            document.getElementById('modal-actual-days').textContent = task.actual_man_days ? task.actual_man_days + '日' : '-';
            document.getElementById('modal-actual-progress').textContent = (task.progress || 0) + '%';
            document.getElementById('modal-actual-progress').className = 'text-sm font-semibold ' + ((task.progress || 0) >= 100 ? 'text-emerald-600' : 'text-blue-600');

            updateCostActual();

            // 履歴を読み込む
            loadTaskHistories(taskId);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// タスクを保存
async function saveTask() {
    // バリデーション（ローディング表示前にチェック）
    const taskName = document.getElementById('task-name').value;
    const processId = document.getElementById('task-process').value;

    if (!taskName || !taskName.trim()) {
        showToast('タスク名を入力してください', 'warning');
        return;
    }
    if (!processId) {
        showToast('工程を選択してください', 'warning');
        return;
    }

    // ローディング表示
    showLoading('保存中...');

    // ブラウザに再描画の時間を与える
    await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));

    try {
        const taskId = document.getElementById('task-id').value;

        const data = {
            project_id: document.getElementById('task-project-id').value,
            task_name: taskName.trim(),
            process_id: processId,
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
            hideLoading();
            closeTaskModal();
            showToast(taskId ? 'タスクを更新しました' : 'タスクを作成しました', 'success');
            // トーストを表示してからリロード
            setTimeout(() => location.reload(), 1500);
        } else {
            hideLoading();
            // エラーメッセージを取得
            let errorMsg = '保存に失敗しました';
            if (result.errors && typeof result.errors === 'object') {
                const errorValues = Object.values(result.errors);
                if (errorValues.length > 0) {
                    errorMsg = errorValues.join('\n');
                }
            } else if (result.error) {
                errorMsg = result.error;
            }
            showToast(errorMsg, 'error');
        }
    } catch (error) {
        console.error('SaveTask Error:', error);
        hideLoading();
        showToast('通信エラーが発生しました', 'error');
    }
}
window.saveTask = saveTask;

// タスクを削除
async function deleteTask() {
    const taskId = document.getElementById('task-id').value;
    if (!taskId) return;

    if (!confirm('このタスクを削除しますか？サブタスクも削除されます。')) return;

    showLoading('削除中...');

    try {
        const response = await fetch(`<?= base_url('api/tasks') ?>/${taskId}`, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await response.json();

        if (result.success) {
            hideLoading();
            showToast('タスクを削除しました', 'success');
            closeTaskModal();
            // トーストを表示してからリロード
            setTimeout(() => location.reload(), 1500);
        } else {
            hideLoading();
            showToast(result.error || '削除に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        hideLoading();
        showToast('通信エラーが発生しました', 'error');
    }
}

// HTMLエスケープ
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>
