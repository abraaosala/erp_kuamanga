# Agent Rules


## Contexto do Projeto

# Kuamanga ERP — Contexto do Projeto

> Sistema de gestão empresarial built sobre micro-framework custom (Illuminate avulsos, não Laravel).
> Idioma: **português**. Fuso horário: `Africa/Luanda`.

## Arquitetura

- Bootstrap: `bootstrap/app.php` → `app/Core/Application.php`
- Helpers: `app/Core/helpers.php` (`app()`, `config()`, `view()`, `current_empresa()`)
- Templates: **BladeOne** (`eftec/bladeone`)
- Banco: Phinx migrations + Eloquent (Capsule)
- Multi-empresa: scoping por `current_empresa()->id`

## Módulos

### User
- Autenticação e gestão de utilizadores
- Rotas: `routes/auth.php`, `routes/users.php`

### Accounting (PGC Angola)
- Contabilidade / Plano Geral de Contabilidade de Angola
- Rotas: `routes/accounting.php`

### RH (Recursos Humanos)
- Estado actual: **13 submódulos implementados** (CRUD completo)
  - Departamentos, Cargos, Funcionários (+ Documentos digitais), Contratos, Assiduidade, Escalas, Banco de Horas, **Vínculo Escala↔Funcionário**, **Rosters/Rotação de Turnos**, **Folha Salarial (IRT/SS/recibos PDF)**, **Férias e Licenças**, **Benefícios**
- Pendentes (roadmap/menu): Recrutamento, Avaliação, Portal do Colaborador, Relatórios, Formação, Carreira e Sucessão, Gestão Disciplinar, Saúde e Segurança, Auditoria, Configurações, Exportação CSV, Impressão/PDF
- Docs de tracking: `docs/modules/rh.md`
- **REGRA: cada actualização ao módulo RH — código, rotas, views, testes, totais — deve manter o `docs/modules/rh.md` coerente no próprio commit/PR (contagens, checkboxes `[x]`, roadmap pendente, linhas injectadas tiradas), de modo a que a doc reflicta sempre o estado real**

## Convenções

- `declare(strict_types=1)` em todos os ficheiros PHP
- Repository → Service com interfaces + implementação
- Controllers injection via constructor (ServiceInterface, BladeOne, Validator)
- Flash messages via `$_SESSION` (`flash_success`/`flash_error`)
- Soft deletes em todos os models
- Mensagens em português

## Skills Obrigatórias

- **A cada codificação**, carregar e aplicar as skills do projecto (em `.opencode/skills/`):
  - `php-psr-best-practices` — PSR / boas práticas PHP moderno
  - `php-84-85-features` — recursos novos do PHP 8.4/8.5
  - `ddd` — Domain-Driven Design (bounded contexts, entities, services)

## Sincronização `.hot/PROJECT.md`

- `.hot/` é **gitignored** (contexto local do Hot). O `PROJECT.md` usa o directivo `@AGENT` para carregar este `AGENTS.md` como fonte única — **sem duplicação**.
- **REGRA: nenhuma alteração no `AGENTS.md` precisa de réplica manual no `PROJECT.md`.**

## QA

- PHPStan nível 9 (verde — rode com `./vendor/bin/phpstan analyse --no-progress --memory-limit=1G`)
- Pest ^5 operacional: `php console test` — **84 testes passando** (177 assertions); suporta filtro (`php console test "prevents duplicate"`); `composer test` funciona
- TestCase bootstraps Eloquent (Capsule + SQLite) com migrations inline; não há `pest.php`/`phpunit.xml` (Pest usa `tests/Pest.php`)
- Comando `php console test` (e `composer test`) roda o Pest — ver `tests/`
- **PHP-CS-Fixer (PER Coding Style 2.0)** é o gate de estilo: `composer cs` (corrige) e `composer cs:check` (dry-run + diff); config em `.php-cs-fixer.php`
- Não há outro linter/formatter configurado

## Frontend

- Tailwind CSS + Alpine.js
- `npm run build` gera `public/css/app.css` + `public/js/app.js` (gitignored)
- `php console serve` inicia PHP server + npm watcher

## GitHub / MCP

- Repositório: `abraaosala/erp_kuamanga` (remote `git@github.com:abraaosala/erp_kuamanga.git`); branch padrão `master`
- **MCP do GitHub funcional** via `opencode.json` → ferramentas `github_*` (branches, issues, PRs, code search, secret scanning)
- `gh` CLI instalado (v2.97) mas com token inválido no keyring — preferir MCP `github_*`
- Conventional Commits; merge de feature branches em `master` (ver skill `branch-and-pr-workflow`)

## Comandos CLI

- `php console serve` (PHP server + npm watcher) | `php console test` (Pest)
- `php console migrate|rollback|status|breakpoint`
- `php console seed:run` | `php console db:create|db:drop`
- `php console make:controller|model|repository|service|view|migration|seed|middleware`
