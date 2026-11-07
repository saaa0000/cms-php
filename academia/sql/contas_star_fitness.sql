-- Importe no phpMyAdmin. Não apaga nenhuma tabela já existente do CMS.
CREATE DATABASE IF NOT EXISTS cms_api
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cms_api;

CREATE TABLE IF NOT EXISTS contas_star_fitness (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_contas_star_fitness_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
