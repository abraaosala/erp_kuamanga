<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePayslipsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('payslips', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('payroll_run_id', 'integer', ['signed' => false])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('gross_salary', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('base_salary', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('overtime_amount', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('overtime_hours', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0])
            ->addColumn('absent_days', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => 0])
            ->addColumn('absent_deduction', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('net_salary', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'rascunho'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['payroll_run_id'])
            ->addIndex(['employee_id'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('payroll_run_id', 'payroll_runs', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}