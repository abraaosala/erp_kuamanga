# Manual do Utilizador: Módulo de RH

Bem-vindo ao **Módulo de Recursos Humanos (RH)** do Kuamanga ERP.
Este módulo foi criado para centralizar a gestão de todos os colaboradores da empresa, desde o registo inicial até ao controlo de dados funcionais e salariais.

Abaixo detalhamos todas as ferramentas disponíveis e como utilizá-las no teu dia a dia.

---

## 1. Seleção de Empresa (Multi-Empresa)
O ERP suporta múltiplas empresas em simultâneo.
*   **Atenção:** Antes de registares qualquer funcionário, deves **sempre** garantir que a empresa correcta está selecionada.
*   Podes alternar a empresa ativa a qualquer momento clicando no botão no topo do menu lateral (Sidebar). Todos os registos de RH refletirão *apenas* dados da empresa selecionada.

---

## 2. Departamentos

O submódulo de Departamentos permite gerir a estrutura organizacional da empresa.

*   **Aceder a:** `RH > Departamentos`

### 2.1. Lista de Departamentos
Tabela com todos os departamentos registados: **Nome**, **Descrição**, **Status** (Ativo/Inativo). Botões de **Editar** e **Excluir** (com confirmação) em cada linha.

### 2.2. Cadastrar Novo Departamento
*   **Aceder a:** `RH > Departamentos > Novo Departamento`

| Campo       | Obrigatório | Descrição                     |
|-------------|-------------|-------------------------------|
| Nome        | Sim         | Ex: Contabilidade, RH         |
| Descrição   | Não         | Breve descrição do departamento|

### 2.3. Editar Departamento
Permite alterar nome, descrição e status (Ativo/Inativo).

### 2.4. Remover Departamento
Clique no ícone de lixeira e confirme. A exclusão é lógica (soft delete).

---

## 3. Cargos

O submódulo de Cargos define as funções e faixas salariais dentro da empresa, opcionalmente associadas a um departamento.

*   **Aceder a:** `RH > Cargos`

### 3.1. Lista de Cargos
Tabela com: **Nome**, **Departamento**, **Faixa Salarial**, **Status**. Botões de **Editar** e **Excluir** em cada linha.

### 3.2. Cadastrar Novo Cargo
*   **Aceder a:** `RH > Cargos > Novo Cargo`

| Campo             | Obrigatório | Descrição                                |
|-------------------|-------------|------------------------------------------|
| Nome              | Sim         | Ex: Contabilista Sénior                  |
| Descrição         | Não         | Responsabilidades do cargo               |
| Departamento      | Não         | Departamento ao qual o cargo pertence    |
| Salário Mínimo    | Não         | Limite inferior da faixa (AOA)           |
| Salário Máximo    | Não         | Limite superior da faixa (AOA)           |

### 3.3. Editar Cargo
Permite alterar todos os campos, incluindo status (Ativo/Inativo).

### 3.4. Remover Cargo
Clique no ícone de lixeira e confirme. A exclusão é lógica (soft delete).

---

## 4. Funcionários

O submódulo de Funcionários é o bloco fundamental do RH. Aqui podes cadastrar, editar, consultar e remover colaboradores, agora com vínculo direto a departamentos e cargos.

*   **Aceder a:** `RH > Funcionários`

### 4.1. Lista de Funcionários
Ao entrares no submódulo, és recebido por uma tabela com todos os colaboradores registados.

*   A tabela mostra: **Nome**, **Email**, **Cargo**, **Departamento** e **Status** (Ativo / Inativo).
*   Cada linha tem botões de **Editar** (ícone de lápis) e **Excluir** (ícone de lixeira com confirmação).
*   No topo da tabela, o total de registos e a paginação (se houver mais de 15 funcionários) ajudam-te a navegar.

### 4.2. Cadastrar Novo Funcionário
*   **Aceder a:** `RH > Funcionários > Novo Funcionário` (botão no topo da lista)

Preenche os seguintes campos:

| Campo             | Obrigatório | Descrição                                |
|-------------------|-------------|------------------------------------------|
| Nome completo     | Sim         | Nome do colaborador                      |
| Email             | Não         | Endereço de correio eletrónico           |
| Telefone          | Não         | Contacto telefónico                      |
| Departamento      | Não         | Selecionar da lista de departamentos     |
| Cargo             | Não         | Selecionar da lista de cargos            |
| Salário (AOA)     | Não         | Salário base em Kwanzas                  |
| Data de Admissão  | Não         | Data em que o colaborador iniciou funções|

Após preencher, clica em **Salvar**. Serás redirecionado de volta à lista com uma mensagem de sucesso.

### 4.3. Editar Funcionário
*   **Aceder a:** Clica no ícone de editar (lápis) ao lado do funcionário na lista.

Podes alterar qualquer campo do registo, incluindo o **Status** (Ativo / Inativo) e a associação a departamento/cargo. Clica em **Atualizar** para guardar as alterações.

### 4.4. Remover Funcionário
*   Na lista, clica no ícone de lixeira.
*   O botão muda para **Confirmar** — clica novamente para confirmares a exclusão.
*   A exclusão é lógica (soft delete): o registo fica oculto, mas pode ser recuperado se necessário.

---

## 5. Submódulos Futuros

O módulo de RH será expandido com os seguintes submódulos nas próximas versões:

*   **Contratos** — Registo de contratos de trabalho, renovações, termos.
*   **Ausências e Férias** — Controlo de faltas, licenças, férias e respectivos saldos.
*   **Processamento Salarial** — Cálculo de salários, impostos (IRT), segurança social e emissão de recibos.
*   **Avaliações** — Gestão de desempenho e avaliações periódicas.
*   **Recrutamento** — Pipeline de candidaturas, entrevistas e admissões.

---

## Próximos Passos

Continua a acompanhar as actualizações do Kuamanga ERP para novidades no módulo de RH. Novos submódulos serão activados automaticamente na sidebar à medida que forem disponibilizados.

*(Fim do Manual. Kuamanga - 2026).*
