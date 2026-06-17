<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLAccountRepository;

class AccountController
{
    private MySQLAccountRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLAccountRepository();
    }

    public function index(): void
    {
        $filters = $_GET;
        $accounts = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $accounts]);
    }

    public function show(int $id): void
    {
        $account = $this->repository->findById($id);
        if (!$account) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Conta não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $account->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['type'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome e tipo são obrigatórios']);
            return;
        }

        $account = new \ErpStocco\Domain\Entities\Account(
            name: $data['name'],
            type: $data['type'],
            balance: (float) ($data['balance'] ?? 0),
            bank: $data['bank'] ?? null,
            agency: $data['agency'] ?? null,
            accountNumber: $data['account_number'] ?? null,
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'active',
        );

        $this->repository->save($account);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $account->toArray()]);
    }

    public function update(int $id): void
    {
        $account = $this->repository->findById($id);
        if (!$account) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Conta não encontrada']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'])) $account->setName($data['name']);
        if (isset($data['type'])) $account->setType($data['type']);
        if (isset($data['bank'])) $account->setBank($data['bank']);
        if (isset($data['agency'])) $account->setAgency($data['agency']);
        if (isset($data['account_number'])) $account->setAccountNumber($data['account_number']);
        if (isset($data['description'])) $account->setDescription($data['description']);
        if (isset($data['status'])) $account->setStatus($data['status']);

        $this->repository->update($account);
        echo json_encode(['error' => false, 'data' => $account->toArray()]);
    }

    public function destroy(int $id): void
    {
        $account = $this->repository->findById($id);
        if (!$account) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Conta não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Conta excluída com sucesso']);
    }

    public function balance(): void
    {
        $total = $this->repository->getTotalBalance();
        echo json_encode(['error' => false, 'data' => ['total_balance' => $total]]);
    }
}
