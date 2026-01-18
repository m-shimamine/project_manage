/**
 * TaskListApp - タスク一覧画面のメインアプリケーションクラス
 * 各コンポーネントを統合し、UIイベントを管理する
 */
class TaskListApp {
    constructor(options = {}) {
        // 設定
        this.projectId = options.projectId || null;
        this.apiBaseUrl = options.apiBaseUrl || '/api/tasks';
        this.csrfToken = options.csrfToken || '';
        this.csrfHash = options.csrfHash || '';

        // 初期データ
        this.initialTasks = options.tasks || [];
        this.processes = options.processes || [];
        this.members = options.members || [];

        // 状態
        this.isEditMode = false;
        this.activeFilters = {
            process: '',
            assignee: '',
            status: '',
            delay: '',
            search: ''
        };

        // コンポーネント
        this.dataManager = null;
        this.tableRenderer = null;
        this.dragDropHandler = null;
        this.contextMenu = null;
        this.keyboardHandler = null;

        // DOM要素キャッシュ
        this.elements = {};

        // 初期化
        this.init();
    }

    /**
     * 初期化
     */
    init() {
        this.cacheElements();
        this.initDataManager();
        this.initTableRenderer();
        this.initDragDropHandler();
        this.initContextMenu();
        this.initKeyboardHandler();
        this.bindEvents();
        this.render();
    }

    /**
     * DOM要素をキャッシュ
     */
    cacheElements() {
        this.elements = {
            // モード切り替え
            viewModeBtn: document.getElementById('btn-view-mode'),
            editModeBtn: document.getElementById('btn-edit-mode'),
            editActions: document.getElementById('edit-actions'),
            viewActions: document.getElementById('view-actions'),

            // 検索・フィルター
            searchInput: document.getElementById('search-input'),
            searchPanel: document.getElementById('search-panel'),
            searchToggle: document.getElementById('search-toggle'),
            filterAssignee: document.getElementById('filter-assignee'),
            filterStatus: document.getElementById('filter-status'),
            filterProcess: document.getElementById('filter-process'),

            // 選択アクションバー
            selectionActionBar: document.getElementById('selection-action-bar'),
            selectedCount: document.getElementById('selected-count'),

            // チェックボックス
            selectAll: document.getElementById('select-all'),

            // テーブル
            tableBody: document.getElementById('task-tbody'),
            tableContainer: document.getElementById('table-container'),

            // モーダル
            taskModal: document.getElementById('task-modal'),
            bulkEditModal: document.getElementById('bulk-edit-modal'),
            progressSummaryModal: document.getElementById('progress-summary-modal'),

            // 右クリックメニュー
            contextMenu: document.getElementById('context-menu'),
            subtaskContextMenu: document.getElementById('subtask-context-menu'),

            // Undoボタン
            undoBtn: document.getElementById('undo-btn'),

            // クリップボードステータス
            clipboardStatus: document.getElementById('clipboard-status'),

            // トーストコンテナ
            toastContainer: document.getElementById('toast-container')
        };
    }

    /**
     * DataManagerを初期化
     */
    initDataManager() {
        this.dataManager = new TaskDataManager({
            projectId: this.projectId,
            tasks: JSON.parse(JSON.stringify(this.initialTasks)), // Deep copy
            processes: this.processes,
            members: this.members,
            apiBaseUrl: this.apiBaseUrl,
            csrfToken: this.csrfToken,
            csrfHash: this.csrfHash,
            onDataChange: (tasks) => this.onDataChange(tasks),
            onStatsUpdate: (stats) => this.onStatsUpdate(stats)
        });
    }

    /**
     * TableRendererを初期化
     */
    initTableRenderer() {
        this.tableRenderer = new TaskTableRenderer({
            tableBody: this.elements.tableBody,
            dataManager: this.dataManager,
            processes: this.processes,
            members: this.members,
            onRowClick: (taskId, task) => this.openTaskModal(taskId),
            onRowContextMenu: (e, task, row) => this.showContextMenu(e, task, row),
            onCheckboxChange: (taskId, checked) => this.onCheckboxChange(taskId, checked),
            onFieldChange: (taskId, field, value) => this.onFieldChange(taskId, field, value)
        });
    }

    /**
     * フィールド変更時のコールバック
     */
    onFieldChange(taskId, field, value) {
        // Undo用に履歴は DataManager 内で自動保存されている
        // 必要であれば追加の処理をここに記述
    }

