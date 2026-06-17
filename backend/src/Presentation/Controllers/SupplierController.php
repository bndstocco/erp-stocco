<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLSupplierRepository;

class SupplierController
{
    private MySQLSupplierRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLSupplierRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $supplier = $this->repository->findById($id);
        if (!$supplier) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Fornecedor não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $supplier->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['company_name'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome da empresa é obrigatório']);
            return;
        }

        $supplier = new \ErpStocco\Domain\Entities\Supplier(
            companyName: $data['company_name'],
            contactName: $data['contact_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            document: $data['document'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipcode: $data['zipcode'] ?? null,
            website: $data['website'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'] ?? 'active',
        );

        $this->repository->save($supplier);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $supplier->toArray()]);
    }

    public function update(int $id): void
    {
        $supplier = $this->repository->findById($id);
        if (!$supplier) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Fornecedor não encontrado']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['company_name'])) $supplier->setCompanyName($data['company_name']);
        if (isset($data['contact_name'])) $supplier->setContactName($data['contact_name']);
        if (isset($data['email'])) $supplier->setEmail($data['email']);
        if (isset($data['phone'])) $supplier->setPhone($data['phone']);
        if (isset($data['document'])) $supplier->setDocument($data['document']);
        if (isset($data['address'])) $supplier->setAddress($data['address']);
        if (isset($data['city'])) $supplier->setCity($data['city']);
        if (isset($data['state'])) $supplier->setState($data['state']);
        if (isset($data['zipcode'])) $supplier->setZipcode($data['zipcode']);
        if (isset($data['website'])) $supplier->setWebsite($data['website']);
        if (isset($data['notes'])) $supplier->setNotes($data['notes']);
        if (isset($data['status'])) $supplier->setStatus($data['status']);

        $this->repository->update($supplier);
        echo json_encode(['error' => false, 'data' => $supplier->toArray()]);
    }

    public function destroy(int $id): void
    {
        $supplier = $this->repository->findById($id);
        if (!$supplier) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Fornecedor não encontrado']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Fornecedor excluído com sucesso']);
    }
}
