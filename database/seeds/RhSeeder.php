<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class RhSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return ['EmpresaSeeder'];
    }

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE positions');
        $this->execute('TRUNCATE departments');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        $empresa = $this->fetchRow("SELECT id FROM empresas LIMIT 1");
        $empresaId = $empresa ? (int) $empresa['id'] : null;

        // Departments
        $departmentsData = [
            ['empresa_id' => $empresaId, 'name' => 'Direcção Geral',      'description' => 'Direcção executiva e estratégica da empresa', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Recursos Humanos',    'description' => 'Gestão de pessoas e talento humano',          'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Contabilidade e Finanças', 'description' => 'Gestão contabilística, fiscal e financeira', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Comercial e Marketing',    'description' => 'Vendas, prospecção e comunicação',         'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Operações e Logística',    'description' => 'Logística, armazém e distribuição',         'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Tecnologias de Informação','description' => 'Infraestrutura TI e desenvolvimento',       'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'name' => 'Jurídico',            'description' => 'Assessoria jurídica e compliance',            'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        $departmentsTable = $this->table('departments');
        $departmentsTable->insert($departmentsData)->saveData();
        echo "[1/2] " . count($departmentsData) . " departments created\n";

        // Fetch inserted department IDs
        $deptMap = [];
        foreach ($departmentsData as $d) {
            $row = $this->fetchRow("SELECT id FROM departments WHERE name = '" . $d['name'] . "' LIMIT 1");
            if ($row) {
                $deptMap[$d['name']] = (int) $row['id'];
            }
        }

        // Positions
        $positionsData = [
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Direcção Geral'] ?? null,      'name' => 'Director Geral',         'description' => 'CEO / Principal executivo',                                 'salary_range_min' => 500000, 'salary_range_max' => 1500000, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Direcção Geral'] ?? null,      'name' => 'Director Executivo',     'description' => 'COO / Director de operações',                               'salary_range_min' => 400000, 'salary_range_max' => 1000000, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Recursos Humanos'] ?? null,    'name' => 'Director de RH',         'description' => 'Gestão estratégica de pessoas',                             'salary_range_min' => 300000, 'salary_range_max' => 700000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Recursos Humanos'] ?? null,    'name' => 'Técnico de RH',          'description' => 'Administração de pessoal, folhas e recrutamento',            'salary_range_min' => 120000, 'salary_range_max' => 300000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Contabilidade e Finanças'] ?? null, 'name' => 'Director Financeiro',   'description' => 'CFO / Director financeiro',                                 'salary_range_min' => 400000, 'salary_range_max' => 900000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Contabilidade e Finanças'] ?? null, 'name' => 'Contabilista Sénior',  'description' => 'Contabilidade geral, fiscal e reportes',                     'salary_range_min' => 200000, 'salary_range_max' => 500000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Contabilidade e Finanças'] ?? null, 'name' => 'Contabilista Júnior',  'description' => 'Lançamentos contabilísticos e reconciliações',               'salary_range_min' => 100000, 'salary_range_max' => 250000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Contabilidade e Finanças'] ?? null, 'name' => 'Tesoureiro',           'description' => 'Gestão de tesouraria, pagamentos e cobranças',                'salary_range_min' => 120000, 'salary_range_max' => 280000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Comercial e Marketing'] ?? null,    'name' => 'Director Comercial',   'description' => 'Direcção de vendas e marketing',                             'salary_range_min' => 350000, 'salary_range_max' => 800000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Comercial e Marketing'] ?? null,    'name' => 'Vendedor',            'description' => 'Prospecção e venda de produtos/serviços',                    'salary_range_min' => 80000,  'salary_range_max' => 200000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Comercial e Marketing'] ?? null,    'name' => 'Assistente de Marketing', 'description' => 'Apoio a campanhas e comunicação',                           'salary_range_min' => 80000,  'salary_range_max' => 180000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Operações e Logística'] ?? null,    'name' => 'Chefe de Logística',  'description' => 'Gestão de armazém, frotas e distribuição',                   'salary_range_min' => 250000, 'salary_range_max' => 500000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Operações e Logística'] ?? null,    'name' => 'Auxiliar de Armazém', 'description' => 'Recepção, conferência e movimentação de mercadorias',         'salary_range_min' => 60000,  'salary_range_max' => 120000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Tecnologias de Informação'] ?? null, 'name' => 'Director de TI',      'description' => 'Gestão de infraestrutura e equipa de TI',                     'salary_range_min' => 350000, 'salary_range_max' => 700000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Tecnologias de Informação'] ?? null, 'name' => 'Analista de TI',      'description' => 'Suporte técnico e manutenção de sistemas',                   'salary_range_min' => 120000, 'salary_range_max' => 300000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Tecnologias de Informação'] ?? null, 'name' => 'Programador',         'description' => 'Desenvolvimento de software e integrações',                  'salary_range_min' => 180000, 'salary_range_max' => 450000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Jurídico'] ?? null,            'name' => 'Jurista',            'description' => 'Assessoria jurídica, contratos e litígios',                  'salary_range_min' => 200000, 'salary_range_max' => 500000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => $empresaId, 'department_id' => $deptMap['Jurídico'] ?? null,            'name' => 'Estagiário Jurídico', 'description' => 'Apoio administrativo e pesquisa jurídica',                    'salary_range_min' => 50000,  'salary_range_max' => 100000,  'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        $positionsTable = $this->table('positions');
        $positionsTable->insert($positionsData)->saveData();
        echo "[2/2] " . count($positionsData) . " positions created\n";

        echo "✅ RH seeded successfully\n";
    }
}
