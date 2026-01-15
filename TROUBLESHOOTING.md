# 🔧 Guia de Solução de Problemas
## Sistema MVC08 - Colégio São Gonçalo

---

## ❌ Internal Server Error

### Causa 1: PHP 8.4+ / 8.5+ Incompatibilidade

**Sintoma:** Internal Server Error após atualizar para PHP 8.4+

**Solução:** ✅ JÁ CORRIGIDO
- Removido `session.sid_length` e `session.sid_bits_per_character` (deprecated)
- Atualizado `.htaccess` para sintaxe Apache 2.4+

### Causa 2: Módulos do Apache Desabilitados

**Verificar:**
```bash
# Windows (XAMPP)
# Abra: C:\xampp\apache\conf\httpd.conf
# Procure e descomente (remova #):
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule headers_module modules/mod_headers.so
```

**Reiniciar Apache:**
- XAMPP: Painel de Controle → Stop → Start
- WAMP: Reiniciar Todos os Serviços
- Laragon: Stop → Start

### Causa 3: .htaccess com Sintaxe Antiga

**Solução:** ✅ JÁ CORRIGIDO
- Atualizado de `Order allow,deny` para `Require all denied` (Apache 2.4+)
- Removido diretivas `php_flag` e `php_value` problemáticas

### Causa 4: AllowOverride Desabilitado

**Verificar:**
```apache
# Em httpd.conf ou apache2.conf
<Directory "C:/xampp/htdocs">
    AllowOverride All  # Deve ser "All", não "None"
</Directory>
```

---

## ❌ 404 - Página não encontrada

### Causa: mod_rewrite desabilitado

**Solução:**
1. Habilitar mod_rewrite no Apache
2. Reiniciar Apache
3. Verificar se `.htaccess` existe na raiz e em `public/`

---

## ❌ Erro de Conexão com Banco de Dados

### Verificar .env

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_admin
DB_USER=root
DB_PASS=
DB_PREFIX=sys08_
```

### Testar Conexão

```bash
php -r "
\$pdo = new PDO('mysql:host=localhost;dbname=sistema_admin', 'root', '');
echo 'Conexão OK!';
"
```

---

## ❌ Erro de Permissões

### Windows

Geralmente não é problema no Windows.

### Linux/Mac

```bash
# Permissões corretas
chmod 755 app/ public/ storage/
chmod 644 .env composer.json
chmod 775 storage/logs storage/cache storage/sessions
```

---

## ❌ Composer não encontrado

```bash
# Instalar dependências
composer install

# Se não tiver composer:
# Baixe em: https://getcomposer.org/download/
```

---

## ❌ Erro "Could not resolve host: github.com"

**Causa:** Problema de DNS/Internet

**Soluções:**
1. Verificar conexão com internet
2. Configurar DNS do Google (8.8.8.8 e 8.8.4.4)
3. Executar: `ipconfig /flushdns`
4. Reiniciar roteador

---

## 🔍 Diagnóstico Rápido

### 1. Verificar PHP

```bash
php -v
# Deve mostrar: PHP 8.0+ (recomendado 8.1 a 8.3)
```

### 2. Verificar Apache

```bash
# Acessar: http://localhost
# Deve mostrar página do Apache/XAMPP
```

### 3. Verificar Módulos

```bash
# Criar arquivo: public/info.php
<?php phpinfo(); ?>

# Acessar: http://localhost/mvc08/info.php
# Procurar por: mod_rewrite, mod_headers
# DELETAR info.php depois!
```

### 4. Verificar .htaccess

```bash
# Verificar se existe
ls .htaccess
ls public/.htaccess

# Verificar sintaxe
# Não deve ter erros de sintaxe
```

### 5. Verificar Logs

```bash
# Apache Error Log
# XAMPP: C:\xampp\apache\logs\error.log
# WAMP: C:\wamp64\logs\apache_error.log
# Laragon: C:\laragon\bin\apache\logs\error.log

# Ver últimas linhas
Get-Content C:\xampp\apache\logs\error.log -Tail 50
```

---

## 🚀 Solução Rápida (Reset)

Se nada funcionar, tente:

```bash
# 1. Parar Apache

# 2. Limpar cache
rm -rf storage/cache/*
rm -rf storage/sessions/*

# 3. Reinstalar dependências
rm -rf vendor/
composer install

# 4. Verificar .env
# Certifique-se que existe e está correto

# 5. Reiniciar Apache

# 6. Acessar: http://localhost/mvc08/
```

---

## 📞 Ainda com Problemas?

### Informações para Suporte

Colete estas informações:

```bash
# 1. Versão do PHP
php -v

# 2. Versão do Apache
httpd -v

# 3. Módulos carregados
httpd -M | findstr rewrite
httpd -M | findstr headers

# 4. Últimos erros do Apache
Get-Content C:\xampp\apache\logs\error.log -Tail 20

# 5. Teste de conexão
php -r "echo 'PHP OK';"
```

---

## ✅ Checklist de Funcionamento

- [ ] Apache rodando
- [ ] PHP 8.0+ instalado
- [ ] mod_rewrite habilitado
- [ ] mod_headers habilitado
- [ ] AllowOverride All configurado
- [ ] .htaccess existe na raiz
- [ ] .htaccess existe em public/
- [ ] .env configurado corretamente
- [ ] Banco de dados criado
- [ ] Tabelas criadas (via instalador)
- [ ] vendor/ instalado (composer install)
- [ ] Acesso: http://localhost/mvc08/

---

**Última atualização:** 15/01/2026  
**Versão:** 1.6.0
