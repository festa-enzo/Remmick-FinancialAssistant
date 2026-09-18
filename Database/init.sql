
USE remmick_finassist;

CREATE TABLE income_origin (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO income_origin (name)
VALUES 	('Empresa'), 
	('Cliente'), 
	('Loja'), 
	('Banco'), 
	('Outros');

CREATE TABLE income_type (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO income_type (name)
VALUES 	('Salário'), 
	('Freelance'), 
	('Vendas'), 
	('Investimentos'), 
	('Outros');


CREATE TABLE months (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO months (name)
VALUES 	('Janeiro'), 
	('Fevereiro'), 
	('Março'), 
	('Abril'), 
	('Maio'), 
	('Junho'), 
	('Julho'), 
	('Agosto'), 
	('Setembro'), 
	('Outubro'), 
	('Novembro'), 
	('Dezembro');


CREATE TABLE categories (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO categories (name)
VALUES	('Alimentação'), 
	('Transporte'), 
	('Casa'), 
	('Compras'), 
	('Lazer'), 
	('Saúde'), 
	('Educação'), 
	('Tecnologia'), 
	('Serviços'), 
	('Roupas'), 
	('Presente'), 
	('Outros');


CREATE TABLE institution (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO institution (name)
VALUES	('Nubank'), 
	('Itau'), 
	('Bradesco'), 
	('Banco_do_Brasil'), 
	('Caixa'), 
	('Santander'), 
	('Inter'), 
	('C6'), 
	('BTG'), 
	('XP'), 
	('Mercado_Pago'), 
	('Pagbank'),
	('Neon'),
	('Picpay'),   
	('Sicredi');


CREATE TABLE methods (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO methods (name)
VALUES	('Dinheiro'), 
	('Débito'), 
	('Crédito'), 
	('Boleto'), 
	('Pix'), 
	('Transferencia'), 
	('Vale_Refeicao'), 
	('Vale_Alimentacao'); 



CREATE TABLE investment_types (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(20)	
);

INSERT INTO investment_types (name)
VALUES	('Tesouro_Direto'), 
	('CDB'), 
	('Poupanca'), 
	('Cripto_Moeda'), 
	('LCI'), 
	('Acoes'), 
	('Fundo_Imobiliario'), 
	('ETF'),
	('BDR'); 


CREATE TABLE users (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) UNIQUE NOT NULL,
    password       VARCHAR(255) NOT NULL,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE expenses (
    id          	INT AUTO_INCREMENT PRIMARY KEY,
    user_id  		INT NOT NULL,
    expense_month_id    INT NOT NULL,
    category_id		INT NOT NULL,
    institution_id	INT NOT NULL,
    method_id		INT NOT NULL,
    expense_year	INT NOT NULL,
    title      		VARCHAR(60) NOT NULL,       
    value       	DECIMAL(12, 2) NOT NULL,
    created_at   	DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   	DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_paid		BOOL NOT NULL DEFAULT FALSE,


    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (expense_month_id) REFERENCES months(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (institution_id) REFERENCES institution(id),
    FOREIGN KEY (method_id) REFERENCES methods(id)
);

CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    value DECIMAL(12, 2) NOT NULL,
    title VARCHAR(60) NOT NULL,
    income_origin_id INT NOT NULL,
    income_month_id INT NOT NULL,
    income_year INT NOT NULL,
    is_received BOOL NOT NULL DEFAULT FALSE,
    income_type_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (income_month_id) REFERENCES months(id),
    FOREIGN KEY (income_type_id) REFERENCES income_type(id),
    FOREIGN KEY (income_origin_id) REFERENCES income_origin(id)
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content VARCHAR(500),	     
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    institution_id INT NOT NULL,
    investment_types_id INT NOT NULL,
    value DECIMAL(12, 2) NOT NULL,         
    investment_date DATE NOT NULL,	     
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES institution(id),
    FOREIGN KEY (investment_types_id) REFERENCES investment_types(id)
);

CREATE TABLE savings_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    target_amount DECIMAL(12, 2) NOT NULL,
    current_amount DECIMAL(12, 2) NOT NULL,
    deadline DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE refresh_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    refresh_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_expire (user_id, expires_at)
);

CREATE TABLE savings_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    goal_id INT NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    type ENUM('deposit', 'withdrawal') NOT NULL,
    description VARCHAR(255),
    movement_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (goal_id) REFERENCES savings_goals(id) ON DELETE CASCADE
);