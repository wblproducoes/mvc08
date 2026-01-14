# Guia Rápido de Início

## Instalação Rápida

### 1. Instale as dependências

```bash
composer install
```

### 2. Configure o .env

```bash
copy .env.example .env
```

Abra o arquivo `.env` e configure:

```env
DB_HOST=localhost
DB_NAME=sistema_admin
DB_USER=root
DB_PASS=sua_senha
```

### 3. Crie o banco de dados

Abra o MySQL/phpMyAdmin e execute:

```sql
CREATE DATABASE sistema_admin;
```

Depois importe o arquivo `database/schema.sql`

### 4. Configure o Apache/Nginx

Aponte o DocumentRoot para a pasta `public/` do projeto.

### 5. Acesse o sistema

- URL: http://localhost (ou seu domínio configurado)
- Usuário: `admin`
- Senha: `admin123`

## Comandos Úteis

### Instalar dependências
```bash
composer install
```

### Atualizar dependências
```bash
composer update
```

### Limpar cache
```bash
del /Q storage\cache\*
```

### Ver logs
```bash
type storage\logs\2026-01-14.log
```

## Estrutura Básica

### Criar uma nova página

1. **Criar rota** em `routes/web.php`:
```php
$router->get('/minha-pagina', 'MeuController', 'index')
       ->middleware('AuthMiddleware');
```

2. **Criar controller** em `app/Controllers/MeuController.php`:
```php
<?php
namespace App\Controllers;
use App\Core\Controller;

class MeuController extends Controller
{
    public function index(): void
    {
        $this->view('default/pages/minha-pagina.twig');
    }
}
```

3. **Criar view** em `app/Views/default/pages/minha-pagina.twig`:
```twig
{% extends "default/layout.twig" %}
{% block content %}
    <h1>Minha Página</h1>
{% endblock %}
```

## Troubleshooting Rápido

### Erro: Class not found
```bash
composer dump-autoload
```

### Erro: Cannot connect to database
- Verifique se o MySQL está rodando
- Confira as credenciais no `.env`

### Página em branco
- Ative debug no `.env`: `APP_DEBUG=true`
- Verifique `storage/logs/`

### .htaccess não funciona
- Habilite mod_rewrite: `a2enmod rewrite`
- Verifique AllowOverride All no VirtualHost
- Reinicie o Apache

## Próximos Passos

1. ✅ Altere a senha padrão
2. ✅ Configure o email no `.env`
3. ✅ Personalize o layout
4. ✅ Adicione suas funcionalidades
5. ✅ Leia a documentação completa em `README.md`

## Suporte

- Documentação completa: `README.md`
- Guia de instalação: `INSTALL.md`
- Changelog: `CHANGELOG.md`
