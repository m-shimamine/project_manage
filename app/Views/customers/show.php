<?= $this->extend('layouts/app') ?>

<?php
$color = \App\Models\CustomerModel::getColor($customer['name']);
$initial = \App\Models\CustomerModel::getInitial($customer['name']);
$statusLabel = \App\Models\CustomerModel::getStatusLabel($customer['status']);
$statusColor = \App\Models\CustomerModel::getStatusColor($customer['status']);
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
                <a href="<?= base_url('customers') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-building mr-1"></i>顧客一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-700 font-medium">顧客詳細</span>
            </nav>
            <!-- アクションボタン -->
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('customers/' . $customer['id'] . '/edit') ?>" class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                    <i class="fas fa-edit mr-1"></i><span class="hidden sm:inline">編集</span>
                </a>
                <form method="POST" action="<?= base_url('customers/' . $customer['id']) ?>" onsubmit="return confirm('本当に削除しますか？');" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="inline-flex items-center px-3 sm:px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                        <i class="fas fa-trash mr-1"></i><span class="hidden sm:inline">削除</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="p-4 sm:p-6">
    <!-- 成功メッセージ -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <!-- 2列レイアウト -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 左カラム -->
        <div class="space-y-6">
            <!-- 基本情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-building text-blue-500 mr-2"></i>会社情報
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-4">
                            <span class="text-white font-bold text-2xl"><?= esc($initial) ?></span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-800 mb-1"><?= esc($customer['name']) ?></h1>
                            <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full">
                                <?= esc($statusLabel) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 住所情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-map-marker-alt text-violet-500 mr-2"></i>住所情報
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">郵便番号</span>
                        <span class="text-sm font-medium text-slate-800"><?= esc($customer['postal_code'] ?: '-') ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">都道府県・市区町村</span>
                        <span class="text-sm font-medium text-slate-800"><?= esc($customer['city'] ?: '-') ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500">番地・建物名</span>
                        <span class="text-sm font-medium text-slate-800"><?= esc($customer['address'] ?: '-') ?></span>
                    </div>
                </div>
            </div>

            <!-- 保守担当情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-tools text-indigo-500 mr-2"></i>保守担当
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div class="py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500 block mb-2">主担当</span>
                        <?php if (!empty($customer['project_leader'])): ?>
                        <span class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-sm font-medium border border-indigo-200">
                            <i class="fas fa-user-tie text-indigo-500 mr-1.5 text-xs"></i><?= esc($customer['project_leader']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                    <div class="py-2">
                        <span class="text-sm text-slate-500 block mb-2">メンバー</span>
                        <?php if (!empty($customer['members']) && count($customer['members']) > 0): ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($customer['members'] as $member): ?>
                            <span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                <i class="fas fa-user text-purple-500 mr-1.5 text-xs"></i><?= esc($member) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 備考 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-sticky-note text-amber-500 mr-2"></i>備考
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($customer['notes'])): ?>
                    <p class="text-sm text-slate-700 whitespace-pre-wrap"><?= esc($customer['notes']) ?></p>
                    <?php else: ?>
                    <p class="text-sm text-slate-400">備考はありません</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 右カラム -->
        <div class="space-y-6">
            <!-- 担当者情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-address-book text-emerald-500 mr-2"></i>担当者情報
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($customer['contacts']) && count($customer['contacts']) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($customer['contacts'] as $index => $contact): ?>
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-slate-700 flex items-center">
                                    <i class="fas fa-user text-emerald-500 mr-2"></i>担当者 <?= $index + 1 ?>
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">担当者名</label>
                                    <div class="text-sm text-slate-800">
                                        <?= esc(is_array($contact) ? ($contact['name'] ?? '-') : $contact) ?>
                                    </div>
                                </div>
                                <?php if (is_array($contact)): ?>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">電話番号</label>
                                    <div class="text-sm text-slate-800">
                                        <?php if (!empty($contact['phone'])): ?>
                                        <a href="tel:<?= esc($contact['phone']) ?>" class="text-blue-600 hover:underline"><?= esc($contact['phone']) ?></a>
                                        <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">メールアドレス</label>
                                    <div class="text-sm text-slate-800">
                                        <?php if (!empty($contact['email'])): ?>
                                        <a href="mailto:<?= esc($contact['email']) ?>" class="text-blue-600 hover:underline"><?= esc($contact['email']) ?></a>
                                        <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-address-book text-4xl mb-2"></i>
                        <p class="text-sm">担当者情報がありません</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- プロジェクト一覧 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-folder-open text-blue-500 mr-2"></i>プロジェクト
                        <?php if (!empty($projects) && count($projects) > 0): ?>
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                            <?= count($projects) ?>件
                        </span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($projects) && count($projects) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($projects as $project): ?>
                        <?php
                            $projectColor = \App\Models\ProjectModel::getColor($project['name']);
                            $projectStatusLabel = \App\Models\ProjectModel::getStatusLabel($project['status']);
                            $projectStatusColor = \App\Models\ProjectModel::getStatusColor($project['status']);
                            $projectMembers = $project['members'] ?? [];
                            if (!is_array($projectMembers)) {
                                $projectMembers = json_decode($projectMembers, true) ?? [];
                            }
                        ?>
                        <a href="<?= base_url('projects/' . $project['id']) ?>" class="block p-4 bg-slate-50 rounded-xl border border-slate-200 hover:shadow-md hover:border-blue-300 transition-all group">
                            <!-- プロジェクト名とステータス -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-folder text-blue-500 mr-2"></i>
                                    <span class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors"><?= esc($project['name']) ?></span>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full <?= esc($projectStatusColor) ?>">
                                    <?= esc($projectStatusLabel) ?>
                                </span>
                            </div>
                            <!-- 詳細情報 -->
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <!-- 期間 -->
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-calendar-alt text-slate-400 w-4 mr-1"></i>
                                    <span>
                                        <?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>
                                            <?= !empty($project['start_date']) ? date('Y/m/d', strtotime($project['start_date'])) : '' ?>
                                            <?php if (!empty($project['start_date']) && !empty($project['end_date'])): ?> 〜 <?php endif; ?>
                                            <?= !empty($project['end_date']) ? date('Y/m/d', strtotime($project['end_date'])) : '' ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <!-- 予算 -->
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-yen-sign text-slate-400 w-4 mr-1"></i>
                                    <span><?= !empty($project['budget']) ? number_format($project['budget']) . '円' : '-' ?></span>
                                </div>
                                <!-- リーダー -->
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-user-tie text-indigo-400 w-4 mr-1"></i>
                                    <span><?= !empty($project['project_leader']) ? esc($project['project_leader']) : '-' ?></span>
                                </div>
                                <!-- メンバー数 -->
                                <div class="flex items-center text-slate-500">
                                    <i class="fas fa-users text-purple-400 w-4 mr-1"></i>
                                    <span>
                                        <?php if (!empty($projectMembers) && count($projectMembers) > 0): ?>
                                            <?= count($projectMembers) ?>名
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (!empty($projectMembers) && count($projectMembers) > 0): ?>
                            <!-- メンバー名 -->
                            <div class="mt-2 pt-2 border-t border-slate-200">
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($projectMembers as $member): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-xs">
                                        <?= esc($member) ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-2"></i>
                        <p class="text-sm">プロジェクトが紐づいていません</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
