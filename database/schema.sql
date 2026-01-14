-- Schema do banco de dados - Versão Otimizada
-- Substitua 'sa_' pelo prefixo definido no .env

-- ============================================
-- TABELAS DE REFERÊNCIA (Lookup Tables)
-- ============================================

-- Tabela de Status
CREATE TABLE `sa_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'secondary',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_status_name` (`name`),
  KEY `idx_status_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabela de status do sistema';

INSERT INTO `sa_status` (`id`, `name`, `translate`, `color`, `description`) VALUES
(1, 'active', 'Ativo', 'success', 'Registro ativo'),
(2, 'inactive', 'Inativo', 'warning', 'Registro inativo'),
(3, 'blocked', 'Bloqueado', 'danger', 'Registro bloqueado'),
(4, 'deleted', 'Excluído', 'dark', 'Registro excluído'),
(5, 'completed', 'Concluído', 'info', 'Registro concluído'),
(6, 'overdue', 'Vencido', 'danger', 'Registro vencido');

-- Tabela de Níveis
CREATE TABLE `sa_levels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_level_name` (`name`),
  KEY `idx_level_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabela de níveis de acesso';

INSERT INTO `sa_levels` (`id`, `name`, `translate`, `description`) VALUES
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
CREATE TABLE `sa_genders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gender_name` (`name`),
  KEY `idx_gender_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabela de gêneros';

INSERT INTO `sa_genders` (`id`, `name`, `translate`, `description`) VALUES
(1, 'Male', 'Masculino', 'Gênero masculino'),
(2, 'Female', 'Feminino', 'Gênero feminino');

-- ============================================
-- TABELA PRINCIPAL DE USUÁRIOS
-- ============================================

CREATE TABLE `sa_users` (
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
  `register_id` int DEFAULT NULL COMMENT 'ID do usuário que registrou este usuário',
  `dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`),
  UNIQUE KEY `uk_unique_code` (`unique_code`),
  UNIQUE KEY `uk_cpf` (`cpf`),
  
  KEY `idx_level_status` (`level_id`, `status_id`),
  KEY `idx_gender` (`gender_id`),
  KEY `idx_register` (`register_id`),
  KEY `idx_deleted` (`deleted_at`),
  KEY `idx_last_access` (`last_access`),
  KEY `idx_password_reset` (`password_reset_token`, `password_reset_expires`),
  KEY `idx_session` (`session_token`),
  KEY `idx_name` (`name`(50)),
  KEY `idx_birth_date` (`birth_date`),
  
  CONSTRAINT `fk_users_level` FOREIGN KEY (`level_id`) REFERENCES `sa_levels` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_users_status` FOREIGN KEY (`status_id`) REFERENCES `sa_status` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_users_gender` FOREIGN KEY (`gender_id`) REFERENCES `sa_genders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_register` FOREIGN KEY (`register_id`) REFERENCES `sa_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabela principal de usuários do sistema';

-- ============================================
-- TABELA DE LOGS DE ACESSO
-- ============================================

