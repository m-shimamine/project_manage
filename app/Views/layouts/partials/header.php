<!-- ヘッダー -->
<header class="bg-white/80 backdrop-blur-lg card-shadow border-b border-slate-200/50 relative z-50">
    <div class="flex items-center justify-between px-3 sm:px-6 py-2 sm:py-3">
        <div class="flex items-center space-x-2 sm:space-x-4 min-w-0">
            <!-- ハンバーガーメニュー + PM System ロゴ -->
            <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center shadow-sm hover:bg-slate-50 transition-all cursor-pointer" id="menu-toggle">
                    <i class="fas fa-bars text-slate-600 text-xs sm:text-sm"></i>
                </div>
                <a href="<?= base_url('dashboard') ?>" class="text-base sm:text-lg font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent hover:from-blue-600 hover:to-indigo-600 transition-all hidden sm:block">PM System</a>
            </div>
            <div class="w-px h-5 sm:h-6 bg-slate-300 hidden sm:block"></div>
            <!-- ページタイトル -->
            <div class="flex items-center space-x-1.5 sm:space-x-2 min-w-0">
                <?php if (!empty($headerIcon)): ?>
                    <div class="flex-shrink-0"><?= $headerIcon ?></div>
                <?php else: ?>
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fas fa-home text-white text-[10px] sm:text-xs"></i>
                    </div>
                <?php endif; ?>
                <h2 class="text-sm sm:text-lg font-semibold text-slate-700 truncate"><?= esc($pageTitle ?? 'ダッシュボード') ?></h2>
            </div>
        </div>
        <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
            <!-- 日付（PCのみ） -->
            <div class="hidden md:flex items-center space-x-2 px-2 sm:px-3 py-1.5 bg-white rounded-lg border border-slate-200 shadow-sm">
                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-alt text-white text-xs"></i>
                </div>
                <span class="text-sm font-medium text-slate-700"><?= date('Y年n月j日') ?></span>
            </div>
            <!-- ユーザーメニュー -->
            <div class="user-menu-container">
                <div id="user-menu-trigger" class="user-menu-trigger flex items-center space-x-1.5 sm:space-x-2 px-2 sm:px-3 py-1.5 bg-white rounded-lg border border-slate-200 shadow-sm cursor-pointer">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-xs"></i>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-slate-700 hidden sm:block max-w-[100px] truncate"><?= esc(session()->get('user_name') ?? 'ゲスト') ?></span>
                    <i class="fas fa-chevron-down text-slate-400 text-[10px] sm:text-xs" id="user-menu-arrow"></i>
                </div>
                <div id="user-menu-dropdown" class="user-menu-dropdown">
                    <div class="user-menu-header">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 truncate"><?= esc(session()->get('user_name') ?? 'ゲスト') ?></div>
                                <div class="text-xs text-slate-500 truncate"><?= esc(session()->get('user_email') ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="user-menu-item" onclick="location.href='#'">
                        <i class="fas fa-user"></i>
                        <span>プロフィール</span>
                    </div>
                    <div class="user-menu-item" onclick="location.href='<?= base_url('settings') ?>'">
                        <i class="fas fa-cog"></i>
                        <span>設定</span>
                    </div>
                    <div class="user-menu-item logout" onclick="location.href='<?= base_url('logout') ?>'">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>ログアウト</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
