# 📋 Plano Geral de Implementação (Fases 2-5)

Este documento resume as próximas etapas de desenvolvimento do ERP, focando nos módulos essenciais.

## 📅 Roadmap de Módulos

### ✅ FASE 1: Core & Auth (Concluído/Em Base)
- Estrutura base do framework.
- Gestão de Usuários, Roles e Permissões.

### 🚀 FASE 2: Contabilidade (Em Desenvolvimento)
- Plano Geral de Contabilidade (PGC Angola - Decreto 82/01).
- Sistema de Lançamentos de Partida Dobrada (Atomicidade).
- Balancetes e Demonstração de Resultados (Próximos passos).
- CRUD completo do Plano de Contas.

### 📊 FASE 3: Fiscalidade & Faturamento
- Gestão de Taxas de IVA.
- Emissão de faturas com log de auditoria imutável.

### 👥 FASE 4: Recursos Humanos (RH)
- Cadastro de colaboradores com SoftDeletes.
- Processamento salarial automático e geração de recibos.

### 📂 FASE 5: Consultoria & Projetos
- Gestão de Tarefas com Kanban.
- Registro de horas (Time Tracking).

---

> [!IMPORTANT]
> A integridade e segurança dos dados são transversais a todos os módulos. O uso de **Transactions** e **SoftDeletes** é obrigatório em registros financeiros e cadastros críticos.
