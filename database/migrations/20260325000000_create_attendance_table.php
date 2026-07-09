<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAttendanceTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('attendance', ['signed' => false]);
        $table
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('date', 'date')
            ->addColumn('check_in', 'time', ['null' => true])
            ->addColumn('check_out', 'time', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'presente'])
            ->addColumn('observations', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['employee_id'])
            ->addIndex(['date'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
