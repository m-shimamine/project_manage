<?php

namespace App\Controllers;

use App\Models\ProcessMasterModel;
use App\Services\ProcessMasterService;

class ProcessMasterController extends BaseController
{
    protected ProcessMasterModel $processModel;
    protected ProcessMasterService $processService;

    public function __construct()
    {
        $this->processModel = new ProcessMasterModel();
        $this->processService = new ProcessMasterService();
    }

    /**
     * 工程マスタ一覧表示
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $data = [
            'pageTitle'  => '工程マスタ',
            'processes'  => $this->processService->getProcessMasters($search, $status),
            'stats'      => $this->processService->getStats(),
            'search'     => $search,
            'status'     => $status,
        ];

        return view('process_masters/index', $data);
    }

    /**
     * 工程詳細取得（Ajax）
     */
    public function show(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $process,
        ]);
    }

    /**
     * 工程保存処理（Ajax）
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '工程名は必須です。',
                'max_length' => '工程名は255文字以内で入力してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '入力エラーがあります。',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $process = $this->processService->create($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を登録しました。',
            'data'    => $process,
        ]);
    }

    /**
     * 工程更新処理（Ajax）
     */
    public function update(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        $rules = [
            'name'   => 'required|max_length[255]',
            'status' => 'required|in_list[active,inactive]',
        ];

        $messages = [
            'name' => [
                'required'   => '工程名は必須です。',
                'max_length' => '工程名は255文字以内で入力してください。',
            ],
            'status' => [
                'required' => 'ステータスは必須です。',
                'in_list'  => '無効なステータスです。',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '入力エラーがあります。',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
        ];

        $updated = $this->processService->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を更新しました。',
            'data'    => $updated,
        ]);
    }

    /**
     * 工程削除処理（Ajax）
     */
    public function delete(int $id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $process = $this->processModel->find($id);
        if (!$process) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => '工程が見つかりません。',
            ]);
        }

        $this->processService->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => '工程を削除しました。',
        ]);
    }

    /**
     * 並び替え保存処理（Ajax）
     */
    public function reorder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => '不正なリクエストです。',
            ]);
        }

        $ordersJson = $this->request->getPost('orders');
        $orders = json_decode($ordersJson, true);

        if (empty($orders) || !is_array($orders)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => '並び順データが不正です。',
            ]);
        }

        $result = $this->processService->updateSortOrder($orders);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => '並び順を保存しました。',
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'success' => false,
            'message' => '保存に失敗しました。',
        ]);
    }
}
