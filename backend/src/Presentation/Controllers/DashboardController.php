<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Application\UseCases\Dashboard\GetDashboardDataUseCase;
use ErpStocco\Infrastructure\Repositories\MySQLSaleRepository;
use ErpStocco\Infrastructure\Repositories\MySQLProductRepository;
use ErpStocco\Infrastructure\Repositories\MySQLCustomerRepository;
use ErpStocco\Infrastructure\Repositories\MySQLAccountRepository;
use ErpStocco\Infrastructure\Repositories\MySQLEmployeeRepository;

class DashboardController
{
    private GetDashboardDataUseCase $useCase;

    public function __construct()
    {
        $this->useCase = new GetDashboardDataUseCase(
            new MySQLSaleRepository(),
            new MySQLProductRepository(),
            new MySQLCustomerRepository(),
            new MySQLAccountRepository(),
            new MySQLEmployeeRepository(),
        );
    }

    public function index(): void
    {
        $data = $this->useCase->execute();
        echo json_encode(['error' => false, 'data' => $data]);
    }
}
