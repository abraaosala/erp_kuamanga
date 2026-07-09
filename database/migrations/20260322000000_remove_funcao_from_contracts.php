<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveFuncaoFromContracts extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contracts');
        $table->removeColumn('funcao');
        $table->save();
    }
}
