<?php

declare(strict_types=1);

namespace ErpStocco\Presentation\Controllers;

use ErpStocco\Infrastructure\Repositories\MySQLAttendanceRepository;

class AttendanceController
{
    private MySQLAttendanceRepository $repository;

    public function __construct()
    {
        $this->repository = new MySQLAttendanceRepository();
    }

    public function index(): void
    {
        $filters = array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 15]);
        $result = $this->repository->findAll($filters);
        echo json_encode(['error' => false, 'data' => $result]);
    }

    public function show(int $id): void
    {
        $attendance = $this->repository->findById($id);
        if (!$attendance) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Registro não encontrado']);
            return;
        }
        echo json_encode(['error' => false, 'data' => $attendance->toArray()]);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['employee_id']) || empty($data['date'])) {
            http_response_code(400);
            echo json_encode(['error' => true, 'message' => 'Funcionário e data são obrigatórios']);
            return;
        }

        $existing = $this->repository->findByEmployeeAndDate((int) $data['employee_id'], $data['date']);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['error' => true, 'message' => 'Registro já existe para esta data']);
            return;
        }

        $attendance = new \ErpStocco\Domain\Entities\Attendance(
            employeeId: (int) $data['employee_id'],
            date: $data['date'],
            checkIn: $data['check_in'] ?? null,
            checkOut: $data['check_out'] ?? null,
            lunchStart: $data['lunch_start'] ?? null,
            lunchEnd: $data['lunch_end'] ?? null,
            status: $data['status'] ?? 'present',
            notes: $data['notes'] ?? null,
        );

        $attendance->calculateHours();

        try {
            $this->repository->save($attendance);
            http_response_code(201);
            echo json_encode(['error' => false, 'data' => $attendance->toArray()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => true, 'message' => 'Erro interno ao salvar registro']);
        }
    }

    public function update(int $id): void
    {
        $attendance = $this->repository->findById($id);
        if (!$attendance) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Registro não encontrado']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['check_in'])) $attendance->setCheckIn($data['check_in']);
        if (isset($data['check_out'])) $attendance->setCheckOut($data['check_out']);
        if (isset($data['lunch_start'])) $attendance->setLunchStart($data['lunch_start']);
        if (isset($data['lunch_end'])) $attendance->setLunchEnd($data['lunch_end']);
        if (isset($data['status'])) $attendance->setStatus($data['status']);
        if (isset($data['notes'])) $attendance->setNotes($data['notes']);

        $attendance->calculateHours();
        try {
            $this->repository->update($attendance);
            echo json_encode(['error' => false, 'data' => $attendance->toArray()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => true, 'message' => 'Erro interno ao atualizar registro']);
        }
    }

    public function destroy(int $id): void
    {
        $attendance = $this->repository->findById($id);
        if (!$attendance) {
            http_response_code(404);
            echo json_encode(['error' => true, 'message' => 'Registro não encontrado']);
            return;
        }

        $this->repository->delete($id);
        echo json_encode(['error' => false, 'message' => 'Registro excluído com sucesso']);
    }

    public function report(int $employeeId): void
    {
        $year = (int) ($_GET['year'] ?? date('Y'));
        $month = (int) ($_GET['month'] ?? date('m'));

        $report = $this->repository->getMonthlyReport($employeeId, $year, $month);
        echo json_encode(['error' => false, 'data' => $report]);
    }
}
