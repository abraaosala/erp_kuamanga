<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateJournalEntriesTables extends AbstractMigration
{
    public function change(): void
    {
        // Journal Entries (Header)
        $entries = $this->table('journal_entries', ['signed' => false]);
        $entries
            ->addColumn('empresa_id', 'integer', ['signed' => false])
            ->addColumn('date', 'date')
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('reference', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['empresa_id'])
            ->addIndex(['date'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();

        // Journal Items (Lines)
        $items = $this->table('journal_items', ['signed' => false]);
        $items
            ->addColumn('entry_id', 'integer', ['signed' => false])
            ->addColumn('account_id', 'integer', ['signed' => false])
            ->addColumn('type', 'enum', ['values' => ['debit', 'credit']])
            ->addColumn('amount', 'decimal', ['precision' => 15, 'scale' => 2])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['entry_id'])
            ->addIndex(['account_id'])
            ->addForeignKey('entry_id', 'journal_entries', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('account_id', 'account_plans', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->create();
    }
}
