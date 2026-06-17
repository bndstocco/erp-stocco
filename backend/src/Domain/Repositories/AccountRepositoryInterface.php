<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\Account;

interface AccountRepositoryInterface
{
    public function findById(int $id): ?Account;
    public function findAll(array $filters = []): array;
    public function save(Account $account): Account;
    public function update(Account $account): Account;
    public function delete(int $id): bool;
    public function count(): int;
    public function getTotalBalance(): float;
}
