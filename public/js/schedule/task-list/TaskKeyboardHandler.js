/**
 * TaskKeyboardHandler - キーボードショートカットの管理
 */
class TaskKeyboardHandler {
    constructor(options = {}) {
        this.app = options.app || null;
        this.dataManager = options.dataManager || null;
        this.tableRenderer = options.tableRenderer || null;

        // クリップボード
        this.clipboard = [];

        // ショートカット設定
        this.shortcuts = {
            // Ctrl+Z: Undo
            undo: { key: 'z', ctrl: true, shift: false, editModeOnly: true },
            // Ctrl+Y: Redo（未実装）
            redo: { key: 'y', ctrl: true, shift: false, editModeOnly: true },
            // Ctrl+C: コピー
            copy: { key: 'c', ctrl: true, shift: false, editModeOnly: true },
            // Ctrl+V: ペースト
            paste: { key: 'v', ctrl: true, shift: false, editModeOnly: true },
            // Ctrl+X: カット
            cut: { key: 'x', ctrl: true, shift: false, editModeOnly: true },
            // Delete: 選択タスク削除
            delete: { key: 'Delete', ctrl: false, shift: false, editModeOnly: true },
            // Ctrl+A: 全選択
            selectAll: { key: 'a', ctrl: true, shift: false, editModeOnly: false },
            // Escape: 選択解除/メニュー閉じる
            escape: { key: 'Escape', ctrl: false, shift: false, editModeOnly: false },
            // Ctrl+S: 保存
            save: { key: 's', ctrl: true, shift: false, editModeOnly: true },
            // Ctrl+E: 編集モード切り替え
            toggleEdit: { key: 'e', ctrl: true, shift: false, editModeOnly: false },
        };

        // 初期化
        this.init();
    }

    /**
     * 初期化
     */
    init() {
        document.addEventListener('keydown', (e) => this.handleKeyDown(e));
        // pasteイベントでクリップボードデータを直接取得
        document.addEventListener('paste', (e) => this.handlePasteEvent(e));
    }

    /**
     * キーダウンイベント処理
     */
    handleKeyDown(e) {
        // 入力フォーカス中は無視
        if (this.isInputFocused()) {
            // Escapeのみ処理
            if (e.key === 'Escape') {
                this.handleEscape(e);
            }
            return;
        }

        // Ctrl+Z: Undo
        if (this.matchShortcut(e, this.shortcuts.undo)) {
            this.handleUndo(e);
            return;
        }

        // Ctrl+C: コピー
        if (this.matchShortcut(e, this.shortcuts.copy)) {
            this.handleCopy(e);
            return;
        }

        // Ctrl+V: ペーストはpasteイベントで処理するため、ここではpreventDefaultのみ
        if (this.matchShortcut(e, this.shortcuts.paste)) {
            // pasteイベントが発火するので、ここでは何もしない
            return;
        }

        // Ctrl+X: カット
        if (this.matchShortcut(e, this.shortcuts.cut)) {
            this.handleCut(e);
            return;
        }

        // Delete: 削除
        if (this.matchShortcut(e, this.shortcuts.delete)) {
            this.handleDelete(e);
            return;
        }

        // Ctrl+A: 全選択
        if (this.matchShortcut(e, this.shortcuts.selectAll)) {
            this.handleSelectAll(e);
            return;
        }

        // Escape: 選択解除/メニュー閉じる
        if (this.matchShortcut(e, this.shortcuts.escape)) {
            this.handleEscape(e);
            return;
        }

        // Ctrl+S: 保存
        if (this.matchShortcut(e, this.shortcuts.save)) {
            this.handleSave(e);
            return;
        }

        // Ctrl+E: 編集モード切り替え
        if (this.matchShortcut(e, this.shortcuts.toggleEdit)) {
            this.handleToggleEdit(e);
            return;
        }
    }

    /**
     * ショートカットが一致するかチェック
     */
    matchShortcut(e, shortcut) {
        if (shortcut.editModeOnly && this.app && !this.app.isEditMode) {
            return false;
        }

        return e.key.toLowerCase() === shortcut.key.toLowerCase() &&
               e.ctrlKey === shortcut.ctrl &&
               e.shiftKey === shortcut.shift;
    }

    /**
     * 入力フォーカス中かチェック
     */
    isInputFocused() {
        const activeElement = document.activeElement;
        if (!activeElement) return false;

        const tagName = activeElement.tagName.toLowerCase();
        return tagName === 'input' || tagName === 'textarea' || tagName === 'select' ||
               activeElement.contentEditable === 'true';
    }

    /**
     * モーダルが開いているか確認
     */
    isModalOpen() {
        // インポートモーダル、エクスポートモーダル等が開いているかチェック
        const importModal = document.getElementById('import-modal');
        const exportModal = document.getElementById('export-modal');
        const bulkDateModal = document.getElementById('bulk-date-modal');

        if (importModal && !importModal.classList.contains('hidden')) return true;
        if (exportModal && !exportModal.classList.contains('hidden')) return true;
        if (bulkDateModal && !bulkDateModal.classList.contains('hidden')) return true;

        return false;
    }

    /**
     * Undo処理
     */
    handleUndo(e) {
        e.preventDefault();

        if (this.dataManager && this.dataManager.undo()) {
            if (this.app) {
                this.app.render();
                this.app.showToast('操作を元に戻しました', 'info');
            }
        } else {
            if (this.app) {
                this.app.showToast('元に戻す操作がありません', 'warning');
            }
        }
    }

    /**
     * コピー処理 - 選択したタスクをCSV形式でシステムクリップボードにコピー
     */
    async handleCopy(e) {
        if (!this.tableRenderer) return;

        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        if (selectedIds.length === 0) return;

        e.preventDefault();

        // 選択されたタスクを取得
        const selectedTasks = selectedIds.map(id => {
            const task = this.dataManager?.getTaskById(id);
            return task ? { ...task } : null;
        }).filter(t => t !== null);

        if (selectedTasks.length === 0) return;

        // CSV形式に変換
        const csvData = this.tasksToCSV(selectedTasks);

        try {
            // システムクリップボードにコピー
            await navigator.clipboard.writeText(csvData);

            // ハイライト表示
            selectedIds.forEach(id => {
                this.tableRenderer.highlightRow(id, 'copied');
            });

            this.showClipboardStatus('コピー', selectedTasks.length);

            if (this.app) {
                this.app.showToast(`${selectedTasks.length}件をコピーしました`, 'success');
            }
        } catch (error) {
            console.error('Copy error:', error);
            if (this.app) {
                this.app.showToast('コピーに失敗しました: ' + error.message, 'error');
            }
        }
    }

    /**
     * タスクをCSV形式に変換（テーブル列順序に合わせる）
     */
    tasksToCSV(tasks) {
        // ステータスマッピング（値 -> 日本語）
        const statusMap = {
            'not_started': '未着手',
            'in_progress': '進行中',
            'completed': '完了',
            'on_hold': '保留'
        };

        // ヘッダー行（テーブルの列順序に合わせる）
        const headers = [
            '工程', 'タスク名', '担当者', 'ステータス',
            '営業工数', '予定工数', '予定開始日', '予定終了日', '予定原価',
            '実績工数', '実績開始日', '実績終了日', '出来高',
            '進捗率', '備考', '親タスク'
        ];

        const rows = [headers.join('\t')];

        tasks.forEach(task => {
            const row = [
                task.process_name || '',
                task.task_name || '',
                task.assignee_name || '',
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
            ];
            rows.push(row.join('\t'));
        });

        return rows.join('\n');
    }

