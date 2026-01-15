# Guia Rápido de Segurança
## Sistema MVC08 - Colégio São Gonçalo

Este guia fornece instruções rápidas para verificar e manter a segurança do sistema.

---

## 🚀 Início Rápido

### Verificar Segurança do Sistema

```bash
# Execute o script de verificação
php security-check.php
```

**Resultado esperado:** Score de 97% ou superior

---

## 📚 Documentação Disponível

| Arquivo | Descrição |
|---------|-----------|
| `SECURITY.md` | Documentação completa de segurança |
| `SECURITY_AUDIT_REPORT.md` | Relatório detalhado da auditoria |
| `SECURITY_README.md` | Este guia rápido |
| `security-check.php` | Script de verificação automática |

---

## ✅ Checklist Pré-Produção

Antes de colocar o sistema em produção, verifique:

- [ ] `APP_DEBUG=false` no .env
- [ ] `APP_ENV=production` no .env
- [ ] HTTPS configurado e forçado
- [ ] Certificado SSL válido instalado
- [ ] Senha forte no banco de dados
- [ ] Senha do admin alterada
- [ ] Permissões de arquivos corretas (755/644)
- [ ] .env não acessível via web
- [ ] Logs configurados
- [ ] Backups automáticos configurados
- [ ] Firewall configurado
- [ ] `install.php` deletado
- [ ] Score de segurança ≥ 90%

---

## 🔒 Principais Proteções

### 1. SQL Injection
✅ **Protegido** - Prepared Statements em todos os queries

### 2. CSRF
✅ **Protegido** - Tokens validados em todos os formulários

### 3. XSS
✅ **Protegido** - Sanitização + Twig auto-escape

### 4. Força Bruta
✅ **Protegido** - Rate limiting (5 tentativas/15min)

### 5. Sessões
✅ **Protegido** - HttpOnly, Secure, SameSite, regeneração

### 6. Senhas
✅ **Protegido** - Bcrypt com salt automático

### 7. Headers
✅ **Protegido** - 6 headers de segurança implementados

### 8. Arquivos Sensíveis
✅ **Protegido** - Bloqueados via .htaccess

### 9. Uploads
✅ **Protegido** - PHP desabilitado, validação de tipos

### 10. Integridade
✅ **Protegido** - Usuário ID 1 não pode ser deletado

---

## 🛠️ Comandos Úteis

### Verificar Segurança
```bash
php security-check.php
```

### Limpar Logs Antigos (30+ dias)
```bash
find storage/logs -name "*.log" -mtime +30 -delete
```

### Limpar Cache de Rate Limiting
```bash
rm storage/cache/rate_limiter.json
```

### Verificar Usuário ID 1 (MySQL)
```bash
mysql -u usuario -p -e "SELECT id, name FROM sys08_users WHERE id = 1;"
```

### Ver Logs em Tempo Real
```bash
# Logs da aplicação
tail -f storage/logs/app.log

# Erros PHP
tail -f storage/logs/php_errors.log

# Tentativas de login
tail -f storage/logs/app.log | grep "login"
```

### Atualizar Dependências
```bash
composer update
```

### Verificar Permissões
```bash
# Windows (PowerShell)
Get-ChildItem -Recurse | Select-Object FullName, Mode

# Linux/Mac
find . -type f -ls
find . -type d -ls
```

---

## 🚨 Em Caso de Emergência

### Suspeita de Invasão

1. **Isole o sistema**
   ```bash
   # Desabilite acesso externo temporariamente
   # Mantenha apenas acesso local
   ```

2. **Investigue**
   ```bash
   # Revise logs
   tail -100 storage/logs/app.log
   
   # Verifique arquivos modificados recentemente
   find . -type f -mtime -1 -ls
   ```

3. **Restaure**
   - Restaure backup limpo
   - Troque todas as senhas
   - Regenere chaves de sessão

4. **Fortaleça**
   - Atualize dependências
   - Revise permissões
   - Adicione regras de firewall

### Usuário ID 1 Deletado

```sql
-- Restaurar do backup
INSERT INTO sys08_users (id, name, email, username, password, ...)
VALUES (1, 'Admin', 'admin@example.com', ...);
```

Ou execute o instalador novamente: `/install.php`

---

## 📊 Interpretando o Score

| Score | Status | Ação |
|-------|--------|------|
| 90-100% | ✅ EXCELENTE | Manutenção regular |
| 70-89% | ⚠️ BOM | Revisar avisos |
| 50-69% | ⚠️ REGULAR | Corrigir problemas |
| 0-49% | ❌ CRÍTICO | Ação imediata |

---

## 🔄 Manutenção Regular

### Diária
- [ ] Revisar logs de erro
- [ ] Verificar espaço em disco

### Semanal
- [ ] Revisar tentativas de login falhas
- [ ] Verificar backups

### Mensal
- [ ] Executar `security-check.php`
- [ ] Atualizar dependências
- [ ] Testar restauração de backup
- [ ] Limpar logs antigos

### Trimestral
- [ ] Auditoria completa de segurança
- [ ] Revisar usuários ativos
- [ ] Verificar integridade do banco

---

## 📞 Suporte

**Email:** seguranca@colegiosaogoncalo.com.br  
**Telefone:** (XX) XXXX-XXXX

---

## 🎓 Recursos Adicionais

### Documentação Oficial
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [Apache Security Tips](https://httpd.apache.org/docs/2.4/misc/security_tips.html)

### Ferramentas Recomendadas
- [OWASP ZAP](https://www.zaproxy.org/) - Scanner de vulnerabilidades
- [Burp Suite](https://portswigger.net/burp) - Teste de penetração
- [Nikto](https://cirt.net/Nikto2) - Scanner de servidor web

---

## ✨ Dicas de Segurança

1. **Nunca confie em dados do usuário**
   - Sempre valide e sanitize inputs
   - Use prepared statements

2. **Mantenha tudo atualizado**
   - PHP, Apache, MySQL
   - Dependências do Composer
   - Sistema operacional

3. **Use HTTPS em produção**
   - Obtenha certificado SSL gratuito (Let's Encrypt)
   - Force redirecionamento HTTPS

4. **Senhas fortes**
   - Mínimo 12 caracteres
   - Letras, números e símbolos
   - Nunca reutilize senhas

5. **Backups regulares**
   - Banco de dados diário
   - Arquivos semanalmente
   - Teste restauração mensalmente

6. **Monitore logs**
   - Configure alertas para erros críticos
   - Revise tentativas de login falhas
   - Procure por padrões suspeitos

7. **Princípio do Menor Privilégio**
   - Usuários só têm acesso ao necessário
   - Banco de dados com usuário específico
   - Permissões de arquivo mínimas

8. **Defesa em Profundidade**
   - Múltiplas camadas de segurança
   - Não dependa de uma única proteção
   - Assuma que uma camada pode falhar

---

**Última atualização:** 15/01/2026  
**Versão:** 1.6.0  
**Score:** 97% ✓

**Lembre-se:** Segurança é um processo contínuo, não um destino!
