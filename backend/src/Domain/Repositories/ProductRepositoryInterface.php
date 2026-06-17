<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findBySku(string $sku): ?Product;
    public function findAll(array $filters = []): array;
    public function save(Product $product): Product;
    public function update(Product $product): Product;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function findLowStock(): array;
    public function updateStock(int $id, int $quantity): bool;
}