    /**
     * ペーストイベント処理 - pasteイベントから直接クリップボードデータを取得
     */
    handlePasteEvent(e) {
        // 入力フォーカス中は通常のペースト動作を許可
        if (this.isInputFocused()) {
            return;
        }

        // 編集モードでない場合は無視
        if (this.app && !this.app.isEditMode) {
            return;
        }

        e.preventDefault();

        // クリップボードからテキストを取得
        const clipboardText = e.clipboardData?.getData('text/plain');

        if (!clipboardText || !clipboardText.trim()) {
            return;
        }

        // CSV/TSVデータをパース
        const tasks = this.parseClipboardData(clipboardText);

        if (tasks.length === 0) {
            return;
        }

        // 工程・担当者の名前→ID変換マップを作成
        const processNameMap = {};
        const memberNameMap = {};

        if (this.app?.processes) {
            this.app.processes.forEach(p => {
                processNameMap[p.name] = p.id;
            });
        }
        if (this.app?.members) {
            this.app.members.forEach(m => {
                memberNameMap[m.name] = m.id;
            });
        }

        // タスクを追加
        const addedTasks = [];
        tasks.forEach(taskData => {
            // 工程名からIDに変換
            if (taskData.process_name && !taskData.process_id) {
                taskData.process_id = processNameMap[taskData.process_name] || null;
            }
            // 担当者名からIDに変換
            if (taskData.assignee_name && !taskData.assignee_id) {
                taskData.assignee_id = memberNameMap[taskData.assignee_name] || null;
            }

            const newTask = {
                ...taskData,
                id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                project_id: this.app?.projectId
            };

            if (this.dataManager) {
                this.dataManager.addTask(newTask);
                addedTasks.push(newTask);
            }
        });

        if (this.app && addedTasks.length > 0) {
            this.app.render();
        }
    }

