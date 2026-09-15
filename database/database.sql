-- Invoice System Database
CREATE DATABASE IF NOT EXISTS invoice_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE invoice_system;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- default admin => email: admin@shop.com / password: admin123
INSERT INTO admins (name, email, password) VALUES
('Admin', 'admin@shop.com', '$2y$10$3euPcmQFCiblsZeEu5s7p.9OL.9bkXHXhWjW/hkuFf.iy8XjQ8h/K');

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand VARCHAR(150),
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(100),
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(150),
    customer_phone VARCHAR(30),
    customer_address VARCHAR(255),
    customer_note TEXT,
    subtotal DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    grand_total DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(30) DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL UNIQUE,
    location VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(150) DEFAULT 'Your Shop',
    shop_logo VARCHAR(255),
    shop_phone VARCHAR(30),
    shop_address VARCHAR(255),
    whatsapp_number VARCHAR(30) DEFAULT ''
);

INSERT INTO settings (shop_name, whatsapp_number) VALUES ('Your Shop Name', '8801XXXXXXXXX');

-- sample data
INSERT INTO categories (name, slug) VALUES
('Mouse', 'mouse'), ('Keyboard', 'keyboard'), ('Monitor', 'monitor'), ('Cable', 'cable');

INSERT INTO products (category_id, name, sku, price, stock, status) VALUES
(1, 'Logitech M185', 'LOG-M185', 850, 25, 1),
(2, 'A4Tech Keyboard', 'A4-KB101', 1200, 15, 1),
(4, 'HDMI Cable 1.5m', 'CAB-HDMI15', 350, 40, 1);
