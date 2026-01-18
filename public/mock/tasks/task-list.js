// タスクデータ（原価・出来高フィールド追加）
let tasks = [
    { id: 1, process: '要件定義', screen: '', name: 'ヒアリング', assignee: '山田', status: '完了', manDays: 2, startDate: '2024-01-15', endDate: '2024-01-25', plannedCost: 200000, actualManDays: 2, actualStartDate: '2024-01-15', actualEndDate: '2024-01-24', actualCost: 200000, progress: 100, delay: 0, note: '' },
    { id: 2, process: '要件定義', screen: '', name: '要件書作成', assignee: '山田', status: '完了', manDays: 3, startDate: '2024-01-26', endDate: '2024-02-15', plannedCost: 300000, actualManDays: 3, actualStartDate: '2024-01-25', actualEndDate: '2024-02-14', actualCost: 300000, progress: 100, delay: 0, note: '' },
    { id: 3, process: '設計', screen: '', name: '画面設計', assignee: '佐藤', status: '完了', manDays: 4, startDate: '2024-02-16', endDate: '2024-03-10', plannedCost: 400000, actualManDays: 5, actualStartDate: '2024-02-15', actualEndDate: '2024-03-12', actualCost: 500000, progress: 100, delay: 2, note: '仕様変更対応' },
    { id: 4, process: '設計', screen: '', name: 'DB設計', assignee: '鈴木', status: '完了', manDays: 3, startDate: '2024-03-01', endDate: '2024-03-20', plannedCost: 300000, actualManDays: 3, actualStartDate: '2024-03-01', actualEndDate: '2024-03-20', actualCost: 300000, progress: 100, delay: 0, note: '' },
    { id: 5, process: '設計', screen: '', name: 'API設計', assignee: '鈴木', status: '完了', manDays: 3, startDate: '2024-03-11', endDate: '2024-03-31', plannedCost: 300000, actualManDays: 3, actualStartDate: '2024-03-13', actualEndDate: '2024-03-31', actualCost: 300000, progress: 100, delay: 0, note: '' },
    { id: 6, process: '開発', screen: 'ログイン画面', name: 'ログイン画面実装', assignee: '田中', status: '完了', manDays: 2, startDate: '2024-04-01', endDate: '2024-04-05', plannedCost: 200000, actualManDays: 2, actualStartDate: '2024-04-01', actualEndDate: '2024-04-05', actualCost: 200000, progress: 100, delay: 0, note: '' },
    { id: 7, process: '開発', screen: 'ダッシュボード', name: 'ダッシュボード実装', assignee: '田中', status: '完了', manDays: 3, startDate: '2024-04-06', endDate: '2024-04-12', plannedCost: 300000, actualManDays: 4, actualStartDate: '2024-04-06', actualEndDate: '2024-04-14', actualCost: 400000, progress: 100, delay: 2, note: 'デザイン調整' },
    { id: 8, process: '開発', screen: '一覧画面', name: '一覧画面実装', assignee: '田中', status: '進行中', manDays: 3, startDate: '2024-04-13', endDate: '2024-04-20', plannedCost: 300000, actualManDays: '', actualStartDate: '2024-04-15', actualEndDate: '', actualCost: 150000, progress: 50, delay: 2, note: '' },
    { id: 9, process: '開発', screen: '詳細画面', name: '詳細画面実装', assignee: '田中', status: '未着手', manDays: 2, startDate: '2024-04-21', endDate: '2024-04-28', plannedCost: 200000, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' },
    { id: 10, process: '開発', screen: 'API', name: 'バックエンドAPI実装', assignee: '鈴木', status: '進行中', manDays: 10, startDate: '2024-04-05', endDate: '2024-05-10', plannedCost: 1000000, actualManDays: '', actualStartDate: '2024-04-05', actualEndDate: '', actualCost: 450000, progress: 45, delay: 0, note: '' },
    { id: 11, process: '開発', screen: '', name: '単体テスト', assignee: '高橋', status: '進行中', manDays: 5, startDate: '2024-04-15', endDate: '2024-05-20', plannedCost: 500000, actualManDays: '', actualStartDate: '2024-04-17', actualEndDate: '', actualCost: 150000, progress: 30, delay: 2, note: '' },
    { id: 12, process: 'テスト', screen: '', name: '結合テスト', assignee: '高橋', status: '未着手', manDays: 4, startDate: '2024-05-21', endDate: '2024-06-05', plannedCost: 400000, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' },
    { id: 13, process: 'テスト', screen: '', name: 'UAT', assignee: '山田', status: '未着手', manDays: 4, startDate: '2024-06-06', endDate: '2024-06-15', plannedCost: 400000, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' },
    { id: 14, process: 'リリース', screen: '', name: '本番環境構築', assignee: '鈴木', status: '未着手', manDays: 2, startDate: '2024-06-16', endDate: '2024-06-25', plannedCost: 200000, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' },
    { id: 15, process: 'リリース', screen: '', name: 'デプロイ・公開', assignee: '鈴木', status: '未着手', manDays: 1, startDate: '2024-06-26', endDate: '2024-06-30', plannedCost: 100000, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' },
];

let isEditMode = false;
let selectedRowIndex = null;
let originalTasks = [];
let undoHistory = [];
const MAX_UNDO = 20;
let copiedTask = null; // Ctrl+Cでコピーしたタスク
let draggedRowIndex = null;
let isAllProjectMode = false;

// プロジェクト変更
function onProjectChange(value) {
    isAllProjectMode = value === 'all';
    console.log('プロジェクト変更:', value, isAllProjectMode ? '(全プロジェクトモード)' : '');
    renderTable();
}

// タスク検索
function searchTasks(keyword) {
    if (!keyword) {
        renderTable();
        return;
    }
    const filtered = tasks.filter(t =>
        t.name.includes(keyword) ||
        t.process.includes(keyword) ||
        t.screen.includes(keyword) ||
        t.assignee.includes(keyword) ||
        t.note.includes(keyword)
    );
    renderFilteredTable(filtered);
}

function renderFilteredTable(filteredTasks) {
    const tbody = document.getElementById('task-tbody');
    tbody.innerHTML = '';
    filteredTasks.forEach((task, displayIndex) => {
        const originalIndex = tasks.findIndex(t => t.id === task.id);
        renderRow(task, originalIndex, displayIndex + 1);
    });
    updateCounts();
}

// 履歴に追加
function saveToHistory() {
    undoHistory.push(JSON.parse(JSON.stringify(tasks)));
    if (undoHistory.length > MAX_UNDO) {
        undoHistory.shift();
    }
}

// 元に戻す
function undoChanges() {
    if (undoHistory.length > 0) {
        tasks = undoHistory.pop();
        renderTable();
    }
}

// コピーして上に追加
function copyTaskAbove() {
    if (selectedRowIndex !== null) {
        saveToHistory();
        const copied = { ...tasks[selectedRowIndex], id: Math.max(...tasks.map(t => t.id)) + 1 };
        tasks.splice(selectedRowIndex, 0, copied);
        renderTable();
    }
    document.getElementById('context-menu').classList.remove('show');
}

// コピーして下に追加
function copyTaskBelow() {
    if (selectedRowIndex !== null) {
        saveToHistory();
        const copied = { ...tasks[selectedRowIndex], id: Math.max(...tasks.map(t => t.id)) + 1 };
        tasks.splice(selectedRowIndex + 1, 0, copied);
        renderTable();
    }
    document.getElementById('context-menu').classList.remove('show');
}

// テーブル描画
function renderTable() {
    const tbody = document.getElementById('task-tbody');
    tbody.innerHTML = '';

    tasks.forEach((task, index) => {
        renderRow(task, index, index + 1);
    });
    updateCounts();
}

function renderRow(task, index, displayNo) {
    const tbody = document.getElementById('task-tbody');
    const row = document.createElement('tr');
    row.className = 'task-row border-b border-slate-100 hover:bg-slate-50 transition-colors';
    row.dataset.index = index;

    if (copiedTask && copiedTask.id === task.id) {
        row.classList.add('copied');
    }

    let statusClass = 'status-not-started';
    if (task.status === '進行中') statusClass = 'status-in-progress';
    else if (task.status === '完了') statusClass = 'status-completed';
    if (task.delay > 0 && task.status !== '完了') statusClass = 'status-delayed';

    let delayText = '-';
    let delayClass = 'text-slate-400';
    if (task.delay > 0) { delayText = task.delay + '日遅れ'; delayClass = 'text-rose-600 font-semibold'; }
    else if (task.delay < 0) { delayText = Math.abs(task.delay) + '日先行'; delayClass = 'text-emerald-600 font-semibold'; }

    const formatCost = (cost) => cost ? '¥' + Number(cost).toLocaleString() : '-';

    if (isEditMode) {
        row.innerHTML = `
            <td class="px-2 py-2 text-center border-r border-slate-100"><input type="checkbox" class="row-checkbox rounded border-slate-300"></td>
            <td class="px-2 py-2 text-center border-r border-slate-100">
                <i class="fas fa-grip-vertical drag-handle" draggable="true"></i>
            </td>
            <td class="px-2 py-2 text-center text-sm text-slate-500 border-r border-slate-100">${displayNo}</td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="edit-input text-xs" data-field="process">
                    <option value="">-</option>
                    <option value="要件定義" ${task.process === '要件定義' ? 'selected' : ''}>要件定義</option>
                    <option value="設計" ${task.process === '設計' ? 'selected' : ''}>設計</option>
                    <option value="開発" ${task.process === '開発' ? 'selected' : ''}>開発</option>
                    <option value="テスト" ${task.process === 'テスト' ? 'selected' : ''}>テスト</option>
                    <option value="リリース" ${task.process === 'リリース' ? 'selected' : ''}>リリース</option>
                </select>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell"><input type="text" class="edit-input text-xs" value="${task.screen || ''}" data-field="screen"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell"><input type="text" class="edit-input text-xs" value="${task.name}" data-field="name"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="edit-input text-xs text-center" data-field="assignee">
                    <option value="">-</option>
                    <option value="山田" ${task.assignee === '山田' ? 'selected' : ''}>山田</option>
                    <option value="佐藤" ${task.assignee === '佐藤' ? 'selected' : ''}>佐藤</option>
                    <option value="鈴木" ${task.assignee === '鈴木' ? 'selected' : ''}>鈴木</option>
                    <option value="田中" ${task.assignee === '田中' ? 'selected' : ''}>田中</option>
                    <option value="高橋" ${task.assignee === '高橋' ? 'selected' : ''}>高橋</option>
                </select>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell">
                <select class="edit-input text-xs text-center" data-field="status">
                    <option value="未着手" ${task.status === '未着手' ? 'selected' : ''}>未着手</option>
                    <option value="進行中" ${task.status === '進行中' ? 'selected' : ''}>進行中</option>
                    <option value="完了" ${task.status === '完了' ? 'selected' : ''}>完了</option>
                </select>
            </td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-slate-50"><input type="number" class="edit-input text-xs text-center" value="${task.manDays}" data-field="manDays" min="0" step="0.5"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-slate-50"><input type="date" class="edit-input text-xs" value="${task.startDate}" data-field="startDate"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-slate-50"><input type="date" class="edit-input text-xs" value="${task.endDate}" data-field="endDate"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-slate-50"><input type="number" class="edit-input text-xs text-right" value="${task.plannedCost || ''}" data-field="plannedCost" min="0"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-amber-50"><input type="number" class="edit-input text-xs text-center" value="${task.actualManDays || ''}" data-field="actualManDays" min="0" step="0.5"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-amber-50"><input type="date" class="edit-input text-xs" value="${task.actualStartDate || ''}" data-field="actualStartDate"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-amber-50"><input type="date" class="edit-input text-xs" value="${task.actualEndDate || ''}" data-field="actualEndDate"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell bg-amber-50"><input type="number" class="edit-input text-xs text-right" value="${task.actualCost || ''}" data-field="actualCost" min="0"></td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell"><input type="number" class="edit-input text-xs text-center" value="${task.progress}" data-field="progress" min="0" max="100"></td>
            <td class="px-2 py-2 text-center border-r border-slate-100 text-xs ${delayClass}">${delayText}</td>
            <td class="px-1 py-1 border-r border-slate-100 editable-cell"><input type="text" class="edit-input text-xs" value="${task.note || ''}" data-field="note"></td>
            <td class="px-2 py-2 text-center">
                <button onclick="openEditModalForRow(${index})" class="text-blue-500 hover:text-blue-700 text-xs"><i class="fas fa-edit"></i></button>
            </td>
        `;
        row.querySelectorAll('.edit-input').forEach(input => {
            input.addEventListener('change', (e) => {
                saveToHistory();
                const field = e.target.dataset.field;
                let value = e.target.value;
                if (['manDays', 'actualManDays', 'progress', 'plannedCost', 'actualCost'].includes(field)) value = parseFloat(value) || '';
                tasks[index][field] = value;
            });
        });

        // ドラッグイベント
        const dragHandle = row.querySelector('.drag-handle');
        dragHandle.addEventListener('dragstart', (e) => {
            draggedRowIndex = index;
            row.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        dragHandle.addEventListener('dragend', () => {
            row.classList.remove('dragging');
            draggedRowIndex = null;
            document.querySelectorAll('.task-row').forEach(r => r.classList.remove('drag-over'));
        });
        row.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (draggedRowIndex !== null && draggedRowIndex !== index) {
                row.classList.add('drag-over');
            }
        });
        row.addEventListener('dragleave', () => {
            row.classList.remove('drag-over');
        });
        row.addEventListener('drop', (e) => {
            e.preventDefault();
            row.classList.remove('drag-over');
            if (draggedRowIndex !== null && draggedRowIndex !== index) {
                saveToHistory();
                const draggedTask = tasks.splice(draggedRowIndex, 1)[0];
                const insertIndex = index > draggedRowIndex ? index - 1 : index;
                tasks.splice(insertIndex, 0, draggedTask);
                renderTable();
            }
        });
    } else {
        row.innerHTML = `
            <td class="px-2 py-2 text-center border-r border-slate-100"><input type="checkbox" class="row-checkbox rounded border-slate-300"></td>
            <td class="px-2 py-2 text-center border-r border-slate-100">
                <i class="fas fa-grip-vertical text-slate-300 text-xs"></i>
            </td>
            <td class="px-2 py-2 text-center text-xs text-slate-500 border-r border-slate-100">${displayNo}</td>
            <td class="px-2 py-2 text-xs text-slate-700 border-r border-slate-100">${task.process}</td>
            <td class="px-2 py-2 text-xs text-slate-600 border-r border-slate-100">${task.screen || '-'}</td>
            <td class="px-2 py-2 text-xs text-slate-800 font-medium border-r border-slate-100">${task.name}</td>
            <td class="px-2 py-2 text-xs text-slate-600 text-center border-r border-slate-100">${task.assignee || '-'}</td>
            <td class="px-2 py-2 text-center border-r border-slate-100"><span class="status-badge ${statusClass}">${task.status}</span></td>
            <td class="px-2 py-2 text-xs text-slate-600 text-center border-r border-slate-100 bg-slate-50">${task.manDays}日</td>
            <td class="px-2 py-2 text-xs text-slate-600 text-center border-r border-slate-100 bg-slate-50">${formatDate(task.startDate)}</td>
            <td class="px-2 py-2 text-xs text-slate-600 text-center border-r border-slate-100 bg-slate-50">${formatDate(task.endDate)}</td>
            <td class="px-2 py-2 text-xs text-slate-600 text-right border-r border-slate-100 bg-slate-50">${formatCost(task.plannedCost)}</td>
            <td class="px-2 py-2 text-xs text-amber-700 text-center border-r border-slate-100 bg-amber-50">${task.actualManDays ? task.actualManDays + '日' : '-'}</td>
            <td class="px-2 py-2 text-xs text-amber-700 text-center border-r border-slate-100 bg-amber-50">${formatDate(task.actualStartDate)}</td>
            <td class="px-2 py-2 text-xs text-amber-700 text-center border-r border-slate-100 bg-amber-50">${formatDate(task.actualEndDate)}</td>
            <td class="px-2 py-2 text-xs text-amber-700 text-right border-r border-slate-100 bg-amber-50">${formatCost(task.actualCost)}</td>
            <td class="px-2 py-2 text-center border-r border-slate-100"><span class="text-xs font-semibold ${task.progress === 100 ? 'text-emerald-600' : task.progress > 0 ? 'text-blue-600' : 'text-slate-400'}">${task.progress}%</span></td>
            <td class="px-2 py-2 text-xs text-center border-r border-slate-100 ${delayClass}">${delayText}</td>
            <td class="px-2 py-2 text-xs text-slate-500 border-r border-slate-100">${task.note || '-'}</td>
            <td class="px-2 py-2 text-center">
                <button onclick="openEditModalForRow(${index})" class="text-blue-500 hover:text-blue-700 text-xs"><i class="fas fa-edit"></i></button>
            </td>
        `;
    }

    row.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        selectedRowIndex = index;
        showContextMenu(e.pageX, e.pageY);
    });
    row.addEventListener('dblclick', () => { selectedRowIndex = index; openTaskModal('edit'); });
    row.addEventListener('click', (e) => {
        if (!e.target.classList.contains('row-checkbox') && !e.target.closest('button') && !e.target.closest('.drag-handle')) {
            selectedRowIndex = index;
            document.querySelectorAll('.task-row').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');
        }
    });
    tbody.appendChild(row);
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return `${date.getMonth() + 1}/${date.getDate()}`;
}

function updateCounts() {
    document.getElementById('total-count').textContent = tasks.length + '件';
    document.getElementById('completed-count').textContent = tasks.filter(t => t.status === '完了').length + '件';
    document.getElementById('progress-count').textContent = tasks.filter(t => t.status === '進行中').length + '件';
    document.getElementById('notstarted-count').textContent = tasks.filter(t => t.status === '未着手').length + '件';
    document.getElementById('delayed-count').textContent = tasks.filter(t => t.delay > 0 && t.status !== '完了').length + '件';
}

function switchViewMode(mode) {
    const btnView = document.getElementById('btn-view-mode');
    const btnEdit = document.getElementById('btn-edit-mode');
    const editActions = document.getElementById('edit-actions');
    const viewActions = document.getElementById('view-actions');
    const tableContainer = document.getElementById('table-container');

    if (mode === 'edit') {
        isEditMode = true;
        originalTasks = JSON.parse(JSON.stringify(tasks));
        undoHistory = [];
        btnView.className = 'px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50';
        btnEdit.className = 'px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600';
        editActions.classList.remove('hidden');
        viewActions.classList.add('hidden');
        tableContainer.classList.add('edit-mode');
    } else {
        isEditMode = false;
        btnView.className = 'px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600';
        btnEdit.className = 'px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50';
        editActions.classList.add('hidden');
        viewActions.classList.remove('hidden');
        tableContainer.classList.remove('edit-mode');
    }
    renderTable();
}

function cancelEdit() {
    if (confirm('編集内容を破棄しますか？')) {
        tasks = JSON.parse(JSON.stringify(originalTasks));
        switchViewMode('view');
    }
}

function saveAllTasks() {
    console.log('保存データ:', tasks);
    alert('タスクを一括登録しました。');
    switchViewMode('view');
}

function addNewRow() {
    saveToHistory();
    tasks.push({ id: Math.max(...tasks.map(t => t.id), 0) + 1, process: '', screen: '', name: '', assignee: '', status: '未着手', manDays: 0, startDate: '', endDate: '', plannedCost: 0, actualManDays: '', actualStartDate: '', actualEndDate: '', actualCost: 0, progress: 0, delay: 0, note: '' });
    renderTable();
    document.getElementById('table-container').scrollTop = document.getElementById('table-container').scrollHeight;
}

function clearAllData() {
    if (confirm('すべてのタスクをクリアしますか？')) {
        saveToHistory();
        tasks = [];
        renderTable();
    }
}

const contextMenu = document.getElementById('context-menu');
function showContextMenu(x, y) {
    // 画面下部に近い場合は上に表示
    const menuHeight = 320;
    const windowHeight = window.innerHeight;

    if (y + menuHeight > windowHeight) {
        contextMenu.style.top = (y - menuHeight) + 'px';
    } else {
        contextMenu.style.top = y + 'px';
    }
    contextMenu.style.left = x + 'px';
    contextMenu.classList.add('show');
}
document.addEventListener('click', () => { contextMenu.classList.remove('show'); });

function openTaskModal(action) {
    contextMenu.classList.remove('show');
    const modal = document.getElementById('task-modal');
    const modalTitle = document.getElementById('modal-title');
    document.getElementById('task-form').reset();
    document.getElementById('modal-progress-value').textContent = '0%';

    if (action === 'edit' && selectedRowIndex !== null) {
        modalTitle.textContent = 'タスク編集';
        const task = tasks[selectedRowIndex];
        document.getElementById('modal-process').value = task.process;
        document.getElementById('modal-screen').value = task.screen || '';
        document.getElementById('modal-task-name').value = task.name;
        document.getElementById('modal-assignee').value = task.assignee;
        document.getElementById('modal-status').value = task.status;
        document.getElementById('modal-man-days').value = task.manDays;
        document.getElementById('modal-start-date').value = task.startDate;
        document.getElementById('modal-end-date').value = task.endDate;
        document.getElementById('modal-planned-cost').value = task.plannedCost || '';
        document.getElementById('modal-actual-man-days').value = task.actualManDays || '';
        document.getElementById('modal-actual-start-date').value = task.actualStartDate || '';
        document.getElementById('modal-actual-end-date').value = task.actualEndDate || '';
        document.getElementById('modal-actual-cost').value = task.actualCost || '';
        document.getElementById('modal-progress').value = task.progress;
        document.getElementById('modal-progress-value').textContent = task.progress + '%';
        document.getElementById('modal-note').value = task.note || '';
    } else {
        modalTitle.textContent = action === 'above' ? 'タスク追加（上に挿入）' : 'タスク追加（下に挿入）';
    }
    modal.classList.remove('hidden');
}

function closeTaskModal() { document.getElementById('task-modal').classList.add('hidden'); }

function saveTaskFromModal() {
    const taskData = {
        process: document.getElementById('modal-process').value,
        screen: document.getElementById('modal-screen').value,
        name: document.getElementById('modal-task-name').value,
        assignee: document.getElementById('modal-assignee').value,
        status: document.getElementById('modal-status').value,
        manDays: parseFloat(document.getElementById('modal-man-days').value) || 0,
        startDate: document.getElementById('modal-start-date').value,
        endDate: document.getElementById('modal-end-date').value,
        plannedCost: parseFloat(document.getElementById('modal-planned-cost').value) || 0,
        actualManDays: parseFloat(document.getElementById('modal-actual-man-days').value) || '',
        actualStartDate: document.getElementById('modal-actual-start-date').value,
        actualEndDate: document.getElementById('modal-actual-end-date').value,
        actualCost: parseFloat(document.getElementById('modal-actual-cost').value) || 0,
        progress: parseInt(document.getElementById('modal-progress').value) || 0,
        delay: 0,
        note: document.getElementById('modal-note').value
    };
    if (!taskData.process || !taskData.name) { alert('工程とタスク名は必須です。'); return; }

    saveToHistory();
    const modalTitle = document.getElementById('modal-title').textContent;
    if (modalTitle === 'タスク編集') {
        tasks[selectedRowIndex] = { ...tasks[selectedRowIndex], ...taskData };
    } else if (modalTitle === 'タスク追加（上に挿入）') {
        taskData.id = Math.max(...tasks.map(t => t.id), 0) + 1;
        if (selectedRowIndex !== null) {
            tasks.splice(selectedRowIndex, 0, taskData);
        } else {
            tasks.unshift(taskData);
        }
    } else {
        taskData.id = Math.max(...tasks.map(t => t.id), 0) + 1;
        if (selectedRowIndex !== null) {
            tasks.splice(selectedRowIndex + 1, 0, taskData);
        } else {
            tasks.push(taskData);
        }
    }
    renderTable();
    closeTaskModal();
}

document.getElementById('modal-progress').addEventListener('input', function() { document.getElementById('modal-progress-value').textContent = this.value + '%'; });

function duplicateTask() { if (selectedRowIndex !== null) { saveToHistory(); tasks.splice(selectedRowIndex + 1, 0, { ...tasks[selectedRowIndex], id: Math.max(...tasks.map(t => t.id), 0) + 1 }); renderTable(); } contextMenu.classList.remove('show'); }
function deleteTask() { if (selectedRowIndex !== null && confirm('このタスクを削除しますか？')) { saveToHistory(); tasks.splice(selectedRowIndex, 1); renderTable(); } contextMenu.classList.remove('show'); }

// Ctrl+C, Ctrl+V 対応
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeTaskModal();
        closeBulkEditModal();
        closeImportModal();
        closeExportModal();
    }
    if (e.ctrlKey && e.key === 'z' && isEditMode) { e.preventDefault(); undoChanges(); }

    // Ctrl+C: 選択行をコピー
    if (e.ctrlKey && e.key === 'c' && selectedRowIndex !== null) {
        if (!e.target.closest('input') && !e.target.closest('textarea') && !e.target.closest('select')) {
            copiedTask = { ...tasks[selectedRowIndex] };
            document.getElementById('clipboard-status').classList.remove('hidden');
            renderTable();
            setTimeout(() => {
                document.getElementById('clipboard-status').classList.add('hidden');
            }, 3000);
        }
    }

    // Ctrl+V: コピーした行を下に挿入
    if (e.ctrlKey && e.key === 'v' && copiedTask) {
        if (!e.target.closest('input') && !e.target.closest('textarea') && !e.target.closest('select')) {
            e.preventDefault();
            saveToHistory();
            const newTask = { ...copiedTask, id: Math.max(...tasks.map(t => t.id), 0) + 1 };
            const insertIndex = selectedRowIndex !== null ? selectedRowIndex + 1 : tasks.length;
            tasks.splice(insertIndex, 0, newTask);
            copiedTask = null;
            renderTable();
        }
    }
});

// 編集ボタンでモーダルを開く機能
function openEditModalForRow(index) {
    selectedRowIndex = index;
    openTaskModal('edit');
}

// サイドバーモーダル
function openSidebarModal() {
    document.getElementById('sidebar-modal').classList.add('show');
    document.getElementById('sidebar-modal-overlay').classList.add('show');
}
function closeSidebarModal() {
    document.getElementById('sidebar-modal').classList.remove('show');
    document.getElementById('sidebar-modal-overlay').classList.remove('show');
}

// ユーザーメニュー
const userMenuTrigger = document.getElementById('user-menu-trigger');
const userMenuDropdown = document.getElementById('user-menu-dropdown');
userMenuTrigger.addEventListener('click', (e) => { e.stopPropagation(); userMenuDropdown.classList.toggle('show'); document.getElementById('user-menu-arrow').style.transform = userMenuDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)'; });
document.addEventListener('click', (e) => { if (!userMenuTrigger.contains(e.target) && !userMenuDropdown.contains(e.target)) { userMenuDropdown.classList.remove('show'); document.getElementById('user-menu-arrow').style.transform = 'rotate(0deg)'; } });
function logout() { if (confirm('ログアウトしますか？')) alert('ログアウトしました'); }

document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateSelectionCount();
});

// 選択数更新
function updateSelectionCount() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkedBoxes.length;
    const actionBar = document.getElementById('selection-action-bar');
    const countEl = document.getElementById('selected-count');

    if (count > 0) {
        actionBar.classList.remove('hidden');
        countEl.textContent = count;
    } else {
        actionBar.classList.add('hidden');
    }
}

