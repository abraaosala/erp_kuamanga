Você é um Arquiteto de Software Sênior e Especialista em PHP, com vasta experiência na construção de sistemas robustos, escaláveis e de alta performance. Seu conhecimento abrange design de arquitetura, padrões de projeto (MVC, Repository, Service), injeção de dependência e as melhores práticas de segurança e desenvolvimento.

---

### **Contexto:**

Estou desenvolvendo um sistema ERP (Enterprise Resource Planning) em PHP, utilizando uma abordagem de "micro-framework" personalizada, que se baseia em componentes selecionados do ecossistema Laravel (Illuminate) para o backend, BladeOne para o sistema de templates e uma stack frontend moderna com TailwindCSS e AlpineJS.

A estrutura de diretórios e as dependências do projeto são fixas e devem ser rigorosamente seguidas:

**Estrutura de Diretórios:**

```text
📁 app/
  📁 Console/
    📁 Commands/
  📁 Core/
  📁 Http/
    📁 Controllers/
      📁 Modules 
    📁 Middleware/
  📁 Models/
  📁 Providers/
    📁 Modules/
  📁 Repositories/
      📁 Modules/
  📁 Services/
    📁 Modules/
📁 config/
📁 database/
  📁 migrations/
  📁 seeds/
📁 public/
  📁 css/
  📁 js/
  📁 uploads/
    📁 receipts/
📁 resources/
  📁 css/
  📁 js/
  📁 views/ 
    📁 auth/
📁 routes/
📁 storage/
  📁 cache/
📁 tests/
  📁 Feature/
  📁 Unit/
```

**`composer.json`:**

```json
{
    "name": "salaab/erp",
    "description": "O projecto da Geralda Bartolomeu para o final do curso",
    "type": "project",
    "require": {
        "php": "^8.1",
        "dompdf/dompdf": "^3.1",
        "eftec/bladeone": "^4.10",
        "filp/whoops": "^2.15",
        "illuminate/container": "^10.0",
        "illuminate/database": "^10.0",
        "illuminate/events": "^10.0",
        "illuminate/http": "^10.0",
        "illuminate/routing": "^10.0",
        "illuminate/validation": "^10.0",
        "robmorgan/phinx": "^0.13",
        "vlucas/phpdotenv": "^5.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    },
    "scripts": {
        "test": "php console test"
    },
    "authors": [
        {
            "name": "Abraao Sala",
            "email": "abraaosala@hotmail.com"
        }
    ],
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true,
        "allow-plugins": {
            "kylekatarnls/update-helper": true,
            "pestphp/pest-plugin": true
        }
    },
    "require-dev": {
        "pestphp/pest": "^2.36"
    }
}
```

**`package.json` (Frontend):**

```json
{
  "name": "ofto",
  "version": "1.0.0",
  "description": "Frontend assets for ofto",
  "main": "index.js",
  "scripts": {
    "dev:css": ".\\node_modules\\.bin\\tailwindcss.cmd -i ./resources/css/app.css -o ./public/css/app.css --watch",
    "dev:js": ".\\node_modules\\.bin\\esbuild.cmd resources/js/app.js --bundle --outfile=public/js/app.js --watch",
    "build:css": ".\\node_modules\\.bin\\tailwindcss.cmd -i ./resources/css/app.css -o ./public/css/app.css --minify",
    "build:js": ".\\node_modules\\.bin\\esbuild.cmd resources/js/app.js --bundle --outfile=public/js/app.js --minify",
    "dev": "(start /B .\\node_modules\\.bin\\tailwindcss.cmd -i ./resources/css/app.css -o ./public/css/app.css --watch) & .\\node_modules\\.bin\\esbuild.cmd resources/js/app.js --bundle --outfile=public/js/app.js --watch",
    "build": "npm run build:css && npm run build:js"
  },
  "devDependencies": {
    "autoprefixer": "^10.4.15",
    "esbuild": "^0.27.4",
    "postcss": "^8.4.28",
    "tailwindcss": "^3.3.3"
  },
  "dependencies": {
    "alpinejs": "^3.13.0"
  }
}
```

**Restrições e Diretrizes:**

*   O backend deve ser "PHP puro" no sentido de que não estamos usando o framework Laravel completo, mas sim os componentes `illuminate/*` de forma desacoplada para construir um framework próprio.
*   A arquitetura deve ser modular, usando a estrutura `app/Http/Controllers/Modules`, `app/Services/Modules`, `app/Repositories/Modules`, e `app/Providers/Modules`.
*   A injeção de dependência deve ser utilizada através do `Illuminate\Container`.
*   O roteamento deve ser gerenciado por `Illuminate\Routing\Router`.
*   A conexão com o banco de dados e o ORM devem utilizar `Illuminate\Database` (Eloquent).
*   A validação deve usar `Illuminate\Validation`.
*   As views devem ser renderizadas com `BladeOne`.
*   As migrações de banco de dados devem ser configuradas para `robmorgan/phinx`.
*   O tratamento de erros deve integrar `filp/whoops`.
*   Variáveis de ambiente devem ser carregadas com `vlucas/phpdotenv`.
*   A segurança é primordial: incluir hash de senhas, proteção CSRF básica (se aplicável para o nível de implementação), e validação de entrada.
*   Os módulos devem ser registrados via Service Providers em `app/Providers/Modules/`.

---

### **Tarefa:**

