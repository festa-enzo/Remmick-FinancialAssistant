
# Remmick Financial Assistant

**Organize suas finanças. Planeje seus próximos passos.**

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![MariaDB](https://img.shields.io/badge/MariaDB-11-003545?logo=mariadb)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?logo=javascript)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker)
![Nginx](https://img.shields.io/badge/Nginx-Alpine-009639?logo=nginx)

O **Remmick Financial Assistant** é uma aplicação web de organização financeira pessoal, desenvolvida para ajudar usuários a acompanhar receitas, despesas e visualizar melhor sua vida financeira.

> 🚧 **Status:** primeira versão concluída. O projeto está em desenvolvimento e pode receber melhorias, correções e novas funcionalidades (Apenas o "README" foi feito com a ajuda de IA).

## ✨ Funcionalidades

- **Autenticação:** cadastro e login de usuários.
- **Gestão de despesas:** cadastro, consulta, edição e exclusão de gastos.
- **Gestão de receitas:** organização de receitas e acompanhamento de recebimentos.
- **Observações:** registro de notas pessoais relacionadas às receitas.
- **Gráficos:** visualização de informações financeiras.
- **Configurações de conta:** atualização de dados pessoais, alteração de senha e exclusão de conta.
- **Interface responsiva:** páginas desenvolvidas para facilitar a navegação e o uso da aplicação.

## 🛠️ Tecnologias

### Frontend

- HTML5
- CSS3
- JavaScript ES6

### Backend

- PHP 8.3
- API REST
- JWT para autenticação
- Composer para gerenciamento de dependências
- PDO para acesso ao banco de dados
- `firebase/php-jwt`
- `vlucas/phpdotenv`

### Banco de dados

- MariaDB 11

### Infraestrutura

- Docker
- Docker Compose
- Nginx
- PHP-FPM

## 🏗️ Arquitetura

A aplicação utiliza uma arquitetura baseada em containers Docker:

```text
                         Internet
                            │
                            ▼
                    ┌───────────────┐
                    │     Nginx     │
                    │    Port 80    │
                    └───────┬───────┘
                            │
                 ┌──────────┴──────────┐
                 │                     │
                 ▼                     ▼
        ┌─────────────────┐   ┌─────────────────┐
        │    Frontend     │   │     Backend     │
        │  HTML / CSS /   │   │   PHP 8.3-FPM   │
        │       JS        │   │                 │
        └─────────────────┘   └────────┬────────┘
                                       │
                                       ▼
                              ┌─────────────────┐
                              │     MariaDB     │
                              │       11        │
                              └─────────────────┘
````

O Nginx funciona como ponto de entrada da aplicação, servindo os arquivos do frontend e encaminhando as requisições da API para o PHP-FPM.

O backend é responsável pelas regras de negócio, autenticação, validações e comunicação com o banco de dados.

O MariaDB é responsável pelo armazenamento dos dados da aplicação.

## 📁 Estrutura do projeto

```text
Remmick-Financial-Assistant/

├── Backend/
│   ├── App/
│   │   ├── Controllers/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Core/
│   │   └── Models/
│   │
│   ├── Config/
│   ├── Routes/
│   ├── Public/
│   ├── composer.json
│   ├── composer.lock
│   ├── .env.example
│   ├── .dockerignore
│   └── Dockerfile
│
├── Frontend/
│   ├── Js/
│   ├── Style/
│   ├── Expenses.html
│   ├── Graphics.html
│   ├── Home.html
│   ├── Incomes.html
│   ├── Login.html
│   ├── Register.html
│   ├── Savings.html
│   └── Settings.html
│
├── Database/
│   └── init.sql
│
├── Nginx/
│   └── default.conf
│
├── docker-compose.yml
├── .env
├── .gitignore
└── README.md
```

## 🐳 Docker

O projeto utiliza **Docker Compose** para executar os serviços da aplicação de forma isolada.

O arquivo responsável pela orquestração dos containers é:

```text
docker-compose.yml
```

A aplicação possui três serviços principais:

| Serviço   | Tecnologia   | Função                                                |
| --------- | ------------ | ----------------------------------------------------- |
| `nginx`   | Nginx Alpine | Servir o frontend e encaminhar requisições para a API |
| `backend` | PHP 8.3-FPM  | Executar a API PHP                                    |
| `mysql`   | MariaDB 11   | Armazenar os dados                                    |

Os serviços utilizam uma rede Docker compartilhada chamada:

```text
remmick-network
```

O backend se comunica com o banco através do serviço:

```text
mysql
```

O Nginx encaminha as requisições PHP para:

```text
backend:9000
```

## 🚀 Como executar

### Pré-requisitos

Para executar o projeto utilizando Docker, é necessário ter instalado:

* Docker
* Docker Compose

Não é necessário instalar diretamente no sistema:

* PHP
* Composer
* MariaDB
* PHP-FPM
* Nginx

Esses componentes são executados pelos containers.

### 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd Remmick-Financial-Assistant
```

### 2. Configure as variáveis de ambiente

O projeto utiliza dois arquivos `.env`.

Na raiz do projeto, crie:

```text
.env
```

com as credenciais utilizadas pelo Docker Compose:

```env
DB_ROOT_PASSWORD=sua_senha_root
DB_PASSWORD=sua_senha_usuario
```

Dentro da pasta `Backend`, crie:

```text
Backend/.env
```

com as configurações utilizadas pela aplicação:

```env
DB_DRIVER=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=remmick_finassist
DB_USERNAME=remmick
DB_PASSWORD=sua_senha_usuario

JWT_SECRET=seu_segredo
JWT_EXPIRATION=3600
JWT_REFRESH_EXPIRATION=86400
JWT_ALGORITHM=HS256
```

As credenciais utilizadas pelo backend devem ser compatíveis com as configuradas no Docker Compose.

> ⚠️ Os arquivos `.env` não devem ser enviados para o GitHub.

O arquivo `Backend/.env.example` pode ser utilizado como referência para a configuração do ambiente.

### 3. Construa e inicie os containers

Na raiz do projeto:

```bash
docker compose up -d --build
```

Esse comando irá:

* Construir a imagem do backend;
* Instalar as dependências do Composer;
* Criar o container PHP-FPM;
* Criar o container MariaDB;
* Inicializar o banco de dados;
* Criar o container Nginx;
* Criar a rede Docker;
* Iniciar os serviços.

### 4. Verifique os containers

```bash
docker compose ps
```

Os serviços esperados são:

```text
remmick-backend
remmick-mysql
remmick-nginx
```

O MariaDB deve apresentar o estado:

```text
healthy
```

O Nginx deve estar expondo a porta `80`.

### 5. Acesse a aplicação

Em ambiente local:

```text
http://localhost
```

Em um ambiente configurado com os domínios do projeto:

```text
http://planner.remmick.com
```

A API:

```text
http://api.remmick.com
```

## 🌐 Configuração de domínio

Quando o projeto estiver hospedado em um servidor, os domínios devem apontar para o endereço IP do servidor.

Exemplo:

```text
planner.remmick.com → IP_DO_SERVIDOR
api.remmick.com     → IP_DO_SERVIDOR
```

Os dois domínios podem apontar para o mesmo endereço IP.

O Nginx diferencia os acessos através do `server_name`.

O domínio:

```text
planner.remmick.com
```

é utilizado para o frontend.

O domínio:

```text
api.remmick.com
```

é utilizado para a API.

## ⚙️ Nginx

A configuração do Nginx está localizada em:

```text
Nginx/default.conf
```

O Nginx possui configurações separadas para o frontend e para a API.

O frontend é servido a partir da pasta:

```text
Frontend/
```

A API utiliza como ponto de entrada:

```text
Backend/Public/Index.php
```

As requisições PHP são encaminhadas para o PHP-FPM através de:

```text
backend:9000
```

## 🗄️ Banco de dados

O banco utilizado pela aplicação é:

```text
remmick_finassist
```

O banco é executado pelo container:

```text
remmick-mysql
```

A estrutura inicial do banco está localizada em:

```text
Database/init.sql
```

Esse arquivo é executado automaticamente pelo MariaDB durante a primeira inicialização do volume.

O banco possui tabelas relacionadas a:

* Usuários;
* Despesas;
* Receitas;
* Notas;
* Meses;
* Categorias;
* Instituições;
* Métodos de pagamento;
* Tipos de receita;
* Origem de receitas;
* Investimentos;
* Metas de economia;
* Movimentações;
* Tokens de atualização.

## 💾 Persistência do banco

Os dados do MariaDB são armazenados no volume Docker:

```text
mysql-data
```

Ao executar:

```bash
docker compose down
```

os containers são removidos, mas o volume permanece.

Ao executar novamente:

```bash
docker compose up -d
```

os dados existentes continuam disponíveis.

> ⚠️ **Atenção:** o comando abaixo remove os volumes:
>
> ```bash
> docker compose down -v
> ```
>
> Isso pode apagar os dados armazenados no banco.

O arquivo `Database/init.sql` é executado automaticamente somente quando o MariaDB inicializa um banco/volume novo. Alterações posteriores nesse arquivo não são aplicadas automaticamente a um volume que já tenha sido inicializado.

## 📦 Composer

As dependências do backend são gerenciadas pelo Composer.

O projeto utiliza:

* `firebase/php-jwt`
* `vlucas/phpdotenv`

Os arquivos relacionados ao Composer estão em:

```text
Backend/composer.json
Backend/composer.lock
```

Durante a construção da imagem Docker, as dependências são instaladas automaticamente.

Não é necessário executar manualmente:

```bash
composer install
```

quando o projeto estiver sendo executado através do Docker.

## 🔑 Autenticação

A aplicação utiliza **JWT (JSON Web Token)** para autenticação.

O sistema possui:

* Cadastro de usuários;
* Login;
* Access Token;
* Refresh Token;
* Hash de senhas;
* Rotas protegidas;
* Validação de autenticação no backend.

As requisições protegidas utilizam o cabeçalho:

```text
Authorization: Bearer TOKEN
```

As configurações dos tokens são definidas através das variáveis de ambiente.

## 🔌 API

A aplicação possui uma API REST desenvolvida em PHP.

### Autenticação

```text
POST /api/register
POST /api/login
```

### Despesas

```text
POST   /api/expense
GET    /api/expense/month
GET    /api/expense/year
PUT    /api/expense/{id}
DELETE /api/expense/{id}
```

### Receitas

```text
POST   /api/income
GET    /api/income/month
GET    /api/income/year
PUT    /api/income/{id}
DELETE /api/income/{id}
```

### Observações

```text
POST /api/income/note
GET  /api/income/note
PUT  /api/income/note/{id}
```

### Gráficos

A API também possui endpoints responsáveis pelo fornecimento dos dados utilizados nos gráficos da aplicação.

As rotas protegidas exigem autenticação através do token JWT.

## 🔄 Desenvolvimento

Após alterações no backend ou na configuração dos containers, pode ser necessário reconstruir a aplicação:

```bash
docker compose up -d --build
```

Para reiniciar somente o backend:

```bash
docker compose restart backend
```

Para reiniciar somente o Nginx:

```bash
docker compose restart nginx
```

## 📋 Comandos Docker

### Iniciar

```bash
docker compose up -d
```

### Iniciar reconstruindo as imagens

```bash
docker compose up -d --build
```

### Verificar containers

```bash
docker compose ps
```

### Parar

```bash
docker compose down
```

### Reiniciar

```bash
docker compose restart
```

### Ver logs

```bash
docker compose logs
```

### Acompanhar logs em tempo real

```bash
docker compose logs -f
```

### Logs do backend

```bash
docker compose logs -f backend
```

### Logs do banco

```bash
docker compose logs -f mysql
```

### Logs do Nginx

```bash
docker compose logs -f nginx
```

### Reconstruir imagens

```bash
docker compose build
```

## 🧪 Testes básicos

Após iniciar os containers, o Nginx pode ser testado com:

```bash
curl -I http://localhost
```

Para testar o frontend utilizando o domínio:

```bash
curl -I -H "Host: planner.remmick.com" http://localhost
```

Para testar a API:

```bash
curl -i -H "Host: api.remmick.com" http://localhost/
```

Para verificar a versão do PHP no container:

```bash
docker exec remmick-backend php -v
```

Para acessar o MariaDB:

```bash
docker exec -it remmick-mysql mariadb -u root -p
```

Depois de entrar no MariaDB:

```sql
USE remmick_finassist;
SHOW TABLES;
```

## 🛠️ Solução de problemas

### Containers não iniciam

Verifique:

```bash
docker compose ps
```

Depois consulte os logs:

```bash
docker compose logs
```

### Backend retorna erro 500

Verifique os logs:

```bash
docker compose logs -f backend
```

Também é possível verificar se as dependências foram instaladas:

```bash
docker exec remmick-backend ls vendor
```

### Banco não está disponível

Verifique:

```bash
docker compose ps
```

O container do banco deve apresentar:

```text
healthy
```

Consulte os logs:

```bash
docker compose logs -f mysql
```

### Tabelas não existem

Entre no MariaDB:

```bash
docker exec -it remmick-mysql mariadb -u root -p
```

Depois:

```sql
USE remmick_finassist;
SHOW TABLES;
```

A estrutura inicial é definida em:

```text
Database/init.sql
```

O `init.sql` é executado automaticamente somente na inicialização de um banco/volume novo.

### Frontend não abre

Verifique os logs do Nginx:

```bash
docker compose logs -f nginx
```

Verifique se os containers estão ativos:

```bash
docker compose ps
```

Também teste:

```bash
curl -I http://localhost
```

### API não responde

Verifique os logs do backend:

```bash
docker compose logs -f backend
```

Verifique os logs do Nginx:

```bash
docker compose logs -f nginx
```

Também teste:

```bash
curl -i -H "Host: api.remmick.com" http://localhost/
```

## 🔐 Segurança

O projeto utiliza:

* JWT para autenticação;
* Hash de senhas;
* Variáveis de ambiente;
* Validação no backend;
* Separação entre frontend e backend;
* PHP-FPM;
* Containers Docker.

Nunca publique no repositório:

* Senhas;
* Tokens;
* Segredos JWT;
* Credenciais do banco;
* Chaves privadas;
* Arquivos `.env`.

> ⚠️ Esta versão foi desenvolvida principalmente como projeto de aprendizado e portfólio.

## 🎯 Objetivo do projeto

O **Remmick Financial Assistant** foi criado para colocar em prática conhecimentos de:

* Desenvolvimento web;
* HTML;
* CSS;
* JavaScript;
* PHP;
* APIs REST;
* Autenticação;
* JWT;
* Banco de dados;
* PDO;
* Arquitetura MVC;
* Docker;
* Docker Compose;
* Nginx;
* PHP-FPM;
* Integração entre frontend e backend;
* Organização de código.

O projeto também serve como espaço para evoluir habilidades de desenvolvimento e construir uma aplicação cada vez mais completa.

## 🧑‍💻 Autor

Desenvolvido por **Festa-Enzo**.
