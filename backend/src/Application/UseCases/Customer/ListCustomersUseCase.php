<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Customer;

use ErpStocco\Domain\Repositories\CustomerRepositoryInterface;

class ListCustomersUseCase
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function execute(array $filters = []): array
    {
        return $this->customerRepository->findAll($filters);
    }
}
