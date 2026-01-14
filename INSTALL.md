# Guia de Instalação

## Requisitos do Sistema

- PHP 8.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Apache 2.4+ ou Nginx
- Composer
- Extensões PHP:
  - PDO
  - pdo_mysql
  - mbstring
  - openssl
  - json
  - curl

## Passo a Passo

### 1. Verificar PHP

```bash
php -v
```

Deve retornar PHP 8.4 ou superior.

### 2. Instalar Composer

Se não tiver o Composer instalado:

**Windows:**
- Baixe de https://getcomposer.org/download/
- Execute o instalador

**Linux/Mac:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Clonar/Baixar o Projeto

```bash
git clone <seu-repositorio>
cd sistema-administrativo
```

### 4. Instalar Dependências

```bash
composer install
```

### 5. Configurar Ambiente

```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

Edite o arquivo `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=sistema_admin
DB_USER=root
DB_PASS=sua_senha
DB_PREFIX=sa_
```

### 6. Criar Banco de Dados

**Opção 1: Via phpMyAdmin**
1. Acesse phpMyAdmin
2. Crie um banco chamado `sistema_admin`
3. Importe o arquivo `database/schema.sql`

**Opção 2: Via linha de comando**
```bash
mysql -u root -p
CREATE DATABASE sistema_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_admin;
SOURCE database/schema.sql;
EXIT;
```

### 7. Configurar Permissões (Linux/Mac)

```bash
chmod -R 775 storage/
chmod -R 775 public/uploads/
```

### 8. Configurar Servidor Web

#### Apache

**Habilitar mod_rewrite:**
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Configurar VirtualHost:**
```apache
<VirtualHost *:80>
    ServerName sistema-admin.local
    DocumentRoot /caminho/para/projeto/public
    
    <Directory /caminho/para/projeto/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sistema-admin-error.log
    CustomLog ${APACHE_LOG_DIR}/sistema-admin-access.log combined
</VirtualHost>
```

Adicione ao hosts:
```
127.0.0.1 sistema-admin.local
```

#### Nginx

```nginx
server {
    listen 80;
    server_name sistema-admin.local;
    root /caminho/para/projeto/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 9. Testar Instalação

Acesse o sistema no navegador:
- URL: http://sistema-admin.local (ou http://localhost conforme sua configuração)
- Usuário: `admin`
- Senha: `admin123`

### 10. Pós-Instalação

1. **Altere a senha padrão** imediatamente
2. Configure o email no `.env` para recuperação de senha
3. Revise as permissões de arquivos
4. Em produção, defina `APP_DEBUG=false`

## Troubleshooting

### Erro: "Class not found"
```bash
composer dump-autoload
```

### Erro: "Permission denied" em storage/
```bash
chmod -R 775 storage/
```

### Erro de conexão com banco
- Verifique credenciais no `.env`
- Confirme que o MySQL está rodando
- Teste conexão: `mysql -u root -p`

### Página em branco
- Verifique logs em `storage/logs/`
- Ative debug: `APP_DEBUG=true` no `.env`
- Verifique logs do Apache/Nginx

### .htaccess não funciona
- Verifique se mod_rewrite está habilitado: `a2enmod rewrite`
- Confirme AllowOverride All no VirtualHost
- Reinicie o Apache: `service apache2 restart`

## Suporte

Para problemas, consulte:
- Logs do sistema: `storage/logs/`
- Logs do servidor: Apache/Nginx error logs
- PHP error log
