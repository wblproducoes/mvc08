# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.2.0] - 2026-01-14

### Corrigido - Instalador Web
- **CRÍTICO: Criação de Tabelas no STEP 2**
  - Corrigido bug onde 0 comandos SQL eram executados
  - Adicionado sistema de remoção inteligente de comentários SQL
  - Remove comentários de linha (`--`) no início e inline
  - Remove linhas vazias e mantém apenas código SQL executável
  - Adicionado debug detalhado para rastrear cada statement
  - Mensagens de erro completas com informações de troubleshooting
  - Agora mostra exatamente por que cada statement foi pulado ou executado
  
- **Links de Redirecionamento**
  - Corrigido botão "Acessar Sistema" que redirecionava para `/` ao invés da URL configurada
  - Corrigido link "Voltar ao Sistema" na tela de senha
  - Agora usa a URL configurada em `app_url` (ex: `http://localhost/mvc08`)
  - Fallback para `/mvc08` se URL não estiver configurada

### Corrigido - Schema SQL
- **CRÍTICO: Particionamento incompatível com Foreign Keys**
  - Removido particionamento da tabela `user_access_logs`
  - MySQL/MariaDB não suporta foreign keys em tabelas particionadas
  - Alterada PRIMARY KEY de `(id, dh_access)` para apenas `(id)`
  - Mantidos todos os índices e foreign keys para integridade referencial
  - Tabela agora cria corretamente sem erros

### Melhorado
- **Sistema de Debug do Instalador**
  - Mostra tamanho do SQL original e limpo
  - Total de statements encontrados
  - Preview do SQL processado
  - Status detalhado de cada statement (VAZIO, COMENTÁRIO, CURTO, EXECUTANDO, ERRO)
  - Lista dos primeiros 10 statements processados
  - Tabelas existentes no banco após execução
  - Erros SQL encontrados durante execução
  
- **Processamento de SQL**
  - Limpeza inteligente de comentários linha por linha
  - Remove comentários completos e inline
  - Preserva código SQL válido
  - Melhor tratamento de statements multi-linha

### Nota Técnica
- Particionamento pode ser adicionado manualmente depois se necessário
- Para usar particionamento, seria necessário remover a foreign key `fk_logs_user`
- Optamos por manter a integridade referencial ao invés do particionamento
- O instalador agora é totalmente funcional e cria todas as tabelas corretamente

## [1.1.0] - 2026-01-14

### Adicionado
- **Instalador Web Inteligente** (`public/install.php`)
  - Interface gráfica completa para instalação
  - Teste de conexão com banco de dados antes de instalar
  - Criação automática de todas as tabelas
  - Criação do primeiro usuário via interface
  - Geração automática do arquivo .env
  - Indicador visual de progresso (4 passos)
- **Detecção Inteligente de Instalação**
  - Verifica existência do .env
  - Verifica existência das tabelas no banco
  - Redireciona automaticamente para instalador se necessário
- **Proteção contra Reinstalação Acidental**
  - Solicita senha do administrador master para reinstalar
  - Validação de senha com hash bcrypt
  - Tela de autorização antes de permitir reinstalação
- **Helper InstallChecker**
  - Classe para verificar status da instalação
  - Métodos para validar .env e tabelas
- **Redirecionamento Automático**
  - .htaccess na raiz redireciona para pasta public/
  - index.php detecta instalação e redireciona se necessário
- **Documentação do Instalador Web**
  - INSTALL_WEB.md com guia completo
  - Instruções para todos os cenários de instalação

### Melhorado
- Sistema de detecção de instalação mais robusto
- Mensagens de erro mais descritivas no instalador
- Feedback visual durante processo de instalação
- Reutilização de configurações do .env existente
- Validação de dados em todas as etapas

### Segurança
- Proteção por senha para reinstalação
- Validação de credenciais antes de criar tabelas
- Verificação de permissões do banco de dados
- Recomendação para deletar install.php após instalação

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
