<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Product;

use ErpStocco\Domain\Entities\Product;
use ErpStocco\Domain\Repositories\ProductRepositoryInterface;

class UpdateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(int $id, array $data): Product
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            throw new \RuntimeException('Produto não encontrado');
        }

        if (isset($data['sku']) && $data['sku'] !== $product->getSku()) {
            $existing = $this->productRepository->findBySku($data['sku']);
            if ($existing) {
                throw new \InvalidArgumentException('SKU já cadastrado');
            }
        }

        if (isset($data['name'])) $product->setName($data['name']);
        if (isset($data['description'])) $product->setDescription($data['description']);
        if (isset($data['sku'])) $product->setSku($data['sku']);
        if (isset($data['barcode'])) $product->setBarcode($data['barcode']);
        if (isset($data['category_id'])) $product->setCategoryId((int) $data['category_id']);
        if (isset($data['unit_price'])) $product->setUnitPrice((float) $data['unit_price']);
        if (isset($data['cost_price'])) $product->setCostPrice((float) $data['cost_price']);
        if (isset($data['min_stock'])) $product->setMinStock((int) $data['min_stock']);
        if (isset($data['max_stock'])) $product->setMaxStock(isset($data['max_stock']) ? (int) $data['max_stock'] : null);
        if (isset($data['unit'])) $product->setUnit($data['unit']);
        if (isset($data['weight'])) $product->setWeight(isset($data['weight']) ? (float) $data['weight'] : null);
        if (isset($data['status'])) $product->setStatus($data['status']);

        return $this->productRepository->update($product);
    }
}
