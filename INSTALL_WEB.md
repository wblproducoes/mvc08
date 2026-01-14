# Instalação via Interface Web

## Instalador Automático

O sistema possui um instalador web que facilita a configuração inicial.

## Como Usar

### 1. Instale as dependências do Composer

```bash
composer install
```

### 2. Acesse o instalador

Abra seu navegador e acesse:
```
http://localhost/mvc08/public/install.php
```

Ou se já configurou o .htaccess na raiz:
```
http://localhost/mvc08/install.php
```

### 3. Siga os passos do instalador

#### Passo 1: Configuração do Banco de Dados
- Nome da aplicação
- URL da aplicação
- Host do banco (localhost)
- Porta (3306)
- Nome do banco (deve existir!)
- Usuário do banco
- Senha do banco
- Prefixo das tabelas (sa_)

Clique em "Testar Conexão" para validar.

#### Passo 2: Criar Tabelas
O instalador criará automaticamente todas as tabelas necessárias:
- **5 Tabelas principais** com o prefixo configurado
- **2 Views** para consultas otimizadas
- **1 Trigger** para limpeza automática
- **2 Stored Procedures** para manutenção
- **2 Eventos agendados** para tarefas automáticas
- **Chaves estrangeiras** para integridade
- **Índices otimizados** para performance

**Importante:** O prefixo configurado no Passo 1 será aplicado automaticamente em todas as tabelas e views.

Exemplo: Se você configurou `app_`, as tabelas serão:
- `app_users`
- `app_status`
- `app_levels`
- etc.

#### Passo 3: Criar Primeiro Usuário
- Nome completo
- E-mail
- Usuário (login)
- Senha (mínimo 6 caracteres)

#### Passo 4: Concluído!
O arquivo `.env` será criado automaticamente e você poderá acessar o sistema.

### 4. Segurança

Por segurança, **delete o arquivo `public/install.php`** após a instalação:

```bash
del public\install.php
```

## Reinstalação

O sistema possui proteção inteligente contra reinstalação acidental.

### Cenários:

**1. Sistema totalmente instalado (.env existe + tabelas existem):**
- Ao acessar `install.php`, será solicitada a senha do administrador master
- Digite a senha do primeiro usuário criado (nível Master)
- Após verificação, você poderá reinstalar o sistema

**2. .env existe mas tabelas não existem:**
- O instalador detecta automaticamente
- Continua do passo 2 (criação de tabelas)
- Usa as configurações do .env existente

**3. .env não existe:**
- Instalação normal desde o início

### Forçar Reinstalação Manual:

Se preferir, você pode:
1. Deletar o arquivo `.env`
2. Acessar `install.php` novamente

## Requisitos

- PHP 8.4+
- MySQL/MariaDB
- Composer instalado
- Banco de dados criado previamente

## Criar Banco de Dados

Antes de usar o instalador, crie o banco de dados:

```sql
CREATE DATABASE sistema_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Troubleshooting

### Erro: "Banco de dados não existe"
Crie o banco de dados antes de continuar com a instalação.

### Erro: "Não foi possível conectar"
Verifique:
- MySQL está rodando
- Credenciais estão corretas
- Host e porta estão corretos

### Erro: "Não foi possível criar tabelas"
Verifique se o usuário tem permissões para criar tabelas.

### Sistema já instalado
Se aparecer "Sistema já instalado", você pode:
- Deletar o `.env` para reinstalar
- Acessar com `?force=1` para forçar reinstalação

## Vantagens do Instalador Web

✅ Interface amigável
✅ Teste de conexão antes de instalar
✅ Criação automática de tabelas
✅ Criação do primeiro usuário
✅ Geração automática do .env
✅ Validação de dados
✅ Feedback visual de cada etapa
