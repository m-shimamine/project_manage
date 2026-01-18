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
$isEdit = isset($customer) && !empty($customer['id']);
$formAction = $isEdit ? base_url('customers/' . $customer['id']) : base_url('customers');
$pageLabel = $isEdit ? '編集' : '新規登録';

// フィールド別エラーを取得
$fieldErrors = session()->getFlashdata('field_errors') ?? [];

// デフォルト値の設定
$defaults = [
    'id'             => $customer['id'] ?? '',
    'name'           => old('name', $customer['name'] ?? ''),
    'status'         => old('status', $customer['status'] ?? 'active'),
    'postal_code'    => old('postal_code', $customer['postal_code'] ?? ''),
    'city'           => old('city', $customer['city'] ?? ''),
    'address'        => old('address', $customer['address'] ?? ''),
    'project_leader' => old('project_leader', $customer['project_leader'] ?? ''),
    'members'        => old('members', $customer['members'] ?? []),
    'contacts'       => old('contacts', $customer['contacts'] ?? []),
    'notes'          => old('notes', $customer['notes'] ?? ''),
];
?>

<?= $this->section('content') ?>
<!-- スティッキーヘッダー -->
<div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
    <div class="px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- パンくずリスト -->
            <nav class="flex items-center space-x-2 text-sm">
                <a href="<?= base_url('customers') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-building mr-1"></i>顧客一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php if ($isEdit): ?>
                <a href="<?= base_url('customers/' . $defaults['id']) ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    顧客詳細
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php endif; ?>
                <span class="text-slate-700 font-medium"><?= $pageLabel ?></span>
            </nav>
            <!-- 保存ボタン -->
            <button type="submit" form="customer-form" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:from-blue-600 hover:to-indigo-700 transition-all">
                <i class="fas fa-save mr-2"></i>
                <span class="hidden sm:inline">保存する</span>
                <span class="sm:hidden">保存</span>
            </button>
        </div>
    </div>
</div>

