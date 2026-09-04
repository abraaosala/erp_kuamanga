<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddBiInssToEmployees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');
        $table
            ->addColumn('bi', 'string', ['limit' => 30, 'null' => false])
            ->addColumn('inss', 'string', ['limit' => 30, 'null' => true])
            ->addIndex(['bi'])
            ->update();
    }
}
