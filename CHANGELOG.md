# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.0.0] - 2026-01-14

### Adicionado
- Arquitetura MVC profissional
- Sistema de autenticação com login/logout
- Recuperação de senha via email
- Proteção CSRF em formulários
- Middleware de autenticação
- Middleware para visitantes
- Sistema de roteamento
- Models base com CRUD
- Controllers base
- Template engine Twig
- Bootstrap 5.3 para UI responsiva
- Helpers de segurança e validação
- Serviço de email (PHPMailer)
- Serviço de PDF (DomPDF)
- Sistema de logs
- Variáveis de ambiente (.env)
- Banco de dados com tabelas:
  - Usuários
  - Logs de acesso
  - Status
  - Níveis de acesso
  - Gêneros
- Documentação completa
- Guia de instalação
- Script de setup automatizado

### Segurança
- Senhas com hash bcrypt
- Prepared Statements (PDO)
- Proteção contra SQL Injection
- Proteção CSRF
- Sanitização de inputs
- Validação de dados
- Sessões seguras
- Soft delete nos registros
