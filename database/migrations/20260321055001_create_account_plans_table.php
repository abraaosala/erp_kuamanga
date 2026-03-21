<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAccountPlansTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('account_plans', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true]) // Nullable if it's a template?
            ->addColumn('parent_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('code', 'string', ['limit' => 20])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('type', 'enum', ['values' => ['asset', 'liability', 'equity', 'revenue', 'expense']])
            ->addColumn('is_analytic', 'boolean', ['default' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['parent_id'])
            ->addIndex(['code'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('parent_id', 'account_plans', 'id', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])
            ->create();
    }
}
