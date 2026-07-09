<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AccountPlanSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return ['EmpresaSeeder'];
    }

    public function run(): void
    {
        $empresa = $this->fetchRow('SELECT id FROM empresas LIMIT 1');
        if (!$empresa) {
            return;
        }

        $empresaId = $empresa['id'];

        $exists = $this->fetchRow("SELECT id FROM account_plans WHERE empresa_id = $empresaId LIMIT 1");
        if ($exists) {
            echo "⚠ Plano de contas já existe, skipping\n";
            return;
        }

        $data = [
            // Classe 1: Meios Monetários
            ['empresa_id' => $empresaId, 'code' => '1', 'name' => 'MEIOS MONETÁRIOS', 'type' => 'asset', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '11', 'name' => 'Caixa', 'type' => 'asset', 'is_analytic' => true, 'parent_code' => '1'],
            ['empresa_id' => $empresaId, 'code' => '12', 'name' => 'Bancos', 'type' => 'asset', 'is_analytic' => true, 'parent_code' => '1'],
            
            // Classe 2: Existências
            ['empresa_id' => $empresaId, 'code' => '2', 'name' => 'EXISTÊNCIAS', 'type' => 'asset', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '21', 'name' => 'Mercadorias', 'type' => 'asset', 'is_analytic' => true, 'parent_code' => '2'],

            // Classe 3: Terceiros
            ['empresa_id' => $empresaId, 'code' => '3', 'name' => 'TERCEIROS', 'type' => 'asset', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '31', 'name' => 'Clientes', 'type' => 'asset', 'is_analytic' => true, 'parent_code' => '3'],
            ['empresa_id' => $empresaId, 'code' => '32', 'name' => 'Fornecedores', 'type' => 'liability', 'is_analytic' => true, 'parent_code' => '3'],
            ['empresa_id' => $empresaId, 'code' => '34', 'name' => 'Estado', 'type' => 'liability', 'is_analytic' => true, 'parent_code' => '3'],

            // Classe 4: Imobilizações
            ['empresa_id' => $empresaId, 'code' => '4', 'name' => 'IMOBILIZAÇÕES', 'type' => 'asset', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '42', 'name' => 'Imobilizações Corpóreas', 'type' => 'asset', 'is_analytic' => true, 'parent_code' => '4'],

            // Classe 5: Capital e Reservas
            ['empresa_id' => $empresaId, 'code' => '5', 'name' => 'CAPITAL E RESERVAS', 'type' => 'equity', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '51', 'name' => 'Capital Social', 'type' => 'equity', 'is_analytic' => true, 'parent_code' => '5'],

            // Classe 6: Proveitos e Ganhos por Natureza
            ['empresa_id' => $empresaId, 'code' => '6', 'name' => 'PROVEITOS E GANHOS', 'type' => 'revenue', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '61', 'name' => 'Vendas', 'type' => 'revenue', 'is_analytic' => true, 'parent_code' => '6'],
            ['empresa_id' => $empresaId, 'code' => '62', 'name' => 'Prestações de Serviços', 'type' => 'revenue', 'is_analytic' => true, 'parent_code' => '6'],

            // Classe 7: Custos e Perdas por Natureza
            ['empresa_id' => $empresaId, 'code' => '7', 'name' => 'CUSTOS E PERDAS', 'type' => 'expense', 'is_analytic' => false],
            ['empresa_id' => $empresaId, 'code' => '71', 'name' => 'Custo das Mercadorias Vendidas', 'type' => 'expense', 'is_analytic' => true, 'parent_code' => '7'],
            ['empresa_id' => $empresaId, 'code' => '72', 'name' => 'Custos com o Pessoal', 'type' => 'expense', 'is_analytic' => true, 'parent_code' => '7'],
            ['empresa_id' => $empresaId, 'code' => '75', 'name' => 'Fornecimentos e Serviços Externos', 'type' => 'expense', 'is_analytic' => true, 'parent_code' => '7'],

            // Classe 8: Resultados
            ['empresa_id' => $empresaId, 'code' => '8', 'name' => 'RESULTADOS', 'type' => 'equity', 'is_analytic' => false],
        ];

        // First pass: Insert roots
        $inserted = [];
        foreach ($data as $item) {
            if (!isset($item['parent_code'])) {
                $row = [
                    'empresa_id' => $item['empresa_id'],
                    'code'       => $item['code'],
                    'name'       => $item['name'],
                    'type'       => $item['type'],
                    'is_analytic' => $item['is_analytic'],
                    'status'     => 'active'
                ];
                $this->table('account_plans')->insert($row)->saveData();
                $inserted[$item['code']] = $this->getAdapter()->getConnection()->lastInsertId();
            }
        }

        // Second pass: Insert children
        foreach ($data as $item) {
            if (isset($item['parent_code'])) {
                $row = [
                    'empresa_id' => $item['empresa_id'],
                    'parent_id'  => $inserted[$item['parent_code']],
                    'code'       => $item['code'],
                    'name'       => $item['name'],
                    'type'       => $item['type'],
                    'is_analytic' => $item['is_analytic'],
                    'status'     => 'active'
                ];
                $this->table('account_plans')->insert($row)->saveData();
            }
        }
    }
}
