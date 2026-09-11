<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRosterTables extends AbstractMigration
{
    public function change(): void
    {
        $rotations = $this->table('rotations', ['signed' => false]);
        $rotations
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('pattern', 'text')
            ->addColumn('start_date', 'date', ['null' => true])
            ->addColumn('end_date', 'date', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'ativo'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['name'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $shifts = $this->table('scheduled_shifts', ['signed' => false]);
        $shifts
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('work_schedule_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('rotation_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('date', 'date')
            ->addColumn('classification', 'string', ['limit' => 20, 'default' => 'TRABALHO'])
            ->addColumn('source', 'string', ['limit' => 20, 'default' => 'GERADA'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['work_schedule_id'])
            ->addIndex(['rotation_id'])
            ->addIndex(['date'])
            ->addIndex(['employee_id', 'date'], ['unique' => true])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('work_schedule_id', 'work_schedules', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->addForeignKey('rotation_id', 'rotations', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
    }
}