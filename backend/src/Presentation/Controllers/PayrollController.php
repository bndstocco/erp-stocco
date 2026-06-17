<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLPayrollRepository;

class PayrollController
{
    private MySQLPayrollRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLPayrollRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $payroll = $this->repository->findById($id);
        if (!$payroll) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Folha de pagamento não encontrada']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $payroll->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['employee_id']) || empty($data['period_start']) || empty($data['period_end'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Funcionário, período inicial e final são obrigatórios']);
            return;
        }

        $payroll = new \ErpStocco\Domain\Entities\Payroll(
            employeeId: (int) $data['employee_id'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end'],
            grossSalary: (float) ($data['gross_salary'] ?? 0),
            bonuses: (float) ($data['bonuses'] ?? 0),
            commissions: (float) ($data['commissions'] ?? 0),
            overtimePay: (float) ($data['overtime_pay'] ?? 0),
            inss: (float) ($data['inss'] ?? 0),
            irrf: (float) ($data['irrf'] ?? 0),
            fgts: (float) ($data['fgts'] ?? 0),
            otherDeductions: (float) ($data['other_deductions'] ?? 0),
            paymentMethod: $data['payment_method'] ?? 'transfer',
            status: $data['status'] ?? 'pending',
            notes: $data['notes'] ?? null,
        );

        $payroll->calculateNetSalary();
        $this->repository->save($payroll);
        http_response_code(201);
        echo json_encode(['error' => false, 'data' => $payroll->toArray()]);
    }

    public function update(int $id): void
    {
        $payroll = $this->repository->findById($id);
        if (!$payroll) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Folha de pagamento não encontrada']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['gross_salary'])) $payroll->setGrossSalary((float) $data['gross_salary']);
        if (isset($data['bonuses'])) $payroll->setBonuses((float) $data['bonuses']);
        if (isset($data['commissions'])) $payroll->setCommissions((float) $data['commissions']);
        if (isset($data['inss'])) $payroll->setInss((float) $data['inss']);
        if (isset($data['irrf'])) $payroll->setIrrf((float) $data['irrf']);
        if (isset($data['fgts'])) $payroll->setFgts((float) $data['fgts']);
        if (isset($data['other_deductions'])) $payroll->setOtherDeductions((float) $data['other_deductions']);
        if (isset($data['payment_date'])) $payroll->setPaymentDate($data['payment_date']);
        if (isset($data['status'])) $payroll->setStatus($data['status']);
        if (isset($data['notes'])) $payroll->setNotes($data['notes']);

        $payroll->calculateNetSalary();
        $this->repository->update($payroll);
        echo json_encode(['error' => false, 'data' => $payroll->toArray()]);
    }

    public function destroy(int $id): void
    {
        $payroll = $this->repository->findById($id);
        if (!$payroll) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Folha de pagamento não encontrada']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Folha de pagamento excluída com sucesso']);
    }

    public function byPeriod(): void
    {
        $year = (int) ($_GET['year'] ?? date('Y'));
        $month = (int) ($_GET['month'] ?? date('m'));

        $data = $this->repository->getPayrollByPeriod($year, $month);
        echo json_encode(['error' => false, 'data' => $data]);
    }
}
