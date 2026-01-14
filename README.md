# Sistema Administrativo MVC

Sistema administrativo completo desenvolvido em PHP com arquitetura MVC Profissional, utilizando as melhores práticas de desenvolvimento e segurança.

## Estrutura de Pastas

```
├── app/
│   ├── Controllers/      # Controladores
│   ├── Models/          # Modelos
│   ├── Views/           # Views (Twig)
│   ├── Services/        # Serviços
│   ├── Middlewares/     # Middlewares
│   ├── Helpers/         # Helpers
│   ├── Config/          # Configurações
│   └── Core/            # Classes core
├── public/              # Pasta pública
│   ├── index.php        # Ponto de entrada
│   ├── assets/          # CSS, JS, imagens
│   └── uploads/         # Uploads
├── routes/              # Rotas
├── storage/             # Cache, logs, sessões
├── database/            # Schema SQL
└── vendor/              # Dependências
```

## Tecnologias

- **PHP 8.4+** - Orientação a Objetos
- **Twig 3.0** - Template Engine
- **Bootstrap 5.3** - Framework CSS
- **PHPMailer** - Envio de emails
- **DomPDF** - Geração de PDFs
- **MySQL/MariaDB** - Banco de dados
- **Composer** - Gerenciador de dependências
- **PHPDotEnv** - Variáveis de ambiente

## Instalação

### Método 1: Instalador Web (Recomendado) 🎉

O sistema possui um instalador web inteligente que facilita todo o processo!

#### 1. Instale as dependências

```bash
composer install
```

#### 2. Crie o banco de dados

```sql
CREATE DATABASE sistema_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 3. Acesse o instalador

```
http://localhost/mvc08/public/install.php
```

#### 4. Siga os passos na interface

- **Passo 1:** Configure banco de dados (com teste de conexão)
- **Passo 2:** Crie tabelas automaticamente
- **Passo 3:** Crie primeiro usuário
- **Passo 4:** Pronto! Sistema instalado

**Recursos do Instalador:**
- ✅ Teste de conexão antes de instalar
- ✅ Criação automática de tabelas
- ✅ Geração automática do .env
- ✅ Interface amigável e intuitiva
- ✅ Detecção inteligente de instalação
- ✅ Proteção por senha para reinstalação

Veja mais detalhes em [INSTALL_WEB.md](INSTALL_WEB.md)

---

### Método 2: Instalação Manual

#### 1. Clone o repositório

```bash
git clone <seu-repositorio>
cd sistema-administrativo
```

#### 2. Instale as dependências

```bash
composer install
```

#### 3. Configure o ambiente

```bash
copy .env.example .env
```

Edite o arquivo `.env` com suas configurações:
- Banco de dados
- Email
- Outras configurações

#### 4. Crie o banco de dados

Execute o script SQL em `database/schema.sql` no seu banco de dados.

#### 5. Configure o servidor web

#### Apache

Certifique-se de que o `mod_rewrite` está habilitado e aponte o DocumentRoot para a pasta `public/`.

## Credenciais Padrão

- **Usuário:** admin
- **Senha:** admin123

**IMPORTANTE:** Altere a senha após o primeiro login!

## Características

- ✅ Arquitetura MVC profissional
- ✅ Autenticação com sessões
- ✅ Proteção CSRF
- ✅ Senhas criptografadas (bcrypt)
- ✅ Prepared Statements (PDO)
- ✅ Middleware de autenticação
- ✅ Sistema de recuperação de senha
- ✅ Logs de acesso
- ✅ Design responsivo (Bootstrap 5.3)
- ✅ Template Engine (Twig)
- ✅ Validação de dados
- ✅ Soft delete
- ✅ Variáveis de ambiente

## Estrutura do Banco de Dados

### Tabelas Principais

- `sa_users` - Usuários do sistema
- `sa_user_access_logs` - Logs de acesso
- `sa_status` - Status dos registros
- `sa_levels` - Níveis de acesso
- `sa_genders` - Gêneros

## Rotas

### Públicas
- `GET /` - Página de login
- `GET /login` - Página de login
- `POST /login` - Processar login
- `GET /forgot-password` - Esqueceu a senha
- `POST /forgot-password` - Processar recuperação

### Autenticadas
- `GET /dashboard` - Dashboard
- `GET /logout` - Logout

## Segurança

- Proteção CSRF em todos os formulários
- Senhas com hash bcrypt
- Prepared Statements para prevenir SQL Injection
- Sanitização de inputs
- Validação de dados
- Sessões seguras
- Logs de acesso

## Desenvolvimento

### Adicionar nova rota

Edite `routes/web.php`:

```php
$router->get('/nova-rota', 'SeuController', 'metodo')
       ->middleware('AuthMiddleware');
```

### Criar novo controller

```php
<?php
namespace App\Controllers;

use App\Core\Controller;

class SeuController extends Controller
{
    public function metodo(): void
    {
        $this->view('default/pages/sua-view.twig', [
            'dados' => 'valor'
        ]);
    }
}
```

### Criar novo model

```php
<?php
namespace App\Models;

use App\Core\Model;

class SeuModel extends Model
{
    protected string $table = 'sua_tabela';
}
```

## Licença

Este projeto é de código aberto.

## Suporte

Para suporte, entre em contato através do email: suporte@example.com
