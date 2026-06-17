<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLTransactionRepository;
use ErpStocco\Infrastructure\Repositories\MySQLAccountRepository;

class TransactionController
{
    private MySQLTransactionRepository $repository;
    private MySQLAccountRepository $accountRepository;

    public function __construct()
    {
        $this->repository = new MySQLTransactionRepository();
        $this->accountRepository = new MySQLAccountRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $transaction = $this->repository->findById($id);
        if (!$transaction) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Transação não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $transaction->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['account_id']) || empty($data['type']) || empty($data['amount'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Conta, tipo e valor são obrigatórios']);
            return;
        }

        $account = $this->accountRepository->findById((int) $data['account_id']);
        if (!$account) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Conta não encontrada']);
            return;
        }

        $amount = (float) $data['amount'];
        $type = $data['type'];

        if ($type === 'expense' && $amount > $account->getBalance()) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Saldo insuficiente']);
            return;
        }

        $transaction = new \ErpStocco\Domain\Entities\Transaction(
            accountId: (int) $data['account_id'],
            type: $type,
            category: $data['category'] ?? null,
            amount: $amount,
            description: $data['description'] ?? null,
            destinationAccountId: isset($data['destination_account_id']) ? (int) $data['destination_account_id'] : null,
            paymentMethod: $data['payment_method'] ?? 'cash',
            status: $data['status'] ?? 'completed',
            transactionDate: $data['transaction_date'] ?? date('Y-m-d'),
        );

        $this->repository->save($transaction);

        if ($type === 'income') {
            $account->credit($amount);
        } elseif ($type === 'expense') {
            $account->debit($amount);
        }
        $this->accountRepository->update($account);

        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $transaction->toArray()]);
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $transaction = $this->repository->findById($id);

        if (!$transaction) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Transação não encontrada']);
            return;
        }

        if (isset($data['category'])) $transaction->setCategory($data['category']);
        if (isset($data['description'])) $transaction->setDescription($data['description']);
        if (isset($data['status'])) $transaction->setStatus($data['status']);
        if (isset($data['payment_method'])) $transaction->setPaymentMethod($data['payment_method']);

        $this->repository->update($transaction);
        echo json_encode(['error' => false, 'data' => $transaction->toArray()]);
    }

    public function destroy(int $id): void
    {
        $transaction = $this->repository->findById($id);
        if (!$transaction) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Transação não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Transação excluída com sucesso']);
    }

    public function report(): void
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $data = $this->repository->getIncomeExpenseByPeriod($startDate, $endDate);
        echo json_encode(['error' => false, 'data' => $data]);
    }
}
