<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmpresasTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('empresas', ['signed' => false]);
        $table
            ->addColumn('nome', 'string', ['limit' => 200])
            ->addColumn('nif', 'string', ['limit' => 20])
            ->addColumn('morada', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('codigo_postal', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('cidade', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('pais', 'string', ['limit' => 100, 'default' => 'Angola'])
            ->addColumn('regime_iva', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('cae', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('data_constituicao', 'date', ['null' => true])
            ->addColumn('logo', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'ativo'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['nif'], ['unique' => true])
            ->create();
    }
}
