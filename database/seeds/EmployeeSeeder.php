<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EmployeeSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return ['RhSeeder'];
    }

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE employees');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        $empresa = $this->fetchRow("SELECT id FROM empresas LIMIT 1");
        $empresaId = $empresa ? (int) $empresa['id'] : null;

        // Get position IDs mapped by position name
        $positions = $this->fetchAll("SELECT id, name, department_id FROM positions");
        $posMap = [];
        foreach ($positions as $p) {
            $posMap[$p['name']] = ['id' => (int) $p['id'], 'department_id' => $p['department_id'] ? (int) $p['department_id'] : null];
        }

        $employeesData = [
            ['name' => 'João Manuel dos Santos',       'email' => 'joao.santos@empresa.ao',      'phone' => '+244 923 100 001', 'position' => 'Director Geral',         'hire_date' => '2018-01-15', 'status' => 'active'],
            ['name' => 'Ana Paula Ferreira',           'email' => 'ana.ferreira@empresa.ao',     'phone' => '+244 923 100 002', 'position' => 'Director Executivo',     'hire_date' => '2019-03-01', 'status' => 'active'],
            ['name' => 'Maria Isabel Lopes',           'email' => 'maria.lopes@empresa.ao',      'phone' => '+244 923 100 003', 'position' => 'Director de RH',         'hire_date' => '2020-06-10', 'status' => 'active'],
            ['name' => 'Carlos Alberto Mendes',        'email' => 'carlos.mendes@empresa.ao',    'phone' => '+244 923 100 004', 'position' => 'Técnico de RH',          'hire_date' => '2021-09-20', 'status' => 'active'],
            ['name' => 'Pedro Miguel Gomes',           'email' => 'pedro.gomes@empresa.ao',      'phone' => '+244 923 100 005', 'position' => 'Director Financeiro',    'hire_date' => '2019-07-05', 'status' => 'active'],
            ['name' => 'Sofia Cristina Ramos',         'email' => 'sofia.ramos@empresa.ao',      'phone' => '+244 923 100 006', 'position' => 'Contabilista Sénior',    'hire_date' => '2020-01-12', 'status' => 'active'],
            ['name' => 'Lucas António Silva',          'email' => 'lucas.silva@empresa.ao',      'phone' => '+244 923 100 007', 'position' => 'Contabilista Júnior',    'hire_date' => '2022-04-18', 'status' => 'active'],
            ['name' => 'Ricardo Jorge Neto',           'email' => 'ricardo.neto@empresa.ao',     'phone' => '+244 923 100 008', 'position' => 'Tesoureiro',             'hire_date' => '2021-11-03', 'status' => 'active'],
            ['name' => 'Fernanda Oliveira Costa',      'email' => 'fernanda.costa@empresa.ao',   'phone' => '+244 923 100 009', 'position' => 'Director Comercial',     'hire_date' => '2020-02-25', 'status' => 'active'],
            ['name' => 'André Filipe Martins',         'email' => 'andre.martins@empresa.ao',    'phone' => '+244 923 100 010', 'position' => 'Vendedor',              'hire_date' => '2022-08-15', 'status' => 'active'],
            ['name' => 'Catarina Isabel Dias',         'email' => 'catarina.dias@empresa.ao',    'phone' => '+244 923 100 011', 'position' => 'Assistente de Marketing', 'hire_date' => '2023-01-10', 'status' => 'active'],
            ['name' => 'Joaquim Pedro Campos',         'email' => 'joaquim.campos@empresa.ao',   'phone' => '+244 923 100 012', 'position' => 'Chefe de Logística',     'hire_date' => '2021-05-20', 'status' => 'active'],
            ['name' => 'Nuno Miguel Pereira',          'email' => 'nuno.pereira@empresa.ao',     'phone' => '+244 923 100 013', 'position' => 'Auxiliar de Armazém',    'hire_date' => '2023-06-01', 'status' => 'active'],
            ['name' => 'Tiago Alexandre Nunes',        'email' => 'tiago.nunes@empresa.ao',      'phone' => '+244 923 100 014', 'position' => 'Director de TI',         'hire_date' => '2020-09-12', 'status' => 'active'],
            ['name' => 'Rui Carlos Simões',            'email' => 'rui.simoes@empresa.ao',       'phone' => '+244 923 100 015', 'position' => 'Analista de TI',         'hire_date' => '2022-02-28', 'status' => 'active'],
            ['name' => 'Hugo Filipe Carvalho',         'email' => 'hugo.carvalho@empresa.ao',    'phone' => '+244 923 100 016', 'position' => 'Programador',            'hire_date' => '2021-10-05', 'status' => 'active'],
            ['name' => 'Marta Alexandra Sousa',        'email' => 'marta.sousa@empresa.ao',      'phone' => '+244 923 100 017', 'position' => 'Jurista',               'hire_date' => '2022-07-22', 'status' => 'active'],
        ];

        $employees = [];
        foreach ($employeesData as $e) {
            $posId = $posMap[$e['position']]['id'] ?? null;
            $deptId = $posMap[$e['position']]['department_id'] ?? null;

            $employees[] = [
                'empresa_id'    => $empresaId,
                'name'          => $e['name'],
                'email'         => $e['email'],
                'phone'         => $e['phone'],
                'department_id' => $deptId,
                'position_id'   => $posId,
                'hire_date'     => $e['hire_date'],
                'status'        => $e['status'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        $table = $this->table('employees');
        $table->insert($employees)->saveData();

        echo "✅ " . count($employees) . " employees created\n";
    }
}