    /**
     * クリップボードのCSV/TSVデータをパース
     */
    parseClipboardData(text) {
        const tasks = [];
        const lines = text.trim().split(/\r?\n/);

        if (lines.length === 0) return tasks;

        // 区切り文字を判定（タブ or カンマ）
        const firstLine = lines[0];
        const delimiter = firstLine.includes('\t') ? '\t' : ',';

        // ヘッダー行をパース
        const headers = this.parseLine(firstLine, delimiter);

        // ヘッダーマッピング（日本語 -> フィールド名）
        // テーブル列順序: 工程, タスク名, 担当者, ステータス, 営業工数, 予定工数, 予定開始日, 予定終了日, 予定原価, 実績工数, 実績開始日, 実績終了日, 出来高, 進捗率, 備考, 親タスク
        const headerMap = {
            '工程': 'process_name',
            'タスク名': 'task_name',
            '担当者': 'assignee_name',
            'ステータス': 'status',
            '営業工数': 'sales_man_days',
            '予定工数': 'planned_man_days',
            '予定開始日': 'planned_start_date',
            '予定終了日': 'planned_end_date',
            '予定原価': 'planned_cost',
            '実績工数': 'actual_man_days',
            '実績開始日': 'actual_start_date',
            '実績終了日': 'actual_end_date',
            '出来高': 'actual_cost',
            '進捗率': 'progress',
            '備考': 'description',
            '親タスク': 'parent_name'
        };

        // ステータスマッピング（日本語 -> 値）
        const statusMap = {
            '未着手': 'not_started',
            '進行中': 'in_progress',
            '完了': 'completed',
            '保留': 'on_hold'
        };

        // ヘッダーのインデックスマップを作成
        const columnMap = {};
        headers.forEach((header, index) => {
            const fieldName = headerMap[header.trim()];
            if (fieldName) {
                columnMap[fieldName] = index;
            }
        });

        // ヘッダー行かどうかを判定（最初の列が「工程」または「タスク名」等のヘッダーキーワードか）
        const headerKeywords = ['工程', '工程名', 'タスク名', 'タスク', 'task', 'Task', 'TASK', '名前', '名称', 'process'];
        const firstCol = headers[0]?.trim()?.toLowerCase() || '';
        const hasHeader = headerKeywords.some(keyword => firstCol.includes(keyword.toLowerCase()));

        // タスク名列が見つからない場合（ヘッダー行なし）
        if (columnMap['task_name'] === undefined) {
            // 固定列順序でパース: 工程, タスク名, 担当者, ステータス, 営業工数, 予定工数, 予定開始日, 予定終了日, 予定原価, 実績工数, 実績開始日, 実績終了日, 出来高, 進捗率, 備考, 親タスク
            const startLine = hasHeader ? 1 : 0;
            for (let i = startLine; i < lines.length; i++) {
                const values = this.parseLine(lines[i], delimiter);
                // タスク名（2列目）が必須
                const taskName = values[1]?.trim();
                if (!taskName) continue;

                const task = {
                    task_name: taskName,
                    status: 'not_started',
                    progress: 0
                };

                // 工程名（1列目）
                if (values[0]?.trim()) {
                    task.process_name = values[0].trim();
                }
                // 担当者（3列目）
                if (values[2]?.trim()) {
                    task.assignee_name = values[2].trim();
                }
                // ステータス（4列目）
                if (values[3]?.trim()) {
                    task.status = statusMap[values[3].trim()] || values[3].trim() || 'not_started';
                }
                // 営業工数（5列目）
                if (values[4]?.trim()) {
                    const num = parseFloat(values[4]);
                    if (!isNaN(num)) task.sales_man_days = num;
                }
                // 予定工数（6列目）
                if (values[5]?.trim()) {
                    const num = parseFloat(values[5]);
                    if (!isNaN(num)) task.planned_man_days = num;
                }
                // 予定開始日（7列目）
                if (values[6]?.trim()) {
                    task.planned_start_date = values[6].trim();
                }
                // 予定終了日（8列目）
                if (values[7]?.trim()) {
                    task.planned_end_date = values[7].trim();
                }
                // 予定原価（9列目）
                if (values[8]?.trim()) {
                    const num = parseFloat(values[8]);
                    if (!isNaN(num)) task.planned_cost = num;
                }
                // 実績工数（10列目）
                if (values[9]?.trim()) {
                    const num = parseFloat(values[9]);
                    if (!isNaN(num)) task.actual_man_days = num;
                }
                // 実績開始日（11列目）
                if (values[10]?.trim()) {
                    task.actual_start_date = values[10].trim();
                }
                // 実績終了日（12列目）
                if (values[11]?.trim()) {
                    task.actual_end_date = values[11].trim();
                }
                // 出来高（13列目）
                if (values[12]?.trim()) {
                    const num = parseFloat(values[12]);
                    if (!isNaN(num)) task.actual_cost = num;
                }
                // 進捗率（14列目）
                if (values[13]?.trim()) {
                    const num = parseInt(values[13], 10);
                    if (!isNaN(num)) task.progress = num;
                }
                // 備考（15列目）
                if (values[14]?.trim()) {
                    task.description = values[14].trim();
                }

                tasks.push(task);
            }
            return tasks;
        }

        // データ行をパース（ヘッダー行をスキップ）
        for (let i = 1; i < lines.length; i++) {
            const values = this.parseLine(lines[i], delimiter);

            // タスク名が空の行はスキップ
            const taskName = columnMap['task_name'] !== undefined ? values[columnMap['task_name']] : null;
            if (!taskName || !taskName.trim()) continue;

            const task = {
                task_name: taskName.trim(),
                status: 'not_started',
                progress: 0
            };

            // 各フィールドをマッピング
            if (columnMap['status'] !== undefined) {
                const statusValue = values[columnMap['status']];
                task.status = statusMap[statusValue?.trim()] || statusValue || 'not_started';
            }

            if (columnMap['progress'] !== undefined) {
                const progressValue = parseInt(values[columnMap['progress']], 10);
                task.progress = isNaN(progressValue) ? 0 : progressValue;
            }

            // 日付フィールド
            ['planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date'].forEach(field => {
                if (columnMap[field] !== undefined && values[columnMap[field]]) {
                    task[field] = values[columnMap[field]].trim() || null;
                }
            });

            // 数値フィールド
            ['planned_man_days', 'sales_man_days', 'planned_cost', 'actual_man_days', 'actual_cost'].forEach(field => {
                if (columnMap[field] !== undefined && values[columnMap[field]]) {
                    const numValue = parseFloat(values[columnMap[field]]);
                    if (!isNaN(numValue)) {
                        task[field] = numValue;
                    }
                }
            });

            // テキストフィールド
            if (columnMap['description'] !== undefined && values[columnMap['description']]) {
                task.description = values[columnMap['description']].trim();
            }

            // 工程名と担当者名は後でIDに変換が必要（ここでは名前のまま保持）
            if (columnMap['process_name'] !== undefined && values[columnMap['process_name']]) {
                task.process_name = values[columnMap['process_name']].trim();
            }
            if (columnMap['assignee_name'] !== undefined && values[columnMap['assignee_name']]) {
                task.assignee_name = values[columnMap['assignee_name']].trim();
            }

            tasks.push(task);
        }

        return tasks;
    }

