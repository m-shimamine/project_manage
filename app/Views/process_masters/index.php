<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .process-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .process-table tbody tr {
        transition: all 0.2s ease;
    }
    .process-table tbody tr:hover {
        background: #f8fafc;
    }
    .cursor-move {
        cursor: move;
    }
    .dragging {
        opacity: 0.5;
        background: #e0f2fe !important;
    }
    /* モーダルアニメーション */
    #process-modal {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    #process-modal.show {
        opacity: 1;
        visibility: visible;
    }
    #process-modal .modal-content {
        transform: translate(-50%, -50%) scale(0.95);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #process-modal.show .modal-content {
        transform: translate(-50%, -50%) scale(1);
    }
    /* 削除確認モーダル */
    #delete-modal {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }
    #delete-modal.show {
        opacity: 1;
        visibility: visible;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- サブヘッダー -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 mb-6 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- 設定に戻る -->
                <a href="<?= base_url('settings') ?>"
                    class="flex items-center space-x-2 px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors">
                    <i class="fas fa-arrow-left text-slate-400 text-sm"></i>
                    <span class="text-sm font-medium text-slate-600">設定に戻る</span>
                </a>
                <!-- 検索 -->
                <div class="relative">
                    <input type="text" id="search-input" placeholder="工程名で検索..."
                        value="<?= esc($search ?? '') ?>"
                        class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-72 text-sm shadow-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                </div>
                <!-- ステータスフィルター -->
                <select id="status-filter"
                    class="border border-slate-300 rounded-lg px-4 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">すべてのステータス</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>有効</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>無効</option>
                </select>
            </div>
            <button onclick="openModal('add')"
                class="btn-primary px-4 py-2 rounded-lg text-sm font-medium text-white flex items-center">
                <i class="fas fa-plus mr-2"></i>工程追加
            </button>
        </div>
    </div>

    <!-- 工程テーブル -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden fade-in">
        <!-- 並び替え保存ボタン（初期は非表示） -->
        <div id="save-reorder-container" class="hidden px-6 py-3 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
            <div class="flex items-center text-blue-700 text-sm font-medium">
                <i class="fas fa-info-circle mr-2"></i>
                工程の順序が変更されました。変更を確定するには保存してください。
            </div>
            <button id="btn-save-reorder"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold shadow-sm transition-all flex items-center">
                <i class="fas fa-save mr-2"></i>並び替えを保存
            </button>
        </div>

        <table class="process-table w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-8"></th>
                    <th class="text-left px-2 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-12">No</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">工程名</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">説明</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">ステータス</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">更新日</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody id="process-tbody" class="divide-y divide-slate-100">
                <?php if (empty($processes)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                        <p>工程が登録されていません</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($processes as $index => $process): ?>
                <tr data-id="<?= esc($process['id']) ?>"
                    draggable="true"
                    class="<?= $process['status'] === 'inactive' ? 'bg-slate-50/50' : '' ?>">
                    <td class="px-4 py-4 text-center cursor-move drag-handle">
                        <i class="fas fa-grip-vertical text-slate-400 hover:text-blue-600 transition-colors"></i>
                    </td>
                    <td class="px-2 py-4 text-sm font-medium text-slate-500 row-no"><?= $index + 1 ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center <?= $process['status'] === 'inactive' ? 'opacity-60' : '' ?>">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br <?= esc(\App\Models\ProcessMasterModel::getIconColor($process['name'])) ?> flex items-center justify-center shadow-md mr-3">
                                <i class="fas <?= esc(\App\Models\ProcessMasterModel::getIcon($process['name'])) ?> text-white text-sm"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800"><?= esc($process['name']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm <?= $process['status'] === 'inactive' ? 'text-slate-400' : 'text-slate-600' ?>"><?= esc($process['description'] ?? '') ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 text-xs font-semibold <?= esc(\App\Models\ProcessMasterModel::getStatusColor($process['status'])) ?> rounded-full">
                            <?= esc(\App\Models\ProcessMasterModel::getStatusLabel($process['status'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm <?= $process['status'] === 'inactive' ? 'text-slate-400' : 'text-slate-500' ?>">
                            <?= $process['updated_at'] ? date('Y/m/d', strtotime($process['updated_at'])) : '-' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <button onclick="openModal('edit', <?= esc($process['id']) ?>)"
                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="編集">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete(<?= esc($process['id']) ?>, '<?= esc($process['name']) ?>')"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="削除">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 追加・編集モーダル -->
<div id="process-modal" class="fixed inset-0 z-[100]">
    <div class="modal-overlay absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="modal-content absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <!-- モーダルヘッダー -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div id="modal-icon-container" class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i id="modal-icon" class="fas fa-plus text-white text-sm"></i>
                </div>
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-800">工程追加</h3>
                    <p id="modal-subtitle" class="text-xs text-slate-500">新しい工程の情報を入力してください</p>
                </div>
            </div>
            <button onclick="closeModal()"
                class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- モーダルボディ -->
        <div class="p-6">
            <form id="process-form">
                <input type="hidden" id="process-id" name="id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">工程名 <span class="text-red-500">*</span></label>
                        <input type="text" id="process-name" name="name" required placeholder="例: システムテスト"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all outline-none">
                        <p id="error-name" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">説明</label>
                        <textarea id="process-description" name="description" rows="3" placeholder="工程の詳細な内容を入力してください"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all resize-none outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ステータス</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="active" checked class="hidden peer">
                                <div class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 peer-checked:bg-blue-50 peer-checked:border-blue-200 peer-checked:text-blue-600 transition-all group-hover:border-slate-300">
                                    <i class="fas fa-check-circle mr-1.5"></i>有効
                                </div>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="inactive" class="hidden peer">
                                <div class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 peer-checked:bg-slate-100 peer-checked:border-slate-300 peer-checked:text-slate-600 transition-all group-hover:border-slate-300">
                                    <i class="fas fa-ban mr-1.5"></i>無効
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- モーダルフッター -->
        <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            <button type="button" onclick="closeModal()"
                class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                キャンセル
            </button>
            <button type="button" id="submit-button" onclick="handleSubmit()"
                class="btn-primary px-6 py-2.5 rounded-lg text-sm font-bold text-white flex items-center shadow-md">
                <i class="fas fa-check mr-2"></i><span id="submit-button-text">登録する</span>
            </button>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div id="delete-modal" class="fixed inset-0 z-[100]">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">削除の確認</h3>
            <p class="text-sm text-slate-600 mb-6">「<span id="delete-process-name" class="font-semibold"></span>」を削除してもよろしいですか？</p>
            <input type="hidden" id="delete-process-id">
            <div class="flex items-center justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                    キャンセル
                </button>
                <button type="button" onclick="executeDelete()"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-bold text-white transition-colors">
                    <i class="fas fa-trash mr-2"></i>削除する
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = '/process-masters';
let isOrderChanged = false;
let draggedRow = null;

// 初期化
document.addEventListener('DOMContentLoaded', function() {
    initDragAndDrop();
    initSearch();
});

// 検索・フィルター
function initSearch() {
    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => filterProcesses(), 300);
    });
    document.getElementById('status-filter').addEventListener('change', filterProcesses);
}

function filterProcesses() {
    const search = document.getElementById('search-input').value;
    const status = document.getElementById('status-filter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    window.location.href = `${baseUrl}?${params.toString()}`;
}

// モーダル操作
function openModal(mode, id = null) {
    const modal = document.getElementById('process-modal');
    const form = document.getElementById('process-form');
    const title = document.getElementById('modal-title');
    const subtitle = document.getElementById('modal-subtitle');
    const icon = document.getElementById('modal-icon');
    const iconContainer = document.getElementById('modal-icon-container');
    const submitBtnText = document.getElementById('submit-button-text');

    // フォームリセット
    form.reset();
    document.getElementById('process-id').value = id || '';
    clearErrors();

    if (mode === 'add') {
        title.textContent = '工程追加';
        subtitle.textContent = '新しい工程の情報を入力してください';
        icon.className = 'fas fa-plus text-white text-sm';
        iconContainer.className = 'w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg';
        submitBtnText.textContent = '登録する';
    } else {
        title.textContent = '工程編集';
        subtitle.textContent = '工程の情報を更新してください';
        icon.className = 'fas fa-edit text-white text-sm';
        iconContainer.className = 'w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg';
        submitBtnText.textContent = '更新する';
        loadProcessData(id);
    }

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('process-modal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

async function loadProcessData(id) {
    try {
        const response = await fetch(`${baseUrl}/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        if (result.success) {
            document.getElementById('process-name').value = result.data.name;
            document.getElementById('process-description').value = result.data.description || '';
            document.querySelector(`input[name="status"][value="${result.data.status}"]`).checked = true;
        }
    } catch (error) {
        console.error('Error loading process:', error);
        showToast('データの取得に失敗しました', 'error');
    }
}

async function handleSubmit() {
    const id = document.getElementById('process-id').value;
    const formData = new FormData(document.getElementById('process-form'));

    const url = id ? `${baseUrl}/${id}` : baseUrl;
    const method = id ? 'PUT' : 'POST';

    const btn = document.getElementById('submit-button');
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>処理中...';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData)
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            closeModal();
            location.reload();
        } else {
            if (result.errors) {
                showErrors(result.errors);
            }
            showToast(result.message || '保存に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error saving process:', error);
        showToast('通信エラーが発生しました', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
}

function clearErrors() {
    document.querySelectorAll('[id^="error-"]').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });
}

function showErrors(errors) {
    clearErrors();
    for (const [field, message] of Object.entries(errors)) {
        const errorEl = document.getElementById(`error-${field}`);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }
    }
}

// 削除
function confirmDelete(id, name) {
    document.getElementById('delete-process-id').value = id;
    document.getElementById('delete-process-name').textContent = name;
    document.getElementById('delete-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('show');
    document.body.style.overflow = '';
}

async function executeDelete() {
    const id = document.getElementById('delete-process-id').value;

    try {
        const response = await fetch(`${baseUrl}/${id}`, {
            method: 'DELETE',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            closeDeleteModal();
            location.reload();
        } else {
            showToast(result.message || '削除に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error deleting process:', error);
        showToast('通信エラーが発生しました', 'error');
    }
}

// ドラッグ&ドロップ
function initDragAndDrop() {
    const tbody = document.getElementById('process-tbody');
    if (!tbody) return;

    tbody.addEventListener('dragstart', handleDragStart);
    tbody.addEventListener('dragover', handleDragOver);
    tbody.addEventListener('drop', handleDrop);
    tbody.addEventListener('dragend', handleDragEnd);
}

function handleDragStart(e) {
    const row = e.target.closest('tr');
    if (!row || !row.dataset.id) return;

    draggedRow = row;
    e.dataTransfer.effectAllowed = 'move';
    row.classList.add('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    const targetRow = e.target.closest('tr');
    if (!targetRow || !draggedRow || targetRow === draggedRow || !targetRow.dataset.id) return;

    const tbody = targetRow.parentNode;
    const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
    const draggedIndex = rows.indexOf(draggedRow);
    const targetIndex = rows.indexOf(targetRow);

    if (draggedIndex < targetIndex) {
        tbody.insertBefore(draggedRow, targetRow.nextSibling);
    } else {
        tbody.insertBefore(draggedRow, targetRow);
    }
    isOrderChanged = true;
}

function handleDrop(e) {
    e.preventDefault();
}

function handleDragEnd(e) {
    if (draggedRow) {
        draggedRow.classList.remove('dragging');
        draggedRow = null;
    }
    if (isOrderChanged) {
        showSaveReorderButton();
        updateRowNumbers();
    }
}

function showSaveReorderButton() {
    document.getElementById('save-reorder-container').classList.remove('hidden');
    document.getElementById('save-reorder-container').classList.add('flex');
}

function updateRowNumbers() {
    document.querySelectorAll('#process-tbody tr[data-id]').forEach((row, index) => {
        const noCell = row.querySelector('.row-no');
        if (noCell) {
            noCell.textContent = index + 1;
            noCell.classList.add('text-blue-600', 'font-bold');
            setTimeout(() => {
                noCell.classList.remove('text-blue-600', 'font-bold');
            }, 2000);
        }
    });
}

// 並び替え保存
document.getElementById('btn-save-reorder')?.addEventListener('click', async function() {
    const rows = document.querySelectorAll('#process-tbody tr[data-id]');
    const orders = Array.from(rows).map((row, index) => ({
        id: parseInt(row.dataset.id),
        sort_order: index + 1
    }));

    const btn = this;
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>保存中...';

    try {
        const response = await fetch(`${baseUrl}/reorder`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ orders: JSON.stringify(orders) })
        });
        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            isOrderChanged = false;
            document.getElementById('save-reorder-container').classList.add('hidden');
        } else {
            showToast(result.message || '保存に失敗しました', 'error');
        }
    } catch (error) {
        console.error('Error saving reorder:', error);
        showToast('通信エラーが発生しました', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
});

// トースト通知
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toastId = 'toast-' + Date.now();

    const iconMap = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };

    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon"><i class="fas ${iconMap[type]}"></i></div>
        <div class="toast-message">${message}</div>
        <div class="toast-close" onclick="closeToast('${toastId}')"><i class="fas fa-times"></i></div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// ESCキーでモーダルを閉じる
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('process-modal').classList.contains('show')) {
            closeModal();
        }
        if (document.getElementById('delete-modal').classList.contains('show')) {
            closeDeleteModal();
        }
    }
});
</script>
<?= $this->endSection() ?>
