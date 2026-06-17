<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Category;
use ErpStocco\Domain\Repositories\CategoryRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'categories');
    }

    public function findById(int $id): ?Category
    {
        $data = (clone $this->qb)->where('id', $id)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        if (!empty($filters['search'])) {
            $qb->whereLike('name', $filters['search']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        $qb->orderBy('name', 'ASC');
        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Category $category): Category
    {
        $id = $this->qb->insert([
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'parent_id' => $category->getParentId(),
            'status' => $category->getStatus(),
        ]);
        $category->setId((int)$id);
        return $category;
    }

    public function update(Category $category): Category
    {
        $qb = clone $this->qb;
        $qb->where('id', $category->getId());
        $qb->update([
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'parent_id' => $category->getParentId(),
            'status' => $category->getStatus(),
        ]);
        return $category;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(): int
    {
        return (clone $this->qb)->count();
    }

    private function hydrate(array $data): Category
    {
        return new Category(
            id: (int) $data['id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ? (int) $data['parent_id'] : null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
