<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Product;

use ErpStocco\Domain\Repositories\ProductRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(int $id): bool
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            throw new \RuntimeException('Produto não encontrado');
        }

        return $this->productRepository->delete($id);
    }
}
