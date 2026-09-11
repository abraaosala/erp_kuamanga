# Módulo RH — Estado de Implementação

> Última actualização: 2026-09-10

---

## Infraestrutura base

- [x] `RhServiceProvider` — regista 13 repos + 13 services (26 bindings)
- [x] Rotas CRUD em `routes/rh.php` (79 rotas, prefixo `rh`, middleware `auth`)
- [x] Multi-empresa — scoping por `current_empresa()` em todos os repositories
- [x] Sidebar menu — 9 itens RH no layout

---

## Submódulos implementados

### 1. Departamentos

- [x] Migration `departments`
- [x] Model `Department` (SoftDeletes)
- [x] Repository interface + implementation
- [x] Service interface + implementation
- [x] Controller `DepartmentController` (CRUD completo)
- [x] Views `rh.departments.*` (index/create/edit)
- [x] Seed — 7 departamentos de exemplo

### 2. Cargos (Positions)

- [x] Migration `positions`
- [x] Model `Position` (SoftDeletes, belongsTo department)
- [x] Repository interface + implementation (`findByDepartment`)
- [x] Service interface + implementation (`getByDepartment`)
- [x] Controller `PositionController` (CRUD completo)
- [x] Views `rh.positions.*` (index/create/edit)
- [x] Seed — 18 cargos de exemplo

### 3. Funcionários

- [x] Migration `employees` + alterações (add department_id/position_id, remove salary/position/department legacy)
- [x] Camposs `bi` e `inss` (números de identificação) na tabela `employees`
- [x] Model `Employee` (SoftDeletes, belongsTo department/position, hasMany contracts)
- [x] Repository interface + implementation (eager-loads position+department)
- [x] Service interface + implementation
- [x] Controller `EmployeeController` (CRUD completo)
- [x] View de perfil `rh.employees.show` — dados pessoais, resumo, contratos, escalas, documentos e saldo de horas
- [x] Rota `GET /rh/employees/{id}` + ícone de perfil na listagem
- [x] Views `rh.employees.*` (index/create/edit) — inclui campos BI/INSS
- [x] Inputs com ícones (lucide) + máscaras JS: BI (`999999999AB000`), telefone (`+244 9XX XXX XXX`), INSS (só números)
- [x] Validação back-end de formato: BI (`^[0-9]{9}[A-Za-z]{2}[0-9]{3}$`), INSS (`digits_between:6,12`)
- [x] Upload de documento inicial opcional no formulário de criação
- [x] Foto de perfil do funcionário: campo `photo` em `employees`, input de preview (Alpine `URL.createObjectURL`) em create/edit, avatar em index/show, rota `GET /rh/employees/{id}/photo`, upload JPG/PNG máx 2MB (storage/uploads/employees/{id}/), remoção do ficheiro em update/destroy
- [x] Seed — 17 funcionários de exemplo

### 3.1 Documentos do Funcionário

- [x] Migration `employee_documents` (employee_id, empresa_id, document_type, document_number, file_path, file_name, file_size, mime_type)
- [x] Model `EmployeeDocument` (belongsTo employee/empresa)
- [x] Relação `documents()` no Model `Employee` (hasMany)
- [x] Repository + Service (`EmployeeDocumentRepository`/`EmployeeDocumentService`)
- [x] Controller `EmployeeDocumentController` (upload/download/remove)
- [x] Rotas em `routes/rh.php` (3 rotas de documentos)
- [x] Helpers `upload_file` / `download_file` em `app/Core/helpers.php`
- [x] Storage — `storage/uploads/employees/{id}/` (fora do `public/`)
- [x] Tipos de documento: BI, INSS, Contrato, Atestado médico, Certificado (vários por tipo)
- [x] Upload/remoção integrados na view `rh.employees.edit` e documento inicial na `rh.employees.create`
- [x] Validação: PDF/JPG/PNG, máx. 2MB
- [x] RhServiceProvider — bindings registados

### 4. Contratos

- [x] Migration `contracts` + alteração (remove funcao)
- [x] Model `Contract` (SoftDeletes, belongsTo employee, casts dates)
- [x] Repository interface + implementation (`findByEmployee`, search by employee name)
- [x] Service interface + implementation (`getByEmployee`)
- [x] Controller `ContractController` (CRUD completo)
- [x] Views `rh.contracts.*` (index/create/edit) — badges de status, @switch tipo_contrato

