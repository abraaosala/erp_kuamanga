<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContractsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contracts', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('tipo_contrato', 'string', ['limit' => 50])
            ->addColumn('data_inicio', 'date')
            ->addColumn('data_fim', 'date', ['null' => true])
            ->addColumn('salario_base', 'decimal', ['precision' => 12, 'scale' => 2, 'null' => true])
            ->addColumn('carga_horaria', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('funcao', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('observacoes', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
