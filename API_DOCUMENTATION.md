# API Documentation

## Base URL
```
http://localhost:8000
```

## Autenticação

A maioria dos endpoints requer autenticação via sessão. Após o login, a sessão é mantida automaticamente.

## Endpoints

### Autenticação

#### POST /login
Realiza login no sistema.

**Request:**
```json
{
  "username": "admin",
  "password": "admin123",
  "csrf_token": "token_gerado"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "redirect": "/dashboard"
}
```

**Response Error (401):**
```json
{
  "success": false,
  "message": "Credenciais inválidas"
}
```

---

#### POST /forgot-password
Solicita recuperação de senha.

**Request:**
```json
{
  "email": "admin@example.com",
  "csrf_token": "token_gerado"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Email de recuperação enviado"
}
```

**Response Error (404):**
```json
{
  "success": false,
  "message": "Email não encontrado"
}
```

---

#### GET /logout
Realiza logout do sistema.

**Response:**
Redireciona para `/login`

---

### Dashboard

#### GET /dashboard
Exibe o dashboard do sistema.

**Autenticação:** Requerida

**Response:**
Página HTML com dashboard

---

## Códigos de Status HTTP

- `200` - OK
- `401` - Não autorizado
- `403` - Proibido (CSRF inválido)
- `404` - Não encontrado
- `422` - Validação falhou
- `500` - Erro interno do servidor

## Proteção CSRF

Todos os formulários POST devem incluir um token CSRF válido no campo `csrf_token`.

O token é gerado automaticamente nas views e deve ser incluído em todas as requisições POST.

## Exemplos de Uso

### JavaScript (Fetch API)

```javascript
// Login
const formData = new FormData();
formData.append('username', 'admin');
formData.append('password', 'admin123');
formData.append('csrf_token', document.querySelector('[name="csrf_token"]').value);

fetch('/login', {
  method: 'POST',
  body: formData
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    window.location.href = data.redirect;
  }
});
```

### cURL

```bash
# Login
curl -X POST http://localhost:8000/login \
  -d "username=admin" \
  -d "password=admin123" \
  -d "csrf_token=TOKEN"
```

## Erros Comuns

### Token CSRF Inválido
```json
{
  "success": false,
  "message": "Token CSRF inválido"
}
```
**Solução:** Certifique-se de incluir o token CSRF válido em todas as requisições POST.

### Sessão Expirada
**Resposta:** Redirecionamento para `/login`
**Solução:** Faça login novamente.

### Validação Falhou
```json
{
  "success": false,
  "errors": {
    "username": "Campo obrigatório",
    "password": "Mínimo de 6 caracteres"
  }
}
```
**Solução:** Corrija os campos indicados nos erros.

## Futuras Implementações

### API REST (Planejado)

#### GET /api/users
Lista todos os usuários.

#### POST /api/users
Cria novo usuário.

#### GET /api/users/{id}
Busca usuário por ID.

#### PUT /api/users/{id}
Atualiza usuário.

#### DELETE /api/users/{id}
Remove usuário (soft delete).

---

**Nota:** Esta é a documentação da versão 1.0.0. Endpoints de API REST serão implementados em versões futuras.
