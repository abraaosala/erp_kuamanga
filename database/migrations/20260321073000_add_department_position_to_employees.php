<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDepartmentPositionToEmployees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');
        $table
            ->addColumn('department_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('position_id', 'integer', ['signed' => false, 'null' => true])
            ->addIndex(['department_id'])
            ->addIndex(['position_id'])
            ->addForeignKey('department_id', 'departments', 'id', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])
            ->addForeignKey('position_id', 'positions', 'id', ['delete'=> 'SET_NULL', 'update'=> 'CASCADE'])
            ->update();
    }
}
