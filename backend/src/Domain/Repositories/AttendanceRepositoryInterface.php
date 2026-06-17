<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Attendance;

interface AttendanceRepositoryInterface
{
    public function findById(int $id): ?Attendance;
    public function findByEmployeeAndDate(int $employeeId, string $date): ?Attendance;
    public function findAll(array $filters = []): array;
    public function save(Attendance $attendance): Attendance;
    public function update(Attendance $attendance): Attendance;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function getMonthlyReport(int $employeeId, int $year, int $month): array;
}