// 行チェックボックスのイベント委譲
document.getElementById('task-tbody').addEventListener('change', function(e) {
    if (e.target.classList.contains('row-checkbox')) {
        updateSelectionCount();
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        document.getElementById('select-all').checked = allCheckboxes.length === checkedBoxes.length && allCheckboxes.length > 0;
    }
});

// 選択解除
function clearSelection() {
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = false; });
    updateSelectionCount();
}

// 選択されたインデックスを取得
function getSelectedIndices() {
    const indices = [];
    document.querySelectorAll('.row-checkbox').forEach((cb, index) => {
        if (cb.checked) indices.push(index);
    });
    return indices;
}

// 一括編集モーダルを開く
function openBulkEditModal() {
    const selectedIndices = getSelectedIndices();
    if (selectedIndices.length === 0) {
        alert('タスクを選択してください。');
        return;
    }
    document.getElementById('bulk-edit-count').textContent = selectedIndices.length;
    document.getElementById('bulk-assignee').value = '';
    document.getElementById('bulk-status').value = '';
    document.getElementById('bulk-progress').value = '';
    document.getElementById('bulk-process').value = '';
    document.getElementById('bulk-edit-modal').classList.remove('hidden');
}

// 一括編集モーダルを閉じる
function closeBulkEditModal() {
    document.getElementById('bulk-edit-modal').classList.add('hidden');
}

