<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RosterSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return ['RhSeeder', 'EmployeeSeeder'];
    }

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $startDate = date('Y-m-d');
        $endDate = (new DateTimeImmutable('+42 days'))->format('Y-m-d');

        @session_start();

        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE work_schedules');
        $this->execute('TRUNCATE employee_schedules');
        $this->execute('TRUNCATE rotations');
        $this->execute('TRUNCATE scheduled_shifts');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        $empresa = $this->fetchRow("SELECT id FROM empresas LIMIT 1");
        $empresaId = $empresa ? (int) $empresa['id'] : null;

        $workSchedulesTable = $this->table('work_schedules');
        $workSchedulesTable->insert([
            ['empresa_id' => $empresaId, 'name' => 'Turno Diurno',      'check_in_time' => '08:00:00', 'check_out_time' => '17:00:00', 'break_minutes' => 60, 'days_of_week' => '1,2,3,4,5,6,7', 'status' => 'ativo', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Turno Operacional', 'check_in_time' => '07:00:00', 'check_out_time' => '16:00:00', 'break_minutes' => 60, 'days_of_week' => '1,2,3,4,5,6,7', 'status' => 'ativo', 'created_at' => $now, 'updated_at' => $now],
        ])->saveData();

        $schedules = $this->fetchAll("SELECT id, name FROM work_schedules ORDER BY id");
        $diurnoId  = (int) ($schedules[0]['id'] ?? 0);
        $operId    = (int) ($schedules[1]['id'] ?? 0);

        $employees = $this->fetchAll(
            "SELECT e.id, p.name AS position
             FROM employees e
             JOIN positions p ON p.id = e.position_id
             ORDER BY e.id",
        );

        $operationalPositions = ['Auxiliar de Armazém', 'Chefe de Logística', 'Vendedor', 'Assistente de Marketing', 'Programador', 'Analista de TI'];

        $employeeSchedules = [];
        $dayIds = [];
        $opIds = [];
        foreach ($employees as $emp) {
            $isOperational = in_array((string) $emp['position'], $operationalPositions, true);
            $scheduleId = $isOperational ? $operId : $diurnoId;

            $employeeSchedules[] = [
                'empresa_id'       => $empresaId,
                'employee_id'      => (int) $emp['id'],
                'work_schedule_id' => $scheduleId,
                'is_default'       => true,
                'start_date'       => $startDate,
                'end_date'         => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            if ($isOperational) {
                $opIds[] = (int) $emp['id'];
            } else {
                $dayIds[] = (int) $emp['id'];
            }
        }

        $employeeSchedulesTable = $this->table('employee_schedules');
        $employeeSchedulesTable->insert($employeeSchedules)->saveData();
        echo "[1/3] " . count($employees) . " employees linked to work schedules\n";

        /** @var \App\Core\Application $app */
        $previousErrorReporting = error_reporting(E_ERROR | E_PARSE);
        try {
            $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        } finally {
            error_reporting($previousErrorReporting);
        }
        $_SESSION['empresa_id'] = $empresaId;
        /** @var \App\Services\Contracts\RosterServiceInterface $rosterService */
        $rosterService = $app->getContainer()->make(\App\Services\Contracts\RosterServiceInterface::class);

        $generated = 0;
        if ($diurnoId !== 0 && $dayIds !== []) {
            $rosterService->generate([
                'name'             => 'Rotação 5×2 — Administrativo',
                'pattern'          => [5, 2],
                'work_schedule_id' => $diurnoId,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'employee_ids'     => $dayIds,
            ]);
            $generated++;
        }

        if ($operId !== 0 && $opIds !== []) {
            $rosterService->generate([
                'name'             => 'Rotação 6×2 — Operações',
                'pattern'          => [6, 2],
                'work_schedule_id' => $operId,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'employee_ids'     => $opIds,
            ]);
            $generated++;
        }

        echo "[2/3] {$generated} rotations generated\n";

        $shiftCount = (int) $this->fetchRow('SELECT COUNT(*) AS c FROM scheduled_shifts')['c'];
        echo "[3/3] {$shiftCount} scheduled shifts materialized\n";
        echo "✅ Roster seeded successfully\n";
    }
}
