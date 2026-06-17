<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Sale;
use ErpStocco\Domain\Entities\SaleItem;
use ErpStocco\Domain\Repositories\SaleRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Auth\UserContext;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLSaleRepository implements SaleRepositoryInterface
{
    private QueryBuilder $qb;
    private QueryBuilder $itemQb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'sales');
        $this->itemQb = new QueryBuilder(Connection::getInstance()->getPdo(), 'sale_items');
    }

    public function findById(int $id): ?Sale
    {
        $qb = clone $this->qb;
        $qb->select(['sales.*', 'customers.name as customer_name', 'users.name as user_name']);
        $qb->join('customers', 'sales.customer_id', '=', 'customers.id', 'LEFT');
        $qb->join('users', 'sales.user_id', '=', 'users.id', 'LEFT');
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('sales.id', $id);
        $data = $qb->first();

        if (!$data) return null;

        $sale = $this->hydrate($data);
        $items = (clone $this->itemQb)->where('sale_id', $id)->get();
        $sale->setItems(array_map(fn($i) => $this->hydrateItem($i), $items));

        return $sale;
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Sale
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('invoice_number', $invoiceNumber);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->select(['sales.*', 'customers.name as customer_name', 'users.name as user_name'])
           ->join('customers', 'sales.customer_id', '=', 'customers.id', 'LEFT')
           ->join('users', 'sales.user_id', '=', 'users.id', 'LEFT');

        if (!empty($filters['search'])) {
            $qb->whereLike('sales.invoice_number', $filters['search']);
        }
        if (!empty($filters['customer_id'])) {
            $qb->where('sales.customer_id', $filters['customer_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('sales.status', $filters['status']);
        }
        if (!empty($filters['payment_method'])) {
            $qb->where('sales.payment_method', $filters['payment_method']);
        }
        if (!empty($filters['date_from'])) {
            $qb->where('sales.sale_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $qb->where('sales.sale_date', '<=', $filters['date_to']);
        }

        $qb->orderBy('sales.created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Sale $sale): Sale
    {
        $id = $this->qb->insert([
            'created_by' => UserContext::getInstance()->getUserId(),
            'invoice_number' => $sale->getInvoiceNumber(),
            'customer_id' => $sale->getCustomerId(),
            'user_id' => $sale->getUserId(),
            'subtotal' => $sale->getSubtotal(),
            'discount' => $sale->getDiscount(),
            'total' => $sale->getTotal(),
            'payment_method' => $sale->getPaymentMethod(),
            'installment_count' => $sale->getInstallmentCount(),
            'status' => $sale->getStatus(),
            'notes' => $sale->getNotes(),
            'sale_date' => $sale->getSaleDate() ?? date('Y-m-d H:i:s'),
        ]);
        $sale->setId((int)$id);

        foreach ($sale->getItems() as $item) {
            $item->setSaleId($sale->getId());
            $this->itemQb->insert([
                'sale_id' => $item->getSaleId(),
                'product_id' => $item->getProductId(),
                'product_name' => $item->getProductName(),
                'quantity' => $item->getQuantity(),
                'unit_price' => $item->getUnitPrice(),
                'subtotal' => $item->getSubtotal(),
            ]);
        }

        return $sale;
    }

    public function update(Sale $sale): Sale
    {
        $qb = clone $this->qb;
        $qb->where('id', $sale->getId());
        $qb->update([
            'customer_id' => $sale->getCustomerId(),
            'subtotal' => $sale->getSubtotal(),
            'discount' => $sale->getDiscount(),
            'total' => $sale->getTotal(),
            'payment_method' => $sale->getPaymentMethod(),
            'status' => $sale->getStatus(),
            'notes' => $sale->getNotes(),
        ]);
        return $sale;
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
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        return $qb->count();
    }

    public function getMonthlyRevenue(int $year): array
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(sale_date) as month,
                COUNT(*) as total_sales,
                SUM(total) as revenue,
                SUM(discount) as total_discounts
            FROM sales 
            WHERE YEAR(sale_date) = :year 
                AND status = 'completed'
                AND created_by = :created_by
            GROUP BY MONTH(sale_date)
            ORDER BY month
        ");
        $stmt->execute(['year' => $year, 'created_by' => UserContext::getInstance()->getUserId()]);
        return $stmt->fetchAll();
    }

    public function getTopProducts(int $limit = 10): array
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            SELECT 
                sale_items.product_id,
                sale_items.product_name,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_revenue
            FROM sale_items
            JOIN sales ON sale_items.sale_id = sales.id
            WHERE sales.status = 'completed'
                AND sales.created_by = :created_by
            GROUP BY sale_items.product_id, sale_items.product_name
            ORDER BY total_quantity DESC
            LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute(['created_by' => UserContext::getInstance()->getUserId()]);
        return $stmt->fetchAll();
    }

    private function hydrate(array $data): Sale
    {
        return new Sale(
            id: (int) $data['id'],
            invoiceNumber: $data['invoice_number'],
            customerId: $data['customer_id'] ? (int) $data['customer_id'] : null,
            customerName: $data['customer_name'] ?? null,
            userId: (int) $data['user_id'],
            userName: $data['user_name'] ?? null,
            subtotal: (float) $data['subtotal'],
            discount: (float) $data['discount'],
            total: (float) $data['total'],
            paymentMethod: $data['payment_method'],
            installmentCount: (int) ($data['installment_count'] ?? 1),
            status: $data['status'],
            notes: $data['notes'] ?? null,
            saleDate: $data['sale_date'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    private function hydrateItem(array $data): SaleItem
    {
        return new SaleItem(
            id: (int) $data['id'],
            saleId: (int) $data['sale_id'],
            productId: $data['product_id'] ? (int) $data['product_id'] : null,
            productName: $data['product_name'],
            quantity: (int) $data['quantity'],
            unitPrice: (float) $data['unit_price'],
            subtotal: (float) $data['subtotal'],
            createdAt: $data['created_at'] ?? null
        );
    }
}
