<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRoleUserTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('role_user', ['id' => false, 'primary_key' => ['role_id', 'user_id']]);
        $table
            ->addColumn('role_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('user_id', 'integer', ['signed' => false, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('role_id', 'roles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