<div class="p-4 sm:p-6">
    <!-- フォーム -->
    <form method="POST" action="<?= $formAction ?>" id="customer-form">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <!-- 2列レイアウト -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 左カラム -->
            <div class="space-y-6">
                <!-- 基本情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-building text-blue-500 mr-2"></i>会社情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">会社名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="field-name" value="<?= esc($defaults['name']) ?>" placeholder="例: 株式会社サンプル"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['name']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['name'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">ステータス <span class="text-red-500">*</span></label>
                            <select name="status" id="field-status" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white <?= isset($fieldErrors['status']) ? 'field-error' : '' ?>">
                                <option value="active" <?= $defaults['status'] === 'active' ? 'selected' : '' ?>>進行中</option>
                                <option value="maintenance" <?= $defaults['status'] === 'maintenance' ? 'selected' : '' ?>>保守</option>
                                <option value="inactive" <?= $defaults['status'] === 'inactive' ? 'selected' : '' ?>>取引停止</option>
                            </select>
                            <?php if (isset($fieldErrors['status'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['status']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 住所 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-map-marker-alt text-violet-500 mr-2"></i>住所
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">郵便番号</label>
                            <input type="text" name="postal_code" id="field-postal_code" value="<?= esc($defaults['postal_code']) ?>" placeholder="123-4567"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['postal_code']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['postal_code'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['postal_code']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">都道府県・市区町村</label>
                            <input type="text" name="city" id="field-city" value="<?= esc($defaults['city']) ?>" placeholder="東京都渋谷区"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['city']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['city'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['city']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">番地・建物名</label>
                            <input type="text" name="address" id="field-address" value="<?= esc($defaults['address']) ?>" placeholder="1-2-3 サンプルビル5F"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['address']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['address'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['address']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 保守担当情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-tools text-indigo-500 mr-2"></i>保守担当
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- 主担当選択（サジェスト＋モーダル） -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">主担当</label>
                            <div id="leader-selector">
                                <div class="flex">
                                    <div class="relative flex-1">
                                        <input type="text" id="leader-search-input"
                                            value="<?= esc($defaults['project_leader']) ?>"
                                            placeholder="主担当者名を入力して検索..."
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

                        <!-- メンバー選択（サジェスト＋モーダル） -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">メンバー（複数選択可）</label>
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
                            <!-- 選択済みメンバー -->
                            <div id="members-container" class="flex flex-wrap gap-2 mt-3">
                                <?php foreach ($defaults['members'] as $member): ?>
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
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-address-book text-emerald-500 mr-2"></i>担当者情報
                        </h4>
                    </div>
                    <div class="p-6">
                        <!-- 担当者リスト -->
                        <div id="contacts-container" class="space-y-4 mb-4">
                            <?php
                            $contacts = !empty($defaults['contacts']) ? $defaults['contacts'] : [['name' => '', 'phone' => '', 'email' => '']];
                            foreach ($contacts as $index => $contact):
                                $contactName = is_array($contact) ? ($contact['name'] ?? '') : $contact;
                                $contactPhone = is_array($contact) ? ($contact['phone'] ?? '') : '';
                                $contactEmail = is_array($contact) ? ($contact['email'] ?? '') : '';
                            ?>
                            <div class="contact-item bg-slate-50 rounded-xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-slate-700 flex items-center">
                                        <i class="fas fa-user text-emerald-500 mr-2"></i>担当者 <span class="contact-number ml-1"><?= $index + 1 ?></span>
                                    </span>
                                    <button type="button" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors remove-contact">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">担当者名</label>
                                        <input type="text" name="contacts[<?= $index ?>][name]" value="<?= esc($contactName) ?>" placeholder="山田 太郎"
                                            class="contact-name w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">電話番号</label>
                                        <input type="tel" name="contacts[<?= $index ?>][phone]" value="<?= esc($contactPhone) ?>" placeholder="03-1234-5678"
                                            class="contact-phone w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">メールアドレス</label>
                                        <input type="email" name="contacts[<?= $index ?>][email]" value="<?= esc($contactEmail) ?>" placeholder="yamada@example.com"
                                            class="contact-email w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- 追加ボタン -->
                        <button type="button" id="add-contact-btn"
                            class="w-full px-4 py-3 border-2 border-dashed border-slate-300 rounded-xl text-sm font-medium text-slate-600 hover:border-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>担当者を追加
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ボタン（モバイル用） -->
        <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-slate-200 sm:hidden">
            <a href="<?= $isEdit ? base_url('customers/' . $defaults['id']) : base_url('customers') ?>" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                キャンセル
            </a>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-sm font-medium text-white flex items-center">
                <i class="fas fa-save mr-2"></i>保存する
            </button>
        </div>
    </form>
</div>

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
    // HTMLエスケープ
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

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
            if ($('#member-selector-modal').hasClass('show')) {
                closeMemberModal();
            }
        }
    });

    // ========================================
    // 主担当選択（サジェスト＋モーダル）
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
            title: '主担当を選択',
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

    // 外部クリックでドロップダウンを閉じる
    $(document).on('click', function(e) {
        var clickedInDropdown = $(e.target).closest('#leader-selector, #member-selector').length > 0;
        if (!clickedInDropdown) {
            $leaderSuggestDropdown.removeClass('show');
            $memberSuggestDropdown.removeClass('show');
        }
    });

    // 担当者追加
    $('#add-contact-btn').on('click', function() {
        var index = $('.contact-item').length;
        var template = `
            <div class="contact-item bg-slate-50 rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-slate-700 flex items-center">
                        <i class="fas fa-user text-emerald-500 mr-2"></i>担当者 <span class="contact-number ml-1">${index + 1}</span>
                    </span>
                    <button type="button" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors remove-contact">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">担当者名</label>
                        <input type="text" name="contacts[${index}][name]" placeholder="山田 太郎"
                            class="contact-name w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">電話番号</label>
                        <input type="tel" name="contacts[${index}][phone]" placeholder="03-1234-5678"
                            class="contact-phone w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">メールアドレス</label>
                        <input type="email" name="contacts[${index}][email]" placeholder="yamada@example.com"
                            class="contact-email w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white">
                    </div>
                </div>
            </div>
        `;
        $('#contacts-container').append(template);
    });

    // 担当者削除
    $(document).on('click', '.remove-contact', function() {
        var container = $('#contacts-container');
        if (container.find('.contact-item').length > 1) {
            $(this).closest('.contact-item').remove();
            // インデックス番号を振り直す
            updateContactIndices();
        } else {
            // 最後の1つは入力内容をクリアするだけ
            var item = $(this).closest('.contact-item');
            item.find('input').val('');
        }
    });

    // 担当者のインデックス番号更新
    function updateContactIndices() {
        $('.contact-item').each(function(index) {
            $(this).find('.contact-number').text(index + 1);
            $(this).find('.contact-name').attr('name', 'contacts[' + index + '][name]');
            $(this).find('.contact-phone').attr('name', 'contacts[' + index + '][phone]');
            $(this).find('.contact-email').attr('name', 'contacts[' + index + '][email]');
        });
    }

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
