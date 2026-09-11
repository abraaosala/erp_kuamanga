<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EmployeeContractSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return ['RhSeeder'];
    }

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE contracts');
        $this->execute('TRUNCATE employees');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        $empresa = $this->fetchRow("SELECT id FROM empresas LIMIT 1");
        $empresaId = $empresa ? (int) $empresa['id'] : null;

        $positions = $this->fetchAll("SELECT id, name, department_id FROM positions");
        $posMap = [];
        foreach ($positions as $p) {
            $posMap[$p['name']] = ['id' => (int) $p['id'], 'department_id' => $p['department_id'] ? (int) $p['department_id'] : null];
        }

        $monthsAgo3 = (new DateTimeImmutable('-3 months'))->format('Y-m-d');
        $fiveYears = '+5 years';

        $employeesData = [
            ['name' => 'Adilson Domingos Paulo',      'email' => 'adilson.paulo@empresa.ao',   'phone' => '+244 923 200 001', 'bi' => '000200001LA041', 'inss' => 'INSS-000200001', 'position' => 'Director Geral',         'hire_date' => $monthsAgo3, 'salario_base' => 1200000.00],
            ['name' => 'Beatriz Eduardo Neves',       'email' => 'beatriz.neves@empresa.ao',   'phone' => '+244 923 200 002', 'bi' => '000200002LA041', 'inss' => 'INSS-000200002', 'position' => 'Director Executivo',     'hire_date' => $monthsAgo3, 'salario_base' => 800000.00],
            ['name' => 'Carlos Domingos Kiala',       'email' => 'carlos.kiala@empresa.ao',    'phone' => '+244 923 200 003', 'bi' => '000200003LA041', 'inss' => 'INSS-000200003', 'position' => 'Director de RH',         'hire_date' => $monthsAgo3, 'salario_base' => 600000.00],
            ['name' => 'Domingos Sebastião Cipriano', 'email' => 'domingos.cipriano@empresa.ao','phone' => '+244 923 200 004', 'bi' => '000200004LA041', 'inss' => 'INSS-000200004', 'position' => 'Técnico de RH',          'hire_date' => $monthsAgo3, 'salario_base' => 250000.00],
            ['name' => 'Edson Manuel Catumbela',      'email' => 'edson.catumbela@empresa.ao', 'phone' => '+244 923 200 005', 'bi' => '000200005LA041', 'inss' => 'INSS-000200005', 'position' => 'Director Financeiro',    'hire_date' => $monthsAgo3, 'salario_base' => 700000.00],
            ['name' => 'Filomena da Conceição Tavares','email' => 'filomena.tavares@empresa.ao','phone' => '+244 923 200 006', 'bi' => '000200006LA041', 'inss' => 'INSS-000200006', 'position' => 'Contabilista Sénior',    'hire_date' => $monthsAgo3, 'salario_base' => 400000.00],
            ['name' => 'Gelson do Rosário Pinto',     'email' => 'gelson.pinto@empresa.ao', 'phone' => '+244 923 200 007', 'bi' => '000200007LA041', 'inss' => 'INSS-000200007', 'position' => 'Contabilista Júnior',    'hire_date' => $monthsAgo3, 'salario_base' => 200000.00],
            ['name' => 'Helena Joaquim Cassoma',      'email' => 'helena.cassoma@empresa.ao',  'phone' => '+244 923 200 008', 'bi' => '000200008LA041', 'inss' => 'INSS-000200008', 'position' => 'Tesoureiro',             'hire_date' => $monthsAgo3, 'salario_base' => 220000.00],
            ['name' => 'Ilídio Manuel Cabinda',       'email' => 'ilidio.cabinda@empresa.ao',  'phone' => '+244 923 200 009', 'bi' => '000200009LA041', 'inss' => 'INSS-000200009', 'position' => 'Director Comercial',     'hire_date' => $monthsAgo3, 'salario_base' => 650000.00],
            ['name' => 'Jéssica Osvaldo Domingos',    'email' => 'jessica.domingos@empresa.ao','phone' => '+244 923 200 010', 'bi' => '000200010LA041', 'inss' => 'INSS-000200010', 'position' => 'Vendedor',              'hire_date' => $monthsAgo3, 'salario_base' => 150000.00],
            ['name' => 'José Francisco Muxiluanda',   'email' => 'jose.muxiluanda@empresa.ao', 'phone' => '+244 923 200 011', 'bi' => '000200011LA041', 'inss' => 'INSS-000200011', 'position' => 'Vendedor',              'hire_date' => $monthsAgo3, 'salario_base' => 150000.00],
            ['name' => 'Kátia Raúl Bunga',            'email' => 'katia.bunga@empresa.ao',     'phone' => '+244 923 200 012', 'bi' => '000200012LA041', 'inss' => 'INSS-000200012', 'position' => 'Assistente de Marketing', 'hire_date' => $monthsAgo3, 'salario_base' => 140000.00],
            ['name' => 'Lázaro André Quissanga',      'email' => 'lazaro.quissanga@empresa.ao','phone' => '+244 923 200 013', 'bi' => '000200013LA041', 'inss' => 'INSS-000200013', 'position' => 'Chefe de Logística',     'hire_date' => $monthsAgo3, 'salario_base' => 400000.00],
            ['name' => 'Luena Campos Ferreira',       'email' => 'luena.ferreira@empresa.ao',  'phone' => '+244 923 200 014', 'bi' => '000200014LA041', 'inss' => 'INSS-000200014', 'position' => 'Auxiliar de Armazém',    'hire_date' => $monthsAgo3, 'salario_base' => 90000.00],
            ['name' => 'Manuel Renato Cabral',        'email' => 'manuel.cabral@empresa.ao',   'phone' => '+244 923 200 015', 'bi' => '000200015LA041', 'inss' => 'INSS-000200015', 'position' => 'Auxiliar de Armazém',    'hire_date' => $monthsAgo3, 'salario_base' => 90000.00],
            ['name' => 'Nádia Simão Venâncio',        'email' => 'nadia.venancio@empresa.ao',  'phone' => '+244 923 200 016', 'bi' => '000200016LA041', 'inss' => 'INSS-000200016', 'position' => 'Auxiliar de Armazém',    'hire_date' => $monthsAgo3, 'salario_base' => 90000.00],
            ['name' => 'Orlando Tito Malanje',        'email' => 'orlando.malanje@empresa.ao', 'phone' => '+244 923 200 017', 'bi' => '000200017LA041', 'inss' => 'INSS-000200017', 'position' => 'Director de TI',         'hire_date' => $monthsAgo3, 'salario_base' => 580000.00],
            ['name' => 'Patrícia Lenine Maia',        'email' => 'patricia.maia@empresa.ao',   'phone' => '+244 923 200 018', 'bi' => '000200018LA041', 'inss' => 'INSS-000200018', 'position' => 'Analista de TI',         'hire_date' => $monthsAgo3, 'salario_base' => 260000.00],
            ['name' => 'Quim Lopes Kwanza',           'email' => 'quim.kwanza@empresa.ao',     'phone' => '+244 923 200 019', 'bi' => '000200019LA041', 'inss' => 'INSS-000200019', 'position' => 'Programador',            'hire_date' => $monthsAgo3, 'salario_base' => 380000.00],
            ['name' => 'Rosa Antunes Lemba',          'email' => 'rosa.lemba@empresa.ao',      'phone' => '+244 923 200 020', 'bi' => '000200020LA041', 'inss' => 'INSS-000200020', 'position' => 'Programador',            'hire_date' => $monthsAgo3, 'salario_base' => 380000.00],
            ['name' => 'Sérgio Ambrósio Huambo',      'email' => 'sergio.huambo@empresa.ao',   'phone' => '+244 923 200 021', 'bi' => '000200021LA041', 'inss' => 'INSS-000200021', 'position' => 'Programador',            'hire_date' => $monthsAgo3, 'salario_base' => 380000.00],
            ['name' => 'Telma Costa Bessa',           'email' => 'telma.bessa@empresa.ao',     'phone' => '+244 923 200 022', 'bi' => '000200022LA041', 'inss' => 'INSS-000200022', 'position' => 'Jurista',               'hire_date' => $monthsAgo3, 'salario_base' => 420000.00],
            ['name' => 'Urbano Francisco Dendê',      'email' => 'urbano.dende@empresa.ao',    'phone' => '+244 923 200 023', 'bi' => '000200023LA041', 'inss' => 'INSS-000200023', 'position' => 'Estagiário Jurídico',  'hire_date' => $monthsAgo3, 'salario_base' => 80000.00],
            ['name' => 'Vera Assunção Gil',           'email' => 'vera.gil@empresa.ao',        'phone' => '+244 923 200 024', 'bi' => '000200024LA041', 'inss' => 'INSS-000200024', 'position' => 'Técnico de RH',          'hire_date' => $monthsAgo3, 'salario_base' => 250000.00],
            ['name' => 'Wilson Mateus Benguela',      'email' => 'wilson.benguela@empresa.ao', 'phone' => '+244 923 200 025', 'bi' => '000200025LA041', 'inss' => 'INSS-000200025', 'position' => 'Analista de TI',         'hire_date' => $monthsAgo3, 'salario_base' => 260000.00],
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
                'bi'            => $e['bi'],
                'inss'          => $e['inss'],
                'department_id' => $deptId,
                'position_id'   => $posId,
                'hire_date'     => $e['hire_date'],
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        $table = $this->table('employees');
        $table->insert($employees)->saveData();

        $inserted = $this->fetchAll("SELECT id FROM employees WHERE empresa_id = " . (int) $empresaId . " ORDER BY id");
        $contracts = [];
        foreach ($inserted as $i => $row) {
            $employee = $employeesData[$i];
            $contracts[] = [
                'empresa_id'    => $empresaId,
                'employee_id'   => (int) $row['id'],
                'tipo_contrato' => 'determinado',
                'data_inicio'   => $employee['hire_date'],
                'data_fim'      => (new DateTimeImmutable($employee['hire_date']))->modify($fiveYears)->format('Y-m-d'),
                'salario_base'  => $employee['salario_base'],
                'carga_horaria' => '40h/semana',
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        $contractTable = $this->table('contracts');
        $contractTable->insert($contracts)->saveData();

        echo "✅ " . count($employees) . " employees with 5-year contracts created\n";
    }
}
