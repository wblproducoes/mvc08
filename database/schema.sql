-- Schema do banco de dados
-- Substitua '' pelo prefixo definido no .env

-- Tabela de Status
CREATE TABLE `status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'secondary',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `status` (`id`, `name`, `translate`, `color`, `description`) VALUES
(1, 'active', 'Ativo', 'success', 'Registro ativo'),
(2, 'inactive', 'Inativo', 'warning', 'Registro inativo'),
(3, 'blocked', 'Bloqueado', 'danger', 'Registro bloqueado'),
(4, 'deleted', 'Excluído', 'dark', 'Registro excluído'),
(5, 'completed', 'Concluído', 'info', 'Registro concluído'),
(6, 'overdue', 'Vencido', 'danger', 'Registro vencido');

-- Tabela de Níveis
CREATE TABLE `levels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `levels` (`id`, `name`, `translate`, `description`) VALUES
(1, 'master', 'Master', 'Acesso total ao sistema'),
(2, 'admin', 'Administrador', 'Administrador do sistema'),
(3, 'direction', 'Direção', 'Direção escolar'),
(4, 'financial', 'Financeiro', 'Setor financeiro'),
(5, 'coordination', 'Coordenação', 'Coordenação pedagógica'),
(6, 'secretary', 'Secretaria', 'Secretaria escolar'),
(7, 'teacher', 'Professor', 'Professor'),
(8, 'employee', 'Funcionário', 'Funcionário geral'),
(9, 'student', 'Aluno', 'Aluno da escola'),
(10, 'guardian', 'Responsável', 'Responsável pelo aluno'),
(11, 'user', 'Usuário', 'Usuário comum');

-- Tabela de Gêneros
CREATE TABLE `genders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `genders` (`id`, `name`, `translate`, `description`) VALUES
(1, 'Male', 'Masculino', 'Gênero masculino'),
(2, 'Female', 'Feminino', 'Gênero feminino');


-- Tabela de Usuários
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender_id` int DEFAULT NULL,
  `phone_home` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_message` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_access_token` text COLLATE utf8mb4_unicode_ci,
  `google_refresh_token` text COLLATE utf8mb4_unicode_ci,
  `google_token_expires` timestamp NULL DEFAULT NULL,
  `google_calendar_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_signature` text COLLATE utf8mb4_unicode_ci COMMENT 'Assinatura HTML para mensagens',
  `signature_include_logo` tinyint(1) DEFAULT '0' COMMENT 'Incluir logo na assinatura',
  `permissions_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Última atualização das permissões individuais',
  `unique_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_access` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `level_id` int NOT NULL DEFAULT '11',
  `status_id` int NOT NULL DEFAULT '1',
  `register_id` int DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `level_id` (`level_id`),
  KEY `status_id` (`status_id`),
  KEY `gender_id` (`gender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Tabela de Logs de Acesso
CREATE TABLE `user_access_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `dh_access` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type_id` int NOT NULL,
  `success` tinyint(1) DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_type_id` (`event_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
