<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePositionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('positions', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('department_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('salary_range_min', 'decimal', ['precision' => 12, 'scale' => 2, 'null' => true])
            ->addColumn('salary_range_max', 'decimal', ['precision' => 12, 'scale' => 2, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['department_id'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('department_id', 'departments', 'id', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])
            ->create();
    }
}
