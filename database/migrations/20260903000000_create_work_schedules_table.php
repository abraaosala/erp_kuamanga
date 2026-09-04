<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateWorkSchedulesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('work_schedules', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('check_in_time', 'time', ['null' => true])
            ->addColumn('check_out_time', 'time', ['null' => true])
            ->addColumn('break_minutes', 'integer', ['default' => 0])
            ->addColumn('days_of_week', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'ativo'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['name'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
