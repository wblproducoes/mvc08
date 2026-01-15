# Documentação de Segurança
## Sistema MVC08 - Colégio São Gonçalo

Este documento descreve todas as medidas de segurança implementadas no sistema.

---

## 📋 Índice

1. [Proteções Implementadas](#proteções-implementadas)
2. [Configurações de Segurança](#configurações-de-segurança)
3. [Boas Práticas](#boas-práticas)
4. [Checklist de Segurança](#checklist-de-segurança)
5. [Manutenção](#manutenção)

---

## 🛡️ Proteções Implementadas

### 1. Proteção contra SQL Injection
- ✅ **Prepared Statements**: Todos os queries usam prepared statements com PDO
- ✅ **Sanitização**: Inputs são sanitizados com `htmlspecialchars()`
- ✅ **Validação**: Validação de tipos e formatos antes de queries
- 📁 Arquivos: `app/Core/Model.php`, `app/Helpers/Security.php`

### 2. Proteção CSRF (Cross-Site Request Forgery)
- ✅ **Tokens CSRF**: Gerados para cada sessão
- ✅ **Validação**: Todos os formulários POST validam token
- ✅ **Middleware**: `CsrfMiddleware` valida automaticamente
- 📁 Arquivos: `app/Helpers/Security.php`, `app/Middlewares/CsrfMiddleware.php`

### 3. Proteção XSS (Cross-Site Scripting)
- ✅ **Sanitização de Output**: Twig escapa automaticamente variáveis
- ✅ **Sanitização de Input**: `Security::sanitize()` em todos os inputs
- ✅ **Headers**: `X-XSS-Protection: 1; mode=block`
- ✅ **CSP**: Content Security Policy configurado
- 📁 Arquivos: `public/index.php`, `app/Helpers/Security.php`

### 4. Rate Limiting (Proteção contra Força Bruta)
- ✅ **Login**: Máximo 5 tentativas em 15 minutos por IP
- ✅ **Bloqueio Temporário**: IP bloqueado após exceder limite
- ✅ **Contador**: Mostra tentativas restantes ao usuário
- ✅ **Limpeza Automática**: Remove tentativas antigas
- 📁 Arquivos: `app/Helpers/RateLimiter.php`, `app/Controllers/AuthController.php`

### 5. Proteção de Sessão
- ✅ **HttpOnly**: Cookies não acessíveis via JavaScript
- ✅ **Secure**: Cookies enviados apenas via HTTPS (em produção)
- ✅ **SameSite**: Proteção contra CSRF via cookies
- ✅ **Strict Mode**: Sessões em modo estrito
- ✅ **Regeneração**: ID de sessão regenerado a cada 30 minutos
- ✅ **ID Longo**: 48 caracteres para dificultar adivinhação
- 📁 Arquivos: `public/index.php`

### 6. Proteção de Senhas
- ✅ **Hashing**: Bcrypt com salt automático
- ✅ **Validação**: Mínimo 6 caracteres (recomendado: 8+)
- ✅ **Nunca em Plain Text**: Senhas nunca armazenadas em texto puro
- 📁 Arquivos: `app/Helpers/Security.php`

### 7. Headers de Segurança
- ✅ **X-Frame-Options**: DENY (previne clickjacking)
- ✅ **X-Content-Type-Options**: nosniff (previne MIME sniffing)
- ✅ **X-XSS-Protection**: 1; mode=block
- ✅ **Referrer-Policy**: strict-origin-when-cross-origin
- ✅ **Permissions-Policy**: Desabilita geolocation, microphone, camera
- ✅ **Content-Security-Policy**: Restringe fontes de conteúdo
- 📁 Arquivos: `public/index.php`, `public/.htaccess`

### 8. Proteção de Arquivos Sensíveis
- ✅ **.env**: Bloqueado via .htaccess
- ✅ **composer.json/lock**: Bloqueado via .htaccess
- ✅ **.git**: Bloqueado via .htaccess
- ✅ **Logs**: Bloqueados via .htaccess
- ✅ **Vendor**: Bloqueado via .htaccess
- ✅ **Storage**: Bloqueado via .htaccess
- 📁 Arquivos: `.htaccess`, `public/.htaccess`

### 9. Proteção de Upload
- ✅ **PHP Desabilitado**: Execução de PHP desabilitada em `/public/uploads`
- ✅ **Validação de Tipo**: Apenas tipos permitidos
- ✅ **Limite de Tamanho**: 10MB por arquivo
- 📁 Arquivos: `.htaccess`, `public/.htaccess`

### 10. Integridade do Sistema
- ✅ **Usuário Master**: ID 1 protegido contra exclusão
- ✅ **Verificação**: Sistema trava se usuário ID 1 não existir
- ✅ **Middleware**: `SystemIntegrityMiddleware` verifica a cada requisição
- ✅ **Tela de Erro**: Mensagem profissional em caso de violação
- 📁 Arquivos: `app/Middlewares/SystemIntegrityMiddleware.php`

### 11. Proteção contra Injeção de Código
- ✅ **.htaccess**: Bloqueia queries maliciosas
- ✅ **Validação**: Filtra caracteres especiais em queries
- ✅ **Sanitização**: Remove tags HTML/JavaScript perigosas
- 📁 Arquivos: `.htaccess`, `public/.htaccess`

---

## ⚙️ Configurações de Segurança

### Arquivo .env (Produção)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

# Sessão
SESSION_LIFETIME=7200

# Senha forte para banco
DB_PASS=SenhaForteAqui123!@#
```

### Apache (Produção)
```apache
# Força HTTPS (descomente em .htaccess)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Desabilita display_errors
php_flag display_errors off
```

### PHP (php.ini - Produção)
```ini
display_errors = Off
log_errors = On
error_log = /caminho/para/logs/php_errors.log
expose_php = Off
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

---

## 📚 Boas Práticas

### Para Desenvolvedores

1. **Nunca confie em dados do usuário**
   - Sempre valide e sanitize inputs
   - Use prepared statements para queries
   - Escape outputs no template

2. **Mantenha dependências atualizadas**
   ```bash
   composer update
   ```

3. **Use HTTPS em produção**
   - Obtenha certificado SSL (Let's Encrypt é gratuito)
   - Force redirecionamento HTTPS

4. **Senhas fortes**
   - Mínimo 12 caracteres
   - Letras maiúsculas, minúsculas, números e símbolos
   - Nunca reutilize senhas

5. **Backups regulares**
   - Banco de dados diário
   - Arquivos semanalmente
   - Teste restauração periodicamente

6. **Logs de auditoria**
   - Monitore tentativas de login falhas
   - Registre ações críticas (exclusões, alterações)
   - Revise logs regularmente

### Para Administradores

1. **Proteja o usuário ID 1**
   - Nunca delete do banco de dados
   - Use senha extremamente forte
   - Não compartilhe credenciais

2. **Permissões de arquivos**
   ```bash
   # Diretórios
   chmod 755 app/ public/ storage/
   
   # Arquivos
   chmod 644 .env composer.json
   
   # Storage (escrita)
   chmod 775 storage/logs storage/cache storage/sessions
   ```

3. **Atualizações de segurança**
   - Mantenha PHP atualizado (mínimo 8.0)
   - Atualize Apache/Nginx
   - Monitore vulnerabilidades conhecidas

4. **Firewall**
   - Configure firewall no servidor
   - Bloqueie portas desnecessárias
   - Use fail2ban para bloquear IPs maliciosos

---

## ✅ Checklist de Segurança

### Antes de ir para Produção

- [ ] `APP_DEBUG=false` no .env
- [ ] `APP_ENV=production` no .env
- [ ] HTTPS configurado e forçado
- [ ] Certificado SSL válido
- [ ] Senha forte no banco de dados
- [ ] Permissões de arquivos corretas
- [ ] .env não acessível via web
- [ ] Logs configurados e funcionando
- [ ] Backups automáticos configurados
- [ ] Firewall configurado
- [ ] Rate limiting testado
- [ ] CSRF tokens funcionando
- [ ] Uploads testados e seguros
- [ ] Usuário ID 1 protegido
- [ ] Todas as dependências atualizadas

### Manutenção Mensal

- [ ] Revisar logs de erro
- [ ] Revisar logs de acesso
- [ ] Verificar tentativas de login falhas
- [ ] Atualizar dependências (composer update)
- [ ] Testar backups
- [ ] Verificar espaço em disco
- [ ] Revisar usuários ativos
- [ ] Verificar integridade do banco

---

## 🔧 Manutenção

### Limpeza de Logs
```bash
# Limpar logs antigos (mais de 30 dias)
find storage/logs -name "*.log" -mtime +30 -delete
```

### Limpeza de Cache
```bash
# Limpar cache de rate limiting
rm storage/cache/rate_limiter.json
```

### Verificar Integridade
```bash
# Verificar se usuário ID 1 existe
mysql -u usuario -p -e "SELECT id, name FROM sys08_users WHERE id = 1;"
```

### Monitoramento
```bash
# Ver últimas tentativas de login
tail -f storage/logs/app.log | grep "login"

# Ver erros PHP
tail -f storage/logs/php_errors.log
```

---

## 🚨 Em Caso de Incidente

### Suspeita de Invasão

1. **Isole o sistema**
   - Desabilite acesso externo temporariamente
   - Mantenha apenas acesso local

2. **Investigue**
   - Revise logs de acesso
   - Verifique arquivos modificados recentemente
   - Procure por backdoors

3. **Restaure**
   - Restaure backup limpo
   - Troque todas as senhas
   - Regenere chaves de sessão

4. **Fortaleça**
   - Atualize todas as dependências
   - Revise permissões
   - Adicione regras de firewall

### Usuário ID 1 Deletado

1. **Restaure do backup**
   ```sql
   -- Restaurar usuário do backup
   INSERT INTO sys08_users (id, name, email, username, password, ...)
   VALUES (1, 'Admin', 'admin@example.com', ...);
   ```

2. **Ou execute o instalador novamente**
   - Acesse `/install.php`
   - Siga o processo de instalação

---

## 📞 Suporte

Para questões de segurança, entre em contato com:
- Email: seguranca@colegiosaogoncalo.com.br
- Telefone: (XX) XXXX-XXXX

---

**Última atualização**: Janeiro 2026  
**Versão do Sistema**: 1.5.0  
**Responsável**: Equipe de Desenvolvimento MVC08
