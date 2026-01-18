<?php

namespace App\Services;

use App\Models\CustomerModel;

class CustomerService
{
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    /**
     * 顧客統計情報を取得
     */
    public function getStats(): array
    {
        return $this->customerModel->getStats();
    }

    /**
     * 顧客一覧を取得
     */
    public function getCustomers(?string $search = null, ?string $status = null): array
    {
        return $this->customerModel->getCustomers($search, $status);
    }

    /**
     * 顧客を作成
     */
    public function create(array $data): array
    {
        $this->customerModel->insert($data);
        return $this->customerModel->find($this->customerModel->getInsertID());
    }

    /**
     * 顧客を更新
     */
    public function update(int $id, array $data): array
    {
        $this->customerModel->update($id, $data);
        return $this->customerModel->find($id);
    }

    /**
     * 顧客を削除
     */
    public function delete(int $id): bool
    {
        return $this->customerModel->delete($id);
    }

    /**
     * IDで顧客を取得
     */
    public function findById(int $id): ?array
    {
        return $this->customerModel->find($id);
    }
}
