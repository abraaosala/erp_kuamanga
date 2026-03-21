# 🏗️ Arquitetura Técnica do Backend

Este documento descreve as decisões arquiteturais e a estrutura do micro-framework que sustenta o ERP.

## 🛠️ Stack Tecnológica
- **Linguagem:** PHP 8.2+
- **Container IoC:** `Illuminate\Container`
- **ORM:** `Illuminate\Database` (Eloquent)
- **Router:** `Illuminate\Routing`
- **Template Engine:** `BladeOne` (Versão standalone do Blade)
- **Migrations:** `Phinx`

## 🧩 Camadas do Sistema

O sistema segue o padrão **Controller -> Service -> Repository -> Model**, garantindo separação de responsabilidades e testabilidade.

### 1. Camada de Apresentação (Controllers)
Localizada em `app/Http/Controllers/Modules/`, recebe as requisições HTTP e delega a lógica para os Services correspondentes.

### 2. Camada de Negócio (Services)
Localizada em `app/Services/Modules/`, contém as regras de negócio puras. Não depende de HTTP ou do Banco de Dados diretamente.

### 3. Camada de Acesso a Dados (Repositories)
Localizada em `app/Repositories/Modules/`, encapsula as consultas ao banco de dados utilizando o Eloquent. Isso permite trocar o motor de banco de dados ou a estratégia de consulta sem afetar a lógica de negócio.

### 4. Camada de Domínio (Models)
Localizada em `app/Models/`, representa a estrutura de dados e relacionamentos.

---

## 🚦 Ciclo de Vida da Requisição
1. `public/index.php` -> Ponto de entrada.
2. `app/Core/Application.php` -> Bootstrapping (Config, DB, Container).
3. `ServiceProviders` -> Registro de dependências e Rotas modulares.
4. `Router` -> Despacho para o Controller injetado.
