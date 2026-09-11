<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLeaveTables extends AbstractMigration
{
    public function change(): void
    {
        $requests = $this->table('leave_requests', ['signed' => false]);
        $requests
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('leave_type', 'string', ['limit' => 30, 'default' => 'ferias'])
            ->addColumn('start_date', 'date')
            ->addColumn('end_date', 'date')
            ->addColumn('days', 'integer', ['default' => 0])
            ->addColumn('reason', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'pendente'])
            ->addColumn('decided_by', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('decided_at', 'timestamp', ['null' => true])
            ->addColumn('decision_notes', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['status'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

        $leaves = $this->table('leaves', ['signed' => false]);
        $leaves
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('leave_request_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('leave_type', 'string', ['limit' => 30, 'default' => 'ferias'])
            ->addColumn('start_date', 'date')
            ->addColumn('end_date', 'date')
            ->addColumn('days', 'integer', ['default' => 0])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'gozada'])
            ->addColumn('observations', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['leave_request_id'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('leave_request_id', 'leave_requests', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
    }
}
