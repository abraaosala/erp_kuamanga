# Módulo RH — Estado de Implementação

> Última actualização: 2026-09-04

---

## Infraestrutura base

- [x] `RhServiceProvider` — regista 7 repos + 7 services (14 bindings)
- [x] Rotas CRUD em `routes/rh.php` (42 rotas, prefixo `rh`, middleware `auth`)
- [x] Multi-empresa — scoping por `current_empresa()` em todos os repositories
- [x] Sidebar menu — 7 itens RH no layout

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
- [x] Tipos de documento: BI, INSS, Contrato, Atestado médico, Certificado, Foto (vários por tipo)
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

### 9. Folha Salarial (Payroll)

- [ ] Migration `payslips` / `payroll_runs`
- [ ] Models (`PayrollRun`, `Payslip`)
- [ ] Repository + Service (cálculo salarial)
- [ ] Controller + Views
- [ ] Integração com contratos (salário_base)
- [ ] Integração com banco de horas (horas extra)
- [ ] Integração com assiduidade (faltas/descontos)
- [ ] Geração de recibos de vencimento (PDF)
- [ ] Gestão de IRT / descontos legais

### 10. Férias e Licenças

- [ ] Migration `leaves` / `leave_requests`
- [ ] Model + Repository + Service
- [ ] Controller + Views
- [ ] Workflow de pedido → aprovação/rejeição
- [ ] Saldo de férias por funcionário
- [ ] Regras legais (dias por antiguidade)

### 11. Benefícios

- [ ] Migration `benefits` / `employee_benefits`
- [ ] Model + Repository + Service
- [ ] Controller + Views
- [ ] Regras de elegibilidade (cargo, departamento, antiguidade)

### 12. Recrutamento e Seleção

- [ ] Migration `job_openings`, `candidates`, `interviews`
- [ ] Models + Repository + Service
- [ ] Controller + Views
- [ ] Pipeline: vaga → candidatura → entrevista → decisão
- [ ] Banco de talentos

### 13. Avaliação de Desempenho

- [ ] Migration `performance_reviews`, `goals`
- [ ] Models + Repository + Service
- [ ] Controller + Views
- [ ] Avaliações periódicas / 360º
- [ ] PDI (Plano de Desenvolvimento Individual)

### 14. Portal do Colaborador

- [ ] Área autenticada do colaborador (self-service)
- [ ] Consulta de dados pessoais, documentos, recibos
- [ ] Pedido de férias / ausências
- [ ] Consulta de ponto e banco de horas
- [ ] Comunicados internos

### 15. Relatórios e Indicadores

- [ ] Dashboard de RH (headcount, turnover, absenteísmo)
- [ ] Relatório de custos com pessoal
- [ ] Relatório de férias pendentes
- [ ] Relatório de horas extras
- [ ] Exportação (PDF/Excel)

---

## Notas técnicas

- **Total de ficheiros RH:** 78 (1 provider, 1 routes, 7 controllers, 14 interfaces, 14 implementations, 7 models, 11 migrations, 2 seeds, 21 views)
- **Status conventions:** employees/departments/positions/contracts usam `active`/`inactive`; schedules usam `ativo`/`inativo`; attendance usa `presente`/`atrasado`/`falta`/`justificado`
- **Soft deletes** em todos os models
- **Nenhum teste unitário** — directório `tests/` não existe (apenas `EmployeeScheduleTest.php`)
- **PHPStan nível 5** — único QA operacional

---

*(Kuamanga ERP — 2026)*
