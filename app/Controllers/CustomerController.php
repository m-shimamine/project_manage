<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\MemberModel;
use App\Models\ProjectModel;
use App\Services\CustomerService;

class CustomerController extends BaseController
{
    protected CustomerModel $customerModel;
    protected MemberModel $memberModel;
    protected ProjectModel $projectModel;
    protected CustomerService $customerService;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->memberModel = new MemberModel();
        $this->projectModel = new ProjectModel();
        $this->customerService = new CustomerService();
    }

    /**
     * 顧客一覧表示
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $data = [
            'pageTitle' => '顧客一覧',
            'customers' => $this->customerService->getCustomers($search, $status),
            'stats'     => $this->customerService->getStats(),
            'search'    => $search,
            'status'    => $status,
        ];

        return view('customers/index', $data);
    }

    /**
     * 顧客詳細表示
     */
    public function show(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // プロジェクト一覧を取得
        $projects = $this->projectModel->getProjectsByCustomerId($id);

        $data = [
            'pageTitle' => '顧客詳細',
            'customer'  => $customer,
            'projects'  => $projects,
        ];

        return view('customers/show', $data);
    }

    /**
     * 顧客作成フォーム表示
     */
    public function create()
    {
        $data = [
            'pageTitle' => '顧客作成',
            'members'   => $this->memberModel->getActiveMembers(),
        ];

        return view('customers/form', $data);
    }

    /**
     * 顧客保存処理
     */
    public function store()
    {
        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,maintenance,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '会社名は必須です。',
                'max_length' => '会社名は255文字以内で入力してください。',
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
            'status'         => $this->request->getPost('status'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'city'           => $this->request->getPost('city'),
            'address'        => $this->request->getPost('address'),
            'project_leader' => $this->request->getPost('project_leader'),
            'members'        => $this->request->getPost('members') ?? [],
            'contacts'       => $this->request->getPost('contacts') ?? [],
            'notes'          => $this->request->getPost('notes'),
        ];

        $this->customerModel->insert($data);

        return redirect()->to('/customers')
            ->with('success', '顧客を登録しました。');
    }

    /**
     * 顧客編集フォーム表示
     */
    public function edit(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 全プロジェクトを取得
        $allProjects = $this->projectModel->findAll();

        $data = [
            'pageTitle'   => '顧客編集',
            'customer'    => $customer,
            'members'     => $this->memberModel->getActiveMembers(),
            'allProjects' => $allProjects,
        ];

        return view('customers/form', $data);
    }

    /**
     * 顧客更新処理
     */
    public function update(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,maintenance,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '会社名は必須です。',
                'max_length' => '会社名は255文字以内で入力してください。',
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
            'status'         => $this->request->getPost('status'),
            'postal_code'    => $this->request->getPost('postal_code'),
            'city'           => $this->request->getPost('city'),
            'address'        => $this->request->getPost('address'),
            'project_leader' => $this->request->getPost('project_leader'),
            'members'        => $this->request->getPost('members') ?? [],
            'contacts'       => $this->request->getPost('contacts') ?? [],
            'notes'          => $this->request->getPost('notes'),
        ];

        $this->customerModel->update($id, $data);

        // プロジェクトの紐付け更新
        $projectIds = $this->request->getPost('project_ids') ?? [];
        $this->updateCustomerProjects($id, $projectIds);

        return redirect()->to('/customers/' . $id)
            ->with('success', '顧客情報を更新しました。');
    }

    /**
     * 顧客削除処理
     */
    public function delete(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->customerModel->delete($id);

        return redirect()->to('/customers')
            ->with('success', '顧客を削除しました。');
    }

    /**
     * 顧客に紐づくプロジェクトを更新
     */
    private function updateCustomerProjects(int $customerId, array $projectIds): void
    {
        // 既存のプロジェクトの紐付けを解除
        $this->projectModel->where('customer_id', $customerId)
            ->whereNotIn('id', $projectIds ?: [0])
            ->set(['customer_id' => null])
            ->update();

        // 新しいプロジェクトを紐付け
        if (!empty($projectIds)) {
            $this->projectModel->whereIn('id', $projectIds)
                ->set(['customer_id' => $customerId])
                ->update();
        }
    }
}
