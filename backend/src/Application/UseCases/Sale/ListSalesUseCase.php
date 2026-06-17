<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Sale;

use ErpStocco\Domain\Repositories\SaleRepositoryInterface;

class ListSalesUseCase
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository
    ) {}

    public function execute(array $filters = []): array
    {
        return $this->saleRepository->findAll($filters);
    }
}
