/**
 * TaskContextMenu - 右クリックメニュー（コンテキストメニュー）の管理
 */
class TaskContextMenu {
    constructor(options = {}) {
        this.app = options.app || null;
        this.dataManager = options.dataManager || null;
        this.tableRenderer = options.tableRenderer || null;

        // DOM要素
        this.taskMenu = options.taskMenu || document.getElementById('context-menu');
        this.subtaskMenu = options.subtaskMenu || document.getElementById('subtask-context-menu');

        // 現在の対象
        this.targetTaskId = null;
        this.targetRow = null;
        this.isSubtask = false;

        // 初期化
        this.init();
    }

    /**
     * 初期化
     */
    init() {
        // ドキュメントクリックでメニューを閉じる
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.context-menu')) {
                this.hide();
            }
        });

        // Escapeキーでメニューを閉じる
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hide();
            }
        });

        // メニュー項目のイベントを設定
        this.attachMenuEvents();
    }

    /**
     * メニュー項目のイベントを設定
     */
    attachMenuEvents() {
        // タスク用メニュー
        if (this.taskMenu) {
            this.taskMenu.querySelectorAll('.context-menu-item').forEach(item => {
                const action = item.getAttribute('onclick');
                if (action) {
                    // onclickを削除してイベントリスナーに置き換え
                    item.removeAttribute('onclick');
                    const actionName = action.replace('()', '').trim();
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                                                this.executeAction(actionName);
                    });
                }
            });
        }

        // サブタスク用メニュー
        if (this.subtaskMenu) {
            this.subtaskMenu.querySelectorAll('.context-menu-item').forEach(item => {
                const action = item.getAttribute('onclick');
                if (action) {
                    item.removeAttribute('onclick');
                    const actionName = action.replace('()', '').trim();
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                                                this.executeAction(actionName);
                    });
                }
            });
        }
    }

    /**
     * メニューを表示
     */
    show(event, taskId, row) {
        event.preventDefault();
        event.stopPropagation();

        // 編集モードでない場合は表示しない
        if (this.app && !this.app.isEditMode) {
            return;
        }

        this.targetTaskId = taskId;
        this.targetRow = row;
        this.isSubtask = row.classList.contains('subtask-row');

        // 他のメニューを閉じる
        this.hide();

        // 適切なメニューを表示
        const menu = this.isSubtask ? this.subtaskMenu : this.taskMenu;
        if (!menu) return;

        // 位置を設定
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';

        // 画面外に出ないように調整
        const menuRect = menu.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        if (event.pageX + menuRect.width > viewportWidth) {
            menu.style.left = (event.pageX - menuRect.width) + 'px';
        }

        if (event.pageY + menuRect.height > viewportHeight) {
            menu.style.top = (event.pageY - menuRect.height) + 'px';
        }

        menu.classList.add('show');
        menu.dataset.taskId = taskId;
    }

    /**
     * メニューを非表示
     */
    hide() {
        [this.taskMenu, this.subtaskMenu].forEach(menu => {
            if (menu) {
                menu.classList.remove('show');
            }
        });
    }

    /**
     * アクションを実行
     */
    executeAction(actionName) {
        if (!this.targetTaskId || !this.dataManager) {
            this.hide();
            return;
        }

        const task = this.dataManager.getTaskById(this.targetTaskId);
        if (!task) {
            this.hide();
            return;
        }

        // 編集モードでない場合は編集モードに切り替え
        if (this.app && !this.app.isEditMode) {
            this.app.switchViewMode('edit');
        }

        switch (actionName) {
            case 'addRowAbove':
                this.addRowAbove(task);
                break;
            case 'addRowBelow':
                this.addRowBelow(task);
                break;
            case 'addSubtaskFromMenu':
                this.addSubtask(task);
                break;
            case 'copyTaskAbove':
                this.copyTaskAbove(task);
                break;
            case 'copyTaskBelow':
                this.copyTaskBelow(task);
                break;
            case 'deleteTaskFromMenu':
            case 'deleteSubtaskFromMenu':
                this.deleteTask(task);
                break;
            case 'addSubtaskAbove':
                this.addSubtaskAbove(task);
                break;
            case 'addSubtaskBelow':
                this.addSubtaskBelow(task);
                break;
            case 'copySubtaskAbove':
                this.copySubtaskAbove(task);
                break;
            case 'copySubtaskBelow':
                this.copySubtaskBelow(task);
                break;
        }

        this.hide();
    }

    /**
     * 上に行を追加
     */
    addRowAbove(task) {
        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == task.id);

        if (index !== -1) {
            const newTask = this.createEmptyTask();
            this.dataManager.addTask(newTask, index);
            this.notifyChange('上に行を追加しました');
        }
    }

    /**
     * 下に行を追加
     */
    addRowBelow(task) {
        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == task.id);

        if (index !== -1) {
            const newTask = this.createEmptyTask();
            this.dataManager.addTask(newTask, index + 1);
            this.notifyChange('下に行を追加しました');
        }
    }

    /**
     * サブタスクを追加
     */
    addSubtask(task) {
        const newSubtask = this.createEmptyTask();
        this.dataManager.addSubtask(task.id, newSubtask);
        this.notifyChange('サブタスクを追加しました');
    }

    /**
     * コピーして上に追加
     */
    copyTaskAbove(task) {
        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == task.id);

        if (index !== -1) {
            const newTask = this.copyTask(task);
            this.dataManager.addTask(newTask, index);
            this.notifyChange('コピーして上に追加しました');
        }
    }

    /**
     * コピーして下に追加
     */
    copyTaskBelow(task) {
        const tasks = this.dataManager.getTasks();
        const index = tasks.findIndex(t => t.id == task.id);

        if (index !== -1) {
            const newTask = this.copyTask(task);
            this.dataManager.addTask(newTask, index + 1);
            this.notifyChange('コピーして下に追加しました');
        }
    }

    /**
     * タスクを削除
     */
    deleteTask(task) {
        if (!confirm('このタスクを削除しますか？')) {
            return;
        }

        this.dataManager.deleteTask(task.id);
        this.notifyChange('削除しました');
    }

    /**
     * 上にサブタスクを追加
     */
    addSubtaskAbove(task) {
        const parentId = this.targetRow?.dataset.parentId;
        if (!parentId) return;

        const parentTask = this.dataManager.getTaskById(parentId);
        if (!parentTask || !parentTask.subtasks) return;

        const index = parentTask.subtasks.findIndex(s => s.id == task.id);
        if (index !== -1) {
            const newSubtask = this.createEmptyTask();
            this.dataManager.addSubtask(parentId, newSubtask, index);
            this.notifyChange('上にサブタスクを追加しました');
        }
    }

    /**
     * 下にサブタスクを追加
     */
    addSubtaskBelow(task) {
        const parentId = this.targetRow?.dataset.parentId;
        if (!parentId) return;

        const parentTask = this.dataManager.getTaskById(parentId);
        if (!parentTask || !parentTask.subtasks) return;

        const index = parentTask.subtasks.findIndex(s => s.id == task.id);
        if (index !== -1) {
            const newSubtask = this.createEmptyTask();
            this.dataManager.addSubtask(parentId, newSubtask, index + 1);
            this.notifyChange('下にサブタスクを追加しました');
        }
    }

    /**
     * サブタスクをコピーして上に追加
     */
    copySubtaskAbove(task) {
        const parentId = this.targetRow?.dataset.parentId;
        if (!parentId) return;

        const parentTask = this.dataManager.getTaskById(parentId);
        if (!parentTask || !parentTask.subtasks) return;

        const index = parentTask.subtasks.findIndex(s => s.id == task.id);
        if (index !== -1) {
            const newSubtask = this.copyTask(task);
            this.dataManager.addSubtask(parentId, newSubtask, index);
            this.notifyChange('コピーして上に追加しました');
        }
    }

    /**
     * サブタスクをコピーして下に追加
     */
    copySubtaskBelow(task) {
        const parentId = this.targetRow?.dataset.parentId;
        if (!parentId) return;

        const parentTask = this.dataManager.getTaskById(parentId);
        if (!parentTask || !parentTask.subtasks) return;

        const index = parentTask.subtasks.findIndex(s => s.id == task.id);
        if (index !== -1) {
            const newSubtask = this.copyTask(task);
            this.dataManager.addSubtask(parentId, newSubtask, index + 1);
            this.notifyChange('コピーして下に追加しました');
        }
    }

    /**
     * 空のタスクを作成
     */
    createEmptyTask() {
        return {
            id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            project_id: this.app?.projectId || null,
            task_name: '新規タスク',
            status: 'not_started',
            progress: 0,
            process_id: null,
            process_name: '',
            assignee_id: null,
            assignee_name: '',
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

    /**
     * タスクをコピー
     */
    copyTask(task) {
        return {
            ...task,
            id: 'new_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            task_name: task.task_name + ' (コピー)',
            subtasks: [] // サブタスクはコピーしない
        };
    }

    /**
     * 変更を通知
     */
    notifyChange(message) {
        // テーブルを再描画
        if (this.app) {
            this.app.render();
        } else if (this.tableRenderer) {
            this.tableRenderer.render(this.dataManager.getTasks());
        }
        // トースト表示は不要（ユーザー要望）
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskContextMenu = TaskContextMenu;
}
