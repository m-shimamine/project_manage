<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .customer-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .customer-card:hover {
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- サブヘッダー -->
<div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <!-- 検索 -->
            <form method="GET" action="<?= base_url('customers') ?>" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="会社名で検索..."
                           class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-72 text-sm shadow-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                </div>
                <!-- ステータスフィルター -->
                <select name="status" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-4 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">すべてのステータス</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>進行中</option>
                    <option value="maintenance" <?= ($status ?? '') === 'maintenance' ? 'selected' : '' ?>>保守</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>取引停止</option>
                </select>
            </form>
        </div>
        <!-- 新規追加ボタン -->
        <a href="<?= base_url('customers/create') ?>" class="btn-stylish inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            <span>新規追加</span>
        </a>
    </div>
</div>

<!-- コンテンツ -->
<div class="p-4 sm:p-6">
    <!-- 成功メッセージ -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
    <?php endif; ?>

    <!-- 統計カード -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 fade-in">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-building text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">総顧客数</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['total']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-play-circle text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">進行中</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['active']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-tools text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">保守</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['maintenance']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-pause-circle text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">取引停止</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['inactive']) ?>件</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 顧客カード一覧 -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 fade-in">
        <?php foreach ($customers as $customer): ?>
        <?php
            $color = \App\Models\CustomerModel::getColor($customer['name']);
            $initial = \App\Models\CustomerModel::getInitial($customer['name']);
            $statusLabel = \App\Models\CustomerModel::getStatusLabel($customer['status']);
            $statusColor = \App\Models\CustomerModel::getStatusColor($customer['status']);
        ?>
        <div class="customer-card bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r <?= esc($color) ?>"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center min-w-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-3 sm:mr-4 flex-shrink-0">
                            <span class="text-white font-bold text-lg sm:text-xl"><?= esc($initial) ?></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 truncate"><?= esc($customer['name']) ?></h3>
                            <?php if (!empty($customer['project_leader'])): ?>
                            <p class="text-sm text-slate-500">PL: <?= esc($customer['project_leader']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full flex-shrink-0 ml-2"><?= esc($statusLabel) ?></span>
                </div>

                <?php if (!empty($customer['city']) || !empty($customer['address'])): ?>
                <div class="mb-3 text-sm text-slate-600">
                    <i class="fas fa-map-marker-alt text-slate-400 w-5 mr-1"></i>
                    <?= esc(($customer['city'] ?? '') . ' ' . ($customer['address'] ?? '')) ?>
                </div>
                <?php endif; ?>

                <div class="pt-4 border-t border-slate-100">
                    <a href="<?= base_url('customers/' . $customer['id']) ?>" class="flex items-center justify-center w-full py-2.5 bg-slate-50 hover:bg-blue-500 hover:text-white rounded-lg text-sm font-semibold text-slate-700 transition-all">
                        <i class="fas fa-eye mr-2"></i>
                        詳細
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 顧客がない場合 -->
    <?php if (empty($customers)): ?>
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-building text-3xl text-slate-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">顧客が登録されていません</h3>
        <p class="text-slate-500 mb-6">新規追加ボタンから顧客を登録してください</p>
        <a href="<?= base_url('customers/create') ?>" class="btn-stylish inline-flex items-center px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            新規追加
        </a>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
