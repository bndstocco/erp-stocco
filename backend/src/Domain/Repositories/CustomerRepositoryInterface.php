<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Customer;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;
    public function findByDocument(string $document): ?Customer;
    public function findAll(array $filters = []): array;
    public function save(Customer $customer): Customer;
    public function update(Customer $customer): Customer;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
}
