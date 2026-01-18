/**
 * TaskDataManager - タスクデータの管理を担当するクラス
 * データの取得、更新、フィルタリング、統計計算を行う
 */
class TaskDataManager {
    constructor(options = {}) {
        this.projectId = options.projectId || null;
        this.tasks = options.tasks || [];
        this.processes = options.processes || [];
        this.members = options.members || [];
        this.apiBaseUrl = options.apiBaseUrl || '/api/tasks';
        this.csrfToken = options.csrfToken || '';
        this.csrfHash = options.csrfHash || '';

        // 変更履歴（Undo用）
        this.history = [];
        this.maxHistorySize = 20;

        // 削除されたタスクIDの追跡（一括登録時にAPI側でも削除するため）
        this.deletedTaskIds = [];

        // コールバック
        this.onDataChange = options.onDataChange || null;
        this.onStatsUpdate = options.onStatsUpdate || null;
    }

    /**
     * タスク一覧を取得
     */
    getTasks() {
        return this.tasks;
    }

    /**
     * タスクをIDで取得
     */
    getTaskById(taskId) {
        const task = this.tasks.find(t => t.id == taskId);
        if (task) return task;

        // サブタスクから検索
        for (const t of this.tasks) {
            if (t.subtasks) {
                const subtask = t.subtasks.find(s => s.id == taskId);
                if (subtask) return subtask;
            }
        }
        return null;
    }

    /**
     * タスクを追加
     */
    addTask(taskData, insertIndex = null) {
        this.saveHistory('add', { taskData, insertIndex });

        if (insertIndex !== null && insertIndex >= 0) {
            this.tasks.splice(insertIndex, 0, taskData);
        } else {
            this.tasks.push(taskData);
        }

        this.notifyDataChange();
        return taskData;
    }

    /**
     * タスクを更新
     */
    updateTask(taskId, updates) {
        const task = this.getTaskById(taskId);
        if (!task) return null;

        this.saveHistory('update', { taskId, oldData: { ...task }, newData: updates });

        Object.assign(task, updates);
        this.notifyDataChange();
        return task;
    }

    /**
     * サブタスクを更新
     */
    updateSubtask(parentId, subtaskId, updates) {
        const parent = this.getTaskById(parentId);
        if (!parent || !parent.subtasks) return null;

        const subtask = parent.subtasks.find(st => st.id == subtaskId);
        if (!subtask) return null;

        this.saveHistory('updateSubtask', { parentId, subtaskId, oldData: { ...subtask }, newData: updates });

        Object.assign(subtask, updates);
        this.notifyDataChange();
        return subtask;
    }

    /**
     * タスクの単一フィールドを更新（インライン編集用）
     */
    updateTaskField(taskId, field, value) {
        const task = this.getTaskById(taskId);
        if (!task) return null;

        // 履歴に保存
        this.saveHistory('updateField', { taskId, field, oldValue: task[field], newValue: value });

        // フィールドを更新
        task[field] = value;

        // 工程や担当者の場合、名前も更新
        if (field === 'process_id' && value) {
            const process = this.processes.find(p => p.id == value);
            if (process) {
                task.process_name = process.name;
            }
        }
        if (field === 'assignee_id' && value) {
            const member = this.members.find(m => m.id == value);
            if (member) {
                task.assignee_name = member.name;
            }
        }

        return task;
    }

    /**
     * タスクを削除
     */
    deleteTask(taskId) {
        const taskIndex = this.tasks.findIndex(t => t.id == taskId);

        if (taskIndex !== -1) {
            const deletedTask = this.tasks[taskIndex];
            this.saveHistory('delete', { taskData: deletedTask, index: taskIndex });
            this.tasks.splice(taskIndex, 1);
            this.notifyDataChange();
            return deletedTask;
        }

        // サブタスクから検索して削除
        for (const task of this.tasks) {
            if (task.subtasks) {
                const subtaskIndex = task.subtasks.findIndex(s => s.id == taskId);
                if (subtaskIndex !== -1) {
                    const deletedSubtask = task.subtasks[subtaskIndex];
                    this.saveHistory('deleteSubtask', {
                        parentId: task.id,
                        subtaskData: deletedSubtask,
                        index: subtaskIndex
                    });
                    task.subtasks.splice(subtaskIndex, 1);
                    this.notifyDataChange();
                    return deletedSubtask;
                }
            }
        }

        return null;
    }

