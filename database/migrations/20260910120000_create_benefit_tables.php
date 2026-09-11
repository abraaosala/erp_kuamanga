<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBenefitTables extends AbstractMigration
{
    public function change(): void
    {
        $benefits = $this->table('benefits', ['signed' => false]);
        $benefits
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 120])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('category', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('position_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('department_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('min_tenure_months', 'integer', ['default' => 0])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['category'])
            ->addIndex(['status'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('position_id', 'positions', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->addForeignKey('department_id', 'departments', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();

        $pivot = $this->table('employee_benefits', ['signed' => false]);
        $pivot
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('benefit_id', 'integer', ['signed' => false])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('started_at', 'date', ['null' => true])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['employee_id', 'benefit_id'], ['unique' => true])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('benefit_id', 'benefits', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
