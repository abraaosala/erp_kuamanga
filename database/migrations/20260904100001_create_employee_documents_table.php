<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmployeeDocumentsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employee_documents', ['signed' => false]);
        $table
            ->addColumn('employee_id', 'integer', ['signed' => false])
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('document_type', 'string', ['limit' => 30])
            ->addColumn('document_number', 'string', ['limit' => 30, 'null' => true])
            ->addColumn('file_path', 'string', ['limit' => 255])
            ->addColumn('file_name', 'string', ['limit' => 255])
            ->addColumn('file_size', 'integer', ['null' => true])
            ->addColumn('mime_type', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['employee_id'])
            ->addIndex(['empresa_id'])
            ->addIndex(['document_type'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }
}
