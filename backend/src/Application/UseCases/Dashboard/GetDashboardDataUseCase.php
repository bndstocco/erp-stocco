<?php

declare(strict_types=1);

namespace ErpStocco\Application\UseCases\Dashboard;

use ErpStocco\Domain\Repositories\SaleRepositoryInterface;
use ErpStocco\Domain\Repositories\ProductRepositoryInterface;
use ErpStocco\Domain\Repositories\CustomerRepositoryInterface;
use ErpStocco\Domain\Repositories\AccountRepositoryInterface;
use ErpStocco\Domain\Repositories\EmployeeRepositoryInterface;

class GetDashboardDataUseCase
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private ProductRepositoryInterface $productRepository,
        private CustomerRepositoryInterface $customerRepository,
        private AccountRepositoryInterface $accountRepository,
        private EmployeeRepositoryInterface $employeeRepository,
    ) {}

    public function execute(): array
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        $salesThisMonth = $this->saleRepository->findAll([
            'date_from' => date('Y-m-01'),
            'date_to' => date('Y-m-t'),
            'status' => 'completed',
        ]);

        $monthlyRevenue = array_sum(array_map(fn($s) => $s['total'] ?? 0, is_array($salesThisMonth) ? $salesThisMonth : []));

        return [
            'summary' => [
                'total_products' => $this->productRepository->count(),
                'total_customers' => $this->customerRepository->count(),
                'total_employees' => $this->employeeRepository->count(),
                'total_balance' => $this->accountRepository->getTotalBalance(),
                'monthly_revenue' => $monthlyRevenue,
                'total_sales' => $this->saleRepository->count(['status' => 'completed']),
                'low_stock_products' => count($this->productRepository->findLowStock()),
            ],
            'monthly_revenue_chart' => $this->saleRepository->getMonthlyRevenue($currentYear),
            'top_products' => $this->saleRepository->getTopProducts(5),
            'recent_sales' => $this->saleRepository->findAll([
                'per_page' => 5,
                'status' => 'completed',
            ]),
        ];
    }
}
