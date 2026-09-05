# Agent Rules

> ERP Kuamanga — sistema de gestão com módulos User, Accounting (PGC Angola) e RH.
> Idioma do projeto: **português** (mensagens, comentários, UI). Código/dados em pt-BR, fuso `Africa/Luanda`.

## Arquitetura — NÃO é Laravel

Este é um micro-framework **custom** construído sobre componentes Illuminate avulsos (`illuminate/database`, `illuminate/routing`, `illuminate/container`, `illuminate/validation`, `illuminate/pagination`). Não há Artisan, nem Laravel app boot.

- Bootstrap: `bootstrap/app.php` → `app/Core/Application.php` (manual service-provider boot, session, Pillar routing, Eloquent via Capsule).
- Entrypoints: `public/index.php` (web) e o script `console` (CLI).
- Helpers globais em `app/Core/helpers.php` (`app()`, `config()`, `view()`, `response()`, `redirect()`, `session()`, `current_empresa()`, `all_empresas()`). Cuidado: só existem os que estão lá.
- Templates usam **BladeOne** (`eftec/bladeone`), não Blade nativo do Laravel. Render via `$blade->run('nome', $data)` ou helper `view()`.

## CLI (`php console <comando>`)

- Principais: `migrate`, `rollback`, `migrate:status`, `migrate:breakpoint` (wrappers Phinx), `db:create`, `db:drop`, `db:seed`, e `make:controller|model|repository|service|middleware|view|migration|seed`, `serve`.
- `serve` inicia o servidor PHP + roda `npm run dev` (watcher de assets). Não usa `php artisan serve`.
- **Duplicação de comandos**: existem classes antigas com sufixo `*Command.php` (`DbSeedCommand`, `MigrateCommand`, `MakeControllerCommand`…) e versões novas sem sufixo (`DbSeed`, `Migrate`, `MakeController`…). O `console` registra as novas via instanciação direta + apenas `DbSeedCommand` (a antiga, conflito `db:seed`). Ao adicionar um comando, registre-o explicitamente em `console` — seguindo o padrão das versões novas.
- `make:migration` delega ao `phinx create`; migrations ficam em `database/migrations`, seeds em `database/seeds`.

## Testes / QA estático

Ferramentas dev **instaladas** em `require-dev`:
- **Pest ^5.0** — framework de testes. Binário: `./vendor/bin/pest` (ou `pest` no Windows).
- **PHPStan ^2.2** — análise estática. Binário: `./vendor/bin/phpstan`.

Estado atual (verificar antes de confiar):
- **Comando `test` registado em `console`**: `php console test` roda o Pest (suporta filtro: `php console test "prevents duplicate"`); `composer test` também funciona (aponta para `php console test`).
- **Pest ^5 é operacional**: `tests/` existe com `Pest.php` (`uses(TestCase::class)->in('Modules')`) e `TestCase.php`. Testes em `tests/Modules/{Modulo}/`. Rode com `php console test` (ou `php vendor/bin/pest` / `./vendor/bin/pest`) — hoje: 9 testes passando (módulo RH, vínculo Escala↔Funcionário). Não há `pest.php` nem `phpunit.xml` (Pest usa `tests/Pest.php`, funciona sem eles). `database/test.sqlite` (gitignored) é recriado por cada teste.
- **TestCase.php bootstraps** Eloquent via Capsule + SQLite de ficheiro, com migrations inline (`runMigrations()`): `empresas`, `departments`, `positions`, `employees`, `work_schedules`, `employee_schedules`. **Atenção:** o schema de teste não espelha automaticamente as migrations Phinx — campos novos (ex.: `employees.bi`, `inss`) precisam ser adicionados manualmente ao `runMigrations()` do `TestCase` para os modelos não falharem em massa nos testes (`photo` já está lá). Helpers factory: `createEmpresa()` / `createEmployee()` / `createSchedule()`.
- **PHPStan está configurado e verde (nível 9)**: `phpstan.neon` (nível 9) + `bootstrap/phpstan.php` (define `BASE_PATH`) + stub `stubs/eloquent.stub`. Rode com `./vendor/bin/phpstan analyse` (sem erros hoje; use `--no-progress --memory-limit=1G` se estourar o pagefile do Windows). O stub suplementa os métodos dinâmicos do Eloquent puro (Model/Builder/shells de Collection) — `@template TModel` + relations genéricas. Regras praticadas no nível 9: models declaram `@extends \Illuminate\Database\Eloquent\Model<self>` + `@var array<string, string>` no `$casts` + `@return BelongsTo/HasMany/BelongsToMany<X, self>` nas relations; repos/services (interface + impl) declaram `@param array<string, mixed> $data` e `@return Collection<int, X>` / `LengthAwarePaginator<int, X>` / `array<string, mixed>`; chains Eloquent cujo Builder não infere o tipo concreto tipam com variável intermediária + `@var` no repositório/service (padrão aprovado do projeto — NÃO é `@phpstan-ignore`); controllers declaram return `string` (blade/vie), `\Illuminate\Http\Response`, `\Illuminate\Http\RedirectResponse` ou `never` (header+exit). Valores `mixed` de `config()`/`env()`/`session()`/`request()`/`make('config.x')`/`$_SESSION`/`$_SERVER`/`$_GET` são tipados com variável intermediária `@var` (array shape/list/class) + guards reais (`is_string`, `is_numeric`, `is_array`, `instanceof`); queries com `join`+`selectRaw` que o PHPStan infere `mixed` tipam como `@var \Illuminate\Support\Collection<int, \stdClass>` antes do `foreach`; `(int)`/`(float)`/`(string)` direto em `mixed` são substituídos por guards (`is_numeric(...) ? (int)... : default`). Cuidados do nível 9 em funções nativas: `glob()`/`file_get_contents()`/`query()` retornam `false` além do tipo — verifique antes de iterar/retornar; `session_name()` retorna `string|false`; `new $providerClass(...)` precisa de `@var class-string<\Illuminate\Support\ServiceProvider>` em variável local (não no parâmetro, para não vazar o narrow pros callers). Não usar `(string) mixed` — sempre guard/filtrar antes. Não substituir o Builder por stub separado — causa OOM no Windows.
- **`App\Core\Application` usa `App\Core\ApplicationContainer`** (extends `Illuminate\Container\Container`, implements `Illuminate\Contracts\Foundation\Application`) como container — resolve os type-hints de `Facade::setFacadeApplication()` e construtores de `EventServiceProvider`/`RoutingServiceProvider` sem mascarar. Mantém o comportamento de container puro para providers de módulo (que tipam `Illuminate\Container\Container`).

**Não assuma que o pipeline de teste funciona** — o comando `console test` não existia; agora existe e roda o Pest (`php console test`). Empiricamente 9 testes passam; verifique antes de prometer mais.

## Módulos (convenção de camadas)

Módulos em `App\Providers\Modules\{User,Accounting,Rh}`. Cada módulo segue o padrão **Repository → Service, com contratos (interfaces) + implementação**:

- Controllers: `app/Http/Controllers/Modules/{Modulo}/` (o controller legacy `CompanyController.php` fica fora dos módulos).
- Repositories: `app/Repositories/Contracts/{Interface}.php` + `app/Repositories/Modules/{Modulo}/{Impl}.php`.
- Services: `app/Services/Contracts/{Interface}.php` + `app/Services/Modules/{Modulo}/{Impl}.php`.
- Models: `app/Models/` (flat, sem subpastas).

Configuração por depência: interfaces → implementações são ligadas nos `*ServiceProvider`. Ao criar um novo repository/service, registre no provider do módulo.

## Rotas

- `routes/web.php` tem o root redirect + CRUD de empresas.
- Cada módulo `require` seu arquivo de rotas no `boot()` do provider (`routes/auth.php`, `routes/users.php`, `routes/accounting.php`, `routes/rh.php`). Registrados em `config/app.php` → `providers`.
- Rotas de módulo costumam usar `AuthMiddleware` ou `'auth'`.

## Multi-empresa (tenant)

Contexto de "empresa" ativa vem da `session()->empresaId()`; helpers `current_empresa()` / `all_empresas()` em `app/Core/helpers.php`. Ao consultar dados que pertencem a empresa, considerar o escopo por empresa.

## Banco de dados

- Migrations/seeds via **Phinx** (`phinx.php` lê do `.env`). Config de conexão em `config/database.php` (default `mysql`, com fallback `sqlite`).
- Eloquent capsula via `app/Core/Database.php`; `User::setPasswordAttribute` faz bcrypt automático.

## Frontend / assets

- Instale com `composer install` + `npm install`.
- `npm run build` (Tailwind CLI + esbuild) gera **`public/css/app.css` e `public/js/app.js` — gitignored**; precisam de `npm run build` (ou `npm run dev`/`npm run serve`) para existir. Não edite esses arquivos gerados.
- Scripts npm usam `.cmd` do Windows (`.\node_modules\.bin\*.cmd`). Tailwind content em `resources/views/**/*.blade.php` e `resources/js/**/*.js`; `darkMode: 'class'`.
- `php console serve` já aciona o watcher npm.

## Stubs

Comandos `make:*` geram arquivos a partir de `stubs/*.stub` (`controller.stub`, `model.stub`, `repository.stub`, `service.stub`, `middleware.stub`, `view.stub`). `controller.stub` estende `Illuminate\Routing\Controller` (é um dummy vazio do Illuminate, não controller de módulo) — ao criar controllers reais use o padrão dos módulos existentes.

## Convenções de código

- `declare(strict_types=1)` no topo de todos os arquivos PHP.
- PHP 8.1+; mensagens de erro/validação em português.
- Sem formatter/linter configurado (sem `.php-cs-fixer`, `phpcs`, Prettier) — siga o estilo dos arquivos existentes.

## Framework de skills (HotStack)

- O projeto usa o **HotStack** (`.hot/`): `config.toml` configurado (`name = "kuamanga-erp"`, `agents.opencode = true`, língua pt, fuso `Africa/Luanda`).
- `PROJECT.md` do HotStack é fonte de contexto para os agents; mantenha-o sincronizado com `AGENTS.md` quando algo mudar.
- Skills locais em `.opencode/skills/{nome}/SKILL.md` (ex.: `clean-architecture`, `ddd`, `deps-upgrade`, `github-pr-workflow`, `php-psr-best-practices`, `php-84-85-features`, `branch-and-pr-workflow`) — estas são as do projeto, não as globais de `~/.agents/skills`. Para novas skills siga o formato SKILL.md destas.

## GitHub / MCP

- Repositório: `abraaosala/erp_kuamanga` (remote `git@github.com:abraaosala/erp_kuamanga.git`); branch padrão **`master`**. Feature branches `feat/{nome}` são mergeados em `master` (merge commit, preservando histórico — ver skill `branch-and-pr-workflow`).
- **MCP do GitHub configurado e funcional** (via `opencode.json` → `https://api.githubcopilot.com/mcp/`). Preferir as ferramentas `github_*` (listar branches/issues/PRs, procurar código, commits, PR reviews, secret scanning) para operações no GitHub — autenticam pelo token do MCP.
- **`gh` CLI instalado (v2.97) mas o token no keyring está inválido** — `gh auth status` falha. Verifique antes de usar `gh`; se falhar, use as ferramentas `github_*` do MCP em alternativa.
- **Conventional Commits** (`feat:`, `fix:`, …). Referência na branch `remote/origin/master`.

## Documentação de módulos

Cada módulo tem um ficheiro de tracking em `docs/modules/{modulo}.md` com checkboxes `[x]`/`[ ]` que reflectem o estado de implementação.

**REGRA OBRIGATÓRIA:** Sempre que houver mudança no código de um módulo (novo controller, migration, model, route, view, etc.), actualizar o correspondente `docs/modules/{modulo}.md` — marcar items como `[x]`, adicionar novos items, ou criar novas secções conforme necessário.

- `docs/modules/rh.md` — estado do módulo RH
- `docs/modules/accounting.md` — estado do módulo Accounting
