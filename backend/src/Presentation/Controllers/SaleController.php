<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Application\UseCases\Sale\CreateSaleUseCase;
use ErpStocco\Application\UseCases\Sale\ListSalesUseCase;
use ErpStocco\Infrastructure\Repositories\MySQLSaleRepository;
use ErpStocco\Infrastructure\Repositories\MySQLProductRepository;

class SaleController
{
    private CreateSaleUseCase $createUseCase;
    private ListSalesUseCase $listUseCase;
    private MySQLSaleRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLSaleRepository();
        $productRepository = new MySQLProductRepository();
        $this->createUseCase = new CreateSaleUseCase($this->repository, $productRepository);
        $this->listUseCase = new ListSalesUseCase($this->repository);
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->listUseCase->execute($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $sale = $this->repository->findById($id);
        if (!$sale) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Venda não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $sale->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Itens da venda são obrigatórios']);
            return;
        }

        $data['user_id'] = $this->getUserId();

        try {
            $sale = $this->createUseCase->execute($data);
            http_response_code(201);
            echo json_encode(['error' => false, 'data' => $sale->toArray()]);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => true, 'message' => 'Erro interno ao salvar venda']);
        }
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $sale = $this->repository->findById($id);

        if (!$sale) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Venda não encontrada']);
            return;
        }

        if (isset($data['status'])) $sale->setStatus($data['status']);
        if (isset($data['payment_method'])) $sale->setPaymentMethod($data['payment_method']);
        if (isset($data['notes'])) $sale->setNotes($data['notes']);

        $this->repository->update($sale);
        echo json_encode(['error' => false, 'data' => $sale->toArray()]);
    }

    public function destroy(int $id): void
    {
        $sale = $this->repository->findById($id);
        if (!$sale) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Venda não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Venda cancelada com sucesso']);
    }

    private function getUserId(): int
    {
        return \ErpStocco\Infrastructure\Auth\UserContext::getInstance()->getUserId();
    }
}
