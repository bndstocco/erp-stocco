<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Payroll;
use ErpStocco\Domain\Repositories\PayrollRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLPayrollRepository implements PayrollRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'payroll');
    }

    public function findById(int $id): ?Payroll
    {
        $data = (clone $this->qb)
            ->select(['payroll.*', 'employees.first_name', 'employees.last_name', "CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"])
            ->join('employees', 'payroll.employee_id', '=', 'employees.id')
            ->where('payroll.id', $id)
            ->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->select(['payroll.*', 'employees.first_name', 'employees.last_name', "CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"])
           ->join('employees', 'payroll.employee_id', '=', 'employees.id');

        if (!empty($filters['employee_id'])) {
            $qb->where('payroll.employee_id', $filters['employee_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('payroll.status', $filters['status']);
        }
        if (!empty($filters['period_start'])) {
            $qb->where('payroll.period_start', '>=', $filters['period_start']);
        }
        if (!empty($filters['period_end'])) {
            $qb->where('payroll.period_end', '<=', $filters['period_end']);
        }

        $qb->orderBy('payroll.created_at', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Payroll $payroll): Payroll
    {
        $id = $this->qb->insert([
            'employee_id' => $payroll->getEmployeeId(),
            'period_start' => $payroll->getPeriodStart(),
            'period_end' => $payroll->getPeriodEnd(),
            'gross_salary' => $payroll->getGrossSalary(),
            'bonuses' => $payroll->getBonuses(),
            'commissions' => $payroll->getCommissions(),
            'overtime_pay' => $payroll->getOvertimePay(),
            'inss' => $payroll->getInss(),
            'irrf' => $payroll->getIrrf(),
            'fgts' => $payroll->getFgts(),
            'other_deductions' => $payroll->getOtherDeductions(),
            'net_salary' => $payroll->getNetSalary(),
            'payment_date' => $payroll->getPaymentDate(),
            'payment_method' => $payroll->getPaymentMethod(),
            'status' => $payroll->getStatus(),
            'notes' => $payroll->getNotes(),
        ]);
        $payroll->setId((int)$id);
        return $payroll;
    }

    public function update(Payroll $payroll): Payroll
    {
        $qb = clone $this->qb;
        $qb->where('id', $payroll->getId());
        $qb->update([
            'gross_salary' => $payroll->getGrossSalary(),
            'bonuses' => $payroll->getBonuses(),
            'commissions' => $payroll->getCommissions(),
            'overtime_pay' => $payroll->getOvertimePay(),
            'inss' => $payroll->getInss(),
            'irrf' => $payroll->getIrrf(),
            'fgts' => $payroll->getFgts(),
            'other_deductions' => $payroll->getOtherDeductions(),
            'net_salary' => $payroll->getNetSalary(),
            'payment_date' => $payroll->getPaymentDate(),
            'payment_method' => $payroll->getPaymentMethod(),
            'status' => $payroll->getStatus(),
            'notes' => $payroll->getNotes(),
        ]);
        return $payroll;
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

    public function getPayrollByPeriod(int $year, int $month): array
    {
        $pdo = Connection::getInstance()->getPdo();
        $periodStart = "{$year}-{$month}-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $stmt = $pdo->prepare("
            SELECT 
                SUM(gross_salary) as total_gross,
                SUM(bonuses) as total_bonuses,
                SUM(commissions) as total_commissions,
                SUM(inss) as total_inss,
                SUM(irrf) as total_irrf,
                SUM(fgts) as total_fgts,
                SUM(other_deductions) as total_deductions,
                SUM(net_salary) as total_net,
                COUNT(*) as total_employees
            FROM payroll 
            WHERE period_start = :period_start 
                AND period_end = :period_end
        ");
        $stmt->execute([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);
        return $stmt->fetch() ?: [];
    }

    private function hydrate(array $data): Payroll
    {
        return new Payroll(
            id: (int) $data['id'],
            employeeId: (int) $data['employee_id'],
            periodStart: $data['period_start'],
            periodEnd: $data['period_end'],
            grossSalary: (float) $data['gross_salary'],
            bonuses: (float) ($data['bonuses'] ?? 0),
            commissions: (float) ($data['commissions'] ?? 0),
            overtimePay: (float) ($data['overtime_pay'] ?? 0),
            inss: (float) ($data['inss'] ?? 0),
            irrf: (float) ($data['irrf'] ?? 0),
            fgts: (float) ($data['fgts'] ?? 0),
            otherDeductions: (float) ($data['other_deductions'] ?? 0),
            netSalary: (float) $data['net_salary'],
            paymentDate: $data['payment_date'] ?? null,
            paymentMethod: $data['payment_method'] ?? 'transfer',
            status: $data['status'],
            notes: $data['notes'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
