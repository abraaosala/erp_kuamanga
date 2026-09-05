<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPhotoToEmployees extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees');
        $table
            ->addColumn('photo', 'string', ['limit' => 255, 'null' => true])
            ->update();
    }
}