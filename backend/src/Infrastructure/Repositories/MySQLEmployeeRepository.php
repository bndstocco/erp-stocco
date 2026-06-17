<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Employee;
use ErpStocco\Domain\Repositories\EmployeeRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Auth\UserContext;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLEmployeeRepository implements EmployeeRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'employees');
    }

    public function findById(int $id): ?Employee
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('id', $id);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findByEmail(string $email): ?Employee
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('email', $email);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        if (!empty($filters['search'])) {
            $qb->where(function($q) use ($filters) {
                $q->whereLike('first_name', $filters['search'])
                  ->orWhereLike('last_name', $filters['search'])
                  ->orWhereLike('email', $filters['search'])
                  ->orWhereLike('document', $filters['search']);
            });
        }
        if (!empty($filters['department'])) {
            $qb->where('department', $filters['department']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }

        $qb->orderBy('first_name', 'ASC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Employee $employee): Employee
    {
        $id = $this->qb->insert([
            'created_by' => UserContext::getInstance()->getUserId(),
            'user_id' => $employee->getUserId(),
            'first_name' => $employee->getFirstName(),
            'last_name' => $employee->getLastName(),
            'email' => $employee->getEmail(),
            'phone' => $employee->getPhone(),
            'document' => $employee->getDocument(),
            'address' => $employee->getAddress(),
            'city' => $employee->getCity(),
            'state' => $employee->getState(),
            'zipcode' => $employee->getZipcode(),
            'birth_date' => $employee->getBirthDate(),
            'department' => $employee->getDepartment(),
            'position' => $employee->getPosition(),
            'salary' => $employee->getSalary(),
            'hire_date' => $employee->getHireDate(),
            'termination_date' => $employee->getTerminationDate(),
            'status' => $employee->getStatus(),
        ]);
        $employee->setId((int)$id);
        return $employee;
    }

    public function update(Employee $employee): Employee
    {
        $qb = clone $this->qb;
        $qb->where('id', $employee->getId());
        $qb->update([
            'user_id' => $employee->getUserId(),
            'first_name' => $employee->getFirstName(),
            'last_name' => $employee->getLastName(),
            'email' => $employee->getEmail(),
            'phone' => $employee->getPhone(),
            'document' => $employee->getDocument(),
            'address' => $employee->getAddress(),
            'city' => $employee->getCity(),
            'state' => $employee->getState(),
            'zipcode' => $employee->getZipcode(),
            'birth_date' => $employee->getBirthDate(),
            'department' => $employee->getDepartment(),
            'position' => $employee->getPosition(),
            'salary' => $employee->getSalary(),
            'hire_date' => $employee->getHireDate(),
            'termination_date' => $employee->getTerminationDate(),
            'status' => $employee->getStatus(),
        ]);
        return $employee;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        if (!empty($filters['department'])) {
            $qb->where('department', $filters['department']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        return $qb->count();
    }

    public function getTotalPayroll(): float
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("SELECT SUM(salary) as total FROM employees WHERE status = 'active' AND created_by = :created_by");
        $stmt->execute(['created_by' => UserContext::getInstance()->getUserId()]);
        return (float) ($stmt->fetch()['total'] ?? 0);
    }

    private function hydrate(array $data): Employee
    {
        return new Employee(
            id: (int) $data['id'],
            userId: $data['user_id'] ? (int) $data['user_id'] : null,
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
            salary: (float) $data['salary'],
            hireDate: $data['hire_date'],
            terminationDate: $data['termination_date'] ?? null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
