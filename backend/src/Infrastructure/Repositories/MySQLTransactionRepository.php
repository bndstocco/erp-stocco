<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Transaction;
use ErpStocco\Domain\Repositories\TransactionRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLTransactionRepository implements TransactionRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'transactions t');
    }

    public function findById(int $id): ?Transaction
    {
        $data = (clone $this->qb)
            ->select(['t.*', 'a.name as account_name'])
            ->join('accounts a', 't.account_id', '=', 'a.id')
            ->where('t.id', $id)
            ->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->select(['t.*', 'a.name as account_name'])
           ->join('accounts a', 't.account_id', '=', 'a.id');

        if (!empty($filters['account_id'])) {
            $qb->where('t.account_id', $filters['account_id']);
        }
        if (!empty($filters['type'])) {
            $qb->where('t.type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $qb->where('t.category', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $qb->where('t.status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $qb->where('t.transaction_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $qb->where('t.transaction_date', '<=', $filters['date_to']);
        }

        $qb->orderBy('t.transaction_date', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Transaction $transaction): Transaction
    {
        $id = $this->qb->insert([
            'account_id' => $transaction->getAccountId(),
            'type' => $transaction->getType(),
            'category' => $transaction->getCategory(),
            'amount' => $transaction->getAmount(),
            'description' => $transaction->getDescription(),
            'destination_account_id' => $transaction->getDestinationAccountId(),
            'payment_method' => $transaction->getPaymentMethod(),
            'status' => $transaction->getStatus(),
            'transaction_date' => $transaction->getTransactionDate(),
        ]);
        $transaction->setId((int)$id);
        return $transaction;
    }

    public function update(Transaction $transaction): Transaction
    {
        $qb = clone $this->qb;
        $qb->where('id', $transaction->getId());
        $qb->update([
            'account_id' => $transaction->getAccountId(),
            'type' => $transaction->getType(),
            'category' => $transaction->getCategory(),
            'amount' => $transaction->getAmount(),
            'description' => $transaction->getDescription(),
            'payment_method' => $transaction->getPaymentMethod(),
            'status' => $transaction->getStatus(),
            'transaction_date' => $transaction->getTransactionDate(),
        ]);
        return $transaction;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        if (!empty($filters['type'])) {
            $qb->where('type', $filters['type']);
        }
        return $qb->count();
    }

    public function getIncomeExpenseByPeriod(string $startDate, string $endDate): array
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            SELECT 
                type,
                SUM(amount) as total,
                COUNT(*) as count
            FROM transactions 
            WHERE transaction_date BETWEEN :start_date AND :end_date
                AND status = 'completed'
            GROUP BY type
        ");
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        return $stmt->fetchAll();
    }

    public function getBalanceByAccount(int $accountId): float
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            FROM transactions 
            WHERE account_id = :account_id AND status = 'completed'
        ");
        $stmt->execute(['account_id' => $accountId]);
        $data = $stmt->fetch();
        return (float) (($data['income'] ?? 0) - ($data['expense'] ?? 0));
    }

    private function hydrate(array $data): Transaction
    {
        return new Transaction(
            id: (int) $data['id'],
            accountId: (int) $data['account_id'],
            type: $data['type'],
            category: $data['category'] ?? null,
            amount: (float) $data['amount'],
            description: $data['description'] ?? null,
            destinationAccountId: $data['destination_account_id'] ? (int) $data['destination_account_id'] : null,
            paymentMethod: $data['payment_method'] ?? 'cash',
            status: $data['status'],
            transactionDate: $data['transaction_date'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
