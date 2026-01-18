<!-- サイドバーモーダル -->
<aside class="sidebar-modal" id="sidebar-modal">
    <div class="p-5 border-b border-slate-700/50">
        <a href="<?= base_url('dashboard') ?>" class="flex items-center">
            <i class="fas fa-project-diagram text-blue-400 mr-3 text-xl"></i>
            <span class="text-xl font-bold text-white">PM System</span>
        </a>
    </div>
    <nav class="mt-4 px-2">
        <a href="<?= base_url('dashboard') ?>" class="nav-item <?= uri_string() === 'dashboard' || uri_string() === '' ? 'active' : '' ?> flex items-center px-4 py-3 <?= uri_string() === 'dashboard' || uri_string() === '' ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-tachometer-alt w-6 mr-3 <?= uri_string() === 'dashboard' || uri_string() === '' ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>ダッシュボード</span>
        </a>
        <a href="<?= base_url('customers') ?>" class="nav-item <?= str_starts_with(uri_string(), 'customers') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_starts_with(uri_string(), 'customers') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-building w-6 mr-3 <?= str_starts_with(uri_string(), 'customers') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>顧客</span>
        </a>
        <a href="<?= base_url('projects') ?>" class="nav-item <?= str_starts_with(uri_string(), 'projects') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_starts_with(uri_string(), 'projects') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-folder w-6 mr-3 <?= str_starts_with(uri_string(), 'projects') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>プロジェクト</span>
        </a>
        <a href="<?= base_url('schedule') ?>" class="nav-item <?= str_starts_with(uri_string(), 'schedule') && !str_contains(uri_string(), 'tasks') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_starts_with(uri_string(), 'schedule') && !str_contains(uri_string(), 'tasks') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-calendar-alt w-6 mr-3 <?= str_starts_with(uri_string(), 'schedule') && !str_contains(uri_string(), 'tasks') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>スケジュール</span>
        </a>
        <a href="<?= base_url('schedule/tasks') ?>" class="nav-item <?= str_contains(uri_string(), 'schedule/tasks') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_contains(uri_string(), 'schedule/tasks') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-tasks w-6 mr-3 <?= str_contains(uri_string(), 'schedule/tasks') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>タスク一覧</span>
        </a>
        <a href="<?= base_url('members') ?>" class="nav-item <?= str_starts_with(uri_string(), 'members') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_starts_with(uri_string(), 'members') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
            <i class="fas fa-users w-6 mr-3 <?= str_starts_with(uri_string(), 'members') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>メンバー</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 text-slate-300 hover:text-white">
            <i class="fas fa-yen-sign w-6 mr-3 text-slate-400"></i><span>原価管理</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 text-slate-300 hover:text-white">
            <i class="fas fa-chart-bar w-6 mr-3 text-slate-400"></i><span>レポート</span>
        </a>
        <div class="mt-4 pt-4 border-t border-slate-700/50">
            <a href="<?= base_url('settings') ?>" class="nav-item <?= str_starts_with(uri_string(), 'settings') || str_starts_with(uri_string(), 'process-masters') ? 'active' : '' ?> flex items-center px-4 py-3 <?= str_starts_with(uri_string(), 'settings') || str_starts_with(uri_string(), 'process-masters') ? 'text-white' : 'text-slate-300 hover:text-white' ?>">
                <i class="fas fa-cog w-6 mr-3 <?= str_starts_with(uri_string(), 'settings') || str_starts_with(uri_string(), 'process-masters') ? 'text-blue-400' : 'text-slate-400' ?>"></i><span>設定</span>
            </a>
        </div>
    </nav>
</aside>
