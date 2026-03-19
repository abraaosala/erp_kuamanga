<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionRoleTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('permission_role', ['id' => false, 'primary_key' => ['permission_id', 'role_id']]);
        $table
            ->addColumn('permission_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('role_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('permission_id', 'permissions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('role_id', 'roles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
