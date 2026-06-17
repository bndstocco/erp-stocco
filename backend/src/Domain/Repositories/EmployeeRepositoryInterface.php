<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Employee;

interface EmployeeRepositoryInterface
{
    public function findById(int $id): ?Employee;
    public function findByEmail(string $email): ?Employee;
    public function findAll(array $filters = []): array;
    public function save(Employee $employee): Employee;
    public function update(Employee $employee): Employee;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function getTotalPayroll(): float;
}
