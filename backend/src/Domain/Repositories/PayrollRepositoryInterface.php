<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Payroll;

interface PayrollRepositoryInterface
{
    public function findById(int $id): ?Payroll;
    public function findAll(array $filters = []): array;
    public function save(Payroll $payroll): Payroll;
    public function update(Payroll $payroll): Payroll;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function getPayrollByPeriod(int $year, int $month): array;
}
