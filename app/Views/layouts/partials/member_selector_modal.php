<!-- メンバー選択モーダル -->
<div id="member-selector-modal" class="fixed inset-0 z-50 hidden">
    <!-- オーバーレイ -->
    <div class="member-modal-overlay absolute inset-0 bg-black/50" onclick="closeMemberModal()"></div>

    <!-- モーダル本体 -->
    <div class="absolute inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:w-full sm:max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
        <!-- ヘッダー -->
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                    <i class="fas fa-users text-purple-500 mr-2"></i><span id="member-modal-title">メンバーを選択</span>
                </h3>
                <button type="button" onclick="closeMemberModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- 検索 -->
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="member-modal-search" placeholder="メンバー名で検索..."
                    class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm transition-all"
                    autocomplete="off">
            </div>
        </div>

        <!-- メンバーリスト -->
        <div class="flex-1 overflow-y-auto p-2" id="member-modal-list">
            <?php foreach ($members as $member): ?>
            <div class="member-modal-item p-3 hover:bg-slate-50 rounded-xl cursor-pointer transition-all flex items-center justify-between group"
                 data-id="<?= esc($member['id']) ?>"
                 data-name="<?= esc($member['name']) ?>">
                <div class="flex items-center min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                        <?= mb_substr($member['name'], 0, 1) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-medium text-slate-800 truncate"><?= esc($member['name']) ?></div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="member-check-icon hidden text-purple-500"><i class="fas fa-check-circle"></i></span>
                    <i class="fas fa-chevron-right text-slate-300 group-hover:text-purple-500 transition-colors"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- フッター -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeMemberModal()" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-white transition-colors">
                閉じる
            </button>
        </div>
    </div>
</div>

<style>
#member-selector-modal.show {
    display: block;
    animation: fadeIn 0.2s ease;
}
#member-selector-modal .member-modal-item.hidden {
    display: none;
}
#member-selector-modal .member-modal-item.selected {
    background: #f3e8ff;
}
#member-selector-modal .member-modal-item.selected .member-check-icon {
    display: block;
}
</style>
