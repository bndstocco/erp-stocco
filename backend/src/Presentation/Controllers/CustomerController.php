<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Application\UseCases\Customer\CreateCustomerUseCase;
use ErpStocco\Application\UseCases\Customer\ListCustomersUseCase;
use ErpStocco\Infrastructure\Repositories\MySQLCustomerRepository;

class CustomerController
{
    private CreateCustomerUseCase $createUseCase;
    private ListCustomersUseCase $listUseCase;
    private MySQLCustomerRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLCustomerRepository();
        $this->createUseCase = new CreateCustomerUseCase($this->repository);
        $this->listUseCase = new ListCustomersUseCase($this->repository);
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->listUseCase->execute($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $customer = $this->repository->findById($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Cliente não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $customer->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome é obrigatório']);
            return;
        }

        try {
            $customer = $this->createUseCase->execute($data);
            http_response_code(201);
            echo json_encode(['error' => false, 'data' => $customer->toArray()]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
        }
    }

    public function update(int $id): void
    {
        $customer = $this->repository->findById($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Cliente não encontrado']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'])) $customer->setName($data['name']);
        if (isset($data['email'])) $customer->setEmail($data['email']);
        if (isset($data['phone'])) $customer->setPhone($data['phone']);
        if (isset($data['document'])) $customer->setDocument($data['document']);
        if (isset($data['address'])) $customer->setAddress($data['address']);
        if (isset($data['city'])) $customer->setCity($data['city']);
        if (isset($data['state'])) $customer->setState($data['state']);
        if (isset($data['zipcode'])) $customer->setZipcode($data['zipcode']);
        if (isset($data['notes'])) $customer->setNotes($data['notes']);
        if (isset($data['status'])) $customer->setStatus($data['status']);

        $this->repository->update($customer);
        echo json_encode(['error' => false, 'data' => $customer->toArray()]);
    }

    public function destroy(int $id): void
    {
        $customer = $this->repository->findById($id);
        if (!$customer) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Cliente não encontrado']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Cliente excluído com sucesso']);
    }
}