Gere o código COMPLETO para a estrutura base do ERP e um módulo **completo de Autenticação e Gestão de Usuários**. Este módulo deve demonstrar a integração de todos os componentes e padrões arquiteturais mencionados.

**O módulo de Autenticação e Gestão de Usuários deve incluir:**

1.  **Modelos:** `User.php`, `Role.php`, `Permission.php`.
2.  **Repositórios:** `UserRepository.php`, `RoleRepository.php`.
3.  **Serviços:** `AuthService.php`, `UserService.php`.
4.  **Controladores:** `AuthController.php`, `UserController.php` (dentro de `app/Http/Controllers/Modules/User/`).
5.  **Rotas:** Rotas para login, logout, registro, listagem de usuários, criação, edição e exclusão de usuários.
6.  **Views BladeOne:** Templates para login, registro, dashboard (pós-login), listagem de usuários, formulário de criação/edição de usuário. As views devem usar classes TailwindCSS e ter um layout básico.
7.  **Migrations Phinx:** Migrações para as tabelas `users`, `roles`, `permissions`, `role_user`, `permission_role`.
8.  **Seeders Phinx:** Um seeder para criar um usuário admin padrão e algumas roles/permissions básicas.
9.  **Middleware:** Um middleware de autenticação (`AuthMiddleware.php`) para proteger rotas.
10. **Service Providers:** `AuthServiceProvider.php` e `UserServiceProvider.php` em `app/Providers/Modules/User/` para registrar suas dependências no container.
11. **Configuração:** Arquivos `config/app.php` e `config/database.php` para configurações gerais e de banco de dados.
12. **Bootstrap da Aplicação:** O ponto de entrada `public/index.php` e a classe `App\Core\Application.php` para inicializar o container, roteador, banco de dados, Whoops e registrar os Service Providers.
13. **Console Command (opcional):** Um comando básico para rodar as migrações via Phinx (`app/Console/Commands/MigrateCommand.php`).

---

### **Formato de Saída Desejado:**

A saída deve consistir em uma lista de todos os arquivos gerados, com seus respectivos caminhos completos, seguidos pelo conteúdo de cada arquivo em um bloco de código Markdown. Inclua também as instruções de configuração e execução.

**Estrutura da Saída:**

```markdown
**Instruções de Configuração e Execução:**

1.  **Crie o arquivo `.env`:**
    ```ini
    # Conteúdo do .env
    ```
2.  **Instale as dependências:**
    ```bash
    composer install
    npm install
    ```
3.  **Gere os assets frontend:**
    ```bash
    npm run build
    ```
4.  **Execute as migrações do banco de dados:**
    ```bash
    # Comando Phinx para rodar migrações
    ```
5.  **Execute os seeders:**
    ```bash
    # Comando Phinx para rodar seeders
    ```
6.  **Inicie o servidor PHP:**
    ```bash
    php -S localhost:8000 -t public
    ```
7.  **Acesse:** `http://localhost:8000`

---

**Arquivos Gerados:**

`./.env.example`
```ini
# Conteúdo do arquivo .env.example
```

`./public/index.php`
```php
<?php
// Conteúdo do arquivo
```

`./config/app.php`
```php
<?php
// Conteúdo do arquivo
```

`./config/database.php`
```php
<?php
// Conteúdo do arquivo
```

`./routes/web.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Core/Application.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Core/Database.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Http/Middleware/AuthMiddleware.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Models/User.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Models/Role.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Models/Permission.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Repositories/Contracts/UserRepositoryInterface.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Repositories/Modules/User/UserRepository.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Repositories/Contracts/RoleRepositoryInterface.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Repositories/Modules/User/RoleRepository.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Services/Contracts/AuthServiceInterface.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Services/Modules/User/AuthService.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Services/Contracts/UserServiceInterface.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Services/Modules/User/UserService.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Http/Controllers/Modules/User/AuthController.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Http/Controllers/Modules/User/UserController.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Providers/Modules/User/AuthServiceProvider.php`
```php
<?php
// Conteúdo do arquivo
```

`./app/Providers/Modules/User/UserServiceProvider.php`
```php
<?php
// Conteúdo do arquivo
```

`./database/migrations/YYYYMMDDHHMMSS_create_users_table.php`
```php
<?php
// Conteúdo da migração de usuários
```

`./database/migrations/YYYYMMDDHHMMSS_create_roles_and_permissions_tables.php`
```php
<?php
// Conteúdo da migração de roles e permissions
```

`./database/seeds/UserSeeder.php`
```php
<?php
// Conteúdo do seeder de usuários
```

`./resources/views/layout/app.blade.php`
```html
<!-- Conteúdo do layout principal -->
```

`./resources/views/auth/login.blade.php`
```html
<!-- Conteúdo da view de login -->
```

`./resources/views/auth/register.blade.php`
```html
<!-- Conteúdo da view de registro -->
```

`./resources/views/dashboard.blade.php`
```html
<!-- Conteúdo da view de dashboard -->
```

`./resources/views/users/index.blade.php`
```html
<!-- Conteúdo da view de listagem de usuários -->
```

`./resources/views/users/create.blade.php`
```html
<!-- Conteúdo da view de criação de usuário -->
```

`./resources/views/users/edit.blade.php`
```html
<!-- Conteúdo da view de edição de usuário -->
```

`./app/Console/Commands/MigrateCommand.php`
```php
<?php
// Conteúdo do comando Migrate
```

`./phinx.php`
```php
<?php
// Configuração do Phinx
```
```