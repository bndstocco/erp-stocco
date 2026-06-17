<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Sale;

use ErpStocco\Domain\Entities\Sale;
use ErpStocco\Domain\Entities\SaleItem;
use ErpStocco\Domain\Repositories\SaleRepositoryInterface;
use ErpStocco\Domain\Repositories\ProductRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;

class CreateSaleUseCase
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(array $data): Sale
    {
        $conn = Connection::getInstance();
        $conn->beginTransaction();

        try {
            $invoiceNumber = $this->generateInvoiceNumber();
            $items = [];

            foreach ($data['items'] as $itemData) {
                $product = $this->productRepository->findById((int) $itemData['product_id']);
                if (!$product) {
                    throw new \RuntimeException("Produto #{$itemData['product_id']} não encontrado");
                }

                if ($product->getStockQuantity() < $itemData['quantity']) {
                    throw new \RuntimeException("Estoque insuficiente para: {$product->getName()}");
                }

                $unitPrice = (float) ($itemData['unit_price'] ?? $product->getUnitPrice());
                $quantity = (int) $itemData['quantity'];

                $item = new SaleItem(
                    productId: $product->getId(),
                    productName: $product->getName(),
                    quantity: $quantity,
                    unitPrice: $unitPrice,
                    subtotal: $quantity * $unitPrice,
                );

                $items[] = $item;
                $product->removeStock($quantity);
                $this->productRepository->updateStock($product->getId(), $product->getStockQuantity());
            }

            $subtotal = array_sum(array_map(fn(SaleItem $i) => $i->getSubtotal(), $items));
            $discount = (float) ($data['discount'] ?? 0);
            $total = $subtotal - $discount;

            $sale = new Sale(
                invoiceNumber: $invoiceNumber,
                customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
                userId: (int) $data['user_id'],
                subtotal: $subtotal,
                discount: $discount,
                total: $total,
                paymentMethod: $data['payment_method'] ?? 'cash',
                installmentCount: (int) ($data['installment_count'] ?? 1),
                status: 'completed',
                notes: $data['notes'] ?? null,
                saleDate: date('Y-m-d H:i:s'),
            );

            foreach ($items as $item) {
                $sale->addItem($item);
            }

            $sale = $this->saleRepository->save($sale);

            $conn->commit();

            return $sale;
        } catch (\Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }

    private function generateInvoiceNumber(): string
    {
        return 'NF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