    /**
     * タスクの順序を変更
     */
    reorderTask(fromIndex, toIndex) {
        if (fromIndex === toIndex) return;

        this.saveHistory('reorder', { fromIndex, toIndex });

        const [movedTask] = this.tasks.splice(fromIndex, 1);
        this.tasks.splice(toIndex, 0, movedTask);

        this.notifyDataChange();
    }

    /**
     * サブタスクを追加
     */
    addSubtask(parentId, subtaskData, insertIndex = null) {
        const parentTask = this.tasks.find(t => t.id == parentId);
        if (!parentTask) return null;

        if (!parentTask.subtasks) {
            parentTask.subtasks = [];
        }

        this.saveHistory('addSubtask', { parentId, subtaskData, insertIndex });

        if (insertIndex !== null && insertIndex >= 0) {
            parentTask.subtasks.splice(insertIndex, 0, subtaskData);
        } else {
            parentTask.subtasks.push(subtaskData);
        }

        this.notifyDataChange();
        return subtaskData;
    }

    /**
     * 一括更新
     */
    bulkUpdate(taskIds, updates) {
        const updatedTasks = [];
        const historyData = [];

        taskIds.forEach(taskId => {
            const task = this.getTaskById(taskId);
            if (task) {
                historyData.push({ taskId, oldData: { ...task } });
                Object.assign(task, updates);
                updatedTasks.push(task);
            }
        });

        if (updatedTasks.length > 0) {
            this.saveHistory('bulkUpdate', { historyData, updates });
            this.notifyDataChange();
        }

        return updatedTasks;
    }

    /**
     * 一括削除
     */
    bulkDelete(taskIds) {
        const deletedTasks = [];
        const historyData = [];

        taskIds.forEach(taskId => {
            const taskIndex = this.tasks.findIndex(t => t.id == taskId);
            if (taskIndex !== -1) {
                const deletedTask = this.tasks[taskIndex];
                historyData.push({ taskData: deletedTask, index: taskIndex, isSubtask: false });
                this.tasks.splice(taskIndex, 1);
                deletedTasks.push(deletedTask);
                // 既存タスク（new_で始まらないID）の場合、削除リストに追加
                if (!String(taskId).startsWith('new_')) {
                    this.deletedTaskIds.push(taskId);
                }
            } else {
                // サブタスクから検索
                for (const task of this.tasks) {
                    if (task.subtasks) {
                        const subtaskIndex = task.subtasks.findIndex(s => s.id == taskId);
                        if (subtaskIndex !== -1) {
                            const deletedSubtask = task.subtasks[subtaskIndex];
                            historyData.push({
                                taskData: deletedSubtask,
                                index: subtaskIndex,
                                parentId: task.id,
                                isSubtask: true
                            });
                            task.subtasks.splice(subtaskIndex, 1);
                            deletedTasks.push(deletedSubtask);
                            // 既存タスク（new_で始まらないID）の場合、削除リストに追加
                            if (!String(taskId).startsWith('new_')) {
                                this.deletedTaskIds.push(taskId);
                            }
                            break;
                        }
                    }
                }
            }
        });

        if (deletedTasks.length > 0) {
            this.saveHistory('bulkDelete', historyData);
            this.notifyDataChange();
        }

        return deletedTasks;
    }

    /**
     * 削除されたタスクIDを取得
     */
    getDeletedTaskIds() {
        return this.deletedTaskIds;
    }

    /**
     * 削除されたタスクIDをクリア
     */
    clearDeletedTaskIds() {
        this.deletedTaskIds = [];
    }

    /**
     * フィルタリング
     */
    filterTasks(filters) {
        return this.tasks.filter(task => {
            return this.matchesFilter(task, filters);
        }).map(task => {
            if (task.subtasks) {
                return {
                    ...task,
                    subtasks: task.subtasks.filter(subtask => this.matchesFilter(subtask, filters))
                };
            }
            return task;
        });
    }

