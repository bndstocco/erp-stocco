<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Sale;

interface SaleRepositoryInterface
{
    public function findById(int $id): ?Sale;
    public function findByInvoiceNumber(string $invoiceNumber): ?Sale;
    public function findAll(array $filters = []): array;
    public function save(Sale $sale): Sale;
    public function update(Sale $sale): Sale;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function getMonthlyRevenue(int $year): array;
    public function getTopProducts(int $limit = 10): array;
}
