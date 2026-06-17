<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function findById(int $id): ?Transaction;
    public function findAll(array $filters = []): array;
    public function save(Transaction $transaction): Transaction;
    public function update(Transaction $transaction): Transaction;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
    public function getIncomeExpenseByPeriod(string $startDate, string $endDate): array;
    public function getBalanceByAccount(int $accountId): float;
}
