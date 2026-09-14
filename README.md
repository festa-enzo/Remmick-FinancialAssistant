<img width="1254" height="1254" alt="Remmick_Logo" src="https://github.com/user-attachments/assets/2fa9d48b-088e-49c4-871c-79f75bc9dd94" />

# Remmick Financial Assistant

**Organize suas finanças. Planeje seus próximos passos.**
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?logo=javascript)

O Remmick Financial Assistant é uma aplicação web de organização financeira pessoal, desenvolvida para ajudar usuários a acompanhar receitas, despesas e visualizar melhor sua vida financeira.

> 🚧 **Status:** primeira versão concluída. O projeto está em desenvolvimento e pode receber melhorias, correções e novas funcionalidades.

## ✨ Funcionalidades

* **Autenticação:** cadastro e login de usuários.
* **Gestão de despesas:** cadastro, consulta, edição e exclusão de gastos.
* **Gestão de receitas:** organização de receitas e acompanhamento de recebimentos.
* **Observações:** registro de notas pessoais relacionadas às receitas.
* **Gráficos:** visualização de informações financeiras.
* **Configurações de conta:** atualização de dados pessoais, alteração de senha e exclusão de conta.
* **Interface responsiva:** páginas desenvolvidas para facilitar a navegação e o uso da aplicação.

## 🛠️ Tecnologias

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP
* API REST
* JWT para autenticação
* Composer para gerenciamento de dependências

### Banco de dados

* MariaDB / MySQL
* PDO para acesso ao banco de dados

## 📁 Estrutura do projeto

```text
Remmick-Financial-Assistant/
├── Backend/
│   ├── App/
│   │   ├── Controllers/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   └── Core/
│   ├── Config/
│   ├── Routes/
│   └── Public/
├── Frontend/
│   ├── Js/
│   ├── Style/
│   └── ...
└── README.md
```

A estrutura segue uma separação entre interface, regras de negócio, acesso a dados e endpoints da API.

## 🚀 Como executar

### Pré-requisitos

* PHP 8.2 ou superior
* Composer
* MariaDB ou MySQL
* Servidor web com suporte a PHP, como Apache ou Nginx

### 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd Remmick-Financial-Assistant
```

### 2. Instale as dependências do backend

Entre na pasta do backend e instale as dependências:

```bash
cd Backend
composer install
```

### 3. Configure o banco de dados

Crie um banco de dados para a aplicação e configure as credenciais de conexão de acordo com as configurações do projeto.

### 4. Configure as variáveis de ambiente

Crie o arquivo `.env` conforme as variáveis utilizadas pelo backend. Não envie esse arquivo para o GitHub.

As configurações devem incluir, conforme a implementação do projeto:

* Credenciais do banco de dados
* Segredo e duração dos tokens JWT
* Configurações necessárias para o ambiente de execução

### 5. Configure o servidor

Configure o servidor web para servir o frontend e encaminhar as requisições da API para o ponto de entrada do backend.

Ajuste também a URL da API no frontend para corresponder ao ambiente em que a aplicação será executada.

## 🔐 Segurança

O projeto utiliza autenticação por tokens JWT e validações no backend.

**Atenção:** esta versão foi desenvolvida principalmente como projeto de aprendizado e portfólio. Antes de disponibilizá-la publicamente para uso com dados financeiros reais, é necessário revisar e fortalecer aspectos como autorização por usuário, HTTPS, gerenciamento de sessões, proteção contra abuso e configuração de produção.

Nunca publique senhas, tokens, chaves privadas ou credenciais no repositório.

## 🎯 Objetivo do projeto

O Remmick Financial Assistant foi criado para colocar em prática conhecimentos de desenvolvimento web, integração entre frontend e backend, APIs, autenticação, banco de dados e organização de código.

O projeto também serve como espaço para evoluir habilidades de desenvolvimento e construir uma aplicação cada vez mais completa.

## 🧑‍💻 Autor

Desenvolvido por **Festa-Enzo**.
