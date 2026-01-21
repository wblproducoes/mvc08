# Guia de Manutenção do Menu

## 📋 Visão Geral

O menu do sistema está **centralizado** em um único arquivo para facilitar a manutenção e evitar duplicações.

## 📁 Arquivo Principal

**Localização**: `app/Views/default/components/sidebar.twig`

Este é o **único arquivo** onde o menu deve ser editado. Todas as páginas do sistema usam este componente.

## 🔗 Páginas que Usam o Menu

1. **Dashboard** - `app/Views/default/pages/dashboard.twig`
   - Inclui: `{% include "default/components/sidebar.twig" %}`

2. **Páginas CRUD** - `app/Views/default/layouts/crud.twig`
   - Inclui: `{% include "default/components/sidebar.twig" %}`

3. **Páginas Antigas** - `app/Views/default/pages/_crud_base.twig`
   - Estende: `{% extends "default/layouts/crud.twig" %}`
   - Herda o menu automaticamente

## ✏️ Como Adicionar um Novo Item ao Menu

### Passo 1: Editar o Arquivo Principal

Abra `app/Views/default/components/sidebar.twig` e localize a seção de submenu:

```twig
<div class="nav-submenu show">
    <a class="nav-link nav-sublink {% if current_page == 'users' or active_menu == 'users' %}active{% endif %}" href="{{ url('users') }}">
        <i class="bi bi-people"></i> Usuários
    </a>
    <!-- Adicione aqui -->
</div>
```

### Passo 2: Adicionar o Novo Item

```twig
<a class="nav-link nav-sublink {% if current_page == 'novo_item' or active_menu == 'novo_item' %}active{% endif %}" href="{{ url('novo-item') }}">
    <i class="bi bi-icon-name"></i> Novo Item
</a>
```

### Passo 3: Usar em Suas Páginas

**Para páginas CRUD (novas)**:
```twig
{% extends "default/layouts/crud.twig" %}
```
O menu será incluído automaticamente com `current_page = 'novo_item'`

**Para páginas antigas**:
```twig
{% set active_menu = 'novo_item' %}
{% extends "default/pages/_crud_base.twig" %}
```

## 🎨 Ícones Disponíveis

Use ícones do Bootstrap Icons (bi):

- `bi-people` - Usuários
- `bi-toggle-on` - Status
- `bi-shield-lock` - Níveis de Acesso
- `bi-person-badge` - Gêneros
- `bi-clock-history` - Logs de Acesso
- `bi-sliders` - Geral
- `bi-speedometer2` - Dashboard
- `bi-gear` - Configurações

[Mais ícones](https://icons.getbootstrap.com/)

## 🔍 Verificar Sincronização

Para garantir que o menu está sincronizado em todas as páginas:

1. Edite `app/Views/default/components/sidebar.twig`
2. Acesse o Dashboard - menu deve estar atualizado
3. Acesse qualquer página CRUD - menu deve estar atualizado
4. Acesse páginas antigas (Status, Níveis) - menu deve estar atualizado

## ⚠️ Erros Comuns

### ❌ Não Faça Isso

```twig
<!-- NÃO edite o menu em dashboard.twig -->
<!-- NÃO edite o menu em _crud_base.twig -->
<!-- NÃO crie cópias do menu em outras páginas -->
```

### ✅ Faça Isso

```twig
<!-- SEMPRE edite em app/Views/default/components/sidebar.twig -->
<!-- SEMPRE use {% include "default/components/sidebar.twig" %} -->
```

## 📝 Exemplo Completo

### Adicionar "Relatórios" ao Menu

**1. Editar `sidebar.twig`**:
```twig
<a class="nav-link nav-sublink {% if current_page == 'reports' or active_menu == 'reports' %}active{% endif %}" href="{{ url('reports') }}">
    <i class="bi bi-file-earmark-text"></i> Relatórios
</a>
```

**2. Criar página CRUD**:
```twig
{% extends "default/layouts/crud.twig" %}

{% block title %}Relatórios - {{ app_name }}{% endblock %}

{% block page_content %}
    <!-- Conteúdo da página -->
{% endblock %}
```

**3. Adicionar rota** em `routes/web.php`:
```php
$router->get('/reports', 'ReportController', 'index')
       ->middleware('AuthMiddleware');
```

**4. Pronto!** O menu será atualizado automaticamente em todas as páginas.

## 🚀 Benefícios

- ✅ Menu sincronizado em todas as páginas
- ✅ Manutenção simplificada
- ✅ Sem duplicação de código
- ✅ Fácil adicionar novos itens
- ✅ Consistência visual garantida
- ✅ Menos bugs relacionados a menu

## 📞 Suporte

Se encontrar problemas com o menu:

1. Verifique se está editando `sidebar.twig`
2. Verifique se a página estende `crud.twig` ou `_crud_base.twig`
3. Verifique se `current_page` ou `active_menu` está definido corretamente
4. Limpe o cache do navegador (Ctrl+Shift+Delete)
