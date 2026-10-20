-- ============================================================
--  Sistema de Controle de Estoque
--  Arquivo: data.sql
--  Como usar: abra o phpMyAdmin (XAMPP), clique em "Importar",
--  selecione este arquivo e execute.
--
--  ATENÇÃO: este script RECRIA as tabelas (DROP TABLE), ou seja,
--  apaga os dados existentes. Isso garante que o schema fique
--  sempre correto ao reimportar durante o desenvolvimento.
-- ============================================================

-- Cria o banco de dados (caso ainda não exista)
CREATE DATABASE IF NOT EXISTS `controle_estoque`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco para as próximas instruções
USE `controle_estoque`;

-- Remove as tabelas antes de recriar (produtos primeiro por causa da FK)
DROP TABLE IF EXISTS `produtos`;
DROP TABLE IF EXISTS `usuarios`;

-- ------------------------------------------------------------
-- Tabela: usuarios  (autenticação)
-- ------------------------------------------------------------
CREATE TABLE `usuarios` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `nome`       VARCHAR(100) NOT NULL,
    `login`      VARCHAR(50)  NOT NULL,
    `senha`      VARCHAR(255) NOT NULL,
    `criado_em`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_usuarios_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: produtos
--   Cada produto pertence a um usuário (usuario_id), de modo que
--   cada usuário enxerga apenas o seu próprio estoque.
--   Ao excluir um usuário, seus produtos são removidos em cascata.
-- ------------------------------------------------------------
CREATE TABLE `produtos` (
    `id`             INT(11)        NOT NULL AUTO_INCREMENT,
    `usuario_id`     INT(11)        NOT NULL,
    `nome`           VARCHAR(150)   NOT NULL,
    `preco`          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `imagem`         VARCHAR(255)   DEFAULT NULL,
    `perecivel`      TINYINT(1)     NOT NULL DEFAULT 0,
    `data_validade`  DATE           DEFAULT NULL,
    `criado_em`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_produtos_usuario` (`usuario_id`),
    KEY `idx_produtos_nome` (`nome`),
    CONSTRAINT `fk_produtos_usuario`
        FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Usuário padrão de acesso
--   Usuário: admin
--   Senha:   123
-- (a senha é gravada como hash bcrypt, nunca em texto puro)
-- ------------------------------------------------------------
INSERT IGNORE INTO `usuarios` (`nome`, `login`, `senha`) VALUES
('Administrador', 'admin', '$2y$10$CnXh2yULHXqhu/EHT9SJX.RIwaiMfYng8k9LgjP8KZY6NJQf/kWG6');

-- Nenhum produto é inserido: cada usuário começa com o estoque vazio.
