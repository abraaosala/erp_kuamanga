<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateHourBankEntriesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('hour_bank_entries', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('date', 'date')
            ->addColumn('hours', 'decimal', ['precision' => 8, 'scale' => 2])
            ->addColumn('type', 'string', ['limit' => 30, 'default' => 'ajuste'])
            ->addColumn('observations', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['date'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
