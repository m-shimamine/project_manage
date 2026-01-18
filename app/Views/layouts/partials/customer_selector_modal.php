<!-- 顧客選択モーダル -->
<div id="customer-selector-modal" class="fixed inset-0 z-50 hidden">
    <!-- オーバーレイ -->
    <div class="customer-modal-overlay absolute inset-0 bg-black/50" onclick="closeCustomerModal()"></div>

    <!-- モーダル本体 -->
    <div class="absolute inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-full sm:max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
        <!-- ヘッダー -->
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                    <i class="fas fa-building text-emerald-500 mr-2"></i>顧客を選択
                </h3>
                <button type="button" onclick="closeCustomerModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- 検索 -->
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="customer-modal-search" placeholder="顧客名で検索..."
                    class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                    autocomplete="off">
            </div>
        </div>

        <!-- 顧客リスト -->
        <div class="flex-1 overflow-y-auto p-2" id="customer-modal-list">
            <?php
            $statusLabels = [
                'active' => '進行中',
                'maintenance' => '保守',
                'inactive' => '取引停止'
            ];
            $statusColors = [
                'active' => 'bg-emerald-100 text-emerald-700',
                'maintenance' => 'bg-blue-100 text-blue-700',
                'inactive' => 'bg-slate-100 text-slate-600'
            ];
            ?>
            <?php foreach ($customers as $customer): ?>
            <div class="customer-modal-item p-3 hover:bg-slate-50 rounded-xl cursor-pointer transition-all flex items-center justify-between group"
                 data-id="<?= esc($customer['id']) ?>"
                 data-name="<?= esc($customer['name']) ?>"
                 data-status="<?= esc($customer['status']) ?>"
                 data-status-label="<?= esc($statusLabels[$customer['status']] ?? $customer['status']) ?>">
                <div class="flex items-center min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                        <?= mb_substr($customer['name'], 0, 1) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-medium text-slate-800 truncate"><?= esc($customer['name']) ?></div>
                        <div class="text-xs text-slate-500"><?= esc($customer['city'] ?? '') ?></div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusColors[$customer['status']] ?? 'bg-slate-100 text-slate-600' ?>">
                        <?= esc($statusLabels[$customer['status']] ?? $customer['status']) ?>
                    </span>
                    <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- フッター -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeCustomerModal()" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-white transition-colors">
                キャンセル
            </button>
        </div>
    </div>
</div>

<style>
#customer-selector-modal.show {
    display: block;
    animation: fadeIn 0.2s ease;
}
#customer-selector-modal .customer-modal-item.hidden {
    display: none;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

