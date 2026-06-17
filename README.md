# ERP Stocco

Sistema de Gestão Empresarial completo com backend em PHP e frontend em React.

## Funcionalidades

- **Dashboard** — Visão geral com gráficos de faturamento mensal, produtos mais vendidos, vendas recentes e indicadores-chave
- **Produtos** — Cadastro, categorias, controle de estoque, SKU, margem de lucro
- **Clientes** — Cadastro completo com documentos, endereço e histórico
- **Fornecedores** — Gestão de fornecedores com dados de contato
- **Vendas** — PDV com seleção de produtos, cálculo automático, desconto e formas de pagamento
- **Compras** — Controle de ordens de compra
- **Financeiro** — Contas e transações financeiras
- **RH** — Funcionários, ponto eletrônico e folha de pagamento

## Stack

### Backend
- **PHP** 8.1+
- Arquitetura com Domain-Driven Design (Entities, Use Cases, Repositories)
- **MySQL** com PDO e Query Builder customizado
- Autenticação JWT

### Frontend
- **React** 18 com TypeScript
- **Vite** 5
- **Tailwind CSS** 3
- **TanStack Query** 5 (React Query)
- **React Router** 6
- **Recharts** para gráficos
- **Axios** para chamadas HTTP

## Requisitos

- PHP 8.1 ou superior
- Composer
- Node.js 18 ou superior
- MySQL 8.0 ou superior
- Extensões PHP: `pdo_mysql`, `mbstring`

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/erp-stocco.git
cd erp-stocco
```

### 2. Backend

```bash
cd backend
composer install
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=erp_stocco
DB_USER=root
DB_PASS=root

JWT_SECRET=sua_chave_secreta_aqui
JWT_EXPIRY=86400

APP_NAME="ERP Stocco"
APP_DEBUG=true
APP_URL=http://localhost:8000

CORS_ORIGIN=http://localhost:5173
```

Execute as migrações e seeders:

```bash
composer run migrate
composer run seed
```

Inicie o servidor:

```bash
php -S localhost:8000 -t public
```

### 3. Frontend

Em outro terminal:

```bash
cd frontend
npm install
npm run dev
```

Acesse `http://localhost:5173` no navegador.

### Credenciais padrão

Após executar os seeders:

- **E-mail:** admin@erpstocco.com.br
- **Senha:** admin123

## Scripts disponíveis

### Backend

| Comando | Descrição |
|---------|-----------|
| `composer run migrate` | Executa migrações do banco |
| `composer run seed` | Popula banco com dados iniciais |
| `php -S localhost:8000 -t public` | Inicia servidor de desenvolvimento |

### Frontend

| Comando | Descrição |
|---------|-----------|
| `npm run dev` | Inicia servidor de desenvolvimento |
| `npm run build` | Compila para produção |
| `npm run preview` | Visualiza build de produção |

## Estrutura do projeto

```
erp-stocco/
├── backend/
│   ├── config/              # Configurações
│   ├── database/
│   │   ├── migrations/      # Schema SQL
│   │   └── seeders/         # Dados iniciais
│   ├── public/
│   │   └── index.php        # Entry point
│   └── src/
│       ├── Application/     # Casos de uso e DTOs
│       ├── Domain/          # Entidades, interfaces, enums
│       ├── Infrastructure/  # Banco, repositórios
│       └── Presentation/    # Controllers, middleware, rotas
└── frontend/
    └── src/
        ├── components/      # Componentes React
        ├── contexts/        # Contextos (Auth)
        ├── pages/           # Páginas da aplicação
        ├── services/        # API e serviços
        ├── types/           # Interfaces TypeScript
        └── utils/           # Utilitários
```

## API

Autenticação via JWT (Bearer token). Todas as rotas exceto `/api/auth/login` exigem token.

### Autenticação

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/auth/login` | Login |
| GET | `/api/auth/me` | Dados do usuário logado |

### Módulos

| Módulo | Rotas |
|--------|-------|
| Dashboard | `/api/dashboard` |
| Produtos | `/api/products` |
| Categorias | `/api/categories` |
| Clientes | `/api/customers` |
| Fornecedores | `/api/suppliers` |
| Vendas | `/api/sales` |
| Compras | `/api/purchases` |
| Contas | `/api/accounts` |
| Transações | `/api/transactions` |
| Funcionários | `/api/employees` |
| Ponto | `/api/attendance` |
| Folha | `/api/payroll` |

Cada módulo segue o padrão RESTful com `GET /api/{recurso}`, `GET /api/{recurso}/{id}`, `POST /api/{recurso}`, `PUT /api/{recurso}/{id}`, `DELETE /api/{recurso}/{id}`.
