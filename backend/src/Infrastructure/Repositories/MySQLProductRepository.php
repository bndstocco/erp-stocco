<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Product;
use ErpStocco\Domain\Repositories\ProductRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Auth\UserContext;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLProductRepository implements ProductRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'products');
    }

    public function findById(int $id): ?Product
    {
        $qb = clone $this->qb;
        $qb->select(['products.*', 'categories.name as category_name']);
        $qb->join('categories', 'products.category_id', '=', 'categories.id', 'LEFT');
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('products.id', $id);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findBySku(string $sku): ?Product
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('sku', $sku);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->select(['products.*', 'categories.name as category_name'])
           ->join('categories', 'products.category_id', '=', 'categories.id', 'LEFT');

        if (!empty($filters['search'])) {
            $qb->where(function($q) use ($filters) {
                $q->whereLike('products.name', $filters['search'])
                  ->orWhereLike('products.sku', $filters['search']);
            });
        }
        if (!empty($filters['category_id'])) {
            $qb->where('products.category_id', $filters['category_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('products.status', $filters['status']);
        }
        if (!empty($filters['low_stock'])) {
            $qb->whereColumn('products.stock_quantity', '<=', 'products.min_stock');
        }

        $qb->orderBy('products.created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Product $product): Product
    {
        $id = $this->qb->insert([
            'created_by' => UserContext::getInstance()->getUserId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'sku' => $product->getSku(),
            'barcode' => $product->getBarcode(),
            'category_id' => $product->getCategoryId(),
            'unit_price' => $product->getUnitPrice(),
            'cost_price' => $product->getCostPrice(),
            'stock_quantity' => $product->getStockQuantity(),
            'min_stock' => $product->getMinStock(),
            'max_stock' => $product->getMaxStock(),
            'unit' => $product->getUnit(),
            'weight' => $product->getWeight(),
            'status' => $product->getStatus(),
        ]);
        $product->setId((int)$id);
        return $product;
    }

    public function update(Product $product): Product
    {
        $qb = clone $this->qb;
        $qb->where('id', $product->getId());
        $qb->update([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'sku' => $product->getSku(),
            'barcode' => $product->getBarcode(),
            'category_id' => $product->getCategoryId(),
            'unit_price' => $product->getUnitPrice(),
            'cost_price' => $product->getCostPrice(),
            'min_stock' => $product->getMinStock(),
            'max_stock' => $product->getMaxStock(),
            'unit' => $product->getUnit(),
            'weight' => $product->getWeight(),
            'status' => $product->getStatus(),
        ]);
        return $product;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        if (!empty($filters['category_id'])) {
            $qb->where('category_id', $filters['category_id']);
        }
        return $qb->count();
    }

    public function findLowStock(): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $data = $qb->whereColumn('products.stock_quantity', '<=', 'products.min_stock')
                   ->where('products.status', 'active')
                   ->get();
        return array_map(fn($d) => $this->hydrate($d)->toArray(), $data);
    }

    public function updateStock(int $id, int $quantity): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->update(['stock_quantity' => $quantity]);
    }

    private function hydrate(array $data): Product
    {
        return new Product(
            id: (int) $data['id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            sku: $data['sku'],
            barcode: $data['barcode'] ?? null,
            categoryId: $data['category_id'] ? (int) $data['category_id'] : null,
            unitPrice: (float) $data['unit_price'],
            costPrice: (float) $data['cost_price'],
            stockQuantity: (int) $data['stock_quantity'],
            minStock: (int) $data['min_stock'],
            maxStock: $data['max_stock'] ? (int) $data['max_stock'] : null,
            unit: $data['unit'] ?? 'un',
            weight: $data['weight'] ? (float) $data['weight'] : null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
