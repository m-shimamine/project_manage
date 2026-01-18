<?php

namespace App\Services;

use App\Models\MemberModel;

class MemberService
{
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    /**
     * メンバー統計情報を取得
     */
    public function getStats(): array
    {
        return $this->memberModel->getStats();
    }

    /**
     * メンバー一覧を取得
     */
    public function getMembers(?string $search = null, ?string $department = null, ?string $status = null): array
    {
        return $this->memberModel->getMembers($search, $department, $status);
    }

    /**
     * アクティブなメンバーを取得
     */
    public function getActiveMembers(): array
    {
        return $this->memberModel->getActiveMembers();
    }

    /**
     * メンバーを作成
     */
    public function create(array $data): array
    {
        $this->memberModel->insert($data);
        return $this->memberModel->find($this->memberModel->getInsertID());
    }

    /**
     * メンバーを更新
     */
    public function update(int $id, array $data): array
    {
        $this->memberModel->update($id, $data);
        return $this->memberModel->find($id);
    }

    /**
     * メンバーを削除
     */
    public function delete(int $id): bool
    {
        return $this->memberModel->delete($id);
    }

    /**
     * IDでメンバーを取得
     */
    public function findById(int $id): ?array
    {
        return $this->memberModel->find($id);
    }

    /**
     * 部署リストを取得
     */
    public function getDepartments(): array
    {
        return MemberModel::getDepartments();
    }
}
