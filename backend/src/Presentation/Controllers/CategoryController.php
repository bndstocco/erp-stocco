<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLCategoryRepository;

class CategoryController
{
    private MySQLCategoryRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLCategoryRepository();
    }

    public function index(): void
    {
        $filters = $_GET;
        $categories = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $categories]);
    }

    public function show(int $id): void
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Categoria não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $category->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome é obrigatório']);
            return;
        }

        $category = new \ErpStocco\Domain\Entities\Category(
            name: $data['name'],
            description: $data['description'] ?? null,
            parentId: isset($data['parent_id']) ? (int) $data['parent_id'] : null,
            status: $data['status'] ?? 'active',
        );

        $this->repository->save($category);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $category->toArray()]);
    }

    public function update(int $id): void
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Categoria não encontrada']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'])) $category->setName($data['name']);
        if (isset($data['description'])) $category->setDescription($data['description']);
        if (isset($data['parent_id'])) $category->setParentId((int) $data['parent_id']);
        if (isset($data['status'])) $category->setStatus($data['status']);

        $this->repository->update($category);
        echo json_encode(['error' => false, 'data' => $category->toArray()]);
    }

    public function destroy(int $id): void
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Categoria não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Categoria excluída com sucesso']);
    }
}
