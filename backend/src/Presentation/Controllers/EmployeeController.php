<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLEmployeeRepository;

class EmployeeController
{
    private MySQLEmployeeRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLEmployeeRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $employee = $this->repository->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Funcionário não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $employee->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Nome, sobrenome e email são obrigatórios']);
            return;
        }

        $employee = new \ErpStocco\Domain\Entities\Employee(
            userId: isset($data['user_id']) ? (int) $data['user_id'] : null,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            document: $data['document'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipcode: $data['zipcode'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            department: $data['department'] ?? null,
            position: $data['position'] ?? null,
            salary: (float) ($data['salary'] ?? 0),
            hireDate: !empty($data['hire_date']) ? $data['hire_date'] : date('Y-m-d'),
            terminationDate: $data['termination_date'] ?? null,
            status: $data['status'] ?? 'active',
        );

        $this->repository->save($employee);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $employee->toArray()]);
    }

    public function update(int $id): void
    {
        $employee = $this->repository->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Funcionário não encontrado']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['first_name'])) $employee->setFirstName($data['first_name']);
        if (isset($data['last_name'])) $employee->setLastName($data['last_name']);
        if (isset($data['email'])) $employee->setEmail($data['email']);
        if (isset($data['phone'])) $employee->setPhone($data['phone']);
        if (isset($data['document'])) $employee->setDocument($data['document']);
        if (isset($data['address'])) $employee->setAddress($data['address']);
        if (isset($data['city'])) $employee->setCity($data['city']);
        if (isset($data['state'])) $employee->setState($data['state']);
        if (isset($data['zipcode'])) $employee->setZipcode($data['zipcode']);
        if (isset($data['birth_date'])) $employee->setBirthDate($data['birth_date']);
        if (isset($data['department'])) $employee->setDepartment($data['department']);
        if (isset($data['position'])) $employee->setPosition($data['position']);
        if (isset($data['salary'])) $employee->setSalary((float) $data['salary']);
        if (isset($data['hire_date'])) $employee->setHireDate($data['hire_date']);
        if (isset($data['termination_date'])) $employee->setTerminationDate($data['termination_date']);
        if (isset($data['status'])) $employee->setStatus($data['status']);

        $this->repository->update($employee);
        echo json_encode(['error' => false, 'data' => $employee->toArray()]);
    }

    public function destroy(int $id): void
    {
        $employee = $this->repository->findById($id);
        if (!$employee) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Funcionário não encontrado']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Funcionário excluído com sucesso']);
    }
}
