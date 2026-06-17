<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Supplier;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;
    public function findByDocument(string $document): ?Supplier;
    public function findAll(array $filters = []): array;
    public function save(Supplier $supplier): Supplier;
    public function update(Supplier $supplier): Supplier;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
}
