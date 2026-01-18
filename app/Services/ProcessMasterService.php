<?php

namespace App\Services;

use App\Models\ProcessMasterModel;

class ProcessMasterService
{
    protected ProcessMasterModel $processModel;

    public function __construct()
    {
        $this->processModel = new ProcessMasterModel();
    }

    /**
     * 統計情報を取得
     */
    public function getStats(): array
    {
        return $this->processModel->getStats();
    }

    /**
     * 工程一覧を取得
     */
    public function getProcessMasters(?string $search = null, ?string $status = null): array
    {
        return $this->processModel->getProcessMasters($search, $status);
    }

    /**
     * 有効な工程を取得
     */
    public function getActiveProcessMasters(): array
    {
        return $this->processModel->getActiveProcessMasters();
    }

    /**
     * IDで取得
     */
    public function findById(int $id): ?array
    {
        return $this->processModel->find($id);
    }

    /**
     * 工程を作成
     */
    public function create(array $data): array
    {
        // sort_orderが未設定の場合、末尾に追加
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->processModel->getNextSortOrder();
        }

        $this->processModel->insert($data);
        return $this->processModel->find($this->processModel->getInsertID());
    }

    /**
     * 工程を更新
     */
    public function update(int $id, array $data): ?array
    {
        $this->processModel->update($id, $data);
        return $this->processModel->find($id);
    }

    /**
     * 工程を削除
     */
    public function delete(int $id): bool
    {
        return $this->processModel->delete($id);
    }

    /**
     * 並び順を更新
     */
    public function updateSortOrder(array $orders): bool
    {
        return $this->processModel->updateSortOrder($orders);
    }
}
