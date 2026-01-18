<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CustomerModel;
use App\Models\MemberModel;
use App\Services\ProjectService;

class ProjectController extends BaseController
{
    protected ProjectModel $projectModel;
    protected CustomerModel $customerModel;
    protected MemberModel $memberModel;
    protected ProjectService $projectService;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->customerModel = new CustomerModel();
        $this->memberModel = new MemberModel();
        $this->projectService = new ProjectService();
    }

    /**
     * プロジェクト一覧表示
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $data = [
            'pageTitle' => 'プロジェクト一覧',
            'projects'  => $this->projectService->getProjects($search, $status),
            'stats'     => $this->projectService->getStats(),
            'search'    => $search,
            'status'    => $status,
        ];

        return view('projects/index', $data);
    }

    /**
     * プロジェクト詳細表示
     */
    public function show(int $id)
    {
        $project = $this->projectModel->getProjectWithCustomer($id);

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pageTitle' => 'プロジェクト詳細',
            'project'   => $project,
        ];

        return view('projects/show', $data);
    }

    /**
     * プロジェクト作成フォーム表示
     */
    public function create()
    {
        $customerId = $this->request->getGet('customer_id');

        $data = [
            'pageTitle'          => 'プロジェクト作成',
            'customers'          => $this->customerModel->findAll(),
            'members'            => $this->memberModel->getActiveMembers(),
            'selectedCustomerId' => $customerId,
        ];

        return view('projects/form', $data);
    }

    /**
     * プロジェクト保存処理
     */
    public function store()
    {
        $rules = [
            'name'        => 'required|max_length[255]',
            'customer_id' => 'required|is_natural_no_zero',
            'status'      => 'required|in_list[planning,in_progress,on_hold,completed,cancelled]',
        ];

        $messages = [
            'name' => [
                'required'   => 'プロジェクト名は必須です。',
                'max_length' => 'プロジェクト名は255文字以内で入力してください。',
            ],
            'customer_id' => [
                'required'          => '顧客は必須です。',
                'is_natural_no_zero' => '有効な顧客を選択してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('error', '入力エラーがあります。')
                ->with('field_errors', $errors);
        }

        $data = [
            'name'           => $this->request->getPost('name'),
            'customer_id'    => $this->request->getPost('customer_id'),
            'status'         => $this->request->getPost('status'),
            'description'    => $this->request->getPost('description'),
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'project_leader' => $this->request->getPost('project_leader'),
            'members'        => $this->request->getPost('members') ?? [],
            'budget'         => $this->request->getPost('budget') ?: null,
            'notes'          => $this->request->getPost('notes'),
        ];

        $this->projectModel->insert($data);

        return redirect()->to('/projects')
            ->with('success', 'プロジェクトを登録しました。');
    }

    /**
     * プロジェクト編集フォーム表示
     */
    public function edit(int $id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pageTitle' => 'プロジェクト編集',
            'project'   => $project,
            'customers' => $this->customerModel->findAll(),
            'members'   => $this->memberModel->getActiveMembers(),
        ];

        return view('projects/form', $data);
    }

    /**
     * プロジェクト更新処理
     */
    public function update(int $id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name'        => 'required|max_length[255]',
            'customer_id' => 'required|is_natural_no_zero',
            'status'      => 'required|in_list[planning,in_progress,on_hold,completed,cancelled]',
        ];

        $messages = [
            'name' => [
                'required'   => 'プロジェクト名は必須です。',
                'max_length' => 'プロジェクト名は255文字以内で入力してください。',
            ],
            'customer_id' => [
                'required'          => '顧客は必須です。',
                'is_natural_no_zero' => '有効な顧客を選択してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('error', '入力エラーがあります。')
                ->with('field_errors', $errors);
        }

        $data = [
            'name'           => $this->request->getPost('name'),
            'customer_id'    => $this->request->getPost('customer_id'),
            'status'         => $this->request->getPost('status'),
            'description'    => $this->request->getPost('description'),
            'start_date'     => $this->request->getPost('start_date') ?: null,
            'end_date'       => $this->request->getPost('end_date') ?: null,
            'project_leader' => $this->request->getPost('project_leader'),
            'members'        => $this->request->getPost('members') ?? [],
            'budget'         => $this->request->getPost('budget') ?: null,
            'notes'          => $this->request->getPost('notes'),
        ];

        $this->projectModel->update($id, $data);

        return redirect()->to('/projects/' . $id)
            ->with('success', 'プロジェクト情報を更新しました。');
    }

    /**
     * プロジェクト削除処理
     */
    public function delete(int $id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->projectModel->delete($id);

        return redirect()->to('/projects')
            ->with('success', 'プロジェクトを削除しました。');
    }
}
