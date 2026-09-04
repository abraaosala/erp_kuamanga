<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmployeeSchedulesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employee_schedules', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('work_schedule_id', 'integer', ['signed' => false])
            ->addColumn('is_default', 'boolean', ['default' => false])
            ->addColumn('start_date', 'date', ['null' => true])
            ->addColumn('end_date', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['work_schedule_id'])
            ->addIndex(['employee_id', 'work_schedule_id'], ['unique' => true])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('work_schedule_id', 'work_schedules', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
