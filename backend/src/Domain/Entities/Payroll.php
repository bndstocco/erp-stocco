<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Payroll
{
    private ?int $id;
    private int $employeeId;
    private string $periodStart;
    private string $periodEnd;
    private float $grossSalary;
    private float $bonuses;
    private float $commissions;
    private float $overtimePay;
    private float $inss;
    private float $irrf;
    private float $fgts;
    private float $otherDeductions;
    private float $netSalary;
    private ?string $paymentDate;
    private string $paymentMethod;
    private string $status;
    private ?string $notes;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        int $employeeId = 0,
        string $periodStart = '',
        string $periodEnd = '',
        float $grossSalary = 0.0,
        float $bonuses = 0.0,
        float $commissions = 0.0,
        float $overtimePay = 0.0,
        float $inss = 0.0,
        float $irrf = 0.0,
        float $fgts = 0.0,
        float $otherDeductions = 0.0,
        float $netSalary = 0.0,
        ?string $paymentDate = null,
        string $paymentMethod = 'transfer',
        string $status = 'pending',
        ?string $notes = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->grossSalary = $grossSalary;
        $this->bonuses = $bonuses;
        $this->commissions = $commissions;
        $this->overtimePay = $overtimePay;
        $this->inss = $inss;
        $this->irrf = $irrf;
        $this->fgts = $fgts;
        $this->otherDeductions = $otherDeductions;
        $this->netSalary = $netSalary;
        $this->paymentDate = $paymentDate;
        $this->paymentMethod = $paymentMethod;
        $this->status = $status;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployeeId(): int { return $this->employeeId; }
    public function getPeriodStart(): string { return $this->periodStart; }
    public function getPeriodEnd(): string { return $this->periodEnd; }
    public function getGrossSalary(): float { return $this->grossSalary; }
    public function getBonuses(): float { return $this->bonuses; }
    public function getCommissions(): float { return $this->commissions; }
    public function getOvertimePay(): float { return $this->overtimePay; }
    public function getInss(): float { return $this->inss; }
    public function getIrrf(): float { return $this->irrf; }
    public function getFgts(): float { return $this->fgts; }
    public function getOtherDeductions(): float { return $this->otherDeductions; }
    public function getNetSalary(): float { return $this->netSalary; }
    public function getPaymentDate(): ?string { return $this->paymentDate; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function getStatus(): string { return $this->status; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setEmployeeId(int $employeeId): void { $this->employeeId = $employeeId; }
    public function setPeriodStart(string $periodStart): void { $this->periodStart = $periodStart; }
    public function setPeriodEnd(string $periodEnd): void { $this->periodEnd = $periodEnd; }
    public function setGrossSalary(float $grossSalary): void { $this->grossSalary = $grossSalary; }
    public function setBonuses(float $bonuses): void { $this->bonuses = $bonuses; }
    public function setCommissions(float $commissions): void { $this->commissions = $commissions; }
    public function setOvertimePay(float $overtimePay): void { $this->overtimePay = $overtimePay; }
    public function setInss(float $inss): void { $this->inss = $inss; }
    public function setIrrf(float $irrf): void { $this->irrf = $irrf; }
    public function setFgts(float $fgts): void { $this->fgts = $fgts; }
    public function setOtherDeductions(float $otherDeductions): void { $this->otherDeductions = $otherDeductions; }
    public function setNetSalary(float $netSalary): void { $this->netSalary = $netSalary; }
    public function setPaymentDate(?string $paymentDate): void { $this->paymentDate = $paymentDate; }
    public function setPaymentMethod(string $paymentMethod): void { $this->paymentMethod = $paymentMethod; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }

    public function calculateNetSalary(): void
    {
        $totalEarnings = $this->grossSalary + $this->bonuses + $this->commissions + $this->overtimePay;
        $totalDeductions = $this->inss + $this->irrf + $this->fgts + $this->otherDeductions;
        $this->netSalary = round($totalEarnings - $totalDeductions, 2);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'gross_salary' => $this->grossSalary,
            'bonuses' => $this->bonuses,
            'commissions' => $this->commissions,
            'overtime_pay' => $this->overtimePay,
            'inss' => $this->inss,
            'irrf' => $this->irrf,
            'fgts' => $this->fgts,
            'other_deductions' => $this->otherDeductions,
            'net_salary' => $this->netSalary,
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
