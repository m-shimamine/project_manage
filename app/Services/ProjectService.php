<?php

namespace App\Services;

use App\Models\ProjectModel;

class ProjectService
{
    protected ProjectModel $projectModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
    }

    /**
     * プロジェクト統計を取得
     */
    public function getStats(): array
    {
        return $this->projectModel->getStats();
    }

    /**
     * プロジェクト一覧を取得
     */
    public function getProjects(?string $search = null, ?string $status = null, ?int $customerId = null): array
    {
        return $this->projectModel->getProjects($search, $status, $customerId);
    }

    /**
     * プロジェクトを作成
     */
    public function create(array $data): array
    {
        $this->projectModel->insert($data);
        return $this->projectModel->find($this->projectModel->getInsertID());
    }

    /**
     * プロジェクトを更新
     */
    public function update(int $id, array $data): array
    {
        $this->projectModel->update($id, $data);
        return $this->projectModel->find($id);
    }

    /**
     * プロジェクトを削除
     */
    public function delete(int $id): bool
    {
        return $this->projectModel->delete($id);
    }

    /**
     * IDでプロジェクトを取得（顧客情報込み）
     */
    public function findById(int $id): ?array
    {
        return $this->projectModel->getProjectWithCustomer($id);
    }
}
