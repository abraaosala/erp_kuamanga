<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddIrtSocialSecurityToPayslips extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('payslips');
        $table
            ->addColumn('social_security', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->addColumn('irt_amount', 'decimal', ['precision' => 14, 'scale' => 2, 'default' => 0])
            ->update();
    }
}