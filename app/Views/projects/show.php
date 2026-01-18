<?= $this->extend('layouts/app') ?>

<?php
$color = \App\Models\ProjectModel::getColor($project['name']);
$initial = \App\Models\ProjectModel::getInitial($project['name']);
$statusLabel = \App\Models\ProjectModel::getStatusLabel($project['status']);
$statusColor = \App\Models\ProjectModel::getStatusColor($project['status']);
?>

<?= $this->section('styles') ?>
<style>
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 40;
    }
    .member-tag {
        transition: all 0.2s ease;
    }
    .member-tag:hover {
        transform: scale(1.05);
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
                <a href="<?= base_url('projects') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-folder mr-1"></i>プロジェクト一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-700 font-medium">プロジェクト詳細</span>
            </nav>
            <!-- アクションボタン -->
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('projects/' . $project['id'] . '/edit') ?>" class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                    <i class="fas fa-edit mr-1"></i><span class="hidden sm:inline">編集</span>
                </a>
                <form method="POST" action="<?= base_url('projects/' . $project['id']) ?>" onsubmit="return confirm('本当に削除しますか？');" class="inline">
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
    <!-- 2列レイアウト -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 左カラム -->
        <div class="space-y-6">
            <!-- 基本情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-folder text-blue-500 mr-2"></i>プロジェクト情報
                    </h4>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-4">
                            <span class="text-white font-bold text-2xl"><?= esc($initial) ?></span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-800 mb-1"><?= esc($project['name']) ?></h1>
                            <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full">
                                <?= esc($statusLabel) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($project['description'])): ?>
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-xs font-medium text-slate-500 mb-1">概要</label>
                        <p class="text-sm text-slate-800 whitespace-pre-wrap"><?= esc($project['description']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 顧客情報 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-building text-emerald-500 mr-2"></i>顧客情報
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($project['customer_name'])): ?>
                    <a href="<?= base_url('customers/' . $project['customer_id']) ?>" class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-200 hover:shadow-md transition-all group">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg mr-4">
                                <span class="text-white font-bold text-lg"><?= mb_substr($project['customer_name'], 0, 1) ?></span>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors"><?= esc($project['customer_name']) ?></span>
                                <p class="text-sm text-slate-500">顧客</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right text-slate-400 group-hover:text-emerald-600 transition-colors"></i>
                    </a>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-building text-4xl mb-2"></i>
                        <p class="text-sm">顧客が設定されていません</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 期間・予算 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-calendar-alt text-green-500 mr-2"></i>期間・予算
                    </h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">開始日</span>
                        <span class="text-sm font-medium text-slate-800"><?= !empty($project['start_date']) ? date('Y年m月d日', strtotime($project['start_date'])) : '-' ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-sm text-slate-500">終了日</span>
                        <span class="text-sm font-medium text-slate-800"><?= !empty($project['end_date']) ? date('Y年m月d日', strtotime($project['end_date'])) : '-' ?></span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-500">予算</span>
                        <span class="text-sm font-medium text-slate-800"><?= !empty($project['budget']) ? '¥' . number_format($project['budget']) : '-' ?></span>
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
                    <?php if (!empty($project['notes'])): ?>
                    <p class="text-sm text-slate-700 whitespace-pre-wrap"><?= esc($project['notes']) ?></p>
                    <?php else: ?>
                    <p class="text-sm text-slate-400">備考はありません</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 右カラム -->
        <div class="space-y-6">
            <!-- プロジェクトリーダー -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-user-tie text-indigo-500 mr-2"></i>プロジェクトリーダー
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($project['project_leader'])): ?>
                    <div class="flex items-center p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-800"><?= esc($project['project_leader']) ?></span>
                            <p class="text-sm text-slate-500">プロジェクトリーダー</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-user-tie text-4xl mb-2"></i>
                        <p class="text-sm">リーダーが設定されていません</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- メンバー -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                    <h4 class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-users text-purple-500 mr-2"></i>メンバー
                        <?php if (!empty($project['members']) && count($project['members']) > 0): ?>
                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                            <?= count($project['members']) ?>名
                        </span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="p-6">
                    <?php if (!empty($project['members']) && count($project['members']) > 0): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($project['members'] as $member): ?>
                        <span class="member-tag inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-full text-sm font-medium border border-purple-200">
                            <i class="fas fa-user text-purple-500 mr-1.5 text-xs"></i>
                            <?= esc($member) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-users text-4xl mb-2"></i>
                        <p class="text-sm">メンバーが登録されていません</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
