<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<style>
    .project-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .project-card:hover {
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
    .btn-detail {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        transition: all 0.3s ease;
    }
    .btn-detail:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
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
<!-- サブヘッダー -->
<div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <!-- 検索 -->
            <form method="GET" action="<?= base_url('projects') ?>" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative">
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="プロジェクト名で検索..."
                           class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-72 text-sm shadow-sm">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400"></i>
                </div>
                <!-- ステータスフィルター -->
                <select name="status" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-4 py-2 text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">すべてのステータス</option>
                    <option value="planning" <?= ($status ?? '') === 'planning' ? 'selected' : '' ?>>計画中</option>
                    <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>進行中</option>
                    <option value="on_hold" <?= ($status ?? '') === 'on_hold' ? 'selected' : '' ?>>保留中</option>
                    <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>完了</option>
                    <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>中止</option>
                </select>
            </form>
        </div>
        <!-- 新規追加ボタン -->
        <a href="<?= base_url('projects/create') ?>" class="btn-stylish inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-lg">
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
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 fade-in">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-project-diagram text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">総プロジェクト</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['total']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-list text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">計画中</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['planning']) ?>件</p>
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
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['in_progress']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-pause-circle text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">保留中</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['on_hold']) ?>件</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-sm sm:text-lg"></i>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs text-slate-500 font-medium">完了</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800"><?= esc($stats['completed']) ?>件</p>
                </div>
            </div>
        </div>
    </div>

    <!-- プロジェクトカード一覧 -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 fade-in">
        <?php foreach ($projects as $project): ?>
        <?php
            $color = \App\Models\ProjectModel::getColor($project['name']);
            $initial = \App\Models\ProjectModel::getInitial($project['name']);
            $statusLabel = \App\Models\ProjectModel::getStatusLabel($project['status']);
            $statusColor = \App\Models\ProjectModel::getStatusColor($project['status']);
        ?>
        <div class="project-card bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r <?= esc($color) ?>"></div>
            <div class="p-4 sm:p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center min-w-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br <?= esc($color) ?> flex items-center justify-center shadow-lg mr-3 sm:mr-4 flex-shrink-0">
                            <span class="text-white font-bold text-lg sm:text-xl"><?= esc($initial) ?></span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 truncate"><?= esc($project['name']) ?></h3>
                            <p class="text-sm text-slate-500"><?= esc($project['customer_name'] ?? '-') ?></p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold <?= esc($statusColor) ?> rounded-full flex-shrink-0 ml-2"><?= esc($statusLabel) ?></span>
                </div>

                <!-- プロジェクトリーダー -->
                <?php if (!empty($project['project_leader'])): ?>
                <div class="mb-3">
                    <div class="flex items-center text-sm mb-2">
                        <i class="fas fa-user-tie text-indigo-500 w-5 mr-2"></i>
                        <span class="text-slate-600 font-medium">PL: <?= esc($project['project_leader']) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- メンバータグ -->
                <?php if (!empty($project['members']) && count($project['members']) > 0): ?>
                <div class="mb-4">
                    <div class="flex flex-wrap gap-1.5">
                        <?php foreach (array_slice($project['members'], 0, 3) as $member): ?>
                        <span class="member-tag inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                            <i class="fas fa-user text-slate-400 mr-1 text-[10px]"></i>
                            <?= esc($member) ?>
                        </span>
                        <?php endforeach; ?>
                        <?php if (count($project['members']) > 3): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                            +<?= count($project['members']) - 3 ?>名
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-calendar text-slate-400 w-5 mr-2"></i>
                        <span class="text-slate-600">
                            <?php if (!empty($project['start_date']) && !empty($project['end_date'])): ?>
                                <?= date('Y/m/d', strtotime($project['start_date'])) ?> 〜 <?= date('Y/m/d', strtotime($project['end_date'])) ?>
                            <?php elseif (!empty($project['start_date'])): ?>
                                <?= date('Y/m/d', strtotime($project['start_date'])) ?> 〜
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if (!empty($project['budget'])): ?>
                    <div class="flex items-center">
                        <i class="fas fa-yen-sign text-slate-400 w-5 mr-2"></i>
                        <span class="text-slate-600"><?= number_format($project['budget']) ?>円</span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <a href="<?= base_url('projects/' . $project['id']) ?>" class="btn-detail flex items-center justify-center w-full py-2.5 rounded-lg text-sm font-semibold text-slate-700">
                        <i class="fas fa-eye mr-2"></i>
                        詳細
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- プロジェクトがない場合 -->
    <?php if (empty($projects)): ?>
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-project-diagram text-3xl text-slate-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">プロジェクトが登録されていません</h3>
        <p class="text-slate-500 mb-6">新規追加ボタンからプロジェクトを登録してください</p>
        <a href="<?= base_url('projects/create') ?>" class="btn-stylish inline-flex items-center px-6 py-3 rounded-lg text-sm font-semibold text-white shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            新規追加
        </a>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
