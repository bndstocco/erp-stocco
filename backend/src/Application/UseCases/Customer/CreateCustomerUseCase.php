<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Customer;

use ErpStocco\Domain\Entities\Customer;
use ErpStocco\Domain\Repositories\CustomerRepositoryInterface;

class CreateCustomerUseCase
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function execute(array $data): Customer
    {
        if (!empty($data['document'])) {
            $existing = $this->customerRepository->findByDocument($data['document']);
            if ($existing) {
                throw new \InvalidArgumentException('Documento já cadastrado');
            }
        }

        $customer = new Customer(
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            document: $data['document'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipcode: $data['zipcode'] ?? null,
            birthDate: !empty($data['birth_date']) ? $data['birth_date'] : null,
            notes: $data['notes'] ?? null,
            status: $data['status'] ?? 'active',
        );

        return $this->customerRepository->save($customer);
    }
}
