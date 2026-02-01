<?php

namespace App\Controllers;

use App\Services\TaskService;
use App\Models\ProjectModel;
use App\Models\ProcessMasterModel;
use App\Models\MemberModel;

class ScheduleController extends BaseController
{
    protected TaskService $taskService;
    protected ProjectModel $projectModel;
    protected ProcessMasterModel $processModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->projectModel = new ProjectModel();
        $this->processModel = new ProcessMasterModel();
        $this->memberModel = new MemberModel();
    }

    /**
     * スケジュール画面表示（ガントチャート）
     */
    public function index()
    {
        $projectId = $this->request->getGet('project_id');
        $view = $this->request->getGet('view') ?? 'gantt'; // gantt or task

        // プロジェクト一覧を顧客別にグループ化
        $projects = $this->projectModel->getProjects();
        $projectsByCustomer = $this->groupProjectsByCustomer($projects);

        // 選択されたプロジェクト情報
        $selectedProject = null;
        $tasks = [];
        $taskStats = [];
        $tasksGrouped = [];
        $isAllProjects = empty($projectId) || $projectId === 'all';

        if ($isAllProjects) {
            // 全プロジェクト横断表示
            $tasksGrouped = $this->taskService->getAllTasksGroupedByProject();
            // 全タスクをフラット化
            foreach ($tasksGrouped as $group) {
                foreach ($group['tasks'] as $task) {
                    $tasks[] = $task;
                }
            }
            $taskStats = $this->taskService->getTaskStats(null);
        } else {
            $selectedProject = $this->projectModel->getProjectWithCustomer($projectId);
            $tasks = $this->taskService->getTasksByProject((int)$projectId);
            $taskStats = $this->taskService->getTaskStats((int)$projectId);
        }

        // 工程マスタ（アクティブのみ）
        $processes = $this->processModel->getActiveProcessMasters();

        // メンバー一覧（プロジェクト選択時はプロジェクト参加者+リーダーのみ）
        $allMembers = $this->memberModel->getActiveMembers();
        if (!$isAllProjects && $selectedProject) {
            // プロジェクト参加者の名前リスト
            $projectMemberNames = $selectedProject['members'] ?? [];
            // プロジェクトリーダーも追加
            if (!empty($selectedProject['project_leader'])) {
                $projectMemberNames[] = $selectedProject['project_leader'];
            }
            $projectMemberNames = array_unique($projectMemberNames);

            if (!empty($projectMemberNames)) {
                // 参加者のみをフィルタリング（名前で比較）
                $members = array_filter($allMembers, function ($member) use ($projectMemberNames) {
                    return in_array($member['name'], $projectMemberNames, true);
                });
                $members = array_values($members); // インデックスを振り直し
            } else {
                $members = $allMembers;
            }
        } else {
            $members = $allMembers;
        }

        return view('schedule/index', [
            'pageTitle'         => 'スケジュール管理',
            'projectId'         => $projectId,
            'selectedProject'   => $selectedProject,
            'projectsByCustomer' => $projectsByCustomer,
            'tasks'             => $tasks,
            'tasksGrouped'      => $tasksGrouped,
            'taskStats'         => $taskStats,
            'processes'         => $processes,
            'members'           => $members,
            'view'              => $view,
            'isAllProjects'     => $isAllProjects,
        ]);
    }

    /**
     * タスク一覧画面表示
     */
    public function taskList()
    {
        $projectId = $this->request->getGet('project_id');

        // プロジェクト一覧を顧客別にグループ化
        $projects = $this->projectModel->getProjects();
        $projectsByCustomer = $this->groupProjectsByCustomer($projects);

        // 選択されたプロジェクト情報
        $selectedProject = null;
        $tasks = [];
        $taskStats = [];
        $tasksGrouped = [];
        $isAllProjects = empty($projectId) || $projectId === 'all';

        if ($isAllProjects) {
            // 全プロジェクト横断表示
            $tasksGrouped = $this->taskService->getAllTasksGroupedByProject();
            foreach ($tasksGrouped as $group) {
                foreach ($group['tasks'] as $task) {
                    $tasks[] = $task;
                }
            }
            $taskStats = $this->taskService->getTaskStats(null);
        } else {
            $selectedProject = $this->projectModel->getProjectWithCustomer($projectId);
            $tasks = $this->taskService->getTasksByProject((int)$projectId);
            $taskStats = $this->taskService->getTaskStats((int)$projectId);
        }

        // 工程マスタ（アクティブのみ）
        $processes = $this->processModel->getActiveProcessMasters();

        // メンバー一覧（プロジェクト選択時はプロジェクト参加者+リーダーのみ）
        $allMembers = $this->memberModel->getActiveMembers();
        if (!$isAllProjects && $selectedProject) {
            // プロジェクト参加者の名前リスト
            $projectMemberNames = $selectedProject['members'] ?? [];
            // プロジェクトリーダーも追加
            if (!empty($selectedProject['project_leader'])) {
                $projectMemberNames[] = $selectedProject['project_leader'];
            }
            $projectMemberNames = array_unique($projectMemberNames);

            if (!empty($projectMemberNames)) {
                // 参加者のみをフィルタリング（名前で比較）
                $members = array_filter($allMembers, function ($member) use ($projectMemberNames) {
                    return in_array($member['name'], $projectMemberNames, true);
                });
                $members = array_values($members); // インデックスを振り直し
            } else {
                $members = $allMembers;
            }
        } else {
            $members = $allMembers;
        }

        return view('schedule/task_list', [
            'pageTitle'         => 'タスク一覧',
            'projectId'         => $projectId,
            'selectedProject'   => $selectedProject,
            'projectsByCustomer' => $projectsByCustomer,
            'tasks'             => $tasks,
            'tasksGrouped'      => $tasksGrouped,
            'taskStats'         => $taskStats,
            'processes'         => $processes,
            'members'           => $members,
            'view'              => 'task',
            'isAllProjects'     => $isAllProjects,
        ]);
    }

    /**
     * プロジェクトを顧客別にグループ化
     */
    protected function groupProjectsByCustomer(array $projects): array
    {
        $grouped = [];

        foreach ($projects as $project) {
            $customerId = $project['customer_id'];
            $customerName = $project['customer_name'] ?? '未分類';

            if (!isset($grouped[$customerId])) {
                $grouped[$customerId] = [
                    'customer_id'   => $customerId,
                    'customer_name' => $customerName,
                    'projects'      => [],
                ];
            }

            $grouped[$customerId]['projects'][] = $project;
        }

        return array_values($grouped);
    }
}
