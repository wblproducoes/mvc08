# Sistema Administrativo MVC

Sistema administrativo completo desenvolvido em PHP com arquitetura MVC Profissional, utilizando as melhores práticas de desenvolvimento e segurança (atingindo o mais próximo de 100% de proteção).

## Estrutura de Pasta 

├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   │   ├── default/
│   │       ├── pages/
│   │   ├── mordern/
│   │       ├── pages/
│   ├── Services/
│   ├── Middlewares/
│   ├── Helpers/
│   └── Config/
│
├── public/
│   ├── index.php   ← ponto de entrada do sistema
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── uploads/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
│   ├── cache/
│   ├── logs/
│   └── sessions/
│
├── vendor/

## Tecnologias 

-  **PHP 8.4+** - Orientação a Objetos (compatível com PHP 8.4 e 8.5)
-  **Twig 3.0** - Template Engine
-  **Bootstrap 5.3** - Framework CSS moderno
-  **PHPMailer 7.0.3** - Envio de emails
-  **DomPDF 3.1.4** - Geração de PDFs
-  **MySQL/MariaDB** - Banco de dados (todas as tabelas vão trabalhar com prefixo)
-  **Composer** - Gerenciador de dependências
- **PHPDotEnv** - Variável de Ambiente

## Caracteristicas

- Documentação detalhada com PHPDocs
- Método para registrar as versões com git
- Página de login: Com os campos username(Usuário) e password(Senha)
- Página de esqueceu a senha: campo de mail (E-mail)
- Proteção CSRF
- Senha criptografadas (bcrypt)
- Variável de Ambiente (.env)
- Middleware de autenticação
- Envio de emails
- Design responsivo UX/UI (Bootstrap 5.3)
- Interface moderna e intuitiva
- Código reutilizável e manutenível
- Prepared Statements (PDO)
- Validação de dados

## Tabelas de Banco de Dados

´´sql

-- 
-- Tableta de Logs de Acesso de Usuário
--
CREATE TABLE `user_access_logs` (
`id` int NOT NULL,
`user_id` int DEFAULT NULL,
`dh_access` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`event_type_id` int NOT NULL,
`success` tinyint(1) DEFAULT '0',
`details` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabela de Usuário
--
CREATE TABLE `users` (
`id` int NOT NULL,
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
`deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tabela de Status
--
CREATE TABLE `status` (
`id` int NOT NULL,
`name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`translate` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`color` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'secondary',
`description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`dh` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`dh_update` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `status` (`id`, `name`, `translate`, `color`, `description`) VALUES
('active', 'Ativo', 'success', 'Registro ativo'),
('inactive', 'Inativo', 'warning', 'Registro inativo'),
('blocked', 'Bloqueado', 'danger', 'Registro bloqueado'),
('deleted', 'Excluído', 'dark', 'Registro excluído'),
('completed', 'Concluído', 'info', 'Registro concluído'),
('overdue', 'Vencido', 'danger', 'Registro vencido');


--
-- Tabela de Nível de Usuário
--

CREATE  TABLE `levels` (
`id`  int  NOT NULL,
`name`  varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`translate`  varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT  NULL,
`description`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT  NULL,
`dh`  timestamp  NULL  DEFAULT CURRENT_TIMESTAMP,
`dh_update`  timestamp  NULL  DEFAULT  NULL  ON UPDATE CURRENT_TIMESTAMP,
`deleted_at`  timestamp  NULL  DEFAULT  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;  

INSERT INTO  `levels` (`id`, `name`, `translate`, `description`) VALUES
('master', 'Master', 'Acesso total ao sistema'),
('admin', 'Administrador', 'Administrador do sistema'),
('direction', 'Direção', 'Direção escolar'),
('financial', 'Financeiro', 'Setor financeiro'),
('coordination', 'Coordenação', 'Coordenação pedagógica'),
('secretary', 'Secretaria', 'Secretaria escolar'),
('teacher', 'Professor', 'Professor'),
('employee', 'Funcionário', 'Funcionário geral'),
('student', 'Aluno', 'Aluno da escola'),
('guardian', 'Responsável', 'Responsável pelo aluno'),
('user', 'Usuário', 'Usuário comum');

--
-- Tabela de Sexo
--
CREATE  TABLE `genders` (
`id`  int  NOT NULL,
`name`  varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`translate`  varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT  NULL,
`description`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT  NULL,
`dh`  timestamp  NULL  DEFAULT CURRENT_TIMESTAMP,
`dh_update`  timestamp  NULL  DEFAULT  NULL  ON UPDATE CURRENT_TIMESTAMP,
`deleted_at`  timestamp  NULL  DEFAULT  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO  `genders` (`id`, `name`, `translate`, `description`) VALUES
('Male', 'Masculino', 'Gênero masculino'),
('Female', 'Feminino', 'Gênero feminino');



