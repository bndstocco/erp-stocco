<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Repositories;

use ErpStocco\Domain\Entities\Attendance;
use ErpStocco\Domain\Repositories\AttendanceRepositoryInterface;
use ErpStocco\Infrastructure\Database\Connection;
use ErpStocco\Infrastructure\Auth\UserContext;
use ErpStocco\Infrastructure\Database\QueryBuilder;

class MySQLAttendanceRepository implements AttendanceRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder(Connection::getInstance()->getPdo(), 'attendance');
    }

    public function findById(int $id): ?Attendance
    {
        $qb = clone $this->qb;
        $qb->select(['attendance.*', 'employees.first_name', 'employees.last_name']);
        $qb->join('employees', 'attendance.employee_id', '=', 'employees.id');
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('attendance.id', $id);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findByEmployeeAndDate(int $employeeId, string $date): ?Attendance
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->where('employee_id', $employeeId);
        $qb->where('date', $date);
        $data = $qb->first();
        return $data ? $this->hydrate($data) : null;
    }

    public function findAll(array $filters = []): array
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        $qb->select(['attendance.*', 'employees.first_name', 'employees.last_name', "CONCAT(employees.first_name, ' ', employees.last_name) as employee_name"])
           ->join('employees', 'attendance.employee_id', '=', 'employees.id');

        if (!empty($filters['employee_id'])) {
            $qb->where('attendance.employee_id', $filters['employee_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('attendance.status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $qb->where('attendance.date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $qb->where('attendance.date', '<=', $filters['date_to']);
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $qb->where('MONTH(attendance.date)', $filters['month']);
            $qb->where('YEAR(attendance.date)', $filters['year']);
        }

        $qb->orderBy('attendance.date', 'DESC');

        if (!empty($filters['per_page'])) {
            $page = $filters['page'] ?? 1;
            $result = $qb->paginate((int)$page, (int)$filters['per_page']);
            $result['data'] = array_map(fn($d) => $this->hydrate($d)->toArray(), $result['data']);
            return $result;
        }

        return array_map(fn($d) => $this->hydrate($d)->toArray(), $qb->get());
    }

    public function save(Attendance $attendance): Attendance
    {
        $id = $this->qb->insert([
            'created_by' => UserContext::getInstance()->getUserId(),
            'employee_id' => $attendance->getEmployeeId(),
            'date' => $attendance->getDate(),
            'check_in' => $attendance->getCheckIn(),
            'check_out' => $attendance->getCheckOut(),
            'lunch_start' => $attendance->getLunchStart(),
            'lunch_end' => $attendance->getLunchEnd(),
            'hours_worked' => $attendance->getHoursWorked(),
            'overtime' => $attendance->getOvertime(),
            'status' => $attendance->getStatus(),
            'notes' => $attendance->getNotes(),
        ]);
        $attendance->setId((int)$id);
        return $attendance;
    }

    public function update(Attendance $attendance): Attendance
    {
        $qb = clone $this->qb;
        $qb->where('id', $attendance->getId());
        $qb->update([
            'check_in' => $attendance->getCheckIn(),
            'check_out' => $attendance->getCheckOut(),
            'lunch_start' => $attendance->getLunchStart(),
            'lunch_end' => $attendance->getLunchEnd(),
            'hours_worked' => $attendance->getHoursWorked(),
            'overtime' => $attendance->getOvertime(),
            'status' => $attendance->getStatus(),
            'notes' => $attendance->getNotes(),
        ]);
        return $attendance;
    }

    public function delete(int $id): bool
    {
        $qb = clone $this->qb;
        return (bool) $qb->where('id', $id)->delete();
    }

    public function count(array $filters = []): int
    {
        $qb = clone $this->qb;
        $qb->where('created_by', UserContext::getInstance()->getUserId());
        if (!empty($filters['employee_id'])) {
            $qb->where('employee_id', $filters['employee_id']);
        }
        if (!empty($filters['status'])) {
            $qb->where('status', $filters['status']);
        }
        if (!empty($filters['month'])) {
            $qb->where('MONTH(date)', $filters['month']);
            $qb->where('YEAR(date)', $filters['year'] ?? date('Y'));
        }
        return $qb->count();
    }

    public function getMonthlyReport(int $employeeId, int $year, int $month): array
    {
        $pdo = Connection::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as days_present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as days_absent,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as days_late,
                SUM(hours_worked) as total_hours,
                SUM(overtime) as total_overtime
            FROM attendance 
            WHERE employee_id = :employee_id 
                AND YEAR(date) = :year 
                AND MONTH(date) = :month
                AND created_by = :created_by
        ");
        $stmt->execute([
            'employee_id' => $employeeId,
            'year' => $year,
            'month' => $month,
            'created_by' => UserContext::getInstance()->getUserId(),
        ]);
        return $stmt->fetch() ?: [];
    }

    private function hydrate(array $data): Attendance
    {
        return new Attendance(
            id: (int) $data['id'],
            employeeId: (int) $data['employee_id'],
            date: $data['date'],
            checkIn: $data['check_in'] ?? null,
            checkOut: $data['check_out'] ?? null,
            lunchStart: $data['lunch_start'] ?? null,
            lunchEnd: $data['lunch_end'] ?? null,
            hoursWorked: (float) ($data['hours_worked'] ?? 0),
            overtime: (float) ($data['overtime'] ?? 0),
            status: $data['status'],
            notes: $data['notes'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
