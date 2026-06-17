<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Purchase;

interface PurchaseRepositoryInterface
{
    public function findById(int $id): ?Purchase;
    public function findByPurchaseOrder(string $purchaseOrder): ?Purchase;
    public function findAll(array $filters = []): array;
    public function save(Purchase $purchase): Purchase;
    public function update(Purchase $purchase): Purchase;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
}
