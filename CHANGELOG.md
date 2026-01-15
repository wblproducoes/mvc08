# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.6.0] - 2026-01-15

**Auditoria completa de segurança com implementação de rate limiting, proteções avançadas e documentação detalhada.**

### Adicionado
- **Rate Limiting (Proteção contra Força Bruta)**
  - Classe `RateLimiter` em `app/Helpers/RateLimiter.php`
  - Limite de 5 tentativas de login em 15 minutos por IP
  - Bloqueio temporário após exceder limite
  - Contador de tentativas restantes exibido ao usuário
  - Limpeza automática de tentativas antigas
  - Armazenamento em arquivo JSON para persistência
  - Integrado ao `AuthController` no método `login()`

- **Proteções Avançadas no .htaccess**
  - Desabilita listagem de diretórios (`Options -Indexes`)
  - Desabilita assinatura do servidor (`ServerSignature Off`)
  - Protege arquivos sensíveis (.env, .git, composer.json, logs, etc)
  - Bloqueia acesso a diretórios críticos (vendor, storage, database, app, routes)
  - Proteção contra injeção de código via query string
  - Bloqueia tentativas de SQL injection via URL
  - Desabilita execução de PHP em diretório de uploads
  - Limita métodos HTTP (apenas GET e POST)
  - Headers de segurança adicionais via mod_headers
  - Remove informações do servidor (Server, X-Powered-By)

- **Headers de Segurança Completos**
  - `X-Frame-Options: DENY` - Previne clickjacking
  - `X-Content-Type-Options: nosniff` - Previne MIME sniffing
  - `X-XSS-Protection: 1; mode=block` - Proteção XSS
  - `Referrer-Policy: strict-origin-when-cross-origin` - Controla referrer
  - `Permissions-Policy` - Desabilita geolocation, microphone, camera
  - `Content-Security-Policy` - Restringe fontes de conteúdo (em produção)

- **Configurações de Sessão Seguras**
  - `session.cookie_httponly = 1` - Previne acesso via JavaScript
  - `session.use_only_cookies = 1` - Usa apenas cookies
  - `session.cookie_secure` - Auto-detecta HTTPS
  - `session.cookie_samesite = Strict` - Proteção CSRF
  - `session.use_strict_mode = 1` - Modo estrito
  - `session.sid_length = 48` - ID de sessão mais longo
  - Regeneração automática de ID a cada 30 minutos

- **Documentação de Segurança**
  - `SECURITY.md` - Documentação completa de segurança
  - Lista todas as proteções implementadas
  - Configurações recomendadas para produção
  - Boas práticas para desenvolvedores e administradores
  - Checklist de segurança pré-produção
  - Guia de manutenção e monitoramento
  - Procedimentos em caso de incidente

### Melhorado
- **AuthController**
  - Integrado rate limiting no método `login()`
  - Mensagens informativas sobre tentativas restantes
  - Bloqueio automático após 5 tentativas falhas
  - Limpeza de tentativas após login bem-sucedido

- **Proteção de Arquivos**
  - `.htaccess` raiz com regras robustas
  - `public/.htaccess` com proteções adicionais
  - Bloqueio de execução PHP em uploads
  - Proteção contra path traversal

- **Logs de Erro**
  - Configurado para produção em `public/index.php`
  - Erros salvos em `storage/logs/php_errors.log`
  - Display de erros desabilitado em produção

### Segurança
- ✅ **SQL Injection**: Prepared statements em todos os queries
- ✅ **CSRF**: Tokens validados em todos os formulários POST
- ✅ **XSS**: Sanitização de inputs e outputs
- ✅ **Rate Limiting**: Proteção contra força bruta no login
- ✅ **Sessões Seguras**: HttpOnly, Secure, SameSite, regeneração periódica
- ✅ **Senhas**: Bcrypt com salt automático
- ✅ **Headers**: 6 headers de segurança implementados
- ✅ **Arquivos Sensíveis**: Bloqueados via .htaccess
- ✅ **Uploads**: PHP desabilitado, validação de tipos
- ✅ **Integridade**: Usuário ID 1 protegido, sistema trava se deletado
- ✅ **Injeção de Código**: Filtros em .htaccess e sanitização

