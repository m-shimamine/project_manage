<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
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
</style>
<?= $this->endSection() ?>

<?php
// 編集モードかどうか判定
$isEdit = isset($member) && !empty($member['id']);
$formAction = $isEdit ? base_url('members/' . $member['id']) : base_url('members');
$pageLabel = $isEdit ? '編集' : '新規登録';

// フィールド別エラーを取得
$fieldErrors = session()->getFlashdata('field_errors') ?? [];

// デフォルト値の設定
$defaults = [
    'id'         => $member['id'] ?? '',
    'name'       => old('name', $member['name'] ?? ''),
    'email'      => old('email', $member['email'] ?? ''),
    'phone'      => old('phone', $member['phone'] ?? ''),
    'department' => old('department', $member['department'] ?? ''),
    'position'   => old('position', $member['position'] ?? ''),
    'role'       => old('role', $member['role'] ?? 'member'),
    'is_active'  => old('is_active', $member['is_active'] ?? 1),
];
?>

<?= $this->section('content') ?>
<!-- スティッキーヘッダー -->
<div id="sticky-header" class="sticky top-0 z-[100] bg-white border-b border-slate-200 shadow-sm">
    <div class="px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- パンくずリスト -->
            <nav class="flex items-center space-x-2 text-sm">
                <a href="<?= base_url('members') ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-users mr-1"></i>メンバー一覧
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php if ($isEdit): ?>
                <a href="<?= base_url('members/' . $defaults['id']) ?>" class="text-slate-500 hover:text-blue-600 transition-colors">
                    メンバー詳細
                </a>
                <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                <?php endif; ?>
                <span class="text-slate-700 font-medium"><?= $pageLabel ?></span>
            </nav>
            <!-- 保存ボタン -->
            <button type="submit" form="member-form" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:from-blue-600 hover:to-indigo-700 transition-all">
                <i class="fas fa-save mr-2"></i>
                <span class="hidden sm:inline">保存する</span>
                <span class="sm:hidden">保存</span>
            </button>
        </div>
    </div>
</div>

<div class="p-4 sm:p-6">
    <!-- フォーム -->
    <form method="POST" action="<?= $formAction ?>" id="member-form">
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
                            <i class="fas fa-user text-blue-500 mr-2"></i>基本情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">氏名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="field-name" value="<?= esc($defaults['name']) ?>" placeholder="例: 山田 太郎"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['name']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['name'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['name']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 連絡先情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-address-card text-emerald-500 mr-2"></i>連絡先情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">メールアドレス <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="field-email" value="<?= esc($defaults['email']) ?>" placeholder="example@company.com"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['email']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['email'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">電話番号</label>
                            <input type="tel" name="phone" id="field-phone" value="<?= esc($defaults['phone']) ?>" placeholder="090-1234-5678"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['phone']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['phone'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['phone']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 所属情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-building text-violet-500 mr-2"></i>所属情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">部署</label>
                            <select name="department" id="field-department" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white <?= isset($fieldErrors['department']) ? 'field-error' : '' ?>">
                                <option value="">選択してください</option>
                                <?php foreach ($departments as $key => $label): ?>
                                <option value="<?= esc($key) ?>" <?= $defaults['department'] === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($fieldErrors['department'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['department']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">役職</label>
                            <input type="text" name="position" id="field-position" value="<?= esc($defaults['position']) ?>" placeholder="例: エンジニア"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['position']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['position'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['position']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 右カラム -->
            <div class="space-y-6">
                <!-- 権限設定 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-shield-alt text-indigo-500 mr-2"></i>権限設定
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">権限 <span class="text-red-500">*</span></label>
                            <select name="role" id="field-role" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all bg-white <?= isset($fieldErrors['role']) ? 'field-error' : '' ?>">
                                <option value="">選択してください</option>
                                <option value="admin" <?= $defaults['role'] === 'admin' ? 'selected' : '' ?>>管理者 - すべての機能が使用可能</option>
                                <option value="leader" <?= $defaults['role'] === 'leader' ? 'selected' : '' ?>>リーダー - 担当範囲の参照・編集が可能</option>
                                <option value="member" <?= $defaults['role'] === 'member' ? 'selected' : '' ?>>メンバー - タスクの参照・編集、他は参照のみ</option>
                            </select>
                            <?php if (isset($fieldErrors['role'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['role']) ?></div>
                            <?php endif; ?>
                        </div>
                        <!-- リーダーの場合の担当顧客選択 -->
                        <div id="leader-options" class="hidden">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">担当顧客（リーダーの場合）</label>
                            <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                                <?php if (isset($customers) && !empty($customers)): ?>
                                    <?php foreach ($customers as $customer): ?>
                                    <label class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" name="customers[]" value="<?= esc($customer['id']) ?>" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-slate-700"><?= esc($customer['name']) ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-slate-500">顧客が登録されていません</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ログイン情報 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-key text-amber-500 mr-2"></i>ログイン情報
                        </h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                パスワード <?php if (!$isEdit): ?><span class="text-red-500">*</span><?php endif; ?>
                            </label>
                            <input type="password" name="password" id="field-password" placeholder="8文字以上"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['password']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['password'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                パスワード（確認） <?php if (!$isEdit): ?><span class="text-red-500">*</span><?php endif; ?>
                            </label>
                            <input type="password" name="password_confirm" id="field-password_confirm" placeholder="再入力"
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all <?= isset($fieldErrors['password_confirm']) ? 'field-error' : '' ?>">
                            <?php if (isset($fieldErrors['password_confirm'])): ?>
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i><?= esc($fieldErrors['password_confirm']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($isEdit): ?>
                        <p class="text-xs text-slate-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>パスワードを変更する場合のみ入力してください
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ステータス -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
                        <h4 class="text-base font-bold text-slate-700 flex items-center">
                            <i class="fas fa-toggle-on text-emerald-500 mr-2"></i>ステータス
                        </h4>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer flex-1">
                                <input type="radio" name="is_active" value="1" <?= $defaults['is_active'] ? 'checked' : '' ?> class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                <span class="ml-3 text-sm text-slate-700 flex items-center">
                                    <i class="fas fa-check-circle text-emerald-500 mr-1.5"></i>有効
                                </span>
                            </label>
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer flex-1">
                                <input type="radio" name="is_active" value="0" <?= !$defaults['is_active'] ? 'checked' : '' ?> class="w-4 h-4 text-slate-600 focus:ring-slate-500">
                                <span class="ml-3 text-sm text-slate-700 flex items-center">
                                    <i class="fas fa-times-circle text-slate-400 mr-1.5"></i>無効
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ボタン（モバイル用） -->
        <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-slate-200 sm:hidden">
            <a href="<?= $isEdit ? base_url('members/' . $defaults['id']) : base_url('members') ?>" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
                キャンセル
            </a>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-sm font-medium text-white flex items-center">
                <i class="fas fa-save mr-2"></i>保存する
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // 権限選択時のリーダーオプション表示切替
    const roleSelect = document.getElementById('field-role');
    const leaderOptions = document.getElementById('leader-options');

    function toggleLeaderOptions() {
        if (roleSelect.value === 'leader') {
            leaderOptions.classList.remove('hidden');
        } else {
            leaderOptions.classList.add('hidden');
        }
    }

    roleSelect.addEventListener('change', toggleLeaderOptions);
    // 初期表示時にも実行
    toggleLeaderOptions();

    // エラーがあるフィールドにフォーカス
    var firstError = $('.field-error').first();
    if (firstError.length) {
        firstError.focus();
    }
});
</script>
<?= $this->endSection() ?>