### 5. Assiduidade (Ponto)

- [x] Migration `attendance`
- [x] Model `Attendance` (SoftDeletes, belongsTo employee)
- [x] Repository interface + implementation
- [x] Service interface + implementation
- [x] Controller `AttendanceController` (CRUD completo)
- [x] Views `rh.attendance.*` (index/create/edit) — @switch status

### 6. Escalas de Trabalho

- [x] Migration `work_schedules`
- [x] Model `WorkSchedule` (SoftDeletes)
- [x] Repository interface + implementation
- [x] Service interface + implementation
- [x] Controller `WorkScheduleController` (CRUD completo)
- [x] Views `rh.schedules.*` (index/create/edit) — checkboxes dias da semana

### 7. Banco de Horas

- [x] Migration `hour_bank_entries`
- [x] Model `HourBankEntry` (SoftDeletes, belongsTo employee)
- [x] Repository interface + implementation (`balanceByEmployee`, `summary`)
- [x] Service interface + implementation
- [x] Controller `HourBankEntryController` (CRUD completo + summary)
- [x] Views `rh.hour_bank.*` (index/create/edit) — grid de saldos por funcionário

---

## O que NÃO existe ainda

### 8. Vínculo Escala ↔ Funcionário

- [x] Migration `employee_schedules` (pivot)
- [x] Relação no Model `Employee` (`belongsToMany WorkSchedule`)
- [x] Relação no Model `WorkSchedule` (`belongsToMany Employee`)
- [x] Repository + Service (`EmployeeScheduleRepository`/`EmployeeScheduleService`)
- [x] Controller `EmployeeScheduleController` (dedicado)
- [x] Rotas em `routes/rh.php` (5 rotas)
- [x] Views `rh.employee_schedules.*` (index, assign)
- [x] RhServiceProvider — bindings registados

### 9. Rosters / Rotação de Turnos

- [x] Migration `rotations` (nome, `pattern` JSON ex. `[6,2]`, datas, status) + `scheduled_shifts` (employee_id, date, work_schedule_id, classification `TRABALHO`/`FOLGA`, source `GERADA`, rotation_id) — unique `(employee_id, date)`
- [x] Models `Rotation` (cast `pattern` array, hasMany scheduledShifts) e `ScheduledShift` (belongsTo employee/workSchedule/rotation, SoftDeletes)
- [x] Repository + Service (`RosterRepository`/`RosterService` interface + impl, bindings no `RhServiceProvider`)
- [x] Geração de rotação com **cobertura contínua da equipa** (`RosterService::generate`): desfasamento (stagger) do ciclo entre funcionários — ex. 2×2 com 2 funcionários → nunca há dia descoberto
- [x] Regeneração segura: `deleteGeneratedBetween` remove turnos `GERADA` do período/scoped por equipa antes de reinserir (sem violar unique `(employee_id, date)`; gerações de outra equipa no mesmo período não são apagadas)
- [x] Controller `RosterController` + rotas em `routes/rh.php` (5 rotas): `index`, `events` (JSON FullCalendar, opcional `?rotation_id=`), `employees` (JSON da escala), `store` (gerar), `destroy`
- [x] View `rh.rosters.index` — FullCalendar (month/week) bundle via npm (`@fullcalendar/core`, `daygrid`, `interaction`) carregados em `resources/js/app.js`; eventos coloridos (trabalho roxo / folga cinza), form de geração com padrão do ciclo, datas e funcionários (multi-select com pesquisa e contador), filtro por rotação no calendário, KPI cards (trabalho/folga hoje, funcionários em rotação, turnos gerados) e presets de período (mês/90 dias/1 ano)
- [x] Seeder `RosterSeeder` — 2 escalas (`Turno Diurno` 08:00–17:00, `Turno Operacional` 07:00–16:00), 25 employee_schedules, 2 rotações demo (`Rotação 5×2 — Administrativo` e `Rotação 6×2 — Operações`, 43 dias a partir de hoje, ~1075 scheduled_shifts); idempotente (`TRUNCATE` das 4 tabelas antes de re-gerar); depende de `EmployeeSeeder` (para utilizar o `RosterService`, o seeder faz bootstrap da app com `@session_start()` para evitar o warning de headers do phinx)
- [x] Testes Pest (8 em `tests/Modules/Rh/RosterTest.php` — inclui "duas rotações no mesmo período mantêm as suas shifts", filtro de eventos por rotação e `stats` agregado)
- [x] Endpoint `events` aceita `?rotation_id=` (filtro server-side); `RosterService::stats(today)` devolve KPI aggregate (trabalho/folga hoje, total de turnos, rotações, funcionários cobertos)
- [ ] Galeria de tarefas futuras: edição manual de dias (drag & drop), integração com assiduidade, folgas rotativas per-funcionário

