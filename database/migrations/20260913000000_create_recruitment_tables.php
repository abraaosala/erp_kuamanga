<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRecruitmentTables extends AbstractMigration
{
    public function change(): void
    {
        $openings = $this->table('job_openings', ['signed' => false]);
        $openings
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('title', 'string', ['limit' => 150])
            ->addColumn('department_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('position_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('requirements', 'text', ['null' => true])
            ->addColumn('openings_count', 'integer', ['default' => 1])
            ->addColumn('salary_range_min', 'decimal', ['precision' => 14, 'scale' => 2, 'null' => true])
            ->addColumn('salary_range_max', 'decimal', ['precision' => 14, 'scale' => 2, 'null' => true])
            ->addColumn('location', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'aberta'])
            ->addColumn('closes_at', 'date', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['status'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('department_id', 'departments', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->addForeignKey('position_id', 'positions', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();

        $candidates = $this->table('candidates', ['signed' => false]);
        $candidates
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('job_opening_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('email', 'string', ['limit' => 190])
            ->addColumn('phone', 'string', ['limit' => 30, 'null' => true])
            ->addColumn('source', 'string', ['limit' => 40, 'default' => 'site'])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'novo'])
            ->addColumn('decided_by', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('decided_at', 'timestamp', ['null' => true])
            ->addColumn('employee_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['job_opening_id'])
            ->addIndex(['status'])
            ->addIndex(['email'])
            ->addIndex(['empresa_id', 'email'], ['unique' => true])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('job_opening_id', 'job_openings', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->addForeignKey('employee_id', 'employees', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();

        $interviews = $this->table('interviews', ['signed' => false]);
        $interviews
            ->addColumn('empresa_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('candidate_id', 'integer', ['signed' => false])
            ->addColumn('job_opening_id', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('scheduled_at', 'datetime', ['null' => true])
            ->addColumn('interviewer', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('result', 'string', ['limit' => 20, 'default' => 'pendente'])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['empresa_id'])
            ->addIndex(['candidate_id'])
            ->addIndex(['job_opening_id'])
            ->addForeignKey('empresa_id', 'empresas', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('candidate_id', 'candidates', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('job_opening_id', 'job_openings', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
    }
}
