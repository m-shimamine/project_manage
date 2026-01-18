<?= $this->extend('layouts/app') ?>

<?php
$color = \App\Models\MemberModel::getColor($member['name']);
$initial = \App\Models\MemberModel::getInitial($member['name']);
$departmentLabel = \App\Models\MemberModel::getDepartmentLabel($member['department'] ?? '');
$departmentColor = \App\Models\MemberModel::getDepartmentColor($member['department'] ?? '');
$roleLabel = \App\Models\MemberModel::getRoleLabel($member['role'] ?? 'member');
$roleColor = \App\Models\MemberModel::getRoleColor($member['role'] ?? 'member');
$statusLabel = \App\Models\MemberModel::getStatusLabel((bool)$member['is_active']);
$statusColor = \App\Models\MemberModel::getStatusColor((bool)$member['is_active']);
?>

<?= $this->section('styles') ?>
<style>
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 40;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- スティッキーヘッダー（パンくず + アクションボタン） -->
<div class="sticky-header bg-white border-b border-slate-200 shadow-sm">
    <div class="px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- パンくずリスト -->
            <nav class="flex items-center space-x-2 text-sm">
                <a href="<?= base_url('members') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-users mr-1"></i>メンバー一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-700 font-medium">メンバー詳細</span>
            </nav>
            <!-- アクションボタン -->
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('members/' . $member['id'] . '/edit') ?>" class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                    <i class="fas fa-edit mr-1"></i><span class="hidden sm:inline">編集</span>
                </a>
                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 sm:px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash mr-1"></i><span class="hidden sm:inline">削除</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="p-4 sm:p-6">
    <!-- 2列レイアウト -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 左カラム -->
        <div class="space-y-6">
            <!-- メンバー情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i>メンバー情報
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-4">
                            <span class="text-white font-bold text-2xl"><?= esc($initial) ?></span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-800 mb-1"><?= esc($member['name']) ?></h1>
                            <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full">
                                <?= esc($statusLabel) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($member['position'])): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-medium text-slate-500 mb-1">役職</label>
                        <p class="text-sm text-slate-800"><?= esc($member['position']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 連絡先情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-address-card text-emerald-500 mr-2"></i>連絡先情報
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">メールアドレス</span>
                        <?php if (!empty($member['email'])): ?>
                        <a href="mailto:<?= esc($member['email']) ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                            <?= esc($member['email']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500">電話番号</span>
                        <?php if (!empty($member['phone'])): ?>
                        <a href="tel:<?= esc($member['phone']) ?>" class="text-sm font-medium text-slate-800">
                            <?= esc($member['phone']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 所属情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-building text-violet-500 mr-2"></i>所属情報
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">部署</span>
                        <?php if (!empty($member['department'])): ?>
                        <span class="px-2.5 py-1 text-xs font-semibold <?= esc($departmentColor) ?> rounded-full">
                            <?= esc($departmentLabel) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500">役職</span>
                        <?php if (!empty($member['position'])): ?>
                        <span class="text-sm font-medium text-slate-800"><?= esc($member['position']) ?></span>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右カラム -->
        <div class="space-y-6">
            <!-- 権限情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-shield-alt text-indigo-500 mr-2"></i>権限情報
                    </h4>
                </div>
                <div class="p-6">
                    <div class="flex items-center p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg mr-4">
                            <?php if (($member['role'] ?? 'member') === 'admin'): ?>
                            <i class="fas fa-crown text-white"></i>
                            <?php elseif (($member['role'] ?? 'member') === 'leader'): ?>
                            <i class="fas fa-user-tie text-white"></i>
                            <?php else: ?>
                            <i class="fas fa-user text-white"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="px-3 py-1 text-sm font-semibold <?= esc($roleColor) ?> rounded-full">
                                <?= esc($roleLabel) ?>
                            </span>
                            <p class="text-sm text-slate-500 mt-1">システム権限</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ステータス情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-toggle-on text-emerald-500 mr-2"></i>ステータス
                    </h4>
                </div>
                <div class="p-6">
                    <div class="flex items-center p-4 <?= $member['is_active'] ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-200' ?> rounded-xl border">
                        <div class="w-12 h-12 rounded-full <?= $member['is_active'] ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : 'bg-gradient-to-br from-slate-400 to-slate-600' ?> flex items-center justify-center shadow-lg mr-4">
                            <?php if ($member['is_active']): ?>
                            <i class="fas fa-check text-white"></i>
                            <?php else: ?>
                            <i class="fas fa-times text-white"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="px-3 py-1 text-sm font-semibold <?= esc($statusColor) ?> rounded-full">
                                <?= esc($statusLabel) ?>
                            </span>
                            <p class="text-sm text-slate-500 mt-1">アカウント状態</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 登録情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-clock text-amber-500 mr-2"></i>登録情報
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">登録日時</span>
                        <span class="text-sm font-medium text-slate-800"><?= !empty($member['created_at']) ? date('Y年m月d日 H:i', strtotime($member['created_at'])) : '-' ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500">最終更新日時</span>
                        <span class="text-sm font-medium text-slate-800"><?= !empty($member['updated_at']) ? date('Y年m月d日 H:i', strtotime($member['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 text-center mb-2">メンバーを削除しますか？</h3>
            <p class="text-slate-500 text-center mb-6">この操作は取り消せません。本当に「<?= esc($member['name']) ?>」を削除してもよろしいですか？</p>
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                    キャンセル
                </button>
                <form action="<?= base_url('members/' . $member['id']) ?>" method="POST" class="flex-1">
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
function confirmDelete() {
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
