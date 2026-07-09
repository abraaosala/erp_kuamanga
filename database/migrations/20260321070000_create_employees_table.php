<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmployeesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('phone', 'string', ['limit' => 30, 'null' => true])
            ->addColumn('position', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('department', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('salary', 'decimal', ['precision' => 12, 'scale' => 2, 'null' => true])
            ->addColumn('hire_date', 'date', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['email'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
