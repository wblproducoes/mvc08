# Schema do Banco de Dados

# Schema do Banco de Dados

## Arquivo Único: schema.sql

Este arquivo contém o schema completo do banco de dados com todos os recursos.

**Contém:**
- ✅ 5 Tabelas principais
- ✅ 2 Views (v_users_active, v_recent_logs)
- ✅ 1 Trigger (limpeza de tokens)
- ✅ 2 Stored Procedures (manutenção)
- ✅ 2 Eventos agendados
- ✅ Chaves estrangeiras
- ✅ Índices otimizados
- ✅ Particionamento de logs
- ✅ Dados iniciais

## Instalação

### Via Instalador Web (Recomendado)
O instalador processa automaticamente o `schema.sql`:

1. Acesse `http://localhost/mvc08/public/install.php`
2. Configure o banco e prefixo
3. Clique em "Criar Tabelas"
4. Pronto!

**Nota:** O instalador web cria tabelas, views, FKs e índices. Triggers, procedures e eventos são ignorados para evitar problemas de parsing. Você pode adicioná-los manualmente depois se necessário.

### Via Linha de Comando (Completo)
Para instalar TUDO incluindo triggers, procedures e eventos:

```bash
mysql -u root -p database_name < database/schema.sql
```

**Importante:** Substitua `sa_` pelo seu prefixo no arquivo antes de importar.

## Prefixo de Tabelas

O sistema utiliza prefixos nas tabelas para permitir múltiplas instalações no mesmo banco de dados.

### Prefixo Padrão

O prefixo padrão no arquivo `schema.sql` é `sa_` (Sistema Administrativo).

### Como Funciona

Durante a instalação, o sistema:

1. Lê o arquivo `schema.sql`
2. Substitui **automaticamente** todas as ocorrências de `sa_` pelo prefixo configurado
3. Cria todas as tabelas, views, triggers, procedures e eventos com o novo prefixo

### Exemplos de Substituição

**Prefixo configurado:** `app_`

| Original | Substituído |
|----------|-------------|
| `sa_users` | `app_users` |
| `sa_status` | `app_status` |
| `sa_v_users_active` | `app_v_users_active` |
| `sp_cleanup_old_logs` | Mantém o nome (procedures não têm prefixo) |

### Objetos Criados

#### Tabelas (5)
- `{prefix}users` - Usuários do sistema
- `{prefix}user_access_logs` - Logs de acesso
- `{prefix}status` - Status dos registros
- `{prefix}levels` - Níveis de acesso
- `{prefix}genders` - Gêneros

#### Views (2)
- `{prefix}v_users_active` - Usuários ativos
- `{prefix}v_recent_logs` - Logs recentes (últimos 7 dias)

#### Triggers (1)
- `tr_users_before_update` - Limpa tokens expirados

#### Stored Procedures (2)
- `sp_cleanup_old_logs()` - Limpa logs antigos (90+ dias)
- `sp_user_statistics()` - Estatísticas de usuários

#### Eventos Agendados (2)
- `ev_cleanup_expired_tokens` - Diário
- `ev_cleanup_old_logs` - Semanal

### Chaves Estrangeiras

O sistema cria automaticamente as seguintes FKs:

```
users.level_id → levels.id
users.status_id → status.id
users.gender_id → genders.id
users.register_id → users.id (auto-referência)
user_access_logs.user_id → users.id
```

### Índices Criados

#### Índices Únicos
- `username`, `email`, `cpf`, `unique_code`

#### Índices Compostos
- `level_id + status_id`
- `user_id + dh_access`

#### Índice FULLTEXT
- `name + email + username` (para busca)

### Particionamento

A tabela `user_access_logs` é particionada por mês para melhor performance.

### Recomendações de Prefixo

✅ **Bons prefixos:**
- `sa_` (padrão)
- `app_`
- `sys_`
- `adm_`

❌ **Evite:**
- Nome do banco como prefixo
- Prefixos muito longos
- Caracteres especiais

### Múltiplas Instalações

Você pode ter múltiplas instalações no mesmo banco usando prefixos diferentes:

```
Instalação 1: sa_users, sa_status, ...
Instalação 2: app_users, app_status, ...
Instalação 3: sys_users, sys_status, ...
```

### Manutenção

#### Limpar Logs Antigos
```sql
CALL sp_cleanup_old_logs();
```

#### Ver Estatísticas
```sql
CALL sp_user_statistics();
```

#### Listar Usuários Ativos
```sql
SELECT * FROM sa_v_users_active;
```

#### Ver Logs Recentes
```sql
SELECT * FROM sa_v_recent_logs;
```

### Backup

Para fazer backup de uma instalação específica:

```bash
mysqldump -u root -p database_name sa_users sa_status sa_levels sa_genders sa_user_access_logs > backup.sql
```

Substitua `sa_` pelo seu prefixo.

### Troubleshooting

#### Erro: "Table already exists"
As tabelas já existem. Delete-as ou use outro prefixo.

#### Erro: "Cannot add foreign key constraint"
Verifique se as tabelas de referência foram criadas primeiro.

#### Eventos não funcionam
Verifique se o event scheduler está ativo:
```sql
SET GLOBAL event_scheduler = ON;
```

### Estrutura Completa

```
database/
├── schema.sql          # Schema completo com prefixo sa_
└── README.md          # Esta documentação
```

### Versão

- **Schema Version:** 1.1.0
- **Última Atualização:** 14/01/2026
- **Compatibilidade:** MySQL 5.7+, MariaDB 10.3+
