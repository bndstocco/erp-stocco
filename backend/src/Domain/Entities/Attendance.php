<?php

declare(strict_types=1);

namespace ErpStocco\Domain\Entities;

class Attendance
{
    private ?int $id;
    private int $employeeId;
    private string $date;
    private ?string $checkIn;
    private ?string $checkOut;
    private ?string $lunchStart;
    private ?string $lunchEnd;
    private float $hoursWorked;
    private float $overtime;
    private string $status;
    private ?string $notes;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        int $employeeId = 0,
        string $date = '',
        ?string $checkIn = null,
        ?string $checkOut = null,
        ?string $lunchStart = null,
        ?string $lunchEnd = null,
        float $hoursWorked = 0.0,
        float $overtime = 0.0,
        string $status = 'present',
        ?string $notes = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->date = $date;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->lunchStart = $lunchStart;
        $this->lunchEnd = $lunchEnd;
        $this->hoursWorked = $hoursWorked;
        $this->overtime = $overtime;
        $this->status = $status;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployeeId(): int { return $this->employeeId; }
    public function getDate(): string { return $this->date; }
    public function getCheckIn(): ?string { return $this->checkIn; }
    public function getCheckOut(): ?string { return $this->checkOut; }
    public function getLunchStart(): ?string { return $this->lunchStart; }
    public function getLunchEnd(): ?string { return $this->lunchEnd; }
    public function getHoursWorked(): float { return $this->hoursWorked; }
    public function getOvertime(): float { return $this->overtime; }
    public function getStatus(): string { return $this->status; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setEmployeeId(int $employeeId): void { $this->employeeId = $employeeId; }
    public function setDate(string $date): void { $this->date = $date; }
    public function setCheckIn(?string $checkIn): void { $this->checkIn = $checkIn; }
    public function setCheckOut(?string $checkOut): void { $this->checkOut = $checkOut; }
    public function setLunchStart(?string $lunchStart): void { $this->lunchStart = $lunchStart; }
    public function setLunchEnd(?string $lunchEnd): void { $this->lunchEnd = $lunchEnd; }
    public function setHoursWorked(float $hoursWorked): void { $this->hoursWorked = $hoursWorked; }
    public function setOvertime(float $overtime): void { $this->overtime = $overtime; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }

    public function calculateHours(): void
    {
        if ($this->checkIn && $this->checkOut) {
            $start = strtotime($this->checkIn);
            $end = strtotime($this->checkOut);
            $totalMinutes = ($end - $start) / 60;

            if ($this->lunchStart && $this->lunchEnd) {
                $lunchStart = strtotime($this->lunchStart);
                $lunchEnd = strtotime($this->lunchEnd);
                $totalMinutes -= ($lunchEnd - $lunchStart) / 60;
            }

            $this->hoursWorked = round($totalMinutes / 60, 2);

            if ($this->hoursWorked > 8) {
                $this->overtime = round($this->hoursWorked - 8, 2);
            }
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'date' => $this->date,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'lunch_start' => $this->lunchStart,
            'lunch_end' => $this->lunchEnd,
            'hours_worked' => $this->hoursWorked,
            'overtime' => $this->overtime,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
