<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class EmpresaSeeder extends AbstractSeed
{
    public function run(): void
    {
        $exists = $this->fetchRow("SELECT id FROM empresas WHERE nif = '500123456'");
        if ($exists) {
            echo "⚠ Empresa de demonstração já existe, skipping\n";
            return;
        }

        $data = [
            [
                'nome'           => 'Empresa de Demonstração LDA',
                'nif'            => '500123456',
                'morada'         => 'Rua da Independência, 123',
                'cidade'         => 'Luanda',
                'regime_iva'     => 'Geral',
                'status'         => 'ativo',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        $table = $this->table('empresas');
        $table->insert($data)->saveData();
        echo "✅ Empresa de demonstração criada\n";
    }
}