    /**
     * CSV/TSV行をパース（引用符対応）
     */
    parseLine(line, delimiter) {
        const result = [];
        let current = '';
        let inQuotes = false;

        for (let i = 0; i < line.length; i++) {
            const char = line[i];

            if (inQuotes) {
                if (char === '"') {
                    if (i + 1 < line.length && line[i + 1] === '"') {
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
                } else if (char === delimiter) {
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

    /**
     * カット処理
     */
    async handleCut(e) {
        if (!this.tableRenderer) return;

        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        if (selectedIds.length === 0) return;

        e.preventDefault();

        // 選択されたタスクを取得
        const selectedTasks = selectedIds.map(id => {
            const task = this.dataManager?.getTaskById(id);
            return task ? { ...task } : null;
        }).filter(t => t !== null);

        if (selectedTasks.length === 0) return;

        // CSV形式に変換してクリップボードにコピー
        const csvData = this.tasksToCSV(selectedTasks);

        try {
            await navigator.clipboard.writeText(csvData);

            // 次に削除
            if (this.dataManager) {
                this.dataManager.bulkDelete(selectedIds);
            }

            if (this.app) {
                this.app.render();
                this.app.clearSelection();
                this.app.showToast(`${selectedTasks.length}件をカットしました`, 'success');
            }

            this.showClipboardStatus('カット', selectedTasks.length);
        } catch (error) {
            console.error('Cut error:', error);
            if (this.app) {
                this.app.showToast('カットに失敗しました: ' + error.message, 'error');
            }
        }
    }

    /**
     * 削除処理
     */
    handleDelete(e) {
        if (!this.tableRenderer) return;

        const selectedIds = this.tableRenderer.getSelectedTaskIds();
        if (selectedIds.length === 0) return;

        e.preventDefault();

        if (!confirm(`${selectedIds.length}件のタスクを削除しますか？`)) {
            return;
        }

        if (this.dataManager) {
            this.dataManager.bulkDelete(selectedIds);
        }

        if (this.app) {
            this.app.render();
            this.app.clearSelection();
            this.app.showToast(`${selectedIds.length}件を削除しました`, 'success');
        }
    }

    /**
     * 全選択処理
     */
    handleSelectAll(e) {
        e.preventDefault();

        if (this.tableRenderer) {
            this.tableRenderer.setAllSelection(true);
        }

        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = true;
        }

        if (this.app) {
            this.app.updateSelectionBar();
        }
    }

    /**
     * Escape処理
     */
    handleEscape(e) {
        // メニューを閉じる
        document.querySelectorAll('.context-menu').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.filter-dropdown').forEach(d => d.classList.remove('show'));

        // モーダルを閉じる
        if (this.app) {
            this.app.closeBulkEditModal();
            this.app.closeTaskModal();
            this.app.closeProgressSummaryModal();
        }

        // 選択解除
        if (this.tableRenderer) {
            this.tableRenderer.setAllSelection(false);
        }

        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = false;
        }

        if (this.app) {
            this.app.updateSelectionBar();
        }
    }

    /**
     * 保存処理
     */
    handleSave(e) {
        e.preventDefault();

        if (this.app) {
            this.app.saveAllTasks();
        }
    }

    /**
     * 編集モード切り替え処理
     */
    handleToggleEdit(e) {
        e.preventDefault();

        if (this.app) {
            const newMode = this.app.isEditMode ? 'view' : 'edit';
            this.app.switchViewMode(newMode);
            this.app.showToast(newMode === 'edit' ? '編集モードに切り替えました' : '表示モードに切り替えました', 'info');
        }
    }

    /**
     * クリップボードステータスを表示
     */
    showClipboardStatus(action, count = 0) {
        const status = document.getElementById('clipboard-status');
        if (status) {
            const span = status.querySelector('span');
            if (span) {
                span.innerHTML = `<i class="fas fa-clipboard-check mr-1"></i>${count}件を${action}しました（Ctrl+Vで貼り付け）`;
            }
            status.classList.remove('hidden');
            setTimeout(() => {
                status.classList.add('hidden');
            }, 3000);
        }
    }

    /**
     * クリップボードをクリア（互換性のため残す）
     */
    clearClipboard() {
        // システムクリップボードは直接クリアできないため、何もしない
    }

    /**
     * クリップボードの内容を取得（互換性のため残す）
     */
    getClipboard() {
        return this.clipboard;
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskKeyboardHandler = TaskKeyboardHandler;
}
