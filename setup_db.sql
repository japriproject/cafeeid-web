CREATE DATABASE IF NOT EXISTS cafee;
USE cafee;

-- Table for members (users)
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reff VARCHAR(50) UNIQUE NOT NULL,
    upline VARCHAR(50) NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    saldo DECIMAL(15,2) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for cafes (cafe owners)
CREATE TABLE IF NOT EXISTS cafes (
    id_cafe INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_cafe VARCHAR(100) NOT NULL,
    alamat TEXT,
    phone VARCHAR(20),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default data
INSERT INTO members (reff, name, phone, password, saldo) VALUES
('CF26010100001', 'User Demo', '081234567890', '$2y$10$orGjnYROFcD5VlF9M5e1bOTgBessmUc6jmFbdjbIDyXD1L4xpUiEq', 100000)
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO cafes (username, password, nama_cafe, alamat, phone) VALUES
('cafe001', '$2y$10$orGjnYROFcD5VlF9M5e1bOTgBessmUc6jmFbdjbIDyXD1L4xpUiEq', 'Cafe Demo', 'Jl. Contoh No. 123', '081234567891')
ON DUPLICATE KEY UPDATE nama_cafe=nama_cafe;