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

## 5. Contratos

O submódulo de Contratos regista os contratos de trabalho de cada colaborador, incluindo tipo, vigência e dados salariais.

*   **Aceder a:** `RH > Contratos`

### 5.1. Lista de Contratos
Tabela com os contratos registados: **Funcionário**, **Tipo de Contrato**, **Data de Início**, **Data de Fim**, **Salário Base**, **Status**. Botões de **Editar** e **Excluir** em cada linha, além de pesquisa e paginação.

### 5.2. Cadastrar Novo Contrato
*   **Aceder a:** `RH > Contratos > Novo Contrato`

| Campo           | Obrigatório | Descrição                                    |
|-----------------|-------------|----------------------------------------------|
| Funcionário     | Sim         | Selecionar da lista de funcionários          |
| Tipo de Contrato| Sim         | Ex: Termo certo, Indeterminado, Estágio      |
| Data de Início  | Sim         | Início da vigência do contrato               |
| Data de Fim     | Não         | Deve ser igual ou posterior à data de início |
| Salário Base (AOA)| Não       | Salário contratual em Kwanzas                |
| Carga Horária   | Não         | Ex: 40h/semanais                            |
| Observações     | Não         | Notas adicionais do contrato                 |

### 5.3. Editar Contrato
Permite alterar todos os campos do contrato, incluindo o **Status** (Ativo / Inativo).

### 5.4. Remover Contrato
A exclusão é lógica (soft delete).

---

## 6. Assiduidade (Ponto)

O submódulo de Assiduidade regista a presença diária dos colaboradores, incluindo entradas, saídas e faltas.

*   **Aceder a:** `RH > Assiduidade`

### 6.1. Lista de Assiduidade
Tabela com os registos de ponto: **Funcionário**, **Data**, **Entrada**, **Saída**, **Status** (Presente/Atrasado/Falta/Justificado). Botões de **Editar** e **Excluir** em cada linha, além de pesquisa e paginação.

### 6.2. Cadastrar Novo Registo de Ponto
*   **Aceder a:** `RH > Assiduidade > Novo Registo`

| Campo           | Obrigatório | Descrição                                    |
|-----------------|-------------|----------------------------------------------|
| Funcionário     | Sim         | Selecionar da lista de funcionários          |
| Data            | Sim         | Data do registo de ponto                     |
| Entrada         | Não         | Hora de entrada (formato HH:mm)              |
| Saída           | Não         | Hora de saída (formato HH:mm)                |
| Status          | Sim         | Presente / Atrasado / Falta / Justificado    |
| Observações     | Não         | Notas do registo                             |

### 6.3. Editar Registo de Ponto
Permite alterar todos os campos do registo.

### 6.4. Remover Registo de Ponto
A exclusão é lógica (soft delete).

---

## 7. Visão / Roadmap do Módulo de RH

O módulo de RH já cobre a **gestão de colaboradores** (departamentos, cargos, funcionários, contratos) e a **assiduidade** (ponto). A seguir está a visão completa do módulo, com as áreas a desenvolver nas próximas versões:

### 7.1. Recrutamento e seleção
- Publicação e gestão de vagas
- Receção e triagem de candidaturas
- Agendamento de entrevistas
- Registo de avaliações e criação de banco de talentos

### 7.2. Admissão e onboarding
- Recolha de documentos do novo colaborador
- Assinatura de contratos e termos
- Checklists de integração
- Atribuição de acessos, equipamentos e formação inicial

### 7.3. Ponto, presença e férias
- Registo de entradas, saídas, atrasos e horas extras
- Gestão de escalas e banco de horas
- Solicitação, aprovação e controlo de férias
- Registo de faltas, licenças e justificações

### 7.4. Folha salarial
- Cálculo de salários, subsídios, descontos e horas extras
- Geração de recibos de vencimento
- Gestão de impostos (IRT), contribuições e obrigações legais aplicáveis
- Exportação ou integração com sistemas contabilísticos e bancários

### 7.5. Benefícios
- Gestão de subsídio de alimentação, transporte, seguro e outros benefícios
- Regras de elegibilidade por cargo, departamento ou antiguidade
- Consulta de benefícios pelo colaborador

### 7.6. Avaliação e desenvolvimento
- Definição de metas e indicadores
- Avaliações de desempenho periódicas ou 360 graus
- Feedback entre gestor e colaborador
- Plano de Desenvolvimento Individual (PDI), cursos e certificações

### 7.7. Portal do colaborador
- Consulta de dados pessoais, documentos e recibos
- Pedido de férias e atualização de informações
- Consulta de ponto, benefícios e avaliações
- Comunicação de avisos internos

### 7.8. Relatórios e indicadores
- Número de colaboradores por departamento
- Rotatividade (*turnover*)
- Absenteísmo
- Custos com pessoal
- Férias pendentes, horas extras e desempenho

---

## Próximos Passos

Continua a acompanhar as actualizações do Kuamanga ERP para novidades no módulo de RH. Novos submódulos serão activados automaticamente na sidebar à medida que forem disponibilizados.

*(Fim do Manual. Kuamanga - 2026).*
