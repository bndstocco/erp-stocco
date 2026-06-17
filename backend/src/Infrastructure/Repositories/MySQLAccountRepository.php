<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Account;
use ErpStocco\Domain\Repositories\AccountRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Auth\UserContext;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLAccountRepository implements AccountRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'accounts');
    }

    public function findById(int $id): ?Account
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('id', $id);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        if (!empty($filters['type'])) {
            $qb->where('type', $filters['type']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        $qb->orderBy('name', 'ASC');
        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Account $account): Account
    {
        $id = $this->qb->insert([
            'created_by' => UserContext::getInstance()->getUserId(),
            'name' => $account->getName(),
            'type' => $account->getType(),
            'balance' => $account->getBalance(),
            'bank' => $account->getBank(),
            'agency' => $account->getAgency(),
            'account_number' => $account->getAccountNumber(),
            'description' => $account->getDescription(),
            'status' => $account->getStatus(),
        ]);
        $account->setId((int)$id);
        return $account;
    }

    public function update(Account $account): Account
    {
        $qb = clone $this->qb;
        $qb->where('id', $account->getId());
        $qb->update([
            'name' => $account->getName(),
            'type' => $account->getType(),
            'balance' => $account->getBalance(),
            'bank' => $account->getBank(),
            'agency' => $account->getAgency(),
            'account_number' => $account->getAccountNumber(),
            'description' => $account->getDescription(),
            'status' => $account->getStatus(),
        ]);
        return $account;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(): int
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        return $qb->count();
    }

    public function getTotalBalance(): float
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("SELECT SUM(balance) as total FROM accounts WHERE status = 'active' AND created_by = :created_by");
        $stmt->execute(['created_by' => UserContext::getInstance()->getUserId()]);
        return (float) ($stmt->fetch()['total'] ?? 0);
    }

    private function hydrate(array $data): Account
    {
        return new Account(
            id: (int) $data['id'],
            name: $data['name'],
            type: $data['type'],
            balance: (float) $data['balance'],
            bank: $data['bank'] ?? null,
            agency: $data['agency'] ?? null,
            accountNumber: $data['account_number'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
