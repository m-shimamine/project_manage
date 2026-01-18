<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .member-tag {
        transition: all 0.2s ease;
    }
    .member-tag:hover {
        transform: scale(1.02);
    }
    .field-error {
        border-color: #ef4444 !important;
    }
    .field-error:focus {
        ring-color: #ef4444 !important;
        border-color: #ef4444 !important;
    }
    .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: flex;
        align-items: center;
    }
    .error-message i {
        margin-right: 4px;
        font-size: 10px;
    }
    /* 選択済み顧客表示 */
    .selected-customer {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .selected-customer .customer-info {
        flex: 1;
    }
    .selected-customer .customer-name {
        font-weight: 500;
        color: #1e293b;
    }
    .selected-customer .customer-status {
        font-size: 12px;
        color: #64748b;
    }
    .selected-customer .remove-btn {
        color: #94a3b8;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .selected-customer .remove-btn:hover {
        color: #ef4444;
        background: #fef2f2;
    }
    /* 顧客サジェストドロップダウン */
    .customer-suggest-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 9999;
        max-height: 240px;
        overflow-y: auto;
        display: none;
        margin-top: 4px;
    }
    .customer-suggest-dropdown.show {
        display: block;
    }
    /* 顧客セレクターの親要素のoverflow制御 */
    #customer-selector {
        position: relative;
        z-index: 50;
    }
    .customer-suggest-item {
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.15s;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .customer-suggest-item:last-child {
        border-bottom: none;
    }
    .customer-suggest-item:hover,
    .customer-suggest-item.highlighted {
        background: #eff6ff;
    }
    .customer-suggest-item .customer-name {
        font-weight: 500;
        color: #1e293b;
    }
    .customer-suggest-item .customer-status {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 9999px;
        background: #f1f5f9;
        color: #64748b;
    }
    /* 検索ボタン */
    .search-btn {
        padding: 0 14px;
        color: white;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-btn.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    .search-btn.blue:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }
    .search-btn.indigo {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }
    .search-btn.indigo:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    }
    .search-btn.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .search-btn.purple:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    }
    /* サジェストドロップダウン共通 */
    .suggest-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 9999;
        max-height: 240px;
        overflow-y: auto;
        display: none;
        margin-top: 4px;
    }
    .suggest-dropdown.show {
        display: block;
    }
    .suggest-item {
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.15s;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .suggest-item:last-child {
        border-bottom: none;
    }
    .suggest-item:hover,
    .suggest-item.highlighted {
        background: #eff6ff;
    }
    .suggest-item .item-name {
        font-weight: 500;
        color: #1e293b;
    }
    /* セレクター親要素のz-index */
    #leader-selector {
        position: relative;
        z-index: 45;
    }
    #member-selector {
        position: relative;
        z-index: 44;
    }
</style>
<?= $this->endSection() ?>

<?php
// 編集モードかどうか判定
$isEdit = isset($project) && !empty($project['id']);
$formAction = $isEdit ? base_url('projects/' . $project['id']) : base_url('projects');
$pageLabel = $isEdit ? '編集' : '新規登録';

// フィールド別エラーを取得
$fieldErrors = session()->getFlashdata('field_errors') ?? [];

// デフォルト値の設定
$defaults = [
    'id'             => $project['id'] ?? '',
    'name'           => old('name', $project['name'] ?? ''),
    'customer_id'    => old('customer_id', $project['customer_id'] ?? ($selectedCustomerId ?? '')),
    'status'         => old('status', $project['status'] ?? 'planning'),
    'description'    => old('description', $project['description'] ?? ''),
    'start_date'     => old('start_date', $project['start_date'] ?? ''),
    'end_date'       => old('end_date', $project['end_date'] ?? ''),
    'project_leader' => old('project_leader', $project['project_leader'] ?? ''),
    'members'        => old('members', $project['members'] ?? []),
    'budget'         => old('budget', $project['budget'] ?? ''),
    'notes'          => old('notes', $project['notes'] ?? ''),
];

// 選択済み顧客情報を取得
$selectedCustomer = null;
if (!empty($defaults['customer_id'])) {
    foreach ($customers as $c) {
        if ($c['id'] == $defaults['customer_id']) {
            $selectedCustomer = $c;
            break;
        }
    }
}

$statusLabels = [
    'active' => '進行中',
    'maintenance' => '保守',
    'inactive' => '取引停止'
];
?>