CREATE TABLE `sa_user_access_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `dh_access` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type_id` int NOT NULL COMMENT '1=Login, 2=Logout, 3=Reset Password, 4=Failed Login',
  `success` tinyint(1) DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  
  PRIMARY KEY (`id`),
  KEY `idx_user_date` (`user_id`, `dh_access`),
  KEY `idx_event_success` (`event_type_id`, `success`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_dh_access` (`dh_access`),
  
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `sa_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Logs de acesso e eventos do sistema';

-- ============================================
-- VIEWS ÚTEIS
-- ============================================

-- View de usuários ativos
CREATE OR REPLACE VIEW `sa_v_users_active` AS
SELECT 
  u.id,
  u.name,
  u.username,
  u.email,
  u.last_access,
  l.translate as level_name,
  s.translate as status_name,
  s.color as status_color
FROM sa_users u
INNER JOIN sa_levels l ON u.level_id = l.id
INNER JOIN sa_status s ON u.status_id = s.id
WHERE u.deleted_at IS NULL AND u.status_id = 1;

-- View de logs recentes
CREATE OR REPLACE VIEW `sa_v_recent_logs` AS
SELECT 
  l.id,
  l.dh_access,
  u.name as user_name,
  u.username,
  l.ip_address,
  l.event_type_id,
  l.success,
  l.details
FROM sa_user_access_logs l
LEFT JOIN sa_users u ON l.user_id = u.id
WHERE l.dh_access >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY l.dh_access DESC;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger para limpar tokens expirados ao fazer login
DELIMITER $$
CREATE TRIGGER `tr_users_before_update` 
BEFORE UPDATE ON `sa_users`
FOR EACH ROW
BEGIN
  -- Limpa token de reset se a senha foi alterada
  IF NEW.password != OLD.password THEN
    SET NEW.password_reset_token = NULL;
    SET NEW.password_reset_expires = NULL;
  END IF;
  
  -- Limpa tokens expirados
  IF NEW.password_reset_expires IS NOT NULL AND NEW.password_reset_expires < NOW() THEN
    SET NEW.password_reset_token = NULL;
    SET NEW.password_reset_expires = NULL;
  END IF;
END$$
DELIMITER ;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure para limpar logs antigos (manter últimos 90 dias)
DELIMITER $$
CREATE PROCEDURE `sp_cleanup_old_logs`()
BEGIN
  DELETE FROM sa_user_access_logs 
  WHERE dh_access < DATE_SUB(NOW(), INTERVAL 90 DAY);
  
  SELECT ROW_COUNT() as deleted_rows;
END$$
DELIMITER ;

-- Procedure para estatísticas de usuários
DELIMITER $$
CREATE PROCEDURE `sp_user_statistics`()
BEGIN
  SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as inactive_users,
    SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as blocked_users,
    SUM(CASE WHEN last_access >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as active_last_week,
    SUM(CASE WHEN last_access >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as active_last_month
  FROM sa_users
  WHERE deleted_at IS NULL;
END$$
DELIMITER ;

-- ============================================
-- EVENTOS AGENDADOS
-- ============================================

-- Evento para limpar tokens expirados diariamente
CREATE EVENT IF NOT EXISTS `ev_cleanup_expired_tokens`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
  UPDATE sa_users 
  SET password_reset_token = NULL, 
      password_reset_expires = NULL
  WHERE password_reset_expires < NOW();

-- Evento para limpar logs antigos semanalmente
CREATE EVENT IF NOT EXISTS `ev_cleanup_old_logs`
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_TIMESTAMP
DO
  CALL sp_cleanup_old_logs();

-- ============================================
-- COMENTÁRIOS E DOCUMENTAÇÃO
-- ============================================

-- Adiciona comentários nas colunas importantes
ALTER TABLE `sa_users` 
  MODIFY COLUMN `level_id` int NOT NULL DEFAULT '11' COMMENT 'FK para sa_levels - Nível de acesso do usuário',
  MODIFY COLUMN `status_id` int NOT NULL DEFAULT '1' COMMENT 'FK para sa_status - Status atual do usuário',
  MODIFY COLUMN `session_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token da sessão ativa',
  MODIFY COLUMN `password_reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token para reset de senha',
  MODIFY COLUMN `password_reset_expires` timestamp NULL DEFAULT NULL COMMENT 'Expiração do token de reset';

-- ============================================
-- ÍNDICES FULL-TEXT PARA BUSCA
-- ============================================

-- Índice full-text para busca de usuários por nome
ALTER TABLE `sa_users` ADD FULLTEXT KEY `ft_user_search` (`name`, `email`, `username`);

-- ============================================
-- OTIMIZAÇÕES FINAIS
-- ============================================

-- Analisa e otimiza as tabelas
ANALYZE TABLE sa_status, sa_levels, sa_genders, sa_users, sa_user_access_logs;
OPTIMIZE TABLE sa_status, sa_levels, sa_genders, sa_users, sa_user_access_logs;