    /**
     * DragDropHandlerを初期化
     */
    initDragDropHandler() {
        if (typeof TaskDragDropHandler !== 'undefined') {
            this.dragDropHandler = new TaskDragDropHandler({
                app: this,
                tableBody: this.elements.tableBody,
                dataManager: this.dataManager,
                tableRenderer: this.tableRenderer,
                onReorder: (taskId, oldIndex, newIndex) => {
                    // トースト表示は不要（ユーザー要望）
                }
            });
        }
    }

    /**
     * ContextMenuを初期化
     */
    initContextMenu() {
        if (typeof TaskContextMenu !== 'undefined') {
            this.contextMenu = new TaskContextMenu({
                app: this,
                dataManager: this.dataManager,
                tableRenderer: this.tableRenderer,
                taskMenu: this.elements.contextMenu,
                subtaskMenu: this.elements.subtaskContextMenu
            });
        }
    }

    /**
     * KeyboardHandlerを初期化
     */
    initKeyboardHandler() {
        if (typeof TaskKeyboardHandler !== 'undefined') {
            this.keyboardHandler = new TaskKeyboardHandler({
                app: this,
                dataManager: this.dataManager,
                tableRenderer: this.tableRenderer
            });
        }
    }

    /**
     * イベントをバインド
     */
    bindEvents() {
        // 表示/編集モード切り替え
        if (this.elements.viewModeBtn) {
            this.elements.viewModeBtn.addEventListener('click', () => this.switchViewMode('view'));
        }
        if (this.elements.editModeBtn) {
            this.elements.editModeBtn.addEventListener('click', () => this.switchViewMode('edit'));
        }

        // 検索パネル切り替え
        if (this.elements.searchToggle) {
            this.elements.searchToggle.addEventListener('click', () => this.toggleSearchPanel());
        }

        // 検索入力
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('keyup', (e) => {
                this.activeFilters.search = e.target.value.toLowerCase();
                this.applyFilters();
            });
        }

        // パネルフィルター
        ['filterAssignee', 'filterStatus', 'filterProcess'].forEach(key => {
            if (this.elements[key]) {
                this.elements[key].addEventListener('change', () => this.applyPanelFilters());
            }
        });

        // 全選択
        if (this.elements.selectAll) {
            this.elements.selectAll.addEventListener('change', (e) => {
                this.tableRenderer.setAllSelection(e.target.checked);
                this.updateSelectionBar();
            });
        }

        // ヘッダーフィルタードロップダウン
        document.querySelectorAll('.header-filter').forEach(header => {
            header.addEventListener('click', (e) => this.toggleHeaderFilter(e));
        });

        // フィルタードロップダウンのアイテム
        document.querySelectorAll('.filter-dropdown-item').forEach(item => {
            item.addEventListener('click', (e) => this.selectHeaderFilter(e));
        });