### 10. Folha Salarial (Payroll)

- [x] Migration `payroll_runs` (período, status, totais, employee_count)
- [x] Migration `payslips` (salário base, horas extra, faltas, líquido)
- [x] Models (`PayrollRun`, `Payslip`) + relação `payslips()` no `Employee`
- [x] Repository + Service (interface + impl, bindings no `RhServiceProvider`)
- [x] Controller `PayrollController` + Views (`rh.payroll.*` index/create/show) + rotas em `routes/rh.php`
- [x] Cálculo salarial (`PayrollService::runFromContracts`): salário base do contrato ativo + horas extra do banco de horas (tipo `horas_extra`, taxa x1.5) − descontos por faltas (assiduidade, status `falta`, diária = salário/30)
- [x] Guarda sem funcionários elegíveis: `hasActiveEligiblePayroll()` bloqueia geração sem contrato activo com salário base, com aviso na view e botão desabilitado
- [x] Seeder `EmployeeContractSeeder` — 25 funcionários demo com contrato de 5 anos (`data_fim` = `data_inicio` + 5 anos, `tipo_contrato` `determinado`, `status` `active`)
- [x] Integração com contratos (`findActiveByEmployee`), banco de horas (`overtimeHoursBetween`) e assiduidade (`absentDaysBetween`)
- [x] Testes Pest (6 novos em `tests/Modules/Rh/PayrollTest.php`)
- [x] Cálculo de descontos legais no `runFromContracts`: Segurança Social (3% do bruto) + IRT Grupo A 2026 (`IrtCalculator` — 11 escalões OGE 2026, Lei n.º 14/25; base = bruto − SS); campo `total_deductions` = SS + IRT + faltas; coluna `net_salary` = bruto − SS − IRT − faltas
- [x] Migration `add_irt_social_security_to_payslips` — colunas `social_security` e `irt_amount` em `payslips` (decimal 14,2, default 0)
- [x] Testes IRT (9 em `tests/Modules/Rh/IrtCalculatorTest.php` — limites de escalão e valores intermédios)
- [x] Geração de recibos de vencimento (PDF via dompdf): rota `GET /rh/payroll/payslip/{id}/recibo`, método `PayrollController::recibo()`, view standalone `rh.payroll.recibo` (cabeçalho da empresa, dados do funcionário, rubricas, líquido, assinaturas); botão de recibo por linha na view `show`
- [x] Recibo por extenso: `App\Support\ValorExtenso` (montantes em kwanzas/cêntimos, pt-AO) + testes em `tests/Modules/Rh/ValorExtensoTest.php`

### 11. Férias e Licenças

- [x] Migration `leaves` / `leave_requests` (decisão, workflow e registo efectivo de férias/licenças)
- [x] Models `Leave` / `LeaveRequest` (SoftDeletes, belongsTo employee) + relações no `Employee`
- [x] `LeavePolicy` — regras legais (LGT Angola, Lei n.º 7/15 e 12/23): 22 dias úteis/ano após 1 ano de serviço; dias por antiguidade no 1.º ano (2 dias/mês completo, mín. 6); dias contados de forma inclusiva
- [x] Repository + Service (`LeaveRepository`/`LeaveService` interface + impl, bindings no `RhServiceProvider`)
- [x] Workflow de pedido → aprovação/rejeição: `pendente` → `aprovado`/`rejeitado`/`cancelado` (só pendentes decidíveis; aprovação regista a férias efectiva em `leaves`; rejeição/cancelamento não gastam saldo)
- [x] Saldo de férias por funcionário: `entitled` (por antiguidade) − `used` (férias efectivas) = `available`; bloqueio de pedidos acima do saldo e de sobreposição com férias já aprovadas
- [x] Controller + Views (`rh.leaves.*` index/create) + rotas em `routes/rh.php` (7 rotas) + item no sidebar; index com separadores (tabs) **Pedidos** (KPIs + tabela) e **Saldos**
- [x] Tipos de pedido: férias, licença de maternidade/paternidade/doença, licença remunerada/não remunerada, falta justificada (só `ferias` consome o saldo anual)
- [x] Testes Pest (18 em `tests/Modules/Rh/LeaveTest.php`)

