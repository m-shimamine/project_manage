<?php

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController extends BaseController
{
    protected DashboardService $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    /**
     * ダッシュボード表示
     */
    public function index()
    {
        $data = [
            'pageTitle'  => 'ダッシュボード',
            'headerIcon' => '<div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm"><i class="fas fa-tachometer-alt text-white text-xs"></i></div>',
            'stats'      => $this->dashboardService->getStats(),
            'activities' => $this->dashboardService->getRecentActivities(),
            'projects'   => $this->dashboardService->getActiveProjects(),
            'chartData'  => $this->dashboardService->getChartData(),
        ];

        return view('dashboard', $data);
    }
}
