<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Purchase;
use ErpStocco\Domain\Entities\PurchaseItem;
use ErpStocco\Domain\Repositories\PurchaseRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLPurchaseRepository implements PurchaseRepositoryInterface
{
    private QueryBuilder $qb;
    private QueryBuilder $itemQb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'purchases p');
        $this->itemQb = new QueryBuilder(Connection::getInstance()->getPdo(), 'purchase_items');
    }

    public function findById(int $id): ?Purchase
    {
        $data = (clone $this->qb)
            ->select(['p.*', 's.company_name as supplier_name'])
            ->join('suppliers s', 'p.supplier_id', '=', 's.id', 'LEFT')
            ->where('p.id', $id)
            ->first();

        if (!$data) return null;

        $purchase = $this->hydrate($data);
        $items = (clone $this->itemQb)->where('purchase_id', $id)->get();
        $purchase->setItems(array_map(fn($i) => $this->hydrateItem($i), $items));

        return $purchase;
    }

    public function findByPurchaseOrder(string $purchaseOrder): ?Purchase
    {
        $data = (clone $this->qb)->where('purchase_order', $purchaseOrder)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->select(['p.*', 's.company_name as supplier_name', 'u.name as user_name'])
           ->join('suppliers s', 'p.supplier_id', '=', 's.id', 'LEFT')
           ->join('users u', 'p.user_id', '=', 'u.id', 'LEFT');

        if (!empty($filters['search'])) {
            $qb->whereLike('p.purchase_order', $filters['search']);
        }
        if (!empty($filters['supplier_id'])) {
            $qb->where('p.supplier_id', $filters['supplier_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('p.status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $qb->where('p.purchase_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $qb->where('p.purchase_date', '<=', $filters['date_to']);
        }

        $qb->orderBy('p.created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Purchase $purchase): Purchase
    {
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        try {
            $id = $this->qb->insert([
                'purchase_order' => $purchase->getPurchaseOrder(),
                'supplier_id' => $purchase->getSupplierId(),
                'user_id' => $purchase->getUserId(),
                'subtotal' => $purchase->getSubtotal(),
                'discount' => $purchase->getDiscount(),
                'total' => $purchase->getTotal(),
                'status' => $purchase->getStatus(),
                'notes' => $purchase->getNotes(),
                'purchase_date' => $purchase->getPurchaseDate() ?? date('Y-m-d H:i:s'),
            ]);
            $purchase->setId((int)$id);

            foreach ($purchase->getItems() as $item) {
                $item->setPurchaseId($purchase->getId());
                $this->itemQb->insert([
                    'purchase_id' => $item->getPurchaseId(),
                    'product_id' => $item->getProductId(),
                    'product_name' => $item->getProductName(),
                    'quantity' => $item->getQuantity(),
                    'unit_price' => $item->getUnitPrice(),
                    'subtotal' => $item->getSubtotal(),
                ]);
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }

        return $purchase;
    }

    public function update(Purchase $purchase): Purchase
    {
        $qb = clone $this->qb;
        $qb->where('id', $purchase->getId());
        $qb->update([
            'supplier_id' => $purchase->getSupplierId(),
            'subtotal' => $purchase->getSubtotal(),
            'discount' => $purchase->getDiscount(),
            'total' => $purchase->getTotal(),
            'status' => $purchase->getStatus(),
            'notes' => $purchase->getNotes(),
        ]);
        return $purchase;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        return $qb->count();
    }

    private function hydrate(array $data): Purchase
    {
        return new Purchase(
            id: (int) $data['id'],
            purchaseOrder: $data['purchase_order'],
            supplierId: $data['supplier_id'] ? (int) $data['supplier_id'] : null,
            userId: (int) $data['user_id'],
            subtotal: (float) $data['subtotal'],
            discount: (float) $data['discount'],
            total: (float) $data['total'],
            status: $data['status'],
            notes: $data['notes'] ?? null,
            purchaseDate: $data['purchase_date'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    private function hydrateItem(array $data): PurchaseItem
    {
        return new PurchaseItem(
            id: (int) $data['id'],
            purchaseId: (int) $data['purchase_id'],
            productId: $data['product_id'] ? (int) $data['product_id'] : null,
            productName: $data['product_name'],
            quantity: (int) $data['quantity'],
            unitPrice: (float) $data['unit_price'],
            subtotal: (float) $data['subtotal'],
            createdAt: $data['created_at'] ?? null
        );
    }
}
