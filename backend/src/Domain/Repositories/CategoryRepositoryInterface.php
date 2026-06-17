<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Category;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function findAll(array $filters = []): array;
    public function save(Category $category): Category;
    public function update(Category $category): Category;
    public function delete(int $id): bool;
    public function count(): int;
}
