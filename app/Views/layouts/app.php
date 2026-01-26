<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($pageTitle ?? 'プロジェクト管理システム') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .card-shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .nav-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 2px 8px;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
        .nav-item.active {
            background: rgba(59, 130, 246, 0.15);
            border-left: 3px solid #3b82f6;
        }
        /* サイドメニューモーダル */
        .sidebar-modal {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            z-index: 200;
            display: none;
            box-shadow: 4px 0 24px rgba(0,0,0,0.3);
        }
        .sidebar-modal.show {
            display: block;
        }
        .sidebar-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 199;
            display: none;
        }
        .sidebar-modal-overlay.show {
            display: block;
        }
        .sidebar-modal .nav-item {
            margin: 4px 12px;
            padding: 12px 16px;
        }
        .sidebar-modal .nav-item:hover {
            background: rgba(59, 130, 246, 0.15);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        /* トースト通知 */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            animation: slideInRight 0.3s ease-out;
            min-width: 300px;
            max-width: 400px;
        }
        .toast-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .toast-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .toast-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .toast-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        .toast-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .toast-message {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }
        .toast-close {
            margin-left: 12px;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .toast-close:hover {
            opacity: 1;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100px); }
        }
        .toast.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }
        /* ユーザーメニュー */
        .user-menu-container {
            position: relative;
        }
        .user-menu-trigger {
            cursor: pointer;
            transition: all 0.2s;
        }
        .user-menu-trigger:hover {
            background: #f8fafc;
        }
        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 9999;
            min-width: 240px;
            padding: 0;
            display: none;
            overflow: hidden;
        }
        .user-menu-dropdown.show {
            display: block;
            animation: fadeInDown 0.2s ease;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-menu-header {
            padding: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
        }
        .user-menu-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #475569;
            font-size: 14px;
        }
        .user-menu-item:hover {
            background: #f8fafc;
        }
        .user-menu-item i {
            width: 20px;
            margin-right: 12px;
            color: #94a3b8;
        }
        .user-menu-item.logout {
            color: #dc2626;
            border-top: 1px solid #e2e8f0;
        }
        .user-menu-item.logout i {
            color: #dc2626;
        }
        .user-menu-item.logout:hover {
            background: #fef2f2;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- サイドバーモーダルオーバーレイ -->
    <div class="sidebar-modal-overlay" id="sidebar-overlay"></div>

    <!-- サイドバーモーダル -->
    <?= $this->include('layouts/partials/sidebar') ?>

    <div class="flex h-screen overflow-hidden">
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- ヘッダー -->
            <?= $this->include('layouts/partials/header') ?>

            <!-- メインコンテンツエリア -->
            <main class="flex-1 <?= $this->renderSection('mainClass') ?: 'overflow-y-auto' ?>">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- トースト通知コンテナ -->
    <div class="toast-container" id="toast-container">
        <?php $successMsg = session()->getFlashdata('success'); ?>
        <?php if ($successMsg && trim($successMsg) !== ''): ?>
        <div class="toast toast-success" id="toast-success">
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-message"><?= esc($successMsg) ?></div>
            <div class="toast-close" onclick="closeToast('toast-success')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <?php endif; ?>
        <?php $errorMsg = session()->getFlashdata('error'); ?>
        <?php if ($errorMsg && trim($errorMsg) !== ''): ?>
        <div class="toast toast-error" id="toast-error">
            <div class="toast-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="toast-message"><?= esc($errorMsg) ?></div>
            <div class="toast-close" onclick="closeToast('toast-error')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <?php endif; ?>
        <?php $infoMsg = session()->getFlashdata('info'); ?>
        <?php if ($infoMsg && trim($infoMsg) !== ''): ?>
        <div class="toast toast-info" id="toast-info">
            <div class="toast-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="toast-message"><?= esc($infoMsg) ?></div>
            <div class="toast-close" onclick="closeToast('toast-info')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?= $this->renderSection('scripts') ?>
    <script>
        // トースト通知
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // 自動的にトーストを閉じる（5秒後）
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                if (toast && toast.parentNode) {
                    toast.classList.add('hiding');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        });

        // サイドバーモーダル
        const sidebarModal = document.getElementById('sidebar-modal');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const menuToggle = document.getElementById('menu-toggle');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebarModal.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebarModal.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // ユーザーメニュー
        const userMenuTrigger = document.getElementById('user-menu-trigger');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        const userMenuArrow = document.getElementById('user-menu-arrow');

        if (userMenuTrigger) {
            userMenuTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('show');
                if (userMenuArrow) {
                    userMenuArrow.style.transform = userMenuDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            });
        }

        document.addEventListener('click', () => {
            if (userMenuDropdown) {
                userMenuDropdown.classList.remove('show');
                if (userMenuArrow) {
                    userMenuArrow.style.transform = 'rotate(0deg)';
                }
            }
        });
    </script>
</body>
</html>
