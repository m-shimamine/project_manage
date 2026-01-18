<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .member-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .member-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .btn-stylish {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-stylish::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    .btn-stylish:hover::before {
        left: 100%;
    }
    .btn-stylish:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
    }
    /* 権限バッジ */
    .role-admin {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
    }
    .role-leader {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .role-member {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    /* テーブルスタイル */
    .member-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .member-table tbody tr {
        transition: all 0.2s ease;
    }
    .member-table tbody tr:hover {
        background: #f8fafc;
    }
    /* ビュー切り替えボタン */
    .view-toggle-btn {
        transition: all 0.2s ease;
    }
    .view-toggle-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }
    /* 詳細ボタン（プロジェクト一覧と同じスタイル） */
    .btn-detail {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        transition: all 0.3s ease;
    }
    .btn-detail:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- サブヘッダー -->
<div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <!-- 検索 -->
            <form method="GET" action="<?= base_url('members') ?>" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="メンバー名・メールで検索..."
                           class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-72 text-sm shadow-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                </div>
                <!-- 部署フィルター -->
                <select name="department" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-4 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">すべての部署</option>
                    <?php foreach ($departments as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= ($department ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- ステータスフィルター -->
                <select name="status" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-4 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">すべてのステータス</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>有効</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>無効</option>
                </select>
            </form>
        </div>
        <div class="flex items-center gap-3">
            <!-- ビュー切り替え（PC/タブレットのみ） -->
            <div class="hidden md:flex items-center bg-slate-100 rounded-lg p-1">
                <button type="button" id="view-table" class="view-toggle-btn active px-3 py-1.5 rounded-md text-sm font-medium">
                    <i class="fas fa-list"></i>
                </button>
                <button type="button" id="view-card" class="view-toggle-btn px-3 py-1.5 rounded-md text-sm font-medium text-slate-600">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>
            <!-- 新規追加ボタン -->
            <a href="<?= base_url('members/create') ?>" class="btn-stylish inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-lg">
                <i class="fas fa-plus mr-2"></i>メンバー追加
            </a>
        </div>
    </div>
</div>

<!-- コンテンツ -->
<main class="flex-1 overflow-y-auto p-4 sm:p-6">
    <!-- 統計カード -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 fade-in">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-slate-500 font-medium">総メンバー数</p>
                    <p class="text-2xl font-bold text-slate-800"><?= esc($stats['total']) ?>名</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-shield text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-slate-500 font-medium">管理者</p>
                    <p class="text-2xl font-bold text-slate-800"><?= esc($stats['departments']['management'] ?? 0) ?>名</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-slate-500 font-medium">リーダー</p>
                    <p class="text-2xl font-bold text-slate-800"><?= esc($stats['departments']['sales'] ?? 0) ?>名</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-5">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-xs text-slate-500 font-medium">開発メンバー</p>
                    <p class="text-2xl font-bold text-slate-800"><?= esc($stats['departments']['development'] ?? 0) ?>名</p>
                </div>
            </div>
        </div>
    </div>

    <!-- テーブルビュー（PC/タブレットのみ） -->
    <div id="table-view" class="hidden md:block bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden fade-in">
        <table class="member-table w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">メンバー</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">権限</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">部署</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">ステータス</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($members as $member): ?>
                <?php
                    $color = \App\Models\MemberModel::getColor($member['name']);
                    $initial = \App\Models\MemberModel::getInitial($member['name']);
                    $departmentLabel = !empty($member['department']) ? esc($member['department']) : '-';
                    $roleLabel = \App\Models\MemberModel::getRoleLabel($member['role'] ?? 'member');
                    $roleColor = \App\Models\MemberModel::getRoleColor($member['role'] ?? 'member');
                    $isActive = (bool)$member['is_active'];
                ?>
                <tr class="<?= !$isActive ? 'bg-slate-50/50' : '' ?>">
                    <td class="px-6 py-4">
                        <div class="flex items-center <?= !$isActive ? 'opacity-60' : '' ?>">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center text-white font-bold mr-3">
                                <?= esc($initial) ?>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800"><?= esc($member['name']) ?></div>
                                <div class="text-xs text-slate-500"><?= esc($member['email'] ?? '-') ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 text-xs font-semibold <?= esc($roleColor) ?> rounded-full"><?= esc($roleLabel) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-600"><?= esc($departmentLabel) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($isActive): ?>
                        <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-600 rounded-full">有効</span>
                        <?php else: ?>
                        <span class="px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-500 rounded-full">無効</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('members/' . $member['id']) ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="詳細">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('members/' . $member['id'] . '/edit') ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="編集">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="confirmDelete(<?= $member['id'] ?>, '<?= esc($member['name']) ?>')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="削除">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($members)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">メンバーが登録されていません</h3>
            <p class="text-slate-500 mb-6">新規追加ボタンからメンバーを登録してください</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- カードビュー（スマホは常時表示、PC/タブレットは切り替え時のみ） -->
    <div id="card-view" class="md:hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 fade-in">
            <?php foreach ($members as $member): ?>
            <?php
                $color = \App\Models\MemberModel::getColor($member['name']);
                $initial = \App\Models\MemberModel::getInitial($member['name']);
                $roleLabel = \App\Models\MemberModel::getRoleLabel($member['role'] ?? 'member');
                $roleColor = \App\Models\MemberModel::getRoleColor($member['role'] ?? 'member');
                $statusLabel = \App\Models\MemberModel::getStatusLabel((bool)$member['is_active']);
                $statusColor = \App\Models\MemberModel::getStatusColor((bool)$member['is_active']);
            ?>
            <div class="member-card bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r <?= esc($color) ?>"></div>
                <div class="p-4 sm:p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center min-w-0">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-3 sm:mr-4 flex-shrink-0">
                                <span class="text-white font-bold text-lg sm:text-xl"><?= esc($initial) ?></span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-bold text-slate-800 truncate"><?= esc($member['name']) ?></h3>
                                <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full"><?= esc($statusLabel) ?></span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-semibold <?= esc($roleColor) ?> rounded-full flex-shrink-0 ml-2"><?= esc($roleLabel) ?></span>
                    </div>

                    <div class="space-y-2 mb-4 text-sm">
                        <?php if (!empty($member['department'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-building text-slate-400 w-5 mr-2"></i>
                            <span class="text-slate-600"><?= esc($member['department']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($member['email'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-slate-400 w-5 mr-2"></i>
                            <span class="text-slate-600 truncate"><?= esc($member['email']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($member['phone'])): ?>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-slate-400 w-5 mr-2"></i>
                            <span class="text-slate-600"><?= esc($member['phone']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <a href="<?= base_url('members/' . $member['id']) ?>" class="btn-detail flex items-center justify-center w-full py-2.5 rounded-lg text-sm font-semibold text-slate-700">
                            <i class="fas fa-eye mr-2"></i>
                            詳細
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($members)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">メンバーが登録されていません</h3>
            <p class="text-slate-500 mb-6">新規追加ボタンからメンバーを登録してください</p>
            <a href="<?= base_url('members/create') ?>" class="btn-stylish inline-flex items-center px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                新規追加
            </a>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- 削除確認モーダル -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 text-center mb-2">メンバーを削除しますか？</h3>
            <p id="delete-message" class="text-slate-500 text-center mb-6">この操作は取り消せません。</p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                    キャンセル
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        削除する
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ビュー切り替え（PC/タブレットのみ）
const viewTableBtn = document.getElementById('view-table');
const viewCardBtn = document.getElementById('view-card');
const tableView = document.getElementById('table-view');
const cardView = document.getElementById('card-view');

// PC/タブレットでのみビュー切り替え機能を有効化
function isMobile() {
    return window.innerWidth < 768;
}

// ローカルストレージから設定を読み込み（PC/タブレットのみ）
function initView() {
    if (!isMobile()) {
        const savedView = localStorage.getItem('memberViewMode') || 'table';
        if (savedView === 'card') {
            switchToCardView();
        } else {
            switchToTableView();
        }
    }
}

initView();

// ウィンドウリサイズ時に再初期化
window.addEventListener('resize', function() {
    if (isMobile()) {
        // スマホではカードビューのみ
        tableView.classList.add('hidden');
        tableView.classList.remove('md:block');
        cardView.classList.remove('hidden');
    } else {
        // PC/タブレットではデフォルトに戻す
        tableView.classList.add('md:block');
        initView();
    }
});

viewTableBtn.addEventListener('click', function() {
    if (!isMobile()) {
        switchToTableView();
        localStorage.setItem('memberViewMode', 'table');
    }
});

viewCardBtn.addEventListener('click', function() {
    if (!isMobile()) {
        switchToCardView();
        localStorage.setItem('memberViewMode', 'card');
    }
});

function switchToTableView() {
    viewTableBtn.classList.add('active');
    viewCardBtn.classList.remove('active');
    tableView.classList.remove('hidden');
    tableView.classList.add('md:block');
    cardView.classList.add('hidden');
    cardView.classList.remove('md:hidden');
}

function switchToCardView() {
    viewCardBtn.classList.add('active');
    viewTableBtn.classList.remove('active');
    cardView.classList.remove('hidden');
    cardView.classList.remove('md:hidden');
    tableView.classList.add('hidden');
    tableView.classList.remove('md:block');
}

// 削除モーダル
function confirmDelete(memberId, memberName) {
    document.getElementById('delete-message').textContent = '「' + memberName + '」を削除してもよろしいですか？この操作は取り消せません。';
    document.getElementById('delete-form').action = '<?= base_url('members') ?>/' + memberId;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

// モーダル外クリックで閉じる
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
<?= $this->endSection() ?>
