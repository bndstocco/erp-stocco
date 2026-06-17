<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Repositories;

use ErpStocco\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findAll(array $filters = []): array;
    public function save(User $user): User;
    public function update(User $user): User;
    public function delete(int $id): bool;
    public function count(array $filters = []): int;
}