### 12. Benefícios

- [x] Migration `benefits` (catálogo) / `employee_benefits` (atribuição; unique `employee_id, benefit_id`)
- [x] Model `Benefit` (SoftDeletes, belongsTo position/department, belongsToMany employees) + relação `benefits()` no `Employee`
- [x] `BenefitPolicy` — regras de elegibilidade puras: restrição a cargo (`position_id`), a departamento (`department_id`) e antiguidade mínima (`min_tenure_months`, calculada a partir de `hire_date`); `violations()` devolve os motivos em português
- [x] Repository + Service (`BenefitRepository`/`BenefitService` interface + impl, bindings no `RhServiceProvider`)
- [x] Controller `BenefitController` (CRUD + show, atribuição/remoção de funcionários)
- [x] Views `rh.benefits.*` (index com KPIs e catálogo, create/edit, show com elegíveis e atribuídos) + rotas em `routes/rh.php` (9 rotas) + item no sidebar
- [x] Atribuição protegida: `assign()` valida elegibilidade e bloqueia não elegíveis e duplicados; remoção não apaga o catálogo
- [x] Benefícios do funcionário visíveis no perfil (`rh.employees.show`): secção própria com estado, categoria, data de início, link para o benefício e remoção directa
- [x] Testes Pest (16 em `tests/Modules/Rh/BenefitTest.php`)

### 13. Recrutamento e Seleção

- [ ] Migration `job_openings`, `candidates`, `interviews`
- [ ] Models + Repository + Service
- [ ] Controller + Views
- [ ] Pipeline: vaga → candidatura → entrevista → decisão
- [ ] Banco de talentos

### 14. Avaliação de Desempenho

- [ ] Migration `performance_reviews`, `goals`
- [ ] Models + Repository + Service
- [ ] Controller + Views
- [ ] Avaliações periódicas / 360º
- [ ] PDI (Plano de Desenvolvimento Individual)

### 15. Portal do Colaborador

- [ ] Área autenticada do colaborador (self-service)
- [ ] Consulta de dados pessoais, documentos, recibos
- [ ] Pedido de férias / ausências
- [ ] Consulta de ponto e banco de horas
- [ ] Comunicados internos

### 16. Relatórios e Indicadores

- [ ] Dashboard de RH (headcount, turnover, absenteísmo)
- [ ] Relatório de custos com pessoal
- [ ] Relatório de férias pendentes
- [ ] Relatório de horas extras
- [ ] Exportação (PDF/Excel)

---

## Notas técnicas

- **Total de ficheiros RH:** 1 provider, 1 routes (79 rotas), 13 controllers, 13 repos (interface+impl), 13 services (interface+impl), 15 models, 16 migrations, 4 seeds, 34 views
- **Status conventions:** employees/departments/positions/contracts usam `active`/`inactive`; schedules/rotations usam `ativo`/`inativo`; attendance usa `presente`/`atrasado`/`falta`/`justificado`; scheduled_shifts usa `TRABALHO`/`FOLGA`
- **Soft deletes** em todos os models
- **Testes Pest operacionais** — 84 testes em `tests/Modules/Rh/` (`RosterTest.php`, `EmployeeScheduleTest.php`, `PayrollTest.php`, `IrtCalculatorTest.php`, `ValorExtensoTest.php`, `LeaveTest.php`, `BenefitTest.php`); rode com `php console test`
- **PHPStan nível 9** — verde (`./vendor/bin/phpstan analyse --no-progress --memory-limit=1G`)

---

*(Kuamanga ERP — 2026)*