### Técnico
- Rate limiter usa arquivo JSON para persistência
- Detecção inteligente de IP (suporta proxies e load balancers)
- Limpeza automática de dados antigos
- Código totalmente documentado com PHPDoc
- Compatível com Apache e mod_rewrite

### Nota de Segurança
O sistema agora possui proteções próximas a 100% contra as principais vulnerabilidades web:
- OWASP Top 10 coberto
- Rate limiting implementado
- Headers de segurança completos
- Arquivos sensíveis protegidos
- Sessões configuradas com máxima segurança
- Documentação completa para manutenção

**Recomendação**: Revise o arquivo `SECURITY.md` antes de colocar em produção.

---

## [1.5.0] - 2026-01-14

**Sistema de paginação completo para todas as listagens do sistema.**

### Adicionado
- **Sistema de Paginação**
  - Classe `Pagination` em `app/Helpers/Pagination.php`
  - Componente Twig reutilizável `pagination.twig`
  - Paginação com 10 registros por página
  - Navegação Anterior/Próximo
  - Exibição de 3 números de página
  - Informação de página atual e total de registros

- **Métodos no Model Base**
  - `count()` - Conta total de registros
  - `paginate()` - Busca com LIMIT e OFFSET

- **Paginação nas Listagens**
  - Usuários (`/users`)
  - Status (`/status`)
  - Níveis de Acesso (`/levels`)

### Melhorado
- Controllers atualizados para usar paginação
- Views com componente de paginação incluído
- CSS responsivo para paginação

---

## [1.4.0] - 2026-01-14

**Sistema CRUD completo de usuários com interface moderna, validações e soft delete.**

### Adicionado
- **CRUD de Usuários Completo**
  - Listagem de usuários com tabela responsiva
  - Criação de novos usuários
  - Edição de usuários existentes
  - Exclusão com soft delete (não remove do banco)
  - Validações de formulário (client e server-side)
  - Proteção CSRF em todos os formulários

- **UserController**
  - Método `index()` - Lista todos os usuários
  - Método `create()` - Exibe formulário de criação
  - Método `store()` - Salva novo usuário
  - Método `edit()` - Exibe formulário de edição
  - Método `update()` - Atualiza usuário
  - Método `destroy()` - Deleta usuário (soft delete)
  - Validações completas de dados
  - Verificação de email e username duplicados
  - Proteção contra auto-exclusão

- **User Model**
  - Método `findByEmail()` - Busca por email
  - Método `findByUsername()` - Busca por username
  - Método `all()` com JOIN de status e nível de acesso
  - Método `find()` com informações relacionadas

- **Views de Usuários**
  - `index.twig` - Listagem com tabela
  - `create.twig` - Formulário de criação
  - `edit.twig` - Formulário de edição
  - Design consistente com dashboard
  - Feedback visual de erros
  - Confirmação antes de deletar

- **Estilos CSS**
  - `users.css` - Estilos dedicados para CRUD
  - Tabelas responsivas
  - Formulários estilizados
  - Badges de status
  - Botões de ação

- **Rotas RESTful**
  - GET `/users` - Lista usuários
  - GET `/users/create` - Formulário de criação
  - POST `/users` - Cria usuário
  - GET `/users/{id}/edit` - Formulário de edição
  - PUT `/users/{id}` - Atualiza usuário
  - DELETE `/users/{id}` - Deleta usuário

### Melhorado
- **Menu Lateral**
  - Configurações agora é dropdown multi-nível
  - Usuários dentro de Configurações
  - Animação suave de expansão/recolhimento
  - Seta indicadora de estado

- **Segurança**
  - Hash bcrypt para senhas
  - Validação de CSRF em todas as ações
  - Sanitização de inputs
  - Prepared statements no banco
  - Soft delete preserva dados

### Técnico
- Arquitetura MVC completa
- Validações server-side e client-side
- AJAX para operações assíncronas
- Feedback visual de erros
- Código limpo e documentado
- Padrão RESTful nas rotas

## [1.3.0] - 2026-01-14

**Dashboard moderno e minimalista com design UX/UI profissional. Interface limpa e focada na usabilidade.**

### Adicionado
- **Topbar Moderna**
  - Logo do Colégio São Gonçalo na navbar
  - Notificações com badge de alerta
  - Menu de usuário com avatar, nome e cargo
  - Dropdown animado com opções de perfil, configurações e logout
  - Design limpo e responsivo

- **Sidebar Simplificada**
  - Fundo escuro (#2c3e50) com excelente contraste
  - Menu organizado por seções
  - Navegação clara com Dashboard e Usuários
  - Item ativo com gradiente roxo/azul vibrante
  - Efeitos hover suaves
  - Ícones grandes e legíveis

- **Layout Responsivo**
  - Menu hamburguer para mobile
  - Sidebar retrátil em telas pequenas
  - Adaptação automática de elementos
  - Touch-friendly para tablets

### Melhorado
- **Experiência do Usuário (UX)**
  - Interface minimalista e focada
  - Navegação intuitiva
  - Feedback visual em todas as interações
  - Hierarquia visual clara
  - Cores com alto contraste para acessibilidade

- **Interface do Usuário (UI)**
  - Design system com variáveis CSS
  - Tipografia moderna e legível
  - Espaçamentos consistentes
  - Bordas arredondadas suaves
  - Sombras sutis para profundidade
  - Paleta de cores harmoniosa

### Removido
- Campo de busca na navbar (simplificação)
- Cards de estatísticas (serão adicionados conforme necessidade)
- Seção de gráficos (será implementada futuramente)
- Atividades recentes (será implementada futuramente)
- Card de boas-vindas (interface mais limpa)
- Logo e nome da sidebar (mais espaço para navegação)
- Ícone de configurações da topbar (disponível no menu do usuário)

### Técnico
- CSS moderno com custom properties
- Grid e Flexbox para layouts
- Transições CSS3 suaves
- JavaScript vanilla para interatividade
- Mobile-first approach
- Código limpo e manutenível
- Cache busting com versionamento de assets

## [1.2.0] - 2026-01-14

**Correções críticas no instalador web, schema do banco de dados e sistema de roteamento. O sistema agora funciona completamente em subdiretórios (ex: /mvc08).**

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

### Corrigido - Sistema de Roteamento
- **CRÍTICO: Rotas não funcionavam em subdiretórios**
  - Corrigido Router para remover prefixo do diretório base (ex: `/mvc08`)
  - Agora detecta automaticamente o base path da aplicação
  - Sistema funciona tanto na raiz quanto em subdiretórios
  - URLs são processadas corretamente independente da instalação

### Adicionado
- **Helper de URLs** (`App\Helpers\Url`)
  - Função `Url::to()` para gerar URLs de rotas com base path correto
  - Função `Url::asset()` para gerar URLs de assets (CSS, JS, imagens)
  - Integração com Twig via funções `url()` e `asset()`
  - Detecta automaticamente o diretório base da aplicação

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

- **Views Twig**
  - Todos os links agora usam função `url()` para gerar caminhos corretos
  - Assets (CSS/JS) usam função `asset()` para caminhos corretos
  - Sistema funciona em qualquer configuração de diretório
  - Links de login, logout, dashboard e forgot-password corrigidos

### Removido
- **Arquivos de Instalação Não Utilizados**
  - `public/install_backup.php`
  - `public/install_old.php`
  - `public/install_simple_tables.php`
  - `public/install_test.php`
  - `public/install.php.backup`
  - `public/test_create_tables.php`
  - `fix_install.txt`
  
- **Documentação Temporária de Correção**
  - `CORRECAO_INSTALL_FINAL.md`
  - `CORRECAO_INSTALL.md`
  - `SOLUCAO_FINAL.md`
  - `TESTE_INSTALADOR.md`

### Nota Técnica
- Particionamento pode ser adicionado manualmente depois se necessário
- Para usar particionamento, seria necessário remover a foreign key `fk_logs_user`
- Optamos por manter a integridade referencial ao invés do particionamento
- O instalador agora é totalmente funcional e cria todas as tabelas corretamente
- Mantido apenas `public/install.php` como arquivo de instalação oficial
- Sistema agora funciona perfeitamente em subdiretórios do Apache/Nginx

## [1.1.0] - 2026-01-14

**Adicionado instalador web completo com interface gráfica, detecção inteligente de instalação e proteção contra reinstalação acidental.**

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

**Lançamento inicial do sistema administrativo MVC com arquitetura profissional, autenticação completa e segurança robusta.**

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
