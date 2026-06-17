<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Product;

use ErpStocco\Domain\Repositories\ProductRepositoryInterface;

class ListProductsUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(array $filters = []): array
    {
        return $this->productRepository->findAll($filters);
    }
}