<?= $this->section('content') ?>
<!-- スティッキーヘッダー -->
<div id="sticky-header" class="sticky top-0 z-[100] bg-white border-b border-slate-200 shadow-sm">
    <div class="px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- パンくずリスト -->
            <nav class="flex items-center space-x-2 text-sm">
                <a href="<?= base_url('projects') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-project-diagram mr-1"></i>プロジェクト一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php if ($isEdit): ?>
                <a href="<?= base_url('projects/' . $defaults['id']) ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    プロジェクト詳細
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php endif; ?>
                <span class="text-slate-700 font-medium"><?= $pageLabel ?></span>
            </nav>
            <!-- 保存ボタン -->
            <button type="submit" form="project-form" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:from-blue-600 hover:to-indigo-700 transition-all">
                <i class="fas fa-save mr-2"></i>
                <span class="hidden sm:inline">保存する</span>
                <span class="sm:hidden">保存</span>
            </button>
        </div>
    </div>
</div>

<div class="p-4 sm:p-6">
    <!-- フォーム -->
    <form method="POST" action="<?= $formAction ?>" id="project-form">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <!-- 2列レイアウト -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 左カラム -->
            <div class="space-y-6">
                <!-- 顧客選択（最初に表示） -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-building text-emerald-500 mr-2"></i>顧客 <span class="text-red-500 ml-1">*</span>
                        </h4>
                    </div>
                    <div class="p-6">
                        <!-- 顧客検索（サジェスト＋モーダル） -->
                        <div class="relative" id="customer-selector">
                            <div class="flex">
                                <div class="relative flex-1">
                                    <input type="text" id="customer-search-input"
                                        value="<?= $selectedCustomer ? esc($selectedCustomer['name']) : '' ?>"
                                        placeholder="顧客名を入力して検索..."
                                        class="w-full px-4 py-2.5 border border-slate-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['customer_id']) ? 'field-error' : '' ?>"
                                        autocomplete="off">
                                    <!-- サジェストドロップダウン -->
                                    <div class="customer-suggest-dropdown" id="customer-suggest-dropdown">
                                        <?php foreach ($customers as $customer): ?>
                                        <div class="customer-suggest-item"
                                             data-id="<?= esc($customer['id']) ?>"
                                             data-name="<?= esc($customer['name']) ?>"
                                             data-status="<?= esc($customer['status']) ?>"
                                             data-status-label="<?= esc($statusLabels[$customer['status']] ?? $customer['status']) ?>">
                                            <span class="customer-name"><?= esc($customer['name']) ?></span>
                                            <span class="customer-status"><?= esc($statusLabels[$customer['status']] ?? $customer['status']) ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- モーダル検索ボタン -->
                                <button type="button" class="search-btn blue" id="open-customer-modal-btn" title="一覧から選択">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- 選択済み顧客情報表示 -->
                            <div id="selected-customer-info" class="mt-2 <?= $selectedCustomer ? '' : 'hidden' ?>">
                                <div class="flex items-center justify-between px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-lg">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                        <span class="text-emerald-700 font-medium" id="selected-customer-label">
                                            <?= $selectedCustomer ? esc($selectedCustomer['name']) : '' ?>
                                        </span>
                                        <span class="text-emerald-600 text-xs ml-2" id="selected-customer-status-label">
                                            <?= $selectedCustomer ? '(' . esc($statusLabels[$selectedCustomer['status']] ?? $selectedCustomer['status']) . ')' : '' ?>
                                        </span>
                                    </div>
                                    <button type="button" class="text-emerald-600 hover:text-red-500 transition-colors" id="clear-customer-btn" title="選択解除">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="customer_id" id="customer_id" value="<?= esc($defaults['customer_id']) ?>">
                        <?php if (isset($fieldErrors['customer_id'])): ?>
                        <div class="error-message mt-2"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['customer_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 基本情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-project-diagram text-blue-500 mr-2"></i>基本情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">プロジェクト名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="field-name" value="<?= esc($defaults['name']) ?>" placeholder="例: Webサイトリニューアル"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['name']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['name'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">ステータス <span class="text-red-500">*</span></label>
                            <select name="status" id="field-status" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white <?= isset($fieldErrors['status']) ? 'field-error' : '' ?>">
                                <option value="planning" <?= $defaults['status'] === 'planning' ? 'selected' : '' ?>>計画中</option>
                                <option value="in_progress" <?= $defaults['status'] === 'in_progress' ? 'selected' : '' ?>>進行中</option>
                                <option value="on_hold" <?= $defaults['status'] === 'on_hold' ? 'selected' : '' ?>>保留中</option>
                                <option value="completed" <?= $defaults['status'] === 'completed' ? 'selected' : '' ?>>完了</option>
                                <option value="cancelled" <?= $defaults['status'] === 'cancelled' ? 'selected' : '' ?>>中止</option>
                            </select>
                            <?php if (isset($fieldErrors['status'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">概要</label>
                            <textarea name="description" id="field-description" rows="3" placeholder="プロジェクトの概要を入力"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all resize-none <?= isset($fieldErrors['description']) ? 'field-error' : '' ?>"><?= esc($defaults['description']) ?></textarea>
                            <?php if (isset($fieldErrors['description'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['description']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 期間・予算 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-calendar-alt text-green-500 mr-2"></i>期間・予算
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">開始日</label>
                                <input type="date" name="start_date" id="field-start_date" value="<?= esc($defaults['start_date']) ?>"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['start_date']) ? 'field-error' : '' ?>">
                                <?php if (isset($fieldErrors['start_date'])): ?>
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['start_date']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">終了日</label>
                                <input type="date" name="end_date" id="field-end_date" value="<?= esc($defaults['end_date']) ?>"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['end_date']) ? 'field-error' : '' ?>">
                                <?php if (isset($fieldErrors['end_date'])): ?>
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['end_date']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">予算</label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-slate-400">¥</span>
                                <input type="number" name="budget" id="field-budget" value="<?= esc($defaults['budget']) ?>" min="0" placeholder="予算を入力"
                                    class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['budget']) ? 'field-error' : '' ?>">
                            </div>
                            <?php if (isset($fieldErrors['budget'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['budget']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 備考 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-sticky-note text-amber-500 mr-2"></i>備考
                        </h4>
                    </div>
                    <div class="p-6">
                        <textarea name="notes" id="field-notes" rows="4" placeholder="メモや補足情報を入力してください"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all resize-none <?= isset($fieldErrors['notes']) ? 'field-error' : '' ?>"><?= esc($defaults['notes']) ?></textarea>
                        <?php if (isset($fieldErrors['notes'])): ?>
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['notes']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 右カラム：担当者情報 -->
            <div class="space-y-6">
                <!-- プロジェクトリーダー -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-user-tie text-indigo-500 mr-2"></i>プロジェクトリーダー
                        </h4>
                    </div>
                    <div class="p-6">
                        <!-- リーダー検索（サジェスト＋モーダル） -->
                        <div id="leader-selector">
                            <div class="flex">
                                <div class="relative flex-1">
                                    <input type="text" id="leader-search-input"
                                        value="<?= esc($defaults['project_leader']) ?>"
                                        placeholder="リーダー名を入力して検索..."
                                        class="w-full px-4 py-2.5 border border-slate-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all <?= isset($fieldErrors['project_leader']) ? 'field-error' : '' ?>"
                                        autocomplete="off">
                                    <!-- サジェストドロップダウン -->
                                    <div class="suggest-dropdown" id="leader-suggest-dropdown">
                                        <?php foreach ($members as $member): ?>
                                        <div class="suggest-item leader-suggest-item"
                                             data-id="<?= esc($member['id']) ?>"
                                             data-name="<?= esc($member['name']) ?>">
                                            <span class="item-name"><?= esc($member['name']) ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- モーダル検索ボタン -->
                                <button type="button" class="search-btn indigo" id="open-leader-modal-btn" title="一覧から選択">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- 選択済みリーダー情報表示 -->
                            <div id="selected-leader-info" class="mt-2 <?= !empty($defaults['project_leader']) ? '' : 'hidden' ?>">
                                <div class="flex items-center justify-between px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                                        <span class="text-indigo-700 font-medium" id="selected-leader-label">
                                            <?= esc($defaults['project_leader']) ?>
                                        </span>
                                    </div>
                                    <button type="button" class="text-indigo-600 hover:text-red-500 transition-colors" id="clear-leader-btn" title="選択解除">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="project_leader" id="project_leader" value="<?= esc($defaults['project_leader']) ?>">
                        <?php if (isset($fieldErrors['project_leader'])): ?>
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['project_leader']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- メンバー -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-users text-purple-500 mr-2"></i>メンバー
                        </h4>
                    </div>
                    <div class="p-6">
                        <!-- メンバー検索（サジェスト＋モーダル） -->
                        <div id="member-selector">
                            <div class="flex">
                                <div class="relative flex-1">
                                    <input type="text" id="member-search-input"
                                        placeholder="メンバー名を入力して検索..."
                                        class="w-full px-4 py-2.5 border border-slate-300 rounded-l-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm transition-all"
                                        autocomplete="off">
                                    <!-- サジェストドロップダウン -->
                                    <div class="suggest-dropdown" id="member-suggest-dropdown">
                                        <?php foreach ($members as $member): ?>
                                        <div class="suggest-item member-suggest-item"
                                             data-id="<?= esc($member['id']) ?>"
                                             data-name="<?= esc($member['name']) ?>">
                                            <span class="item-name"><?= esc($member['name']) ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- モーダル検索ボタン -->
                                <button type="button" class="search-btn purple" id="open-member-modal-btn" title="一覧から選択">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            サジェストから選択、またはボタンで一覧から複数選択
                        </p>
                        <!-- 選択済みメンバー（フォームの下に配置） -->
                        <div id="members-container" class="flex flex-wrap gap-2 mt-4 <?= empty($selectedMembers) ? '' : '' ?>">
                            <?php
                            $selectedMembers = $defaults['members'];
                            if (!is_array($selectedMembers)) {
                                $selectedMembers = json_decode($selectedMembers, true) ?? [];
                            }
                            foreach ($selectedMembers as $member):
                            ?>
                            <span class="member-tag inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                <i class="fas fa-user text-purple-500 mr-1.5 text-xs"></i>
                                <span><?= esc($member) ?></span>
                                <button type="button" class="ml-2 text-purple-500 hover:text-purple-700 remove-member">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <input type="hidden" name="members[]" value="<?= esc($member) ?>">
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ボタン（モバイル用） -->
        <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-slate-200 sm:hidden">
            <a href="<?= $isEdit ? base_url('projects/' . $defaults['id']) : base_url('projects') ?>" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                キャンセル
            </a>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-sm font-medium text-white flex items-center">
                <i class="fas fa-save mr-2"></i>保存する
            </button>
        </div>
    </form>
</div>

<!-- 顧客選択モーダル -->
<?= $this->include('layouts/partials/customer_selector_modal') ?>

<!-- メンバー選択モーダル -->
<?= $this->include('layouts/partials/member_selector_modal') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
// jQueryの読み込みを待つ
function initWhenReady() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initWhenReady, 50);
        return;
    }
    var $ = jQuery;

// ========================================
// 顧客選択モーダル（グローバル関数）
// ========================================
window.customerModalCallback = null;

window.openCustomerModal = function(callback) {
    window.customerModalCallback = callback;
    var modal = document.getElementById('customer-selector-modal');
    var search = document.getElementById('customer-modal-search');
    var items = document.querySelectorAll('.customer-modal-item');

    if (modal) {
        modal.classList.add('show');
    }
    if (search) {
        search.value = '';
        search.focus();
    }
    if (items) {
        items.forEach(function(item) {
            item.classList.remove('hidden');
        });
    }
};

window.closeCustomerModal = function() {
    var modal = document.getElementById('customer-selector-modal');
    if (modal) {
        modal.classList.remove('show');
    }
    window.customerModalCallback = null;
};

// ========================================
// メンバー選択モーダル（グローバル関数）
// ========================================
window.memberModalCallback = null;
window.memberModalMultiSelect = false;
window.memberModalSelectedIds = [];

window.openMemberModal = function(callback, options) {
    options = options || {};
    window.memberModalCallback = callback;
    window.memberModalMultiSelect = options.multiSelect || false;
    window.memberModalSelectedIds = options.selectedIds || [];

    var modal = document.getElementById('member-selector-modal');
    var search = document.getElementById('member-modal-search');
    var items = document.querySelectorAll('.member-modal-item');
    var title = document.getElementById('member-modal-title');

    if (title) {
        title.textContent = options.title || 'メンバーを選択';
    }

    if (modal) {
        modal.classList.add('show');
    }
    if (search) {
        search.value = '';
        search.focus();
    }
    if (items) {
        items.forEach(function(item) {
            item.classList.remove('hidden');
            // 選択状態を更新
            if (window.memberModalSelectedIds.indexOf(item.dataset.name) !== -1) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });
    }
};

window.closeMemberModal = function() {
    var modal = document.getElementById('member-selector-modal');
    if (modal) {
        modal.classList.remove('show');
    }
    window.memberModalCallback = null;
    window.memberModalMultiSelect = false;
    window.memberModalSelectedIds = [];
};

// jQuery準備完了時の処理
$(document).ready(function() {
    // スティッキーヘッダーのz-index制御
    var $stickyHeader = $('#sticky-header');

    function hideHeaderForDropdown() {
        $stickyHeader.css('z-index', '40');
    }

    function showHeaderAfterDropdown() {
        $stickyHeader.css('z-index', '100');
    }

    // HTMLエスケープ
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ========================================
    // 顧客選択モーダル
    // ========================================
    $('#customer-modal-search').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.customer-modal-item').each(function() {
            var name = $(this).data('name').toString().toLowerCase();
            if (name.indexOf(query) !== -1) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    });

    $(document).on('click', '.customer-modal-item', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var status = $(this).data('status');
        var statusLabel = $(this).data('status-label');

        if (customerModalCallback) {
            customerModalCallback({
                id: id,
                name: name,
                status: status,
                statusLabel: statusLabel
            });
        }
        closeCustomerModal();
    });

    // ========================================
    // メンバー選択モーダル
    // ========================================
    $('#member-modal-search').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.member-modal-item').each(function() {
            var name = $(this).data('name').toString().toLowerCase();
            if (name.indexOf(query) !== -1) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    });

    $(document).on('click', '.member-modal-item', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        if (window.memberModalMultiSelect) {
            // 複数選択モード
            $(this).toggleClass('selected');
            var idx = window.memberModalSelectedIds.indexOf(name);
            if (idx !== -1) {
                window.memberModalSelectedIds.splice(idx, 1);
            } else {
                window.memberModalSelectedIds.push(name);
            }
            if (window.memberModalCallback) {
                window.memberModalCallback({
                    id: id,
                    name: name,
                    action: $(this).hasClass('selected') ? 'add' : 'remove'
                });
            }
        } else {
            // 単一選択モード
            if (window.memberModalCallback) {
                window.memberModalCallback({
                    id: id,
                    name: name
                });
            }
            closeMemberModal();
        }
    });

    // ESCキーでモーダルを閉じる
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            if ($('#customer-selector-modal').hasClass('show')) {
                closeCustomerModal();
            }
            if ($('#member-selector-modal').hasClass('show')) {
                closeMemberModal();
            }
        }
    });

    // ========================================
    // 顧客選択（サジェスト＋モーダル）
    // ========================================
    var $customerSearchInput = $('#customer-search-input');
    var $customerSuggestDropdown = $('#customer-suggest-dropdown');
    var $customerSuggestItems = $customerSuggestDropdown.find('.customer-suggest-item');
    var $customerIdInput = $('#customer_id');
    var $customerSelectedInfo = $('#selected-customer-info');
    var customerHighlightedIndex = -1;

    function selectCustomer(id, name, statusLabel) {
        $customerIdInput.val(id);
        $customerSearchInput.val(name);
        $('#selected-customer-label').text(name);
        $('#selected-customer-status-label').text('(' + statusLabel + ')');
        $customerSelectedInfo.removeClass('hidden');
        $customerSuggestDropdown.removeClass('show');
    }

    function clearCustomer() {
        $customerIdInput.val('');
        $customerSearchInput.val('');
        $customerSelectedInfo.addClass('hidden');
    }

    $customerSearchInput.on('input', function() {
        var query = $(this).val().toLowerCase();
        var hasMatch = false;
        $customerSuggestItems.each(function() {
            var name = $(this).data('name').toString().toLowerCase();
            if (name.indexOf(query) !== -1) {
                $(this).show();
                hasMatch = true;
            } else {
                $(this).hide();
            }
        });
        if (query.length > 0 && hasMatch) {
            $customerSuggestDropdown.addClass('show');
        } else if (query.length === 0) {
            $customerSuggestItems.show();
            $customerSuggestDropdown.addClass('show');
        } else {
            $customerSuggestDropdown.removeClass('show');
        }
        customerHighlightedIndex = -1;
    });

    $customerSearchInput.on('focus', function() {
        $customerSuggestItems.show();
        $customerSuggestDropdown.addClass('show');
        hideHeaderForDropdown();
    });

    $customerSearchInput.on('keydown', function(e) {
        var visibleItems = $customerSuggestItems.filter(':visible');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            customerHighlightedIndex = Math.min(customerHighlightedIndex + 1, visibleItems.length - 1);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(customerHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            customerHighlightedIndex = Math.max(customerHighlightedIndex - 1, 0);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(customerHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (customerHighlightedIndex >= 0 && visibleItems.length > 0) {
                visibleItems.eq(customerHighlightedIndex).click();
            }
        } else if (e.key === 'Escape') {
            $customerSuggestDropdown.removeClass('show');
        }
    });

    $customerSuggestItems.on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var statusLabel = $(this).data('status-label');
        selectCustomer(id, name, statusLabel);
    });

    $('#clear-customer-btn').on('click', function() {
        clearCustomer();
        $customerSearchInput.focus();
    });

    $('#open-customer-modal-btn').on('click', function() {
        openCustomerModal(function(customer) {
            selectCustomer(customer.id, customer.name, customer.statusLabel);
        });
    });

    // 外部クリックでドロップダウンを閉じてヘッダーを復帰
    $(document).on('click', function(e) {
        var clickedInDropdown = $(e.target).closest('#customer-selector, #leader-selector, #member-selector').length > 0;
        if (!clickedInDropdown) {
            $customerSuggestDropdown.removeClass('show');
            $('#leader-suggest-dropdown').removeClass('show');
            $('#member-suggest-dropdown').removeClass('show');
            showHeaderAfterDropdown();
        }
    });

    // ========================================
    // プロジェクトリーダー選択（サジェスト＋モーダル）
    // ========================================
    var $leaderSearchInput = $('#leader-search-input');
    var $leaderSuggestDropdown = $('#leader-suggest-dropdown');
    var $leaderSuggestItems = $leaderSuggestDropdown.find('.leader-suggest-item');
    var $leaderInput = $('#project_leader');
    var $leaderSelectedInfo = $('#selected-leader-info');
    var leaderHighlightedIndex = -1;

    function selectLeader(name) {
        $leaderInput.val(name);
        $leaderSearchInput.val(name);
        $('#selected-leader-label').text(name);
        $leaderSelectedInfo.removeClass('hidden');
        $leaderSuggestDropdown.removeClass('show');
    }

    function clearLeader() {
        $leaderInput.val('');
        $leaderSearchInput.val('');
        $leaderSelectedInfo.addClass('hidden');
    }

    $leaderSearchInput.on('input', function() {
        var query = $(this).val().toLowerCase();
        var hasMatch = false;
        $leaderSuggestItems.each(function() {
            var name = $(this).data('name').toString().toLowerCase();
            if (name.indexOf(query) !== -1) {
                $(this).show();
                hasMatch = true;
            } else {
                $(this).hide();
            }
        });
        if (query.length > 0 && hasMatch) {
            $leaderSuggestDropdown.addClass('show');
        } else if (query.length === 0) {
            $leaderSuggestItems.show();
            $leaderSuggestDropdown.addClass('show');
        } else {
            $leaderSuggestDropdown.removeClass('show');
        }
        leaderHighlightedIndex = -1;
    });

    $leaderSearchInput.on('focus', function() {
        $leaderSuggestItems.show();
        $leaderSuggestDropdown.addClass('show');
        hideHeaderForDropdown();
    });

    $leaderSearchInput.on('keydown', function(e) {
        var visibleItems = $leaderSuggestItems.filter(':visible');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            leaderHighlightedIndex = Math.min(leaderHighlightedIndex + 1, visibleItems.length - 1);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(leaderHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            leaderHighlightedIndex = Math.max(leaderHighlightedIndex - 1, 0);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(leaderHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (leaderHighlightedIndex >= 0 && visibleItems.length > 0) {
                visibleItems.eq(leaderHighlightedIndex).click();
            }
        } else if (e.key === 'Escape') {
            $leaderSuggestDropdown.removeClass('show');
        }
    });

    $leaderSuggestItems.on('click', function() {
        var name = $(this).data('name');
        selectLeader(name);
    });

    $('#clear-leader-btn').on('click', function() {
        clearLeader();
        $leaderSearchInput.focus();
    });

    $('#open-leader-modal-btn').on('click', function() {
        openMemberModal(function(member) {
            selectLeader(member.name);
        }, {
            title: 'プロジェクトリーダーを選択',
            multiSelect: false
        });
    });

    // ========================================
    // メンバー選択（サジェスト＋モーダル）
    // ========================================
    var $memberSearchInput = $('#member-search-input');
    var $memberSuggestDropdown = $('#member-suggest-dropdown');
    var $memberSuggestItems = $memberSuggestDropdown.find('.member-suggest-item');
    var memberHighlightedIndex = -1;

    function addMember(name) {
        // 重複チェック
        var exists = false;
        $('#members-container input[name="members[]"]').each(function() {
            if ($(this).val() === name) {
                exists = true;
                return false;
            }
        });

        if (!exists) {
            var tag = $('<span class="member-tag inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">' +
                '<i class="fas fa-user text-purple-500 mr-1.5 text-xs"></i>' +
                '<span>' + escapeHtml(name) + '</span>' +
                '<button type="button" class="ml-2 text-purple-500 hover:text-purple-700 remove-member">' +
                '<i class="fas fa-times text-xs"></i>' +
                '</button>' +
                '<input type="hidden" name="members[]" value="' + escapeHtml(name) + '">' +
                '</span>');
            $('#members-container').append(tag);
        }
        $memberSearchInput.val('');
        $memberSuggestDropdown.removeClass('show');
    }

    function removeMember(name) {
        $('#members-container input[name="members[]"]').each(function() {
            if ($(this).val() === name) {
                $(this).closest('.member-tag').remove();
                return false;
            }
        });
    }

    function getSelectedMemberNames() {
        var names = [];
        $('#members-container input[name="members[]"]').each(function() {
            names.push($(this).val());
        });
        return names;
    }

    $memberSearchInput.on('input', function() {
        var query = $(this).val().toLowerCase();
        var hasMatch = false;
        $memberSuggestItems.each(function() {
            var name = $(this).data('name').toString().toLowerCase();
            if (name.indexOf(query) !== -1) {
                $(this).show();
                hasMatch = true;
            } else {
                $(this).hide();
            }
        });
        if (query.length > 0 && hasMatch) {
            $memberSuggestDropdown.addClass('show');
        } else if (query.length === 0) {
            $memberSuggestItems.show();
            $memberSuggestDropdown.addClass('show');
        } else {
            $memberSuggestDropdown.removeClass('show');
        }
        memberHighlightedIndex = -1;
    });

    $memberSearchInput.on('focus', function() {
        $memberSuggestItems.show();
        $memberSuggestDropdown.addClass('show');
        hideHeaderForDropdown();
    });

    $memberSearchInput.on('keydown', function(e) {
        var visibleItems = $memberSuggestItems.filter(':visible');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            memberHighlightedIndex = Math.min(memberHighlightedIndex + 1, visibleItems.length - 1);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(memberHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            memberHighlightedIndex = Math.max(memberHighlightedIndex - 1, 0);
            visibleItems.removeClass('highlighted');
            visibleItems.eq(memberHighlightedIndex).addClass('highlighted');
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (memberHighlightedIndex >= 0 && visibleItems.length > 0) {
                visibleItems.eq(memberHighlightedIndex).click();
            }
        } else if (e.key === 'Escape') {
            $memberSuggestDropdown.removeClass('show');
        }
    });

    $memberSuggestItems.on('click', function() {
        var name = $(this).data('name');
        addMember(name);
    });

    $('#open-member-modal-btn').on('click', function() {
        openMemberModal(function(member) {
            if (member.action === 'add') {
                addMember(member.name);
            } else if (member.action === 'remove') {
                removeMember(member.name);
            }
        }, {
            title: 'メンバーを選択（複数可）',
            multiSelect: true,
            selectedIds: getSelectedMemberNames()
        });
    });

    // メンバー削除
    $(document).on('click', '.remove-member', function() {
        $(this).closest('.member-tag').remove();
    });

    // エラーがあるフィールドにフォーカス
    var firstError = $('.field-error').first();
    if (firstError.length) {
        firstError.focus();
    }
});
} // end of initWhenReady

// DOMContentLoaded後にinitWhenReadyを呼び出す
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWhenReady);
} else {
    initWhenReady();
}
})(); // IIFE終了
</script>
<?= $this->endSection() ?>
