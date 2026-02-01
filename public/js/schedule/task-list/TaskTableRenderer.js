/**
 * TaskTableRenderer - タスクテーブルの描画を担当するクラス
 * テーブル行の生成、更新、フッター統計の表示を行う
 * 編集モードではインライン編集（直接テーブル上で編集）をサポート
 */
class TaskTableRenderer {
    constructor(options = {}) {
        this.tableBody = options.tableBody || document.getElementById('task-tbody');
        this.dataManager = options.dataManager || null;
        this.isEditMode = false;

        // 工程と担当者のオプション
        this.processes = options.processes || [];
        this.members = options.members || [];

        // DOM要素のキャッシュ
        this.elements = {
            totalCount: document.getElementById('total-count'),
            completedCount: document.getElementById('completed-count'),
            progressCount: document.getElementById('progress-count'),
            notStartedCount: document.getElementById('notstarted-count'),
            delayedCount: document.getElementById('delayed-count'),
            undoBtn: document.getElementById('undo-btn')
        };

        // ステータス表示設定
        this.statusConfig = {
            not_started: { class: 'status-not-started', label: '未着手' },
            in_progress: { class: 'status-in-progress', label: '進行中' },
            completed: { class: 'status-completed', label: '完了' },
            on_hold: { class: 'status-on-hold', label: '保留' }
        };

        // 担当者カラー設定
        this.assigneeColors = {};
        this.colorPalette = [
            'from-blue-400 to-indigo-500',
            'from-purple-400 to-pink-500',
            'from-cyan-400 to-blue-500',
            'from-orange-400 to-red-500',
            'from-teal-400 to-cyan-500',
            'from-emerald-400 to-green-500',
            'from-rose-400 to-pink-500',
            'from-amber-400 to-orange-500'
        ];
        this.colorIndex = 0;

        // コールバック
        this.onRowClick = options.onRowClick || null;
        this.onRowContextMenu = options.onRowContextMenu || null;
        this.onCheckboxChange = options.onCheckboxChange || null;
        this.onFieldChange = options.onFieldChange || null;
    }

    /**
     * テーブル全体を再描画
     */
    render(tasks) {
        if (!this.tableBody) return;

        this.tableBody.innerHTML = '';
        let rowNo = 1;

        tasks.forEach((task, taskIndex) => {
            const taskRow = this.createTaskRow(task, rowNo++, taskIndex);
            this.tableBody.appendChild(taskRow);

            // サブタスクの描画
            if (task.subtasks && task.subtasks.length > 0) {
                task.subtasks.forEach((subtask, subtaskIndex) => {
                    const subtaskRow = this.createSubtaskRow(subtask, rowNo++, task.id, subtaskIndex);
                    this.tableBody.appendChild(subtaskRow);
                });
            }
        });

        // 統計を更新
        if (this.dataManager) {
            const stats = this.dataManager.calculateStats();
            this.updateFooterStats(stats);
        }
    }

    /**
     * タスク行を生成
     */
    createTaskRow(task, rowNo, index) {
        const tr = document.createElement('tr');
        const isCompleted = task.status === 'completed';
        const statusConfig = this.statusConfig[task.status] || this.statusConfig.not_started;

        tr.className = `task-row ${isCompleted ? 'completed-row' : ''} border-b border-slate-200 hover:bg-slate-50`;
        tr.dataset.taskId = task.id;
        tr.dataset.index = index;

        if (this.isEditMode) {
            // 編集モード：インライン編集フィールドを表示
            tr.innerHTML = this.createEditableRowHtml(task, rowNo, false);
        } else {
            // 表示モード
            tr.innerHTML = `
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <input type="checkbox" class="row-checkbox task-checkbox rounded border-slate-300" data-task-id="${task.id}">
                </td>
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <span class="drag-handle text-slate-400 hover:text-blue-500 cursor-grab" title="ドラッグで並び替え">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                </td>
                <td class="px-2 py-2 text-center text-xs text-slate-500 border-r border-slate-100">${rowNo}</td>
                <td class="px-2 py-2 text-xs border-r border-slate-100">${this.escapeHtml(task.process_name || '')}</td>
                <td class="px-2 py-2 border-r border-slate-100">
                    <div class="flex items-center">
                        ${this.createTaskIcon(false)}
                        <a href="javascript:void(0)" class="task-name-link text-sm text-slate-800 hover:text-blue-600 font-medium" data-task-id="${task.id}">
                            ${this.escapeHtml(task.task_name)}
                        </a>
                    </div>
                </td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100">${this.createAssigneeHtml(task.assignee_name)}</td>
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <span class="status-badge ${statusConfig.class}">${statusConfig.label}</span>
                </td>
                <!-- 予定 -->
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${task.sales_man_days || '-'}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${task.planned_man_days || '-'}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30">${this.formatDate(task.planned_start_date)}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30">${this.formatDate(task.planned_end_date)}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${this.formatCost(task.planned_cost)}</td>
                <!-- 実績 -->
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30">${task.actual_man_days || '-'}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30">${this.formatDate(task.actual_start_date)}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30">${this.formatDate(task.actual_end_date)}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30">${this.formatCost(task.actual_cost)}</td>
                <!-- 進捗 -->
                <td class="px-2 py-2 border-r border-slate-100">
                    ${this.createProgressBar(task.progress || 0)}
                </td>
                <!-- 遅延 -->
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    ${this.createDelayBadge(task.delay_days)}
                </td>
                <td class="px-2 py-2 text-xs text-slate-500">${this.escapeHtml(task.description || '')}</td>
            `;
        }

        // イベントリスナーを設定
        this.attachRowEventListeners(tr, task);

        return tr;
    }

