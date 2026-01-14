# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

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
