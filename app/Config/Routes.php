<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 認証不要のルート
$routes->get('/', 'DashboardController::index');
$routes->get('login', 'Auth\LoginController::showLoginForm');
$routes->post('login', 'Auth\LoginController::login');
$routes->get('logout', 'Auth\LoginController::logout');

// ダッシュボード
$routes->get('dashboard', 'DashboardController::index');

// 顧客管理
$routes->group('customers', function ($routes) {
    $routes->get('/', 'CustomerController::index');
    $routes->get('create', 'CustomerController::create');
    $routes->post('/', 'CustomerController::store');
    $routes->get('(:num)', 'CustomerController::show/$1');
    $routes->get('(:num)/edit', 'CustomerController::edit/$1');
    $routes->put('(:num)', 'CustomerController::update/$1');
    $routes->delete('(:num)', 'CustomerController::delete/$1');
});

// プロジェクト管理
$routes->group('projects', function ($routes) {
    $routes->get('/', 'ProjectController::index');
    $routes->get('create', 'ProjectController::create');
    $routes->post('/', 'ProjectController::store');
    $routes->get('(:num)', 'ProjectController::show/$1');
    $routes->get('(:num)/edit', 'ProjectController::edit/$1');
    $routes->put('(:num)', 'ProjectController::update/$1');
    $routes->delete('(:num)', 'ProjectController::delete/$1');
});

// メンバー管理
$routes->group('members', function ($routes) {
    $routes->get('/', 'MemberController::index');
    $routes->get('create', 'MemberController::create');
    $routes->post('/', 'MemberController::store');
    $routes->get('(:num)', 'MemberController::show/$1');
    $routes->get('(:num)/edit', 'MemberController::edit/$1');
    $routes->put('(:num)', 'MemberController::update/$1');
    $routes->delete('(:num)', 'MemberController::delete/$1');
});

// 設定
$routes->get('settings', 'SettingsController::index');

// 工程マスタ管理
$routes->group('process-masters', function ($routes) {
    $routes->get('/', 'ProcessMasterController::index');
    $routes->post('/', 'ProcessMasterController::store');
    $routes->get('(:num)', 'ProcessMasterController::show/$1');
    $routes->put('(:num)', 'ProcessMasterController::update/$1');
    $routes->delete('(:num)', 'ProcessMasterController::delete/$1');
    $routes->post('reorder', 'ProcessMasterController::reorder');
});

// スケジュール管理
$routes->group('schedule', function ($routes) {
    $routes->get('/', 'ScheduleController::index');           // ガントチャート画面
    $routes->get('tasks', 'ScheduleController::taskList');    // タスク一覧画面
});

// タスクAPI（Ajax用）
$routes->group('api/tasks', function ($routes) {
    $routes->get('/', 'Api\TaskApiController::index');                    // タスク一覧取得
    $routes->get('stats', 'Api\TaskApiController::stats');                // 統計情報取得
    $routes->get('(:num)', 'Api\TaskApiController::show/$1');             // タスク詳細取得
    $routes->post('/', 'Api\TaskApiController::create');                  // タスク作成
    $routes->put('(:num)', 'Api\TaskApiController::update/$1');           // タスク更新
    $routes->delete('(:num)', 'Api\TaskApiController::delete/$1');        // タスク削除
    $routes->post('bulk-update', 'Api\TaskApiController::bulkUpdate');    // 一括更新（カレンダー用）
    $routes->post('bulk-edit', 'Api\TaskApiController::bulkEdit');        // 一括編集（タスク一覧用）
    $routes->post('bulk-delete', 'Api\TaskApiController::bulkDelete');    // 一括削除
    $routes->post('import', 'Api\TaskApiController::import');             // インポート
    $routes->post('reorder', 'Api\TaskApiController::reorder');           // 並び替え
    $routes->post('(:num)/progress', 'Api\TaskApiController::updateProgress/$1');  // 進捗更新
    $routes->post('(:num)/subtasks', 'Api\TaskApiController::addSubtask/$1');      // サブタスク追加
    $routes->get('(:num)/subtasks', 'Api\TaskApiController::subtasks/$1');         // サブタスク一覧
    $routes->post('(:num)/copy', 'Api\TaskApiController::copy/$1');                // タスクコピー
    $routes->get('(:num)/history', 'Api\TaskApiController::history/$1');           // 履歴取得
});
