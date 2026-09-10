<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePayrollRunsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('payroll_runs', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('period_start', 'date')
            ->addColumn('period_end', 'date')
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'rascunho'])
            ->addColumn('total_gross', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0, 'null' => true])
            ->addColumn('total_deductions', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0, 'null' => true])
            ->addColumn('total_net', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0, 'null' => true])
            ->addColumn('employee_count', 'integer', ['signed' => false, 'default' => 0, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['period_start'])
            ->addIndex(['period_end'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}