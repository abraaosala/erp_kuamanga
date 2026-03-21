# Manual do Utilizador: Módulo de Contabilidade

Bem-vindo ao **Módulo de Contabilidade Financeira** do Kuamanga ERP.
Este módulo foi desenhado respeitando rigorosamente as normas do **Plano Geral de Contabilidade (PGC) de Angola**. A sua principal função é o registo do histórico financeiro do negócio através do método das partidas dobradas (Débitos e Créditos).

Abaixo detalhamos todas as ferramentas disponíveis e como utilizá-las no teu dia a dia.

---

## 1. Seleção de Empresa (Multi-Empresa)
O ERP suporta múltiplas empresas em simultâneo.
*   **Atenção:** Antes de registares qualquer documento, deves **sempre** garantir que a empresa correcta está selecionada.
*   Podes alternar a empresa ativa a qualquer momento clicando no botão no topo do menu lateral (Sidebar). Todos os Lançamentos e Mapas reflectirão *apenas* dados da empresa selecionada.

---

## 2. Plano de Contas
O Plano de Contas é o esqueleto da contabilidade. Aqui podes criar, editar ou apagar rubricas contabilísticas.
*   **Aceder a:** `Contabilidade > Plano de Contas`
*   **Regra de Hierarquia:** Podes criar contas raiz (ex: Classe 3 - Existências), subcontas (ex: 31 - Compras) e contas de detalhe (ex: 31.1 - Fornecedor X).
*   **Dica:** Sugerimos manteres a base original das 8 classes do PGC, criando apenas subcontas consoante a tua necessidade empresarial específica.

---

## 3. Lançamentos Diários (Journal Entries)
É aqui que os movimentos diários da tua empresa tomam forma real.
*   **Aceder a:** `Contabilidade > Lançamentos Diários`
*   **Como criar:** Quando clicas em "Novo Lançamento", deves especificar a data, uma descrição (ex: *Pagamento Fatura Água*), e de seguida gerar as linhas (Items) deste lançamento.
*   **A Regra Dourada:** Um lançamento **NUNCA** pode ser guardado se a soma total da coluna dos Débitos for diferente da soma total da coluna dos Créditos. O botão de "Guardar" fica bloqueado (vermelho) até que as contas estejam certas (D = C).

---

## 4. O Razão Geral e os Balancetes
Para consultar saldos ou encontrar erros, usa estas duas ferramentas cruciais. Acedidas através do menu respectivo.
*   **Razão Geral:** Funciona como um detetive. Escolhe uma única conta (ex: 11 - Caixa) e define uma janela de datas. Verás todos os lançamentos que entraram ou saíram do teu Caixa ao cêntimo.
*   **Balancetes:** A vista de "pássaro". Seleciona um período para ver, numa única ecrã, todas as contas da tua empresa num formato grelha, mostrando o saldo devedor/credor de cada rubrica no final desse período.

---

## 5. Dashboard Analítico
Para os gestores, o Dashboard é a estrela do módulo.
*   **Aceder a:** `Contabilidade > Dashboard Analítico`
*   **Utilização:** Seleciona um Ano e Mês no topo. 
*   A página atualizará dinamicamente mostrando-te os Activos, Passivos, Receitas e Despesas *daquele mês específico*. Podes também ver gráficos dinâmicos comparando os Proveitos ganhos com as Despesas pagas, ano e mês, num piscar de olhos.

---

## 6. Mapas Oficiais (Balanço & DRE)
O fim da jornada contabilística. Estes são os ficheiros que a Administração ou o Estado precisam de consultar todos os anos.
*   **Demonstração de Resultados (DRE):** Aqui as tuas "Receitas" combatem as tuas "Despesas" (Classes 6 e 7 do PGC). O ficheiro mostrar-te-á se a empresa teve **Lucro ou Prejuízo** nesse período selecionado.
*   **Balanço Patrimonial:** Fotografa quem deve e a quem deves. Compara os Activos (Classes 1,2,3,4) com o Passivo e o Capital Próprio. O Lucro calculado pela DRE é automaticamente somado ao teu Capital Próprio. Num ecrã em baixo, o sistema valida automaticamente se a tua Balança está "Desequilibrada".
*   **Como Imprimir:** Entrando em qualquer um dos Mapas, basta clicares no botão "Imprimir / Exportar PDF" no topo. O nosso ERP tem estilos de impressão personalizados que eliminarão o menu e vão imprimir de forma clara, preta/branca, pronta a ser assinada no fim de página pela Gerência e pelo Contabilista.

---

## Próximos Passos
Se a empresa for crescendo, de futuro este módulo ligará a novos submódulos, evitando que continues a ter de registrar faturas (Compras / Vendas) manualmente como Lançamentos Diários, através da automatização pura.

*(Fim do Manual. Kuamanga - 2026).*
