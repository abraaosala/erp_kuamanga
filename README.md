# ERP Sistema - Gestão de Usuários e Autenticação

Este é um sistema ERP focado em gestão de usuários, implementado com uma arquitetura moderna e intuitiva.

## 🚀 Funcionalidades

- **Autenticação**: Sistema de login seguro com middleware de proteção.
- **Gestão de Usuários**: CRUD completo (Criar, Ler, Editar, Excluir) de usuários.
- **Sistema de Níveis (Roles)**: Atribuição de múltiplas funções por usuário (Administrador, Gestor, Funcionário).
- **Interface Premium**:
  - **Tema Claro/Escuro**: Alternador de tema persistente (salvo no navegador).
  - **Select2**: Seleção de funções com busca inteligente e múltipla escolha, integrada via npm.
  - **Design Moderno**: Estética baseada em variáveis CSS e Tailwind CSS para uma experiência limpa e profissional.

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.x, Illuminate components (Database, Routing, Validation, Pagination).
- **Frontend**: BladeOne (Motor de Templates), Tailwind CSS, Alpine.js, Select2, jQuery.
- **Banco de Dados**: Phinx (Migrações e Seeds), Eloquent ORM.
- **Build Tool**: Esbuild (JS) e Tailwind CLI (CSS).

## 📥 Instalação

### Pré-requisitos
- PHP 8.x
- Composer
- Node.js e NPM
- Banco de Dados MySQL/MariaDB

### Passos
1. Clone o repositório.
2. Copie o `.env.example` para `.env` e configure as credenciais do banco de dados.
3. Instale as dependências do PHP:
   ```bash
   composer install
   ```
4. Instale as dependências do Node:
   ```bash
   npm install
   ```
5. Execute as migrações e seeds:
   ```bash
   php console migrate
   php console seed
   ```
6. Processe os assets do frontend:
   ```bash
   npm run build
   ```

## 💻 Comandos CLI (Custom)

- `php console db:create` - Cria o banco de dados.
- `php console migrate` - Executa as migrações do Phinx.
- `php console seed` - Executa os seeders do Phinx.
- `php console make:model {Name}` - Cria um novo Model.
- `php console make:controller {Name}` - Cria um novo Controller.

---
Desenvolvido com foco em escalabilidade e facilidade de uso.
