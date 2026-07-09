<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveSalaryFromEmployees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');
        $table->removeColumn('salary');
        $table->save();
    }
}
