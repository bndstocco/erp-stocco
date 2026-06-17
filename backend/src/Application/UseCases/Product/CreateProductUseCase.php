<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Product;

use ErpStocco\Domain\Entities\Product;
use ErpStocco\Domain\Repositories\ProductRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(array $data): Product
    {
        if ($this->productRepository->findBySku($data['sku'])) {
            throw new \InvalidArgumentException('SKU já cadastrado');
        }

        $product = new Product(
            name: $data['name'],
            description: $data['description'] ?? null,
            sku: $data['sku'],
            barcode: $data['barcode'] ?? null,
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            unitPrice: (float) ($data['unit_price'] ?? 0),
            costPrice: (float) ($data['cost_price'] ?? 0),
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            minStock: (int) ($data['min_stock'] ?? 0),
            maxStock: isset($data['max_stock']) ? (int) $data['max_stock'] : null,
            unit: $data['unit'] ?? 'un',
            weight: isset($data['weight']) ? (float) $data['weight'] : null,
            status: $data['status'] ?? 'active',
        );

        return $this->productRepository->save($product);
    }
}
