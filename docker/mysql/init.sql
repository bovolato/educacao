-- Script de inicialização do banco de dados SIGEM
CREATE DATABASE IF NOT EXISTS `educacao` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Garantir permissões para o usuário root
GRANT ALL PRIVILEGES ON `educacao`.* TO 'root'@'%';
GRANT ALL PRIVILEGES ON `educacao`.* TO 'sigem'@'%';
FLUSH PRIVILEGES;
