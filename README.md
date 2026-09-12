# Kuamanga ERP — Gestão Empresarial

Sistema ERP multi-módulo construído sobre um micro-framework custom (componentes Illuminate avulsos, não Laravel). Inclui autenticação e gestão de utilizadores, contabilidade segundo o PGC Angola e um módulo de Recursos Humanos completo.

## 🚀 Funcionalidades

- **Autenticação**: Login seguro com middleware de proteção e sessões PHP.
- **Gestão de Utilizadores**: CRUD completo + atribuição de múltiplas funções (Administrador, Gestor, Funcionário).
- **Multi-empresa**: scoping por `current_empresa()` em todos os repositórios.
- **Interface Premium**:
  - Tema claro/escuro persistente (select2, Tailwind CSS) e design moderno baseado em variáveis CSS.
  - Alpine.js para interações.

## 📦 Módulos do Sistema

A arquitetura está dividida por módulos orientados ao negócio. Documentação específica de cada módulo:

- 📊 **[Contabilidade — PGC Angola](docs/modules/accounting.md)**: Plano de Contas, Lançamentos, Razão, Balancetes e Mapas Oficiais (Balanço & DRE).
- 👥 **[Recursos Humanos](docs/modules/rh.md)**: **13 submódulos implementados** — Departamentos, Cargos, Funcionários (incl. documentos digitais e foto), Contratos, Assiduidade, Escalas, Banco de Horas, Vínculo Escala↔Funcionário, Rosters/Rotação de Turnos, Folha Salarial (IRT/SS com recibos PDF), Férias e Licenças e Benefícios. Roadmap pendente em `docs/modules/rh.md`.

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.4/8.5, components Illuminate (Database/Eloquent, Routing, Validation, Pagination, Container).
- **Frontend**: BladeOne (motor de templates), Tailwind CSS, Alpine.js, Select2, jQuery, FullCalendar.
- **Banco de Dados**: Phinx (migrações e seeds), Eloquent ORM, MySQL/MariaDB (SQLite nos testes).
- **Build Tool**: esbuild (JS) e Tailwind CLI (CSS).
- **Extras**: dompdf (recibos PDF), Pest (testes), PHPStan nível 9.

## 📥 Instalação

### Pré-requisitos

- PHP 8.4+
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
   php console seed:run
   ```
6. Processe os assets do frontend:
   ```bash
   npm run build
   ```

## 💻 Comandos CLI

- `php console serve` — servidor PHP + watcher npm (dev).
- `php console test` — roda a suíte Pest (`tests/`).
- `php console migrate` — executa as migrações do Phinx.
- `php console rollback` — reverte a última migração.
- `php console status` — estado das migrações.
- `php console breakpoint` — define/redefine breakpoint de migração.
- `php console seed:run` — executa os seeders.
- `php console db:create|db:drop` — cria/remove o banco.
- `php console db:export [--stdout] [--structure-only] [--no-db-header] [-o arquivo.sql]` — exporta o banco para SQL (PHP puro, em `storage/dumps/`). Use `--no-db-header` para importar direto numa BD já selecionada (ex.: phpMyAdmin do InfinityFree).
- `php console make:controller|model|repository|service|view|migration|seed|middleware {Name}` — gera código a partir de stubs.

## ✅ Qualidade

- **Testes**: `php console test` (Pest, 84 testes em `tests/` no módulo RH).
- **Análise estática**: PHPStan nível 9 (`./vendor/bin/phpstan analyse --no-progress --memory-limit=1G`).

---

Desenvolvido com foco em escalabilidade e facilidade de uso.