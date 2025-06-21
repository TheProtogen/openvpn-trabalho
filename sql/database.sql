CREATE DATABASE vpn_db;
USE vpn_db;

-- Tabela usuários
CREATE TABLE usuarios (
    email VARCHAR(255) PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN NOT NULL,
    ultimo_login DATETIME DEFAULT NULL
);

INSERT INTO usuarios (email, nome, senha, ativo) VALUES
('admin@protonmail.com', 'admin', '$2y$10$k8WT0mlfXoNQEQiMlThQ60kjbV7A440IVPFJS4OUEtyE1P80TnGf', 1);

-- Tabela certificados
CREATE TABLE certificados (
    id CHAR(7) PRIMARY KEY,
    data DATETIME NOT NULL,
    validade DATE NOT NULL
);