// 一括編集を適用
function applyBulkEdit() {
    const selectedIndices = getSelectedIndices();
    if (selectedIndices.length === 0) return;

    const assignee = document.getElementById('bulk-assignee').value;
    const status = document.getElementById('bulk-status').value;
    const progress = document.getElementById('bulk-progress').value;
    const process = document.getElementById('bulk-process').value;

    if (!assignee && !status && !progress && !process) {
        alert('変更する項目を1つ以上選択してください。');
        return;
    }

    saveToHistory();

    selectedIndices.forEach(index => {
        if (assignee) tasks[index].assignee = assignee;
        if (status) tasks[index].status = status;
        if (progress !== '') tasks[index].progress = parseInt(progress);
        if (process) tasks[index].process = process;
    });

    renderTable();
    closeBulkEditModal();
    clearSelection();
    alert(selectedIndices.length + '件のタスクを更新しました。');
}

// 一括削除
function bulkDelete() {
    const selectedIndices = getSelectedIndices();
    if (selectedIndices.length === 0) {
        alert('タスクを選択してください。');
        return;
    }

    if (!confirm(selectedIndices.length + '件のタスクを削除しますか？')) return;

    saveToHistory();

    // 後ろから削除（インデックスがずれないように）
    selectedIndices.sort((a, b) => b - a).forEach(index => {
        tasks.splice(index, 1);
    });

    renderTable();
    clearSelection();
    alert('削除しました。');
}

// ========================================
// 絞り込み機能
// ========================================
let isFiltered = false;
let filterOriginalTasks = [];

function toggleFilterPanel() {
    const panel = document.getElementById('filter-panel');
    const btn = document.getElementById('filter-btn');
    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        btn.classList.add('bg-blue-50', 'border-blue-400', 'text-blue-700');
        btn.classList.remove('border-slate-300', 'text-slate-700');
    } else {
        panel.classList.add('hidden');
        btn.classList.remove('bg-blue-50', 'border-blue-400', 'text-blue-700');
        btn.classList.add('border-slate-300', 'text-slate-700');
    }
}

function applyFilter() {
    const process = document.getElementById('filter-process').value;
    const assignee = document.getElementById('filter-assignee').value;
    const status = document.getElementById('filter-status').value;
    const progress = document.getElementById('filter-progress').value;
    const delay = document.getElementById('filter-delay').value;

    if (!isFiltered) {
        filterOriginalTasks = [...tasks];
    }

    let filtered = [...filterOriginalTasks];

    if (process) {
        filtered = filtered.filter(t => t.process === process);
    }
    if (assignee) {
        filtered = filtered.filter(t => t.assignee === assignee);
    }
    if (status) {
        filtered = filtered.filter(t => t.status === status);
    }
    if (progress) {
        if (progress === '0') {
            filtered = filtered.filter(t => t.progress === 0);
        } else if (progress === '1-99') {
            filtered = filtered.filter(t => t.progress > 0 && t.progress < 100);
        } else if (progress === '100') {
            filtered = filtered.filter(t => t.progress === 100);
        }
    }
    if (delay) {
        if (delay === 'delayed') {
            filtered = filtered.filter(t => t.delay > 0);
        } else if (delay === 'ontime') {
            filtered = filtered.filter(t => t.delay <= 0);
        }
    }

    tasks = filtered;
    isFiltered = true;
    renderTable();
}

function clearFilter() {
    document.getElementById('filter-process').value = '';
    document.getElementById('filter-assignee').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-progress').value = '';
    document.getElementById('filter-delay').value = '';

    if (isFiltered) {
        tasks = [...filterOriginalTasks];
        isFiltered = false;
        renderTable();
    }
}

// ========================================
// インポート機能
// ========================================
let importData = [];

function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
    document.getElementById('import-preview').classList.add('hidden');
    document.getElementById('import-execute-btn').disabled = true;
    importData = [];
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
}

function handleImportFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const rows = content.trim().split('\n');

        // ヘッダー行をスキップ
        const headers = rows[0].includes('\t') ? rows[0].split('\t') : rows[0].split(',');
        const dataRows = rows.slice(1);

        if (dataRows.length === 0) {
            alert('インポートするデータがありません。');
            return;
        }

        // プレビュー表示
        const headerRow = document.getElementById('import-preview-header');
        headerRow.innerHTML = headers.map(h => `<th class="px-2 py-1 border-r border-slate-200 text-left">${h.trim()}</th>`).join('');

        const tbody = document.getElementById('import-preview-body');
        tbody.innerHTML = '';
        importData = [];

        dataRows.slice(0, 5).forEach(row => {
            const cols = row.includes('\t') ? row.split('\t') : row.split(',');
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-100';
            tr.innerHTML = cols.map(c => `<td class="px-2 py-1 border-r border-slate-100">${c.trim()}</td>`).join('');
            tbody.appendChild(tr);
        });

        dataRows.forEach(row => {
            const cols = row.includes('\t') ? row.split('\t') : row.split(',');
            if (cols.length >= 3 && cols[0].trim()) {
                importData.push({
                    process: cols[0]?.trim() || '',
                    screen: cols[1]?.trim() || '',
                    name: cols[2]?.trim() || '',
                    assignee: cols[3]?.trim() || '',
                    status: cols[4]?.trim() || '未着手',
                    manDays: parseFloat(cols[5]) || 0,
                    startDate: cols[6]?.trim() || '',
                    endDate: cols[7]?.trim() || '',
                    plannedCost: parseFloat(cols[8]) || 0,
                    actualManDays: parseFloat(cols[9]) || '',
                    actualStartDate: cols[10]?.trim() || '',
                    actualEndDate: cols[11]?.trim() || '',
                    actualCost: parseFloat(cols[12]) || 0,
                    progress: parseInt(cols[13]) || 0,
                    note: cols[14]?.trim() || ''
                });
            }
        });

        document.getElementById('import-row-count').textContent = importData.length;
        document.getElementById('import-preview').classList.remove('hidden');
        document.getElementById('import-execute-btn').disabled = importData.length === 0;
    };
    reader.readAsText(file, 'UTF-8');
    event.target.value = '';
}

