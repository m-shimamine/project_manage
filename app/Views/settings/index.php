<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .setting-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .setting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
    }
    .setting-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .setting-card.disabled:hover {
        transform: none;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-8">
    <!-- ページタイトル -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center shadow-lg">
                <i class="fas fa-cog text-white"></i>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">設定</h1>
        </div>
        <p class="text-sm text-slate-500 ml-13">システムの各種設定を管理します</p>
    </div>

    <!-- 設定カードグリッド -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- 工程マスタ -->
        <a href="<?= base_url('process-masters') ?>" class="setting-card bg-white rounded-2xl p-6 card-shadow fade-in block">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-stream text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700">マスタ</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">工程マスタ</h3>
            <p class="text-sm text-slate-500 mb-4">プロジェクトで使用する工程の定義と管理を行います</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-list text-slate-400 text-xs"></i>
                    <span class="text-xs text-slate-600">登録済み: <?= esc($processCount ?? 0) ?>件</span>
                </div>
                <i class="fas fa-arrow-right text-blue-600"></i>
            </div>
        </a>

        <!-- ユーザー管理（準備中） -->
        <div class="setting-card disabled bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users-cog text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400">準備中</span>
            </div>
            <h3 class="text-lg font-bold text-slate-400 mb-2">ユーザー管理</h3>
            <p class="text-sm text-slate-400 mb-4">システムユーザーの権限と設定を管理します</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock text-slate-300 text-xs"></i>
                    <span class="text-xs text-slate-400">近日公開</span>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </div>

        <!-- 通知設定（準備中） -->
        <div class="setting-card disabled bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-bell text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400">準備中</span>
            </div>
            <h3 class="text-lg font-bold text-slate-400 mb-2">通知設定</h3>
            <p class="text-sm text-slate-400 mb-4">システム通知の受信設定を管理します</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock text-slate-300 text-xs"></i>
                    <span class="text-xs text-slate-400">近日公開</span>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </div>

        <!-- データ管理（準備中） -->
        <div class="setting-card disabled bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-database text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400">準備中</span>
            </div>
            <h3 class="text-lg font-bold text-slate-400 mb-2">データ管理</h3>
            <p class="text-sm text-slate-400 mb-4">バックアップとデータのエクスポート設定</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock text-slate-300 text-xs"></i>
                    <span class="text-xs text-slate-400">近日公開</span>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </div>

        <!-- 表示設定（準備中） -->
        <div class="setting-card disabled bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-palette text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400">準備中</span>
            </div>
            <h3 class="text-lg font-bold text-slate-400 mb-2">表示設定</h3>
            <p class="text-sm text-slate-400 mb-4">テーマやレイアウトのカスタマイズ</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock text-slate-300 text-xs"></i>
                    <span class="text-xs text-slate-400">近日公開</span>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </div>

        <!-- セキュリティ（準備中） -->
        <div class="setting-card disabled bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-start justify-between mb-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 text-slate-400">準備中</span>
            </div>
            <h3 class="text-lg font-bold text-slate-400 mb-2">セキュリティ</h3>
            <p class="text-sm text-slate-400 mb-4">パスワードとアクセス制御の設定</p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-lock text-slate-300 text-xs"></i>
                    <span class="text-xs text-slate-400">近日公開</span>
                </div>
                <i class="fas fa-arrow-right text-slate-300"></i>
            </div>
        </div>
    </div>

    <!-- システム情報 -->
    <div class="mt-8 bg-white rounded-2xl p-6 card-shadow fade-in">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            システム情報
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                <span class="text-sm text-slate-600">バージョン</span>
                <span class="text-sm font-semibold text-slate-800">v1.0.0</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                <span class="text-sm text-slate-600">最終更新</span>
                <span class="text-sm font-semibold text-slate-800"><?= date('Y/m/d') ?></span>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                <span class="text-sm text-slate-600">ライセンス</span>
                <span class="text-sm font-semibold text-slate-800">Enterprise</span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
