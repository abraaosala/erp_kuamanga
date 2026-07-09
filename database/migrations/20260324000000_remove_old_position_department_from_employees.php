<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveOldPositionDepartmentFromEmployees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');
        $table->removeColumn('position');
        $table->removeColumn('department');
        $table->save();
    }
}