function executeImport() {
    if (importData.length === 0) {
        alert('インポートするデータがありません。');
        return;
    }

    const maxId = Math.max(...tasks.map(t => t.id), 0);
    const newTasks = importData.map((data, index) => ({
        id: maxId + index + 1,
        ...data,
        delay: 0
    }));

    tasks = tasks.concat(newTasks);
    if (!isFiltered) {
        filterOriginalTasks = [...tasks];
    }
    renderTable();
    closeImportModal();
    alert(newTasks.length + '件のタスクをインポートしました。');
}

// テンプレートダウンロード
function downloadTemplate() {
    const headers = ['工程', '画面/機能', 'タスク名', '担当者', 'ステータス', '工数', '開始日', '終了日', '原価', '実工数', '実開始日', '実終了日', '出来高', '進捗', '備考'];
    const sampleData = [
        ['設計', 'ログイン画面', '画面設計', '山田', '未着手', '2', '2024-04-01', '2024-04-05', '200000', '', '', '', '0', '0', ''],
        ['開発', 'ログイン画面', 'フロント実装', '佐藤', '未着手', '3', '2024-04-06', '2024-04-10', '300000', '', '', '', '0', '0', ''],
    ];

    const csvContent = [headers.join(','), ...sampleData.map(row => row.join(','))].join('\n');
    const bom = '\uFEFF'; // UTF-8 BOM
    const blob = new Blob([bom + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'task_template.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// ========================================
// エクスポート機能
// ========================================
function openExportModal() {
    document.getElementById('export-modal').classList.remove('hidden');
    document.getElementById('export-count').textContent = tasks.length;
}

function closeExportModal() {
    document.getElementById('export-modal').classList.add('hidden');
}

function executeExport() {
    const format = document.querySelector('input[name="export-format"]:checked').value;

    if (tasks.length === 0) {
        alert('エクスポートするデータがありません。');
        return;
    }

    const headers = ['工程', '画面/機能', 'タスク名', '担当者', 'ステータス', '工数', '開始日', '終了日', '原価', '実工数', '実開始日', '実終了日', '出来高', '進捗', '備考'];
    const rows = tasks.map(task => [
        task.process || '',
        task.screen || '',
        task.name || '',
        task.assignee || '',
        task.status || '',
        task.manDays || '',
        task.startDate || '',
        task.endDate || '',
        task.plannedCost || '',
        task.actualManDays || '',
        task.actualStartDate || '',
        task.actualEndDate || '',
        task.actualCost || '',
        task.progress || 0,
        task.note || ''
    ].map(val => {
        const str = String(val);
        if (str.includes(',') || str.includes('"') || str.includes('\n')) {
            return '"' + str.replace(/"/g, '""') + '"';
        }
        return str;
    }).join(','));

    const csvContent = [headers.join(','), ...rows].join('\n');
    const bom = '\uFEFF';
    const blob = new Blob([bom + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const now = new Date();
    const dateStr = now.getFullYear() +
        ('0' + (now.getMonth() + 1)).slice(-2) +
        ('0' + now.getDate()).slice(-2) + '_' +
        ('0' + now.getHours()).slice(-2) +
        ('0' + now.getMinutes()).slice(-2);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'tasks_export_' + dateStr + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    closeExportModal();
    alert(tasks.length + '件のデータをエクスポートしました。');
}

// ドラッグ&ドロップ対応
const dropZone = document.getElementById('import-drop-zone');
if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-emerald-400', 'bg-emerald-50');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-emerald-400', 'bg-emerald-50');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-emerald-400', 'bg-emerald-50');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImportFile({ target: { files: files, value: '' } });
        }
    });
}

// 初期化
renderTable();
