<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\CustomerModel;
use App\Services\MemberService;

class MemberController extends BaseController
{
    protected MemberModel $memberModel;
    protected CustomerModel $customerModel;
    protected MemberService $memberService;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->customerModel = new CustomerModel();
        $this->memberService = new MemberService();
    }

    /**
     * メンバー一覧表示
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $department = $this->request->getGet('department');
        $status = $this->request->getGet('status');

        $data = [
            'pageTitle'   => 'メンバー一覧',
            'members'     => $this->memberService->getMembers($search, $department, $status),
            'stats'       => $this->memberService->getStats(),
            'departments' => MemberModel::getDepartments(),
            'search'      => $search,
            'department'  => $department,
            'status'      => $status,
        ];

        return view('members/index', $data);
    }

    /**
     * メンバー詳細表示
     */
    public function show(int $id)
    {
        $member = $this->memberModel->find($id);

        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pageTitle' => 'メンバー詳細',
            'member'    => $member,
        ];

        return view('members/show', $data);
    }

    /**
     * メンバー作成フォーム表示
     */
    public function create()
    {
        $data = [
            'pageTitle'   => 'メンバー登録',
            'departments' => MemberModel::getDepartments(),
            'customers'   => $this->customerModel->findAll(),
        ];

        return view('members/form', $data);
    }

    /**
     * メンバー保存処理
     */
    public function store()
    {
        $rules = [
            'name'             => 'required|max_length[255]',
            'email'            => 'required|valid_email|max_length[255]',
            'phone'            => 'permit_empty|max_length[20]',
            'department'       => 'permit_empty|max_length[255]',
            'role'             => 'required|in_list[admin,leader,member]',
            'password'         => 'required|min_length[8]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'name' => [
                'required'   => '氏名は必須です。',
                'max_length' => '氏名は255文字以内で入力してください。',
            ],
            'email' => [
                'required'    => 'メールアドレスは必須です。',
                'valid_email' => '有効なメールアドレスを入力してください。',
                'max_length'  => 'メールアドレスは255文字以内で入力してください。',
            ],
            'phone' => [
                'max_length' => '電話番号は20文字以内で入力してください。',
            ],
            'department' => [
                'max_length' => '部署は255文字以内で入力してください。',
            ],
            'role' => [
                'required' => '権限は必須です。',
                'in_list'  => '無効な権限です。',
            ],
            'password' => [
                'required'   => 'パスワードは必須です。',
                'min_length' => 'パスワードは8文字以上で入力してください。',
                'max_length' => 'パスワードは255文字以内で入力してください。',
            ],
            'password_confirm' => [
                'required' => 'パスワード（確認）は必須です。',
                'matches'  => 'パスワードが一致しません。',
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
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'department' => $this->request->getPost('department'),
            'position'   => $this->request->getPost('position'),
            'role'       => $this->request->getPost('role'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->memberModel->insert($data);

        return redirect()->to('/members')
            ->with('success', 'メンバーを登録しました。');
    }

    /**
     * メンバー編集フォーム表示
     */
    public function edit(int $id)
    {
        $member = $this->memberModel->find($id);

        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'pageTitle'   => 'メンバー編集',
            'member'      => $member,
            'departments' => MemberModel::getDepartments(),
            'customers'   => $this->customerModel->findAll(),
        ];

        return view('members/form', $data);
    }

    /**
     * メンバー更新処理
     */
    public function update(int $id)
    {
        $member = $this->memberModel->find($id);

        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name'       => 'required|max_length[255]',
            'email'      => 'required|valid_email|max_length[255]',
            'phone'      => 'permit_empty|max_length[20]',
            'department' => 'permit_empty|max_length[255]',
            'role'       => 'required|in_list[admin,leader,member]',
        ];

        $messages = [
            'name' => [
                'required'   => '氏名は必須です。',
                'max_length' => '氏名は255文字以内で入力してください。',
            ],
            'email' => [
                'required'    => 'メールアドレスは必須です。',
                'valid_email' => '有効なメールアドレスを入力してください。',
                'max_length'  => 'メールアドレスは255文字以内で入力してください。',
            ],
            'phone' => [
                'max_length' => '電話番号は20文字以内で入力してください。',
            ],
            'department' => [
                'max_length' => '部署は255文字以内で入力してください。',
            ],
            'role' => [
                'required' => '権限は必須です。',
                'in_list'  => '無効な権限です。',
            ],
        ];

        // パスワードが入力された場合のみバリデーション
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]|max_length[255]';
            $rules['password_confirm'] = 'matches[password]';
            $messages['password'] = [
                'min_length' => 'パスワードは8文字以上で入力してください。',
                'max_length' => 'パスワードは255文字以内で入力してください。',
            ];
            $messages['password_confirm'] = [
                'matches' => 'パスワードが一致しません。',
            ];
        }

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('error', '入力エラーがあります。')
                ->with('field_errors', $errors);
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => $this->request->getPost('phone'),
            'department' => $this->request->getPost('department'),
            'position'   => $this->request->getPost('position'),
            'role'       => $this->request->getPost('role'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // パスワードが入力された場合のみ更新
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->memberModel->update($id, $data);

        return redirect()->to('/members/' . $id)
            ->with('success', 'メンバー情報を更新しました。');
    }

    /**
     * メンバー削除処理
     */
    public function delete(int $id)
    {
        $member = $this->memberModel->find($id);

        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->memberModel->delete($id);

        return redirect()->to('/members')
            ->with('success', 'メンバーを削除しました。');
    }
}
