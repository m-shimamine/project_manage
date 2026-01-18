<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .menu-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-8">
    <!-- メニューカード -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-8">
        <a href="<?= base_url('customers') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-building text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">顧客</span>
        </a>
        <a href="<?= base_url('projects') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-folder text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">プロジェクト</span>
        </a>
        <a href="<?= base_url('schedule') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">スケジュール</span>
        </a>
        <a href="<?= base_url('schedule/tasks') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-tasks text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">タスク一覧</span>
        </a>
        <a href="<?= base_url('members') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-users text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">メンバー</span>
        </a>
        <a href="#" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-yen-sign text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">原価管理</span>
        </a>
        <a href="#" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-chart-bar text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">レポート</span>
        </a>
        <a href="<?= base_url('settings') ?>" class="menu-card bg-white rounded-xl p-4 card-shadow flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center shadow-lg mb-3">
                <i class="fas fa-cog text-white"></i>
            </div>
            <span class="text-sm font-semibold text-slate-700">設定</span>
        </a>
    </div>

    <!-- 統計カード -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- プロジェクト数 -->
        <div class="stat-card bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-folder text-white"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg"><?= esc($stats['projects']['change'] ?? '+0') ?></span>
            </div>
            <div class="mb-2">
                <p class="text-3xl font-bold text-slate-800"><?= esc($stats['projects']['count'] ?? 0) ?></p>
                <p class="text-sm text-slate-500 font-medium"><?= esc($stats['projects']['label'] ?? 'プロジェクト') ?></p>
            </div>
            <div class="flex items-center text-xs text-slate-400">
                <i class="fas fa-arrow-up mr-1"></i>
                <span><?= esc($stats['projects']['detail'] ?? '') ?></span>
            </div>
        </div>

        <!-- タスク数 -->
        <div class="stat-card bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-tasks text-white"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg"><?= esc($stats['tasks']['change'] ?? '+0') ?></span>
            </div>
            <div class="mb-2">
                <p class="text-3xl font-bold text-slate-800"><?= esc($stats['tasks']['count'] ?? 0) ?></p>
                <p class="text-sm text-slate-500 font-medium"><?= esc($stats['tasks']['label'] ?? 'タスク') ?></p>
            </div>
            <div class="flex items-center text-xs text-slate-400">
                <i class="fas fa-clock mr-1"></i>
                <span><?= esc($stats['tasks']['detail'] ?? '') ?></span>
            </div>
        </div>

        <!-- メンバー数 -->
        <div class="stat-card bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg"><?= esc($stats['members']['change'] ?? '+0') ?></span>
            </div>
            <div class="mb-2">
                <p class="text-3xl font-bold text-slate-800"><?= esc($stats['members']['count'] ?? 0) ?></p>
                <p class="text-sm text-slate-500 font-medium"><?= esc($stats['members']['label'] ?? 'メンバー') ?></p>
            </div>
            <div class="flex items-center text-xs text-slate-400">
                <i class="fas fa-user-check mr-1"></i>
                <span><?= esc($stats['members']['detail'] ?? '') ?></span>
            </div>
        </div>

        <!-- 進捗率 -->
        <div class="stat-card bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg"><?= esc($stats['progress']['change'] ?? '+0') ?></span>
            </div>
            <div class="mb-2">
                <p class="text-3xl font-bold text-slate-800"><?= esc($stats['progress']['count'] ?? '0%') ?></p>
                <p class="text-sm text-slate-500 font-medium"><?= esc($stats['progress']['label'] ?? '平均進捗率') ?></p>
            </div>
            <div class="flex items-center text-xs text-slate-400">
                <i class="fas fa-trending-up mr-1"></i>
                <span><?= esc($stats['progress']['detail'] ?? '') ?></span>
            </div>
        </div>
    </div>

    <!-- グラフとプロジェクト一覧 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- 進捗グラフ -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 card-shadow fade-in">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">プロジェクト進捗状況</h3>
                <select class="text-xs border border-slate-200 rounded-lg px-3 py-1.5 bg-white focus:ring-2 focus:ring-blue-500">
                    <option>過去30日</option>
                    <option>過去7日</option>
                    <option>過去90日</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- 最近の活動 -->
        <div class="bg-white rounded-2xl p-6 card-shadow fade-in">
            <h3 class="text-lg font-bold text-slate-800 mb-6">最近の活動</h3>
            <div class="space-y-4">
                <?php foreach ($activities as $activity): ?>
                <div class="flex items-start space-x-3 pb-4 <?= !$activity['is_last'] ? 'border-b border-slate-100' : '' ?>">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br <?= esc($activity['color']) ?> flex items-center justify-center flex-shrink-0">
                        <i class="fas <?= esc($activity['icon']) ?> text-white text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800"><?= esc($activity['title']) ?></p>
                        <p class="text-xs text-slate-500"><?= esc($activity['description']) ?></p>
                        <p class="text-xs text-slate-400 mt-1"><?= esc($activity['time']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- プロジェクト一覧 -->
    <div class="bg-white rounded-2xl p-6 card-shadow fade-in">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">進行中のプロジェクト</h3>
            <a href="<?= base_url('projects') ?>" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center">
                すべて見る <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">プロジェクト名</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">担当者</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">進捗</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">期限</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600 uppercase tracking-wide">ステータス</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($projects as $project): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br <?= esc($project['color']) ?> flex items-center justify-center">
                                    <i class="fas <?= esc($project['icon']) ?> text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800"><?= esc($project['name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= esc($project['task_count']) ?>タスク</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br <?= esc($project['assignee_color']) ?> ring-2 ring-white"></div>
                                <span class="text-sm text-slate-700"><?= esc($project['assignee']) ?></span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 w-24 bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r <?= esc($project['progress_color']) ?> h-2 rounded-full" style="width: <?= esc($project['progress']) ?>%"></div>
                                </div>
                                <span class="text-sm font-semibold text-<?= esc($project['status_color']) ?>-600"><?= esc($project['progress']) ?>%</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-sm text-slate-700"><?= esc($project['deadline']) ?></span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-<?= esc($project['status_color']) ?>-50 text-<?= esc($project['status_color']) ?>-700"><?= esc($project['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// 進捗グラフ
const ctx = document.getElementById('progressChart').getContext('2d');
const chartData = <?= json_encode($chartData ?? ['labels' => [], 'datasets' => [['label' => '', 'data' => [], 'borderColor' => '', 'backgroundColor' => ''], ['label' => '', 'data' => [], 'borderColor' => '', 'backgroundColor' => '']]]) ?>;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: chartData.datasets[0].label,
            data: chartData.datasets[0].data,
            borderColor: chartData.datasets[0].borderColor,
            backgroundColor: chartData.datasets[0].backgroundColor,
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: chartData.datasets[0].borderColor,
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }, {
            label: chartData.datasets[1].label,
            data: chartData.datasets[1].data,
            borderColor: chartData.datasets[1].borderColor,
            backgroundColor: chartData.datasets[1].backgroundColor,
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 5,
            pointBackgroundColor: chartData.datasets[1].borderColor,
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.1)'
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