        // ドキュメント全体のクリック（メニューを閉じる）
        document.addEventListener('click', (e) => {
            // コンテキストメニューを閉じる
            if (!e.target.closest('.context-menu')) {
                this.hideContextMenus();
            }
            // ヘッダーフィルタードロップダウンを閉じる
            if (!e.target.closest('.header-filter')) {
                document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.remove('show'));
            }
        });

        // キーボードショートカット
        document.addEventListener('keydown', (e) => this.handleKeyboardShortcut(e));

        // 右クリック
        document.addEventListener('contextmenu', (e) => this.handleContextMenu(e));
    }

    /**
     * 描画
     */
    render() {
        const tasks = this.dataManager.getTasks();
        this.tableRenderer.render(tasks);

        // ドラッグ&ドロップハンドラを更新（テーブル再描画後に必要）
        if (this.dragDropHandler) {
            this.dragDropHandler.refresh();
        }
    }

    /**
     * データ変更時のコールバック
     */
    onDataChange(tasks) {
        this.applyFilters();
        this.tableRenderer.updateUndoButton(this.dataManager.canUndo());
    }

    /**
     * 統計更新時のコールバック
     */
    onStatsUpdate(stats) {
        this.tableRenderer.updateFooterStats(stats);
    }

    // ========== 表示/編集モード ==========

    /**
     * 表示モードを切り替え
     */
    switchViewMode(mode) {
        this.isEditMode = (mode === 'edit');
        this.tableRenderer.setEditMode(this.isEditMode);

        if (this.elements.viewModeBtn && this.elements.editModeBtn) {
            if (this.isEditMode) {
                this.elements.viewModeBtn.className = 'px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 border-r border-slate-300';
                this.elements.editModeBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600';
            } else {
                this.elements.viewModeBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600';
                this.elements.editModeBtn.className = 'px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 border-l border-slate-300';
            }
        }

        if (this.elements.editActions) {
            this.elements.editActions.classList.toggle('hidden', !this.isEditMode);
        }
        if (this.elements.viewActions) {
            this.elements.viewActions.classList.toggle('hidden', this.isEditMode);
        }

        // 表示モードに切り替え時、選択状態と選択バーをクリア
        if (!this.isEditMode) {
            this.clearSelection();
        }

        // テーブルを再描画（インライン編集フィールドの表示/非表示切り替え）
        this.render();

        // ドラッグ&ドロップハンドラーを更新
        if (this.dragDropHandler) {
            this.dragDropHandler.refresh();
        }
    }

    // ========== フィルター機能 ==========

    /**
     * 検索パネルを切り替え
     */
    toggleSearchPanel() {
        if (this.elements.searchPanel) {
            this.elements.searchPanel.classList.toggle('show');
        }
    }

    /**
     * ヘッダーフィルタードロップダウンを切り替え
     */
    toggleHeaderFilter(event) {
        event.stopPropagation();

        const header = event.currentTarget;
        const filterType = this.getFilterTypeFromHeader(header);
        const dropdown = document.getElementById('filter-dropdown-' + filterType);

        // 他のドロップダウンを閉じる
        document.querySelectorAll('.filter-dropdown').forEach(d => {
            if (d !== dropdown) d.classList.remove('show');
        });

        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    /**
     * ヘッダーからフィルタータイプを取得
     */
    getFilterTypeFromHeader(header) {
        // data-filter-type属性から取得
        return header.dataset.filterType || '';
    }

    /**
     * ヘッダーフィルターを選択
     */
    selectHeaderFilter(event) {
        event.stopPropagation();

        const item = event.currentTarget;
        const dropdown = item.closest('.filter-dropdown');
        const filterType = dropdown.id.replace('filter-dropdown-', '');
        const value = item.dataset.value || '';

        this.activeFilters[filterType] = value;

        // ドロップダウンを閉じる
        dropdown.classList.remove('show');

        // ヘッダーのスタイル更新
        const header = document.querySelector(`th[data-filter-type="${filterType}"]`);
        if (header) {
            header.classList.toggle('has-filter', !!value);
        }

        // パネルフィルターと同期
        const panelSelect = document.getElementById('filter-' + filterType);
        if (panelSelect) {
            panelSelect.value = value;
        }

        this.applyFilters();
    }

    /**
     * パネルフィルターを適用
     */
    applyPanelFilters() {
        this.activeFilters.assignee = this.elements.filterAssignee?.value || '';
        this.activeFilters.status = this.elements.filterStatus?.value || '';
        this.activeFilters.process = this.elements.filterProcess?.value || '';

        // ヘッダーのスタイル更新
        ['process', 'assignee', 'status'].forEach(type => {
            const header = document.querySelector(`th[data-filter-type="${type}"]`);
            if (header) {
                header.classList.toggle('has-filter', !!this.activeFilters[type]);
            }
        });

        this.applyFilters();
    }

    /**
     * フィルターを適用
     */
    applyFilters() {
        const tasks = this.dataManager.getTasks();

        tasks.forEach(task => {
            const visible = this.dataManager.matchesFilter(task, this.activeFilters);
            this.tableRenderer.setRowVisibility(task.id, visible);

            if (task.subtasks) {
                task.subtasks.forEach(subtask => {
                    const subtaskVisible = this.dataManager.matchesFilter(subtask, this.activeFilters);
                    this.tableRenderer.setRowVisibility(subtask.id, subtaskVisible);
                });
            }
        });

        this.updateFilterCount();
    }

    /**
     * フィルター件数を更新
     */
    updateFilterCount() {
        const visibleRows = document.querySelectorAll('#task-tbody .task-row:not([style*="display: none"])');
        const totalRows = document.querySelectorAll('#task-tbody .task-row');

        if (visibleRows.length < totalRows.length) {
            this.showToast(`${visibleRows.length}件表示中（全${totalRows.length}件）`, 'info');
        }
    }

    /**
     * フィルターをクリア
     */
    clearFilter() {
        this.activeFilters = { process: '', assignee: '', status: '', delay: '', search: '' };

        // UIをリセット
        document.querySelectorAll('#search-panel select').forEach(el => el.selectedIndex = 0);
        if (this.elements.searchInput) {
            this.elements.searchInput.value = '';
        }

        // ヘッダーのスタイルをリセット
        document.querySelectorAll('.header-filter').forEach(h => h.classList.remove('has-filter'));

        // 全行表示
        this.tableRenderer.showAllRows();

        this.showToast('フィルターをクリアしました', 'info');
    }

    // ========== 選択管理 ==========

    /**
     * チェックボックス変更時
     */
    onCheckboxChange(taskId, checked) {
        this.tableRenderer.setRowSelection(taskId, checked);
        this.updateSelectionBar();
    }

    /**
     * 選択アクションバーを更新
     */
    updateSelectionBar() {
        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        const count = selectedIds.length;

        if (this.elements.selectionActionBar) {
            // 編集モードでない場合は常に非表示
            const shouldShow = this.isEditMode && count > 0;
            this.elements.selectionActionBar.classList.toggle('hidden', !shouldShow);
        }
        if (this.elements.selectedCount) {
            this.elements.selectedCount.textContent = count;
        }
    }

    /**
     * 選択をクリア
     */
    clearSelection() {
        this.tableRenderer.setAllSelection(false);
        if (this.elements.selectAll) {
            this.elements.selectAll.checked = false;
        }
        this.updateSelectionBar();
    }

    // ========== 右クリックメニュー ==========

    /**
     * コンテキストメニューを表示
     */
    showContextMenu(e, task, row) {
        // TaskContextMenuのshowメソッドを使用（targetTaskIdを設定するため）
        if (this.contextMenu) {
            this.contextMenu.show(e, task.id, row);
        } else {
            // TaskContextMenuが無い場合はフォールバック
            const isSubtask = row.classList.contains('subtask-row');
            const menu = isSubtask ? this.elements.subtaskContextMenu : this.elements.contextMenu;

            this.hideContextMenus();

            if (menu) {
                menu.style.left = e.pageX + 'px';
                menu.style.top = e.pageY + 'px';
                menu.classList.add('show');
                menu.dataset.taskId = task.id;
            }
        }
    }

    /**
     * 右クリックイベントを処理
     */
    handleContextMenu(e) {
        const taskRow = e.target.closest('.task-row');
        if (taskRow && this.isEditMode) {
            e.preventDefault();
            const taskId = taskRow.dataset.taskId;
            const task = this.dataManager.getTaskById(taskId);
            if (task) {
                this.showContextMenu(e, task, taskRow);
            }
        }
    }

    /**
     * コンテキストメニューを非表示
     */
    hideContextMenus() {
        document.querySelectorAll('.context-menu').forEach(m => m.classList.remove('show'));
    }

    // ========== キーボードショートカット ==========

    /**
     * キーボードショートカットを処理
     * 注: Ctrl+C/V/ZはTaskKeyboardHandlerで処理するため、ここではEscapeのみ処理
     */
    handleKeyboardShortcut(e) {
        // Escape: メニューやモーダルを閉じる
        if (e.key === 'Escape') {
            this.hideContextMenus();
            this.closeBulkEditModal();
            this.closeTaskModal();
            this.closeProgressSummaryModal();
        }
        // Ctrl+C/V/ZはTaskKeyboardHandlerで処理
    }

    /**
     * モーダルが開いているか確認
     */
    isModalOpen() {
        const modals = ['import-modal', 'export-modal', 'bulk-date-modal', 'bulk-edit-modal', 'task-modal', 'progress-summary-modal'];
        return modals.some(id => {
            const modal = document.getElementById(id);
            return modal && !modal.classList.contains('hidden');
        });
    }

    // ========== Undo機能 ==========

    /**
     * Undo実行
     */
    undo() {
        if (this.dataManager.undo()) {
            this.render();
            this.showToast('操作を元に戻しました', 'info');
        } else {
            this.showToast('元に戻す操作がありません', 'warning');
        }
    }

    // ========== クリップボード機能 ==========

    /**
     * クリップボードにコピー
     */
    copyToClipboard(taskIds) {
        this.clipboard = taskIds.map(id => {
            const task = this.dataManager.getTaskById(id);
            return task ? { ...task } : null;
        }).filter(t => t !== null);

        // ハイライト表示
        taskIds.forEach(id => this.tableRenderer.highlightRow(id, 'copied'));

        this.showClipboardStatus();
        this.showToast(`${this.clipboard.length}件をコピーしました`, 'success');
    }

    /**
     * クリップボードからペースト
     */
    pasteFromClipboard() {
        if (!this.clipboard || this.clipboard.length === 0) {
            this.showToast('コピーされたタスクがありません', 'warning');
            return;
        }

        this.clipboard.forEach(task => {
            const newTask = {
                ...task,
                id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                task_name: task.task_name + ' (コピー)'
            };
            this.dataManager.addTask(newTask);
        });

        this.render();
        this.showToast(`${this.clipboard.length}件を貼り付けました`, 'success');
    }

    /**
     * クリップボードステータスを表示
     */
    showClipboardStatus() {
        if (this.elements.clipboardStatus) {
            this.elements.clipboardStatus.classList.remove('hidden');
            setTimeout(() => {
                this.elements.clipboardStatus.classList.add('hidden');
            }, 3000);
        }
    }

    // ========== モーダル管理 ==========

    /**
     * タスクモーダルを開く
     */
    openTaskModal(taskId = null) {
        if (typeof window.openTaskModal === 'function') {
            window.openTaskModal(taskId);
        }
    }

    /**
     * タスクモーダルを閉じる
     */
    closeTaskModal() {
        if (this.elements.taskModal) {
            this.elements.taskModal.classList.add('hidden');
        }
    }

    /**
     * 一括編集モーダルを開く
     */
    openBulkEditModal() {
        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        if (selectedIds.length === 0) {
            this.showToast('タスクを選択してください', 'warning');
            return;
        }

        document.getElementById('bulk-edit-count').textContent = selectedIds.length;
        if (this.elements.bulkEditModal) {
            this.elements.bulkEditModal.classList.remove('hidden');
        }
    }

    /**
     * 一括編集モーダルを閉じる
     */
    closeBulkEditModal() {
        if (this.elements.bulkEditModal) {
            this.elements.bulkEditModal.classList.add('hidden');
        }
    }

    /**
     * 一括編集を適用
     */
    applyBulkEdit() {
        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        const updates = {};

        const bulkAssignee = document.getElementById('bulk-assignee')?.value;
        const bulkStatus = document.getElementById('bulk-status')?.value;
        const bulkProgress = document.getElementById('bulk-progress')?.value;
        const bulkProcess = document.getElementById('bulk-process')?.value;

        if (bulkAssignee) updates.assignee_id = bulkAssignee;
        if (bulkStatus) updates.status = bulkStatus;
        if (bulkProgress) updates.progress = parseInt(bulkProgress);
        if (bulkProcess) updates.process_id = bulkProcess;

        if (Object.keys(updates).length === 0) {
            this.showToast('変更する項目を選択してください', 'warning');
            return;
        }

        this.dataManager.bulkUpdate(selectedIds, updates);
        this.render();
        this.closeBulkEditModal();
        this.clearSelection();
    }

    /**
     * 一括削除
     */
    bulkDelete() {
        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        if (selectedIds.length === 0) {
            this.showToast('タスクを選択してください', 'warning');
            return;
        }

        if (!confirm(`${selectedIds.length}件のタスクを削除しますか？`)) {
            return;
        }

        this.dataManager.bulkDelete(selectedIds);
        this.render();
        this.clearSelection();
    }

    /**
     * プロジェクトの進捗状況モーダルを開く
     */
    openProgressSummaryModal() {
        if (this.elements.progressSummaryModal) {
            // 統計情報を更新
            const stats = this.dataManager.calculateStats();
            const statsByProcess = this.dataManager.calculateStatsByProcess();

            // 進捗状況を更新
            document.getElementById('progress-overall').textContent = stats.progress_avg + '%';
            document.getElementById('progress-overall-bar').style.width = stats.progress_avg + '%';
            document.getElementById('progress-completed').textContent = stats.completed;
            document.getElementById('progress-total').textContent = stats.total;
            document.getElementById('progress-stat-completed').textContent = stats.completed;
            document.getElementById('progress-stat-in-progress').textContent = stats.in_progress;
            document.getElementById('progress-stat-not-started').textContent = stats.not_started;
            document.getElementById('progress-stat-delayed').textContent = stats.delayed;

            // 工程別進捗を更新
            const processContainer = document.getElementById('progress-by-process');
            if (processContainer) {
                processContainer.innerHTML = '';
                Object.values(statsByProcess).forEach(stat => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center';
                    div.innerHTML = `
                        <div class="w-24 text-sm text-slate-600 truncate">${stat.name}</div>
                        <div class="flex-1 mx-3">
                            <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full" style="width: ${stat.progress_avg}%"></div>
                            </div>
                        </div>
                        <div class="w-12 text-right text-sm text-slate-600">${stat.progress_avg}%</div>
                    `;
                    processContainer.appendChild(div);
                });
            }

            this.elements.progressSummaryModal.classList.remove('hidden');
        }
    }

    /**
     * プロジェクトの進捗状況モーダルを閉じる
     */
    closeProgressSummaryModal() {
        if (this.elements.progressSummaryModal) {
            this.elements.progressSummaryModal.classList.add('hidden');
        }
    }

    // ========== 編集アクション ==========

    /**
     * 編集をキャンセル
     */
    cancelEdit() {
        // 元のデータに戻す
        this.dataManager.tasks = JSON.parse(JSON.stringify(this.initialTasks));
        this.dataManager.clearHistory();
        this.render();
        this.switchViewMode('view');
        this.showToast('編集をキャンセルしました', 'info');
    }

    /**
     * 全タスクを保存
     */
    async saveAllTasks() {
        try {
            const tasks = this.dataManager.getTasks();

            // 保存用のタスクデータを準備（サブタスクも含む）
            const tasksToSave = [];

            tasks.forEach((task, index) => {
                // 親タスクのデータを準備
                const taskData = {
                    id: String(task.id).startsWith('new_') ? null : task.id,
                    project_id: task.project_id || this.projectId,
                    parent_id: null,
                    task_name: task.task_name,
                    process_id: task.process_id || null,
                    assignee_id: task.assignee_id || null,
                    status: task.status || 'not_started',
                    progress: task.progress || 0,
                    planned_start_date: task.planned_start_date || null,
                    planned_end_date: task.planned_end_date || null,
                    planned_man_days: task.planned_man_days || null,
                    sales_man_days: task.sales_man_days || null,
                    planned_cost: task.planned_cost || null,
                    actual_start_date: task.actual_start_date || null,
                    actual_end_date: task.actual_end_date || null,
                    actual_man_days: task.actual_man_days || null,
                    actual_cost: task.actual_cost || null,
                    description: task.description || null,
                    sort_order: index
                };
                tasksToSave.push(taskData);

                // サブタスクも追加
                if (task.subtasks && task.subtasks.length > 0) {
                    task.subtasks.forEach((subtask, subIndex) => {
                        const subtaskData = {
                            id: String(subtask.id).startsWith('new_') ? null : subtask.id,
                            project_id: subtask.project_id || this.projectId,
                            parent_id: String(task.id).startsWith('new_') ? `temp_${index}` : task.id,
                            task_name: subtask.task_name,
                            process_id: subtask.process_id || null,
                            assignee_id: subtask.assignee_id || null,
                            status: subtask.status || 'not_started',
                            progress: subtask.progress || 0,
                            planned_start_date: subtask.planned_start_date || null,
                            planned_end_date: subtask.planned_end_date || null,
                            planned_man_days: subtask.planned_man_days || null,
                            sales_man_days: subtask.sales_man_days || null,
                            planned_cost: subtask.planned_cost || null,
                            actual_start_date: subtask.actual_start_date || null,
                            actual_end_date: subtask.actual_end_date || null,
                            actual_man_days: subtask.actual_man_days || null,
                            actual_cost: subtask.actual_cost || null,
                            description: subtask.description || null,
                            sort_order: subIndex
                        };
                        tasksToSave.push(subtaskData);
                    });
                }
            });

            // 削除されたタスクIDを取得
            const deletedTaskIds = this.dataManager.getDeletedTaskIds();

            // APIに送信
            const response = await fetch(this.dataManager.apiBaseUrl + '/bulk-update', {
                method: 'POST',
                headers: this.dataManager.getApiHeaders(),
                body: JSON.stringify({
                    tasks: tasksToSave,
                    deleted_task_ids: deletedTaskIds
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('保存しました', 'success');
                this.dataManager.clearHistory();
                this.dataManager.clearDeletedTaskIds();
                this.switchViewMode('view');
                location.reload();
            } else {
                const errorMsg = result.error || result.errors || '保存に失敗しました';
                this.showToast(typeof errorMsg === 'object' ? JSON.stringify(errorMsg) : errorMsg, 'error');
            }
        } catch (error) {
            console.error('Save error:', error);
            this.showToast('保存に失敗しました: ' + error.message, 'error');
        }
    }

    // ========== 行操作（右クリックメニュー） ==========

    /**
     * 現在のコンテキストメニューのタスクIDを取得
     */
    getContextMenuTaskId() {
        // 表示中のメニューからtaskIdを取得
        const menu = document.querySelector('.context-menu.show');
        return menu?.dataset.taskId || null;
    }

    /**
     * 上に行を追加
     */
    addRowAbove() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (!this.isEditMode) {
            this.switchViewMode('edit');
        }

        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == taskId);
        if (index === -1) return;

        const newTask = this.createEmptyTask();
        this.dataManager.addTask(newTask, index);
        this.render();
        this.hideContextMenus();
    }

    /**
     * 下に行を追加
     */
    addRowBelow() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (!this.isEditMode) {
            this.switchViewMode('edit');
        }

        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == taskId);
        if (index === -1) return;

        const newTask = this.createEmptyTask();
        this.dataManager.addTask(newTask, index + 1);
        this.render();
        this.hideContextMenus();
    }

    /**
     * サブタスクを追加
     */
    addSubtaskFromMenu() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (!this.isEditMode) {
            this.switchViewMode('edit');
        }

        const newSubtask = this.createEmptyTask();
        this.dataManager.addSubtask(taskId, newSubtask);
        this.render();
        this.hideContextMenus();
    }

    /**
     * コピーして上に追加
     */
    copyTaskAbove() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (!this.isEditMode) {
            this.switchViewMode('edit');
        }

        const task = this.dataManager.getTaskById(taskId);
        if (!task) return;

        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == taskId);
        if (index === -1) return;

        const newTask = {
            ...task,
            id: 'new_' + Date.now(),
            task_name: task.task_name + ' (コピー)',
            subtasks: []
        };
        this.dataManager.addTask(newTask, index);
        this.render();
        this.hideContextMenus();
    }

    /**
     * コピーして下に追加
     */
    copyTaskBelow() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (!this.isEditMode) {
            this.switchViewMode('edit');
        }

        const task = this.dataManager.getTaskById(taskId);
        if (!task) return;

        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == taskId);
        if (index === -1) return;

        const newTask = {
            ...task,
            id: 'new_' + Date.now(),
            task_name: task.task_name + ' (コピー)',
            subtasks: []
        };
        this.dataManager.addTask(newTask, index + 1);
        this.render();
        this.hideContextMenus();
    }

    /**
     * タスクを削除（メニューから）
     */
    deleteTaskFromMenu() {
        const taskId = this.getContextMenuTaskId();
        if (!taskId) return;

        if (confirm('このタスクを削除しますか？')) {
            this.dataManager.deleteTask(taskId);
            this.render();
        }
        this.hideContextMenus();
    }

    /**
     * 空のタスクを作成
     */
    createEmptyTask() {
        return {
            id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            project_id: this.projectId,
            task_name: '新規タスク',
            status: 'not_started',
            progress: 0,
            process_id: null,
            assignee_id: null,
            planned_start_date: null,
            planned_end_date: null,
            planned_man_days: null,
            sales_man_days: null,
            planned_cost: null,
            actual_start_date: null,
            actual_end_date: null,
            actual_man_days: null,
            actual_cost: null,
            description: '',
            subtasks: []
        };
    }

    // ========== ユーティリティ ==========

    /**
     * トースト表示
     */
    showToast(message, type = 'info') {
        if (!this.elements.toastContainer) return;

        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            info: 'info-circle',
            warning: 'exclamation-triangle'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon"><i class="fas fa-${icons[type] || icons.info}"></i></div>
            <div class="toast-message">${message}</div>
            <div class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></div>
        `;

        this.elements.toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskListApp = TaskListApp;
}
