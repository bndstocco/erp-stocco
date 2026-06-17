-- ERP Stocco - Seed Data
USE erp_stocco;

-- Roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrador do sistema com acesso total'),
('manager', 'Gerente com acesso a gerenciamento'),
('seller', 'Vendedor com acesso ao modulo de vendas'),
('stockist', 'Estoquista com acesso ao modulo de estoque'),
('financial', 'Financeiro com acesso ao modulo financeiro');

-- Permissions
INSERT INTO permissions (name, slug) VALUES
('Visualizar Dashboard', 'dashboard.view'),
('Gerenciar Produtos', 'products.manage'),
('Visualizar Produtos', 'products.view'),
('Gerenciar Categorias', 'categories.manage'),
('Gerenciar Clientes', 'customers.manage'),
('Visualizar Clientes', 'customers.view'),
('Gerenciar Fornecedores', 'suppliers.manage'),
('Visualizar Fornecedores', 'suppliers.view'),
('Gerenciar Vendas', 'sales.manage'),
('Visualizar Vendas', 'sales.view'),
('Gerenciar Compras', 'purchases.manage'),
('Visualizar Compras', 'purchases.view'),
('Gerenciar Financeiro', 'financial.manage'),
('Visualizar Financeiro', 'financial.view'),
('Gerenciar RH', 'hr.manage'),
('Visualizar RH', 'hr.view'),
('Gerenciar Usuarios', 'users.manage'),
('Configurar Sistema', 'system.configure');

-- Admin permissions (all)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- Manager permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions WHERE slug NOT IN ('users.manage', 'system.configure');

-- Seller permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions WHERE slug IN (
    'dashboard.view', 'products.view', 'customers.manage', 'customers.view',
    'sales.manage', 'sales.view'
);

-- Stockist permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions WHERE slug IN (
    'dashboard.view', 'products.manage', 'products.view', 'categories.manage',
    'purchases.manage', 'purchases.view'
);

-- Financial permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions WHERE slug IN (
    'dashboard.view', 'financial.manage', 'financial.view',
    'sales.view', 'purchases.view'
);

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role_id) VALUES
('Administrador', 'admin@erpstocco.com.br', '$2y$10$a0hczMdGQyTLQSZ/cMe8Be/zBg9M5yuEwRJ6wNUfu48eQbzyC5Vk2', 1);

-- Sample categories
INSERT INTO categories (name, description) VALUES
('Eletronicos', 'Produtos eletronicos e tecnologia'),
('Moveis', 'Moveis e decoracao'),
('Vestuario', 'Roupas e acessorios'),
('Alimentacao', 'Alimentos e bebidas'),
('Material Escritorio', 'Material de escritorio e papelaria');

-- Sample customers
INSERT INTO customers (name, email, phone, document, city, state) VALUES
('Joao Silva', 'joao@email.com', '(11) 99999-0001', '123.456.789-01', 'Sao Paulo', 'SP'),
('Maria Santos', 'maria@email.com', '(11) 99999-0002', '987.654.321-01', 'Sao Paulo', 'SP'),
('Carlos Oliveira', 'carlos@email.com', '(21) 99999-0003', '111.222.333-44', 'Rio de Janeiro', 'RJ'),
('Ana Costa', 'ana@email.com', '(31) 99999-0004', '555.666.777-88', 'Belo Horizonte', 'MG');

-- Sample suppliers
INSERT INTO suppliers (company_name, contact_name, email, phone, document, city, state) VALUES
('Distribuidora Tech Ltda', 'Pedro Alves', 'pedro@distech.com.br', '(11) 3333-0001', '11.111.111/0001-01', 'Sao Paulo', 'SP'),
('Moveis & Cia', 'Lucia Mendes', 'lucia@moveisecia.com.br', '(11) 3333-0002', '22.222.222/0001-02', 'Sao Paulo', 'SP'),
('Industria Textil BR', 'Roberto Lima', 'roberto@textilbr.com.br', '(11) 3333-0003', '33.333.333/0001-03', 'Sao Paulo', 'SP');

-- Sample products
INSERT INTO products (name, sku, category_id, unit_price, cost_price, stock_quantity, min_stock) VALUES
('Notebook Pro 15', 'NB001', 1, 4999.99, 3800.00, 10, 3),
('Smartphone X', 'SP001', 1, 2499.99, 1800.00, 25, 5),
('Mesa Escritorio', 'ME001', 2, 899.99, 550.00, 15, 3),
('Cadeira Ergo', 'CE001', 2, 1299.99, 800.00, 20, 5),
('Camisa Polo', 'CP001', 3, 89.99, 45.00, 50, 10),
('Calca Jeans', 'CJ001', 3, 149.99, 80.00, 35, 10),
('Cafe Gourmet', 'CG001', 4, 29.99, 18.00, 100, 20),
('Agua Mineral', 'AM001', 4, 4.99, 2.50, 200, 50),
('Papel A4', 'PA001', 5, 24.99, 15.00, 80, 20),
('Caneta Azul', 'CA001', 5, 2.99, 1.20, 500, 100);

-- Sample accounts
INSERT INTO accounts (name, type, balance, bank) VALUES
('Caixa', 'cash', 5000.00, 'Caixa'),
('Conta Corrente', 'checking', 25000.00, 'Banco do Brasil'),
('Conta Poupanca', 'savings', 50000.00, 'Banco do Brasil'),
('Cartao Credito', 'credit_card', 0.00, 'Nubank');
