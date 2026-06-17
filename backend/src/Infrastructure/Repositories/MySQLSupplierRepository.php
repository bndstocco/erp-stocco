<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Supplier;
use ErpStocco\Domain\Repositories\SupplierRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLSupplierRepository implements SupplierRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'suppliers');
    }

    public function findById(int $id): ?Supplier
    {
        $data = (clone $this->qb)->where('id', $id)->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findByDocument(string $document): ?Supplier
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
                $q->whereLike('company_name', $filters['search'])
                  ->orWhereLike('contact_name', $filters['search'])
                  ->orWhereLike('email', $filters['search']);
            });
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        if (!empty($filters['state'])) {
            $qb->where('state', $filters['state']);
        }

        $qb->orderBy('company_name', 'ASC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Supplier $supplier): Supplier
    {
        $id = $this->qb->insert([
            'company_name' => $supplier->getCompanyName(),
            'contact_name' => $supplier->getContactName(),
            'email' => $supplier->getEmail(),
            'phone' => $supplier->getPhone(),
            'document' => $supplier->getDocument(),
            'address' => $supplier->getAddress(),
            'city' => $supplier->getCity(),
            'state' => $supplier->getState(),
            'zipcode' => $supplier->getZipcode(),
            'website' => $supplier->getWebsite(),
            'notes' => $supplier->getNotes(),
            'status' => $supplier->getStatus(),
        ]);
        $supplier->setId((int)$id);
        return $supplier;
    }

    public function update(Supplier $supplier): Supplier
    {
        $qb = clone $this->qb;
        $qb->where('id', $supplier->getId());
        $qb->update([
            'company_name' => $supplier->getCompanyName(),
            'contact_name' => $supplier->getContactName(),
            'email' => $supplier->getEmail(),
            'phone' => $supplier->getPhone(),
            'document' => $supplier->getDocument(),
            'address' => $supplier->getAddress(),
            'city' => $supplier->getCity(),
            'state' => $supplier->getState(),
            'zipcode' => $supplier->getZipcode(),
            'website' => $supplier->getWebsite(),
            'notes' => $supplier->getNotes(),
            'status' => $supplier->getStatus(),
        ]);
        return $supplier;
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

    private function hydrate(array $data): Supplier
    {
        return new Supplier(
            id: (int) $data['id'],
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
            status: $data['status'],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
