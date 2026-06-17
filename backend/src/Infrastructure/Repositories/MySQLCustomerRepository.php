<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Customer;
use ErpStocco\Domain\Repositories\CustomerRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLCustomerRepository implements CustomerRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'customers');
    }

    public function findById(int $id): ?Customer
    {
        $data = (clone $this->qb)->where('id', $id)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findByDocument(string $document): ?Customer
    {
        $clean = preg_replace('/[^0-9]/', '', $document);
        $data = (clone $this->qb)->where('document', $clean)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        if (!empty($filters['search'])) {
            $qb->where(function($q) use ($filters) {
                $q->whereLike('name', $filters['search'])
                  ->orWhereLike('email', $filters['search'])
                  ->orWhereLike('phone', $filters['search'])
                  ->orWhereLike('document', $filters['search']);
            });
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        if (!empty($filters['city'])) {
            $qb->where('city', $filters['city']);
        }
        if (!empty($filters['state'])) {
            $qb->where('state', $filters['state']);
        }

        $qb->orderBy('created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Customer $customer): Customer
    {
        $id = $this->qb->insert([
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'document' => $customer->getDocument(),
            'address' => $customer->getAddress(),
            'city' => $customer->getCity(),
            'state' => $customer->getState(),
            'zipcode' => $customer->getZipcode(),
            'birth_date' => $customer->getBirthDate(),
            'notes' => $customer->getNotes(),
            'status' => $customer->getStatus(),
        ]);
        $customer->setId((int)$id);
        return $customer;
    }

    public function update(Customer $customer): Customer
    {
        $qb = clone $this->qb;
        $qb->where('id', $customer->getId());
        $qb->update([
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'document' => $customer->getDocument(),
            'address' => $customer->getAddress(),
            'city' => $customer->getCity(),
            'state' => $customer->getState(),
            'zipcode' => $customer->getZipcode(),
            'birth_date' => $customer->getBirthDate(),
            'notes' => $customer->getNotes(),
            'status' => $customer->getStatus(),
        ]);
        return $customer;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        return $qb->count();
    }

    private function hydrate(array $data): Customer
    {
        return new Customer(
            id: (int) $data['id'],
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            document: $data['document'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipcode: $data['zipcode'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            notes: $data['notes'] ?? null,
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
