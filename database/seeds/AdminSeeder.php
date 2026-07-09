<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AdminSeeder extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [];
    }

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // Clean up tables to avoid duplicate entries and foreign key issues
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->execute('TRUNCATE permission_role');
        $this->execute('TRUNCATE role_user');
        $this->execute('TRUNCATE permissions');
        $this->execute('TRUNCATE roles');
        $this->execute('TRUNCATE users');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

        echo "--- Seeding Database ---\n";

        // Create default roles
        $rolesTable = $this->table('roles');
        $rolesData = [
            ['name' => 'admin',   'display_name' => 'Administrador', 'description' => 'Acesso total ao sistema', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'manager', 'display_name' => 'Gerente',       'description' => 'Acesso gerencial',        'created_at' => $now, 'updated_at' => $now],
            ['name' => 'user',    'display_name' => 'Usuário',       'description' => 'Acesso básico',           'created_at' => $now, 'updated_at' => $now],
        ];

        $rolesTable->insert($rolesData)->saveData();
        echo "[1/4] Roles created\n";

        // Create default permissions
        $permissionsTable = $this->table('permissions');
        $permissionsData = [
            ['name' => 'users.view',   'display_name' => 'Ver Usuários',   'description' => 'Pode visualizar usuários',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.create', 'display_name' => 'Criar Usuários', 'description' => 'Pode criar usuários',       'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.edit',   'display_name' => 'Editar Usuários', 'description' => 'Pode editar usuários',      'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.delete', 'display_name' => 'Excluir Usuários', 'description' => 'Pode excluir usuários',   'created_at' => $now, 'updated_at' => $now],
        ];

        $permissionsTable->insert($permissionsData)->saveData();
        echo "[2/4] Permissions created\n";

        // Fetch the inserted IDs for admin role and permissions
        $adminRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'admin'");
        $managerRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'manager'");
        $adminPermissions = $this->fetchAll("SELECT id FROM permissions");
        $managerPermissions = $this->fetchAll("SELECT id FROM permissions WHERE name IN ('users.view', 'users.edit')");

        // Assign permissions to roles
        $permissionRoleTable = $this->table('permission_role');
        $prData = [];
        
        // Admin gets all
        foreach ($adminPermissions as $p) {
            $prData[] = ['permission_id' => $p['id'], 'role_id' => $adminRole['id'], 'created_at' => $now];
        }
        
        // Manager gets some
        foreach ($managerPermissions as $p) {
            $prData[] = ['permission_id' => $p['id'], 'role_id' => $managerRole['id'], 'created_at' => $now];
        }

        $permissionRoleTable->insert($prData)->saveData();
        echo "[3/4] Permissions assigned to Roles\n";

        // Create admin user
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $usersTable    = $this->table('users');
        
        $usersTable->insert([
            [
                'name'       => 'Administrador',
                'email'      => 'admin@erp.com',
                'password'   => $adminPassword,
                'active'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ])->saveData();

        // Assign admin role to admin user
        $adminUser    = $this->fetchRow("SELECT id FROM users WHERE email = 'admin@erp.com'");
        $roleUserTable = $this->table('role_user');
        $roleUserTable->insert([
            ['role_id' => $adminRole['id'], 'user_id' => $adminUser['id'], 'created_at' => $now],
        ])->saveData();

        echo "[4/4] Admin user created: admin@erp.com (pw: admin123)\n";
        echo "✅ All seeds completed successfully\n";
    }
}
