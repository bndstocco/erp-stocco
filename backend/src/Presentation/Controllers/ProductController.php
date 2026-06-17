<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Application\UseCases\Product\CreateProductUseCase;
use ErpStocco\Application\UseCases\Product\ListProductsUseCase;
use ErpStocco\Application\UseCases\Product\UpdateProductUseCase;
use ErpStocco\Application\UseCases\Product\DeleteProductUseCase;
use ErpStocco\Infrastructure\Repositories\MySQLProductRepository;

class ProductController
{
    private CreateProductUseCase $createUseCase;
    private ListProductsUseCase $listUseCase;
    private UpdateProductUseCase $updateUseCase;
    private DeleteProductUseCase $deleteUseCase;
    private MySQLProductRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLProductRepository();
        $this->createUseCase = new CreateProductUseCase($this->repository);
        $this->listUseCase = new ListProductsUseCase($this->repository);
        $this->updateUseCase = new UpdateProductUseCase($this->repository);
        $this->deleteUseCase = new DeleteProductUseCase($this->repository);
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->listUseCase->execute($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Produto não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $product->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $product = $this->createUseCase->execute($data);
            http_response_code(201);
            echo json_encode(['error' => false, 'data' => $product->toArray()]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $product = $this->updateUseCase->execute($id, $data);
            echo json_encode(['error' => false, 'data' => $product->toArray()]);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(int $id): void
    {
        try {
            $this->deleteUseCase->execute($id);
            echo json_encode(['error' => false, 'message' => 'Produto excluído com sucesso']);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function lowStock(): void
    {
        $products = $this->repository->findLowStock();
        echo json_encode(['error' => false, 'data' => $products]);
    }
}
