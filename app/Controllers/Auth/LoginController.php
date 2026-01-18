<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class LoginController extends BaseController
{
    protected UserModel $userModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();
    }

    /**
     * ログインフォーム表示
     */
    public function showLoginForm()
    {
        // 既にログイン済みの場合はダッシュボードへ
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * ログイン処理
     */
    public function login()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        $messages = [
            'email' => [
                'required'    => 'メールアドレスを入力してください。',
                'valid_email' => '有効なメールアドレスを入力してください。',
            ],
            'password' => [
                'required'   => 'パスワードを入力してください。',
                'min_length' => 'パスワードは6文字以上で入力してください。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'メールアドレスまたはパスワードが正しくありません。');
        }

        // セッションにユーザー情報を保存
        $this->session->set([
            'user_id'   => $user['id'],
            'user_name' => $user['name'],
            'email'     => $user['email'],
            'logged_in' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'ログインしました。');
    }

    /**
     * ログアウト処理
     */
    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/login')->with('success', 'ログアウトしました。');
    }
}