    /**
     * フィルター条件にマッチするかチェック
     */
    matchesFilter(task, filters) {
        // 工程フィルター
        if (filters.process && task.process_id != filters.process) {
            return false;
        }

        // 担当者フィルター
        if (filters.assignee && task.assignee_id != filters.assignee) {
            return false;
        }

        // ステータスフィルター
        if (filters.status && task.status !== filters.status) {
            return false;
        }

        // 遅延フィルター
        if (filters.delay) {
            const isDelayed = task.delay_days && task.delay_days > 0;
            if (filters.delay === 'delayed' && !isDelayed) return false;
            if (filters.delay === 'on-time' && isDelayed) return false;
        }

        // テキスト検索
        if (filters.search) {
            const searchLower = filters.search.toLowerCase();
            const taskName = (task.task_name || '').toLowerCase();
            const assigneeName = (task.assignee_name || '').toLowerCase();
            const description = (task.description || '').toLowerCase();

            if (!taskName.includes(searchLower) &&
                !assigneeName.includes(searchLower) &&
                !description.includes(searchLower)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 統計情報を計算
     */
    calculateStats() {
        const stats = {
            total: 0,
            completed: 0,
            in_progress: 0,
            not_started: 0,
            on_hold: 0,
            delayed: 0,
            progress_sum: 0,
            progress_avg: 0
        };

        const processStats = (task) => {
            stats.total++;
            stats.progress_sum += (task.progress || 0);

            switch (task.status) {
                case 'completed':
                    stats.completed++;
                    break;
                case 'in_progress':
                    stats.in_progress++;
                    break;
                case 'not_started':
                    stats.not_started++;
                    break;
                case 'on_hold':
                    stats.on_hold++;
                    break;
            }

            if (task.delay_days && task.delay_days > 0) {
                stats.delayed++;
            }
        };

        this.tasks.forEach(task => {
            processStats(task);
            if (task.subtasks) {
                task.subtasks.forEach(subtask => processStats(subtask));
            }
        });

        stats.progress_avg = stats.total > 0 ? Math.round(stats.progress_sum / stats.total) : 0;

        if (this.onStatsUpdate) {
            this.onStatsUpdate(stats);
        }

        return stats;
    }

    /**
     * 工程別統計を計算
     */
    calculateStatsByProcess() {
        const statsByProcess = {};

        this.processes.forEach(process => {
            statsByProcess[process.id] = {
                name: process.name,
                total: 0,
                completed: 0,
                progress_sum: 0,
                progress_avg: 0
            };
        });

        const processStats = (task) => {
            if (task.process_id && statsByProcess[task.process_id]) {
                const stat = statsByProcess[task.process_id];
                stat.total++;
                stat.progress_sum += (task.progress || 0);
                if (task.status === 'completed') {
                    stat.completed++;
                }
            }
        };

        this.tasks.forEach(task => {
            processStats(task);
            if (task.subtasks) {
                task.subtasks.forEach(subtask => processStats(subtask));
            }
        });

        Object.values(statsByProcess).forEach(stat => {
            stat.progress_avg = stat.total > 0 ? Math.round(stat.progress_sum / stat.total) : 0;
        });

        return statsByProcess;
    }

    /**
     * 履歴を保存（Undo用）
     */
    saveHistory(action, data) {
        this.history.push({
            action,
            data,
            timestamp: Date.now()
        });

        // 履歴のサイズ制限
        if (this.history.length > this.maxHistorySize) {
            this.history.shift();
        }
    }

    /**
     * Undo実行
     */
    undo() {
        if (this.history.length === 0) return false;

        const lastAction = this.history.pop();

        switch (lastAction.action) {
            case 'add':
                // 追加を取り消し
                const addedIndex = this.tasks.findIndex(t => t === lastAction.data.taskData);
                if (addedIndex !== -1) {
                    this.tasks.splice(addedIndex, 1);
                }
                break;

            case 'update':
                // 更新を取り消し
                const taskToRevert = this.getTaskById(lastAction.data.taskId);
                if (taskToRevert) {
                    Object.assign(taskToRevert, lastAction.data.oldData);
                }
                break;

            case 'delete':
                // 削除を取り消し
                this.tasks.splice(lastAction.data.index, 0, lastAction.data.taskData);
                break;

            case 'reorder':
                // 並び替えを取り消し
                const { fromIndex, toIndex } = lastAction.data;
                const [movedBack] = this.tasks.splice(toIndex, 1);
                this.tasks.splice(fromIndex, 0, movedBack);
                break;

            case 'addSubtask':
                const parent = this.tasks.find(t => t.id == lastAction.data.parentId);
                if (parent && parent.subtasks) {
                    const idx = parent.subtasks.findIndex(s => s === lastAction.data.subtaskData);
                    if (idx !== -1) {
                        parent.subtasks.splice(idx, 1);
                    }
                }
                break;

            case 'deleteSubtask':
                const parentTask = this.tasks.find(t => t.id == lastAction.data.parentId);
                if (parentTask) {
                    if (!parentTask.subtasks) parentTask.subtasks = [];
                    parentTask.subtasks.splice(lastAction.data.index, 0, lastAction.data.subtaskData);
                }
                break;

            case 'bulkUpdate':
                lastAction.data.historyData.forEach(item => {
                    const task = this.getTaskById(item.taskId);
                    if (task) {
                        Object.assign(task, item.oldData);
                    }
                });
                break;

            case 'bulkDelete':
                lastAction.data.forEach(item => {
                    if (item.isSubtask) {
                        const parentTask = this.tasks.find(t => t.id == item.parentId);
                        if (parentTask) {
                            if (!parentTask.subtasks) parentTask.subtasks = [];
                            parentTask.subtasks.splice(item.index, 0, item.taskData);
                        }
                    } else {
                        this.tasks.splice(item.index, 0, item.taskData);
                    }
                });
                break;
        }

        this.notifyDataChange();
        return true;
    }

    /**
     * Undo可能かどうか
     */
    canUndo() {
        return this.history.length > 0;
    }

    /**
     * 履歴をクリア
     */
    clearHistory() {
        this.history = [];
    }

    /**
     * データ変更を通知
     */
    notifyDataChange() {
        if (this.onDataChange) {
            this.onDataChange(this.tasks);
        }
    }

    // ========== API通信メソッド ==========

    /**
     * APIヘッダーを取得
     */
    getApiHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (this.csrfToken && this.csrfHash) {
            headers[this.csrfToken] = this.csrfHash;
        }
        return headers;
    }

    /**
     * タスク一覧をAPIから取得
     */
    async fetchTasks() {
        try {
            const response = await fetch(`${this.apiBaseUrl}?project_id=${this.projectId}`, {
                headers: this.getApiHeaders()
            });
            const result = await response.json();

            if (result.success) {
                this.tasks = result.data;
                this.notifyDataChange();
                return result.data;
            }
            throw new Error(result.error || 'Failed to fetch tasks');
        } catch (error) {
            console.error('Error fetching tasks:', error);
            throw error;
        }
    }

    /**
     * タスクをAPIに保存
     */
    async saveTaskToApi(taskData) {
        const url = taskData.id
            ? `${this.apiBaseUrl}/${taskData.id}`
            : this.apiBaseUrl;
        const method = taskData.id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method,
                headers: this.getApiHeaders(),
                body: JSON.stringify(taskData)
            });
            const result = await response.json();

            if (result.success) {
                return result.data;
            }
            throw new Error(result.error || result.errors || 'Failed to save task');
        } catch (error) {
            console.error('Error saving task:', error);
            throw error;
        }
    }

    /**
     * タスクをAPIから削除
     */
    async deleteTaskFromApi(taskId) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/${taskId}`, {
                method: 'DELETE',
                headers: this.getApiHeaders()
            });
            const result = await response.json();

            if (result.success) {
                return true;
            }
            throw new Error(result.error || 'Failed to delete task');
        } catch (error) {
            console.error('Error deleting task:', error);
            throw error;
        }
    }

    /**
     * 一括削除API
     */
    async bulkDeleteFromApi(taskIds) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/bulk-delete`, {
                method: 'POST',
                headers: this.getApiHeaders(),
                body: JSON.stringify({ task_ids: taskIds })
            });
            const result = await response.json();

            if (result.success) {
                return result.data;
            }
            throw new Error(result.error || 'Failed to bulk delete');
        } catch (error) {
            console.error('Error bulk deleting:', error);
            throw error;
        }
    }

    /**
     * 一括更新API
     */
    async bulkUpdateToApi(taskIds, updates) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/bulk-update`, {
                method: 'POST',
                headers: this.getApiHeaders(),
                body: JSON.stringify({ task_ids: taskIds, updates })
            });
            const result = await response.json();

            if (result.success) {
                return result.data;
            }
            throw new Error(result.error || 'Failed to bulk update');
        } catch (error) {
            console.error('Error bulk updating:', error);
            throw error;
        }
    }

    /**
     * 並び順を保存
     */
    async saveOrderToApi(taskOrders) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/reorder`, {
                method: 'POST',
                headers: this.getApiHeaders(),
                body: JSON.stringify({ orders: taskOrders })
            });
            const result = await response.json();

            if (result.success) {
                return true;
            }
            throw new Error(result.error || 'Failed to save order');
        } catch (error) {
            console.error('Error saving order:', error);
            throw error;
        }
    }

    /**
     * 統計情報をAPIから取得
     */
    async fetchStats() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/stats?project_id=${this.projectId}`, {
                headers: this.getApiHeaders()
            });
            const result = await response.json();

            if (result.success) {
                if (this.onStatsUpdate) {
                    this.onStatsUpdate(result.data);
                }
                return result.data;
            }
            throw new Error(result.error || 'Failed to fetch stats');
        } catch (error) {
            console.error('Error fetching stats:', error);
            throw error;
        }
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskDataManager = TaskDataManager;
}