    /**
     * 編集可能な行のHTMLを生成
     */
    createEditableRowHtml(task, rowNo, isSubtask) {
        const processOptions = this.processes.map(p =>
            `<option value="${p.id}" ${task.process_id == p.id ? 'selected' : ''}>${this.escapeHtml(p.name)}</option>`
        ).join('');

        const memberOptions = this.members.map(m =>
            `<option value="${m.id}" ${task.assignee_id == m.id ? 'selected' : ''}>${this.escapeHtml(m.name)}</option>`
        ).join('');

        const statusOptions = Object.entries(this.statusConfig).map(([value, config]) =>
            `<option value="${value}" ${task.status === value ? 'selected' : ''}>${config.label}</option>`
        ).join('');

        // 枠なしスタイリッシュデザイン（モックに合わせる）
        const inputClass = 'edit-input text-xs';
        const selectClass = 'edit-input text-xs';
        const numberInputClass = inputClass + ' text-center';
        const dateInputClass = inputClass;

        return `
            <td class="px-2 py-1 text-center border-r border-slate-100">
                <input type="checkbox" class="row-checkbox task-checkbox rounded border-slate-300" data-task-id="${task.id}">
            </td>
            <td class="px-2 py-1 text-center border-r border-slate-100">
                <span class="drag-handle text-slate-400 hover:text-blue-500" style="cursor: grab;" title="ドラッグで並び替え">
                    <i class="fas fa-grip-vertical"></i>
                </span>
            </td>
            <td class="px-2 py-1 text-center text-xs text-slate-500 border-r border-slate-100">${rowNo}</td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="${selectClass}" data-field="process_id" data-task-id="${task.id}">
                    <option value="">-</option>
                    ${processOptions}
                </select>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <div class="flex items-center${isSubtask ? ' pl-4' : ''}">
                    ${isSubtask ? '<span class="text-slate-400 mr-1 flex-shrink-0">└</span>' : ''}
                    <input type="text" class="${inputClass} flex-1" value="${this.escapeHtml(task.task_name || '')}" data-field="task_name" data-task-id="${task.id}">
                </div>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="${selectClass}" data-field="assignee_id" data-task-id="${task.id}">
                    <option value="">-</option>
                    ${memberOptions}
                </select>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="${selectClass}" data-field="status" data-task-id="${task.id}">
                    ${statusOptions}
                </select>
            </td>
            <!-- 予定 -->
            <td class="px-1 py-1 border-r border-slate-100 bg-sky-50/50 editable-cell">
                <input type="number" class="${numberInputClass}" value="${task.sales_man_days || ''}" data-field="sales_man_days" data-task-id="${task.id}" step="0.5" min="0">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-sky-50/50 editable-cell">
                <input type="number" class="${numberInputClass}" value="${task.planned_man_days || ''}" data-field="planned_man_days" data-task-id="${task.id}" step="0.5" min="0">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-sky-50/50 editable-cell">
                <input type="date" class="${dateInputClass}" value="${task.planned_start_date || ''}" data-field="planned_start_date" data-task-id="${task.id}">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-sky-50/50 editable-cell">
                <input type="date" class="${dateInputClass}" value="${task.planned_end_date || ''}" data-field="planned_end_date" data-task-id="${task.id}">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-sky-50/50 editable-cell">
                <input type="number" class="${inputClass} text-right" value="${task.planned_cost || ''}" data-field="planned_cost" data-task-id="${task.id}" min="0">
            </td>
            <!-- 実績 -->
            <td class="px-1 py-1 border-r border-slate-100 bg-amber-50/50 editable-cell">
                <input type="number" class="${numberInputClass}" value="${task.actual_man_days || ''}" data-field="actual_man_days" data-task-id="${task.id}" step="0.5" min="0">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-amber-50/50 editable-cell">
                <input type="date" class="${dateInputClass}" value="${task.actual_start_date || ''}" data-field="actual_start_date" data-task-id="${task.id}">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-amber-50/50 editable-cell">
                <input type="date" class="${dateInputClass}" value="${task.actual_end_date || ''}" data-field="actual_end_date" data-task-id="${task.id}">
            </td>
            <td class="px-1 py-1 border-r border-slate-100 bg-amber-50/50 editable-cell">
                <input type="number" class="${inputClass} text-right" value="${task.actual_cost || ''}" data-field="actual_cost" data-task-id="${task.id}" min="0">
            </td>
            <!-- 進捗 -->
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <input type="number" class="${numberInputClass}" value="${task.progress || 0}" data-field="progress" data-task-id="${task.id}" min="0" max="100">
            </td>
            <!-- 遅延 -->
            <td class="px-2 py-1 text-center border-r border-slate-100">
                ${this.createDelayBadge(task.delay_days)}
            </td>
            <td class="px-1 py-1 editable-cell">
                <input type="text" class="${inputClass}" value="${this.escapeHtml(task.description || '')}" data-field="description" data-task-id="${task.id}">
            </td>
        `;
    }

