/**
 * TaskDragDropHandler - タスクのドラッグ&ドロップ並び替え機能
 */
class TaskDragDropHandler {
    constructor(options = {}) {
        this.app = options.app || null;
        this.tableBody = options.tableBody || document.getElementById('task-tbody');
        this.dataManager = options.dataManager || null;
        this.tableRenderer = options.tableRenderer || null;

        // ドラッグ状態
        this.draggedRow = null;
        this.draggedTaskId = null;
        this.dragStartY = 0;
        this.placeholder = null;

        // コールバック
        this.onReorder = options.onReorder || null;

        // 初期化
        this.init();
    }

    /**
     * 初期化
     */
    init() {
        if (!this.tableBody) return;

        // イベント委譲パターン：テーブル全体でmousedownを監視
        this.tableBody.addEventListener('mousedown', (e) => {
            const handle = e.target.closest('.drag-handle');
            if (handle) {
                this.onDragStart(e);
            }
        });

        // グローバルイベント
        document.addEventListener('mousemove', (e) => this.onMouseMove(e));
        document.addEventListener('mouseup', (e) => this.onMouseUp(e));
    }

    /**
     * ドラッグハンドルにイベントを設定（後方互換性のため残す）
     */
    attachDragHandlers() {
        // イベント委譲を使用しているため、個別のイベント設定は不要
    }

    /**
     * 新しい行にドラッグハンドラーを設定（後方互換性のため残す）
     */
    attachToRow(row) {
        // イベント委譲を使用しているため、個別のイベント設定は不要
    }

    /**
     * ドラッグ開始
     */
    onDragStart(e) {
        // 編集モードでない場合は無視
        if (this.app && !this.app.isEditMode) return;

        e.preventDefault();

        const row = e.target.closest('.task-row');
        if (!row) return;

        // サブタスク行はドラッグ不可
        if (row.classList.contains('subtask-row')) return;

        this.draggedRow = row;
        this.draggedTaskId = row.dataset.taskId;
        this.dragStartY = e.clientY;

        // ドラッグ開始時の元のインデックスを保存
        this.originalIndex = this.getTaskIndex(this.draggedTaskId);

        // ドラッグ中のスタイルを適用
        row.classList.add('dragging');

        // プレースホルダーを作成
        this.createPlaceholder(row);

        // カーソルを変更
        document.body.style.cursor = 'grabbing';
    }

    /**
     * マウス移動時
     */
    onMouseMove(e) {
        if (!this.draggedRow) return;

        e.preventDefault();

        // ドラッグ中の行を移動
        const deltaY = e.clientY - this.dragStartY;

        // ドロップ先を検出
        const targetRow = this.getTargetRow(e.clientY);

        if (targetRow && targetRow !== this.draggedRow && targetRow !== this.placeholder) {
            // プレースホルダーを移動
            this.movePlaceholder(targetRow, e.clientY);
        }
    }

    /**
     * マウスアップ時（ドラッグ終了）
     */
    onMouseUp(e) {
        if (!this.draggedRow) return;

        // スタイルをリセット
        this.draggedRow.classList.remove('dragging');
        document.body.style.cursor = '';

        // プレースホルダーの位置に行を移動
        if (this.placeholder && this.placeholder.parentNode) {
            // プレースホルダーの位置から新しいインデックスを計算
            const newIndex = this.getNewIndexFromPlaceholder();
            const oldIndex = this.originalIndex;

            // プレースホルダーを削除して実際の行を挿入
            this.placeholder.parentNode.insertBefore(this.draggedRow, this.placeholder);
            this.placeholder.remove();

            // データを更新
            if (this.dataManager && oldIndex !== -1 && newIndex !== -1 && oldIndex !== newIndex) {
                this.dataManager.reorderTask(oldIndex, newIndex);

                // テーブルを再描画
                if (this.app) {
                    this.app.render();
                }

                // コールバック
                if (this.onReorder) {
                    this.onReorder(this.draggedTaskId, oldIndex, newIndex);
                }
            }
        }

        // ドラッグオーバースタイルを削除
        this.tableBody.querySelectorAll('.drag-over').forEach(row => {
            row.classList.remove('drag-over');
        });

        // 状態をリセット
        this.draggedRow = null;
        this.draggedTaskId = null;
        this.placeholder = null;
        this.originalIndex = -1;

        // 行番号を更新
        this.updateRowNumbers();
    }

    /**
     * プレースホルダーを作成
     */
    createPlaceholder(row) {
        this.placeholder = document.createElement('tr');
        this.placeholder.className = 'task-row-placeholder';
        this.placeholder.style.cssText = `
            height: ${row.offsetHeight}px;
            background: #dbeafe;
            border: 2px dashed #3b82f6;
        `;

        // 空のセルを追加
        const cellCount = row.querySelectorAll('td').length;
        for (let i = 0; i < cellCount; i++) {
            this.placeholder.appendChild(document.createElement('td'));
        }

        // 元の位置にプレースホルダーを挿入
        row.parentNode.insertBefore(this.placeholder, row.nextSibling);
    }

    /**
     * プレースホルダーを移動
     */
    movePlaceholder(targetRow, mouseY) {
        if (!this.placeholder) return;

        const targetRect = targetRow.getBoundingClientRect();
        const targetMiddle = targetRect.top + targetRect.height / 2;

        // マウスが行の上半分にある場合は上に、下半分の場合は下に挿入
        if (mouseY < targetMiddle) {
            targetRow.parentNode.insertBefore(this.placeholder, targetRow);
        } else {
            targetRow.parentNode.insertBefore(this.placeholder, targetRow.nextSibling);
        }

        // ドラッグオーバースタイル
        this.tableBody.querySelectorAll('.drag-over').forEach(row => {
            row.classList.remove('drag-over');
        });
        targetRow.classList.add('drag-over');
    }

    /**
     * マウス位置から対象の行を取得
     */
    getTargetRow(mouseY) {
        // サブタスク行は除外して親タスク行のみ対象
        const rows = this.tableBody.querySelectorAll('.task-row:not(.dragging):not(.task-row-placeholder):not(.subtask-row)');

        for (const row of rows) {
            const rect = row.getBoundingClientRect();
            if (mouseY >= rect.top && mouseY <= rect.bottom) {
                return row;
            }
        }

        return null;
    }

    /**
     * 行のインデックスを取得
     */
    getRowIndex(row) {
        const rows = Array.from(this.tableBody.querySelectorAll('.task-row:not(.task-row-placeholder)'));
        return rows.indexOf(row);
    }

    /**
     * タスクIDからデータマネージャー上のインデックスを取得
     */
    getTaskIndex(taskId) {
        if (!this.dataManager) return -1;
        const tasks = this.dataManager.getTasks();
        return tasks.findIndex(t => t.id == taskId);
    }

    /**
     * プレースホルダーの位置から新しいインデックスを計算
     */
    getNewIndexFromPlaceholder() {
        if (!this.placeholder || !this.dataManager) return -1;

        // プレースホルダーの前にある親タスク行の数をカウント
        let count = 0;
        let sibling = this.placeholder.previousElementSibling;

        while (sibling) {
            // 親タスク行のみカウント（サブタスク行は除く）
            if (sibling.classList.contains('task-row') &&
                !sibling.classList.contains('subtask-row') &&
                !sibling.classList.contains('task-row-placeholder') &&
                sibling !== this.draggedRow) {
                count++;
            }
            sibling = sibling.previousElementSibling;
        }

        return count;
    }

    /**
     * 行番号を更新
     */
    updateRowNumbers() {
        let rowNo = 1;
        this.tableBody.querySelectorAll('.task-row').forEach(row => {
            const noCell = row.querySelector('td:nth-child(3)');
            if (noCell) {
                noCell.textContent = rowNo++;
            }
            row.dataset.index = rowNo - 2;
        });
    }

    /**
     * テーブル再描画後に呼び出す
     */
    refresh() {
        this.attachDragHandlers();
    }
}

// グローバルにエクスポート
if (typeof window !== 'undefined') {
    window.TaskDragDropHandler = TaskDragDropHandler;
}