    /**
     * サブタスク行を生成
     */
    createSubtaskRow(subtask, rowNo, parentId, index) {
        const tr = document.createElement('tr');
        const isCompleted = subtask.status === 'completed';
        const statusConfig = this.statusConfig[subtask.status] || this.statusConfig.not_started;

        tr.className = `task-row subtask-row ${isCompleted ? 'completed-row' : ''} border-b border-slate-200`;
        tr.dataset.taskId = subtask.id;
        tr.dataset.parentId = parentId;
        tr.dataset.index = index;

        if (this.isEditMode) {
            // 編集モード：インライン編集フィールドを表示
            tr.innerHTML = this.createEditableRowHtml(subtask, rowNo, true);
        } else {
            // 表示モード
            tr.innerHTML = `
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <input type="checkbox" class="row-checkbox task-checkbox rounded border-slate-300" data-task-id="${subtask.id}">
                </td>
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <span class="drag-handle text-slate-400 hover:text-blue-500 cursor-grab" title="ドラッグで並び替え">
                        <i class="fas fa-grip-vertical"></i>
                    </span>
                </td>
                <td class="px-2 py-2 text-center text-xs text-slate-400 border-r border-slate-100">${rowNo}</td>
                <td class="px-2 py-2 text-xs text-slate-400 border-r border-slate-100">${this.escapeHtml(subtask.process_name || '')}</td>
                <td class="px-2 py-2 border-r border-slate-100">
                    <div class="flex items-center pl-4">
                        ${this.createTaskIcon(true)}
                        <a href="javascript:void(0)" class="task-name-link text-sm text-slate-600 hover:text-blue-600" data-task-id="${subtask.id}">
                            ${this.escapeHtml(subtask.task_name)}
                        </a>
                    </div>
                </td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100">${this.createAssigneeHtml(subtask.assignee_name)}</td>
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    <span class="status-badge ${statusConfig.class}">${statusConfig.label}</span>
                </td>
                <!-- 予定 -->
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${subtask.sales_man_days || '-'}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${subtask.planned_man_days || '-'}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30">${this.formatDate(subtask.planned_start_date)}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-sky-50/30">${this.formatDate(subtask.planned_end_date)}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-sky-50/30">${this.formatCost(subtask.planned_cost)}</td>
                <!-- 実績 -->
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30">${subtask.actual_man_days || '-'}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30">${this.formatDate(subtask.actual_start_date)}</td>
                <td class="px-2 py-2 text-center text-xs border-r border-slate-100 bg-amber-50/30">${this.formatDate(subtask.actual_end_date)}</td>
                <td class="px-2 py-2 text-right text-xs border-r border-slate-100 bg-amber-50/30">${this.formatCost(subtask.actual_cost)}</td>
                <!-- 進捗 -->
                <td class="px-2 py-2 border-r border-slate-100">
                    ${this.createProgressBar(subtask.progress || 0)}
                </td>
                <!-- 遅延 -->
                <td class="px-2 py-2 text-center border-r border-slate-100">
                    ${this.createDelayBadge(subtask.delay_days)}
                </td>
                <td class="px-2 py-2 text-xs text-slate-500">${this.escapeHtml(subtask.description || '')}</td>
            `;
        }

        // イベントリスナーを設定
        this.attachRowEventListeners(tr, subtask);

        return tr;
    }

    /**
     * 行にイベントリスナーを設定
     */
    attachRowEventListeners(row, task) {
        // タスク名クリック（表示モードのみ）
        const taskNameLink = row.querySelector('.task-name-link');
        if (taskNameLink && this.onRowClick && !this.isEditMode) {
            taskNameLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.onRowClick(task.id, task);
            });
        }

        // チェックボックス変更
        const checkbox = row.querySelector('.task-checkbox');
        if (checkbox && this.onCheckboxChange) {
            checkbox.addEventListener('change', () => {
                // チェックボックスクリック前に、同じ行の日付入力値をDataManagerに保存
                // これにより、日付編集後にチェックを入れても値が失われない
                if (this.isEditMode && this.dataManager) {
                    const dateFields = ['planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date'];
                    dateFields.forEach(fieldName => {
                        const input = row.querySelector(`input[data-field="${fieldName}"]`);
                        if (input && input.value) {
                            const inputTaskId = input.dataset.taskId;
                            if (inputTaskId) {
                                this.dataManager.updateTaskField(inputTaskId, fieldName, input.value);
                            }
                        }
                    });
                }
                this.onCheckboxChange(task.id, checkbox.checked);
            });
        }

        // 右クリックメニュー
        if (this.onRowContextMenu) {
            row.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.onRowContextMenu(e, task, row);
            });
        }

        // 編集モード時：フィールド変更イベント
        if (this.isEditMode) {
            const editInputs = row.querySelectorAll('.edit-input');
            const parentId = row.dataset.parentId; // サブタスクの場合、行に設定されている親タスクID

            const handleFieldChange = (e) => {
                const field = e.target.dataset.field;
                const taskId = e.target.dataset.taskId;
                let value = e.target.value;

                // 数値フィールドの変換
                if (['sales_man_days', 'planned_man_days', 'actual_man_days', 'progress', 'planned_cost', 'actual_cost'].includes(field)) {
                    value = value === '' ? null : parseFloat(value);
                }

                // コールバック呼び出し
                if (this.onFieldChange) {
                    this.onFieldChange(taskId, field, value);
                }

                // DataManager を直接更新
                if (this.dataManager) {
                    this.dataManager.updateTaskField(taskId, field, value);
                }

                // 日付フィールドの場合の処理
                if (['planned_start_date', 'planned_end_date'].includes(field)) {
                    // 遅延日数を再計算して更新
                    this.updateDelayBadgeForTask(taskId, row);

                    // サブタスクの場合、親タスクの日付を同期
                    if (parentId) {
                        this.syncParentTaskDates(parentId);
                        window.onDateEdited && window.onDateEdited(parseInt(parentId), parseInt(taskId), field);
                    } else {
                        window.onDateEdited && window.onDateEdited(parseInt(taskId), null, field);
                    }
                }
            };

            editInputs.forEach(input => {
                input.addEventListener('change', handleFieldChange);

                // 日付フィールドはinputイベントでも即時更新
                const field = input.dataset.field;
                if (['planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date'].includes(field)) {
                    input.addEventListener('input', handleFieldChange);
                }
            });
        }
    }

    /**
     * 特定の行を更新
     */
    updateRow(taskId, task) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (!row) return;

        const statusConfig = this.statusConfig[task.status] || this.statusConfig.not_started;
        const isCompleted = task.status === 'completed';

        // クラスを更新
        row.className = `task-row ${row.classList.contains('subtask-row') ? 'subtask-row' : ''} ${isCompleted ? 'completed-row' : ''} border-b border-slate-200 hover:bg-slate-50`;

        // 各セルを更新
        const cells = row.querySelectorAll('td');

        // 工程
        cells[3].innerHTML = this.escapeHtml(task.process_name || '');

        // タスク名
        const taskNameCell = cells[4];
        const taskNameLink = taskNameCell.querySelector('.task-name-link');
        if (taskNameLink) {
            taskNameLink.textContent = task.task_name;
        }

        // 担当者
        cells[5].innerHTML = this.escapeHtml(task.assignee_name || '-');

        // ステータス
        cells[6].innerHTML = `<span class="status-badge ${statusConfig.class}">${statusConfig.label}</span>`;

        // 予定
        cells[7].textContent = task.sales_man_days || '-';
        cells[8].textContent = task.planned_man_days || '-';
        cells[9].textContent = this.formatDate(task.planned_start_date);
        cells[10].textContent = this.formatDate(task.planned_end_date);
        cells[11].innerHTML = this.formatCost(task.planned_cost);

        // 実績
        cells[12].textContent = task.actual_man_days || '-';
        cells[13].textContent = this.formatDate(task.actual_start_date);
        cells[14].textContent = this.formatDate(task.actual_end_date);
        cells[15].innerHTML = this.formatCost(task.actual_cost);

        // 進捗
        cells[16].innerHTML = this.createProgressBar(task.progress || 0);

        // 遅延
        cells[17].innerHTML = this.createDelayBadge(task.delay_days);

        // 備考
        cells[18].innerHTML = this.escapeHtml(task.description || '');
    }

    /**
     * 行を削除
     */
    removeRow(taskId) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.remove();
        }
    }

    /**
     * 進捗バーを生成
     */
    createProgressBar(progress) {
        const progressClass = progress < 30 ? 'low' : (progress < 70 ? 'medium' : 'high');
        return `
            <div class="flex items-center gap-1">
                <div class="progress-bar-container">
                    <div class="progress-bar ${progressClass}" style="width: ${progress}%"></div>
                </div>
                <span class="text-xs text-slate-600">${progress}%</span>
            </div>
        `;
    }

    /**
     * 遅延バッジを生成
     */
    createDelayBadge(delayDays) {
        if (!delayDays || delayDays === 0) {
            return '<span class="delay-badge delay-ontime">予定通り</span>';
        }

        if (delayDays > 0) {
            return `<span class="delay-badge delay-late">${delayDays}日遅れ</span>`;
        } else {
            return `<span class="delay-badge delay-early">${Math.abs(delayDays)}日先行</span>`;
        }
    }

    /**
     * 遅延日数を計算
     */
    calculateDelayDays(plannedEndDate, status, progress, actualEndDate) {
        if (!plannedEndDate) return 0;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const plannedEnd = new Date(plannedEndDate);
        plannedEnd.setHours(0, 0, 0, 0);

        // 完了している場合は実績終了日と予定終了日を比較
        if ((status === 'completed' || progress === 100) && actualEndDate) {
            const actualEnd = new Date(actualEndDate);
            actualEnd.setHours(0, 0, 0, 0);
            return Math.round((actualEnd - plannedEnd) / (1000 * 60 * 60 * 24));
        } else {
            // 未完了の場合は今日と予定終了日を比較
            return Math.round((today - plannedEnd) / (1000 * 60 * 60 * 24));
        }
    }

    /**
     * タスクの遅延バッジを更新
     */
    updateDelayBadgeForTask(taskId, row) {
        if (!this.dataManager) return;

        const task = this.dataManager.getTaskById(taskId);
        if (!task) return;

        // 現在の行から日付を取得（入力フィールドがある場合）
        const plannedEndInput = row.querySelector('input[data-field="planned_end_date"]');
        const plannedEnd = plannedEndInput ? plannedEndInput.value : task.planned_end_date;

        const actualEndInput = row.querySelector('input[data-field="actual_end_date"]');
        const actualEnd = actualEndInput ? actualEndInput.value : task.actual_end_date;

        const statusSelect = row.querySelector('select[data-field="status"]');
        const status = statusSelect ? statusSelect.value : task.status;

        const progressInput = row.querySelector('input[data-field="progress"]');
        const progress = progressInput ? parseInt(progressInput.value) || 0 : task.progress || 0;

        // 遅延日数を計算
        const delayDays = this.calculateDelayDays(plannedEnd, status, progress, actualEnd);

        // DataManagerも更新
        this.dataManager.updateTaskField(taskId, 'delay_days', delayDays);

        // 遅延バッジセルを更新
        const delayCells = row.querySelectorAll('td');
        // 遅延は17番目のセル（0-indexed）- 編集モードでも同じ位置
        if (delayCells.length > 17) {
            delayCells[17].innerHTML = this.createDelayBadge(delayDays);
        }
    }

    /**
     * 親タスクの日付をサブタスクに合わせて同期
     */
    syncParentTaskDates(parentId) {
        if (!this.dataManager) return;

        const parentTask = this.dataManager.getTaskById(parentId);
        if (!parentTask || !parentTask.subtasks || parentTask.subtasks.length === 0) return;

        // サブタスクから最小開始日、最大終了日を取得
        let minStartDate = null;
        let maxEndDate = null;

        parentTask.subtasks.forEach(subtask => {
            // 現在の入力値も考慮（DOMから取得）
            const subtaskRow = this.tableBody.querySelector(`tr[data-task-id="${subtask.id}"]`);
            let startDate = subtask.planned_start_date;
            let endDate = subtask.planned_end_date;

            if (subtaskRow) {
                const startInput = subtaskRow.querySelector('input[data-field="planned_start_date"]');
                const endInput = subtaskRow.querySelector('input[data-field="planned_end_date"]');
                if (startInput && startInput.value) startDate = startInput.value;
                if (endInput && endInput.value) endDate = endInput.value;
            }

            if (startDate) {
                const start = new Date(startDate);
                if (!minStartDate || start < minStartDate) {
                    minStartDate = start;
                }
            }
            if (endDate) {
                const end = new Date(endDate);
                if (!maxEndDate || end > maxEndDate) {
                    maxEndDate = end;
                }
            }
        });

        // 親タスクの行を更新
        const parentRow = this.tableBody.querySelector(`tr[data-task-id="${parentId}"]`);
        if (!parentRow) return;

        let needsUpdate = false;

        if (minStartDate) {
            const startInput = parentRow.querySelector('input[data-field="planned_start_date"]');
            const newStartDate = this.formatDateForInput(minStartDate);
            if (startInput && startInput.value !== newStartDate) {
                startInput.value = newStartDate;
                this.dataManager.updateTaskField(parentId, 'planned_start_date', newStartDate);
                needsUpdate = true;
            }
        }

        if (maxEndDate) {
            const endInput = parentRow.querySelector('input[data-field="planned_end_date"]');
            const newEndDate = this.formatDateForInput(maxEndDate);
            if (endInput && endInput.value !== newEndDate) {
                endInput.value = newEndDate;
                this.dataManager.updateTaskField(parentId, 'planned_end_date', newEndDate);
                needsUpdate = true;
            }
        }

        // 親タスクの遅延日数も更新
        if (needsUpdate) {
            this.updateDelayBadgeForTask(parentId, parentRow);
            // 変更を記録（ハイライト表示）
            parentRow.classList.add('modified');
        }
    }

    /**
     * 日付をinput[type=date]用にフォーマット
     */
    formatDateForInput(date) {
        const d = date instanceof Date ? date : new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * 担当者のカラーを取得
     */
    getAssigneeColor(assigneeName) {
        if (!assigneeName || assigneeName === '-') return 'from-gray-400 to-gray-500';
        if (!this.assigneeColors[assigneeName]) {
            this.assigneeColors[assigneeName] = this.colorPalette[this.colorIndex % this.colorPalette.length];
            this.colorIndex++;
        }
        return this.assigneeColors[assigneeName];
    }

    /**
     * 担当者表示HTMLを生成（カラー付き丸アイコン）
     */
    createAssigneeHtml(assigneeName) {
        if (!assigneeName || assigneeName === '-') {
            return '<span class="text-slate-500 text-xs">-</span>';
        }
        const color = this.getAssigneeColor(assigneeName);
        return `<div class="flex items-center justify-center">
            <div class="w-5 h-5 rounded-full bg-gradient-to-br ${color} mr-1 flex-shrink-0"></div>
            <span class="text-xs text-slate-600 truncate">${this.escapeHtml(assigneeName)}</span>
        </div>`;
    }

    /**
     * タスクアイコンを生成
     */
    createTaskIcon(isSubtask) {
        return isSubtask
            ? '<i class="fas fa-caret-right text-blue-500 mr-2"></i>'
            : '<i class="fas fa-clipboard-list text-blue-500 mr-2"></i>';
    }

    /**
     * フッター統計を更新
     */
    updateFooterStats(stats) {
        if (this.elements.totalCount) {
            this.elements.totalCount.textContent = `${stats.total}件`;
        }
        if (this.elements.completedCount) {
            this.elements.completedCount.textContent = `${stats.completed}件`;
        }
        if (this.elements.progressCount) {
            this.elements.progressCount.textContent = `${stats.in_progress}件`;
        }
        if (this.elements.notStartedCount) {
            this.elements.notStartedCount.textContent = `${stats.not_started}件`;
        }
        if (this.elements.delayedCount) {
            this.elements.delayedCount.textContent = `${stats.delayed}件`;
        }
    }

    /**
     * Undoボタンの状態を更新
     */
    updateUndoButton(canUndo) {
        if (this.elements.undoBtn) {
            this.elements.undoBtn.disabled = !canUndo;
        }
    }

    /**
     * 行の選択状態を設定
     */
    setRowSelection(taskId, selected) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            if (selected) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }

            const checkbox = row.querySelector('.task-checkbox');
            if (checkbox) {
                checkbox.checked = selected;
            }
        }
    }

    /**
     * 全選択/全解除
     */
    setAllSelection(selected) {
        // 選択前に全ての日付入力値をDataManagerに保存
        if (this.isEditMode && this.dataManager) {
            const dateFields = ['planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date'];
            const rows = this.tableBody.querySelectorAll('.task-row');
            rows.forEach(row => {
                dateFields.forEach(fieldName => {
                    const input = row.querySelector(`input[data-field="${fieldName}"]`);
                    if (input && input.value) {
                        const inputTaskId = input.dataset.taskId;
                        if (inputTaskId) {
                            this.dataManager.updateTaskField(inputTaskId, fieldName, input.value);
                        }
                    }
                });
            });
        }

        const checkboxes = this.tableBody.querySelectorAll('.task-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selected;
            const row = cb.closest('.task-row');
            if (row) {
                if (selected) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }
        });
    }

    /**
     * 選択されたタスクIDを取得
     */
    getSelectedTaskIds() {
        const selected = [];
        this.tableBody.querySelectorAll('.task-checkbox:checked').forEach(cb => {
            selected.push(cb.dataset.taskId);
        });
        return selected;
    }

    /**
     * 行にハイライトを追加
     */
    highlightRow(taskId, highlightClass = 'copied') {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.classList.add(highlightClass);
            setTimeout(() => {
                row.classList.remove(highlightClass);
            }, 2000);
        }
    }

    /**
     * ドラッグ中のスタイルを適用
     */
    setDraggingStyle(taskId, isDragging) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            if (isDragging) {
                row.classList.add('dragging');
            } else {
                row.classList.remove('dragging');
            }
        }
    }

    /**
     * ドラッグオーバーのスタイルを適用
     */
    setDragOverStyle(taskId, isOver) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            if (isOver) {
                row.classList.add('drag-over');
            } else {
                row.classList.remove('drag-over');
            }
        }
    }

    /**
     * 編集モードを設定
     */
    setEditMode(isEditMode) {
        this.isEditMode = isEditMode;
        const container = document.getElementById('table-container');
        if (container) {
            if (isEditMode) {
                container.classList.add('edit-mode');
            } else {
                container.classList.remove('edit-mode');
            }
        }
    }

    /**
     * 行の表示/非表示を設定（フィルター用）
     */
    setRowVisibility(taskId, visible) {
        const row = this.tableBody.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.style.display = visible ? '' : 'none';
        }
    }

    /**
     * すべての行を表示
     */
    showAllRows() {
        this.tableBody.querySelectorAll('.task-row').forEach(row => {
            row.style.display = '';
        });
    }

    // ========== ユーティリティメソッド ==========

    /**
     * HTMLエスケープ
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * 日付をフォーマット
     */
    formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        const yy = String(date.getFullYear()).slice(-2);
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yy}/${mm}/${dd}`;
    }

    /**
     * 金額をフォーマット
     */
    formatCost(cost) {
        if (!cost) return '-';
        return '¥' + Number(cost).toLocaleString();
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskTableRenderer = TaskTableRenderer;
}
