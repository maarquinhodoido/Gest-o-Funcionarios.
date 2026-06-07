# Relatório do Projeto — Gestão Interna SaaS

## Arquitetura

**Backend:** Laravel 11 + PHP 8.2 + SQL Server (Domain-Driven Design)  
**Frontend:** Vue 3 + Vite + Tailwind CSS v4 + Pinia  
**Auth:** JWT (tymon/jwt-auth) + Spatie Permissions

### Estrutura de Pastas (Backend)
```
app/
├── Domain/
│   ├── Entities/        → 9 entidades (User, Equipment, Company, etc.)
│   ├── ValueObjects/    → Email, Phone, Address
│   └── Repositories/    → Interfaces (contratos)
├── Application/
│   ├── Services/        → Lógica de negócio (11 services)
│   ├── DTOs/            → Data Transfer Objects (6)
│   └── UseCases/        → (reservado para futuros use cases)
├── Infrastructure/
│   ├── Models/          → Eloquent models (9)
│   ├── Repositories/    → Implementações concretas
│   ├── Middleware/       → CompanyContext, Tenant, Audit
│   └── Providers/       → RepositoryServiceProvider, DatabaseServiceProvider
└── Presentation/
    └── Controllers/API/ → 12 controllers
```

### Estrutura de Pastas (Frontend)
```
frontend/src/
├── views/          → 12 views (Dashboard, Users, Equipment, etc.)
├── components/
│   ├── layout/     → Sidebar, Header
│   └── *.vue       → Modal, StatCard, ProgressBar, DashboardChart, ConfirmDialog
├── stores/         → auth, theme, notifications, toast, confirm
├── router/         → Vue Router (10 rotas autenticadas)
└── lib/            → Axios instance
```

---

## Funcionalidades Implementadas

### Módulos CRUD Completos
| Módulo | Criar | Editar | Eliminar | Detalhes |
|--------|-------|--------|----------|----------|
| Utilizadores | ✅ | ✅ | ✅ (soft) | ✅ (departamento, equipamentos, roles) |
| Equipamentos | ✅ | ✅ | ✅ (soft) | ✅ (atribuições, histórico) |
| Empresas | ✅ | ✅ | ✅ (soft) | ✅ |
| Departamentos | ✅ | ✅ | ✅ (soft) | ✅ |
| Roles | ✅ | ✅ | ✅ | ❌ |
| Perfis Funcionários | ✅ | ✅ | ✅ (soft) | ✅ |
| Atribuições | ✅ | ❌ (só devolver) | ✅ | ✅ (existente) |
| Onboarding/Offboarding | ✅ | ❌ | ✅ (offboard) | ❌ |

### Dashboard
- **6 stat cards** com ícones e subtítulos
- **Gráfico de barras** com tooltips ao hover (seguem o rato)
- **Gráfico donut** com tooltips ao hover
- **Atividades recentes** com modal de detalhes (IP, user agent, valores antigos/novos)
- **Ações rápidas** para criar utilizador, equipamento, atribuição, perfil

### Auditoria
- **3 abas:** Todos / Suspeitas / Logins Falhados
- Modal de detalhes completo
- Nome do utilizador real (não "Sistema")
- Todas as operações CRUD registadas automaticamente

### Notificações
- **Sino** no header com badge de não lidas
- **Dropdown** com lista de notificações
- **Modal de detalhes** ao clicar numa notificação
- **Página dedicada** em `/notifications`
- Polling a cada 30s (pausa quando a aba está oculta)
- Notificações criadas automaticamente em todas as operações CRUD
- Toasts de sucesso no canto inferior direito

### Design System
- **Variáveis CSS** em `:root` para cores, spacing, radius, shadows
- **Modo escuro** com toggle manual + `prefers-color-scheme`
- **Componentes:** `.btn` (4 variantes), `.input`, `.card`, `.badge` (5 cores), `.table-wrap`, `.alert` (4 cores)
- **Tipografia:** Inter (Google Fonts)

---

## Bugs Corrigidos

| # | Problema | Solução |
|---|----------|---------|
| 1 | `CompanyController::store()` — PHP crash com `...$validated` (snake_case vs camelCase) | Substituído por named arguments explícitos |
| 2 | `UserService::update()` — DTO data nunca aplicada à entidade | Adicionados setters + aplicação dos dados |
| 3 | `EquipmentService::update()` — `$data` ignorado | Criado novo Equipment com dados atualizados |
| 4 | `DepartmentController::update()` — validated data ignorada | Aplicados dados validados ao recontruir entidade |
| 5 | `RoleController` sem `show()`/`update()` | Métodos adicionados |
| 6 | `RoleService` — `company_id`/`description` em colunas inexistentes | Removidos campos não existentes |
| 7 | `DB_ENCRYPT=n` inválido para ODBC Driver | Alterado para `DB_ENCRYPT=no` |
| 8 | SQL datetime conversion (microseconds) | Formato `Y-m-d H:i:s` sem microssegundos |
| 9 | `EquipmentType` hardcoded com IDs fixos | Criado `EquipmentTypeController` + API endpoint |
| 10 | `markAvailable` sem route/controller/service | Adicionados |
| 11 | PHP 8.2 — optional `readonly` params antes de required em DTOs/Entities | Reordenados parâmetros (required first) |
| 12 | AuditLog sem `user_name` — mostrava "Sistema" | Adicionado campo `$userName` + resolução automática via `auth()->id()` |
| 13 | Notificações não apareciam (user_id = null) | Controller agora busca notificações da empresa também |
| 14 | Donut chart todo vermelho (valores zero) | Trocado para SVG arcs + `hasData` check |
| 15 | Dropdown de roles não funcional (`@click.outside` não é nativo) | Substituído por overlay `fixed inset-0` |
| 16 | Confirm dialogs nativos (`confirm()`) | Substituído por modal customizado |
| 17 | Search sem debounce — API call por tecla | Adicionado `watch` com debounce 300ms |
| 18 | Polling notificações a cada 15s infinito | Reduzido para 30s + pausa quando aba oculta |

---

## Performance — Otimizações

| Antes | Depois |
|-------|--------|
| Users: 4 API calls no mount (sequencial) | 4 chamadas concorrentes |
| openDetail Users: 4 chamadas sequenciais | 1 chamada (dados em cache) |
| Polling notificações: 15s infinito | 30s + pausa quando oculto |
| Search: API call por tecla | Debounce 300ms |
| Equipment types: carregado no mount | Lazy load (só quando abre modal) |
| Notifications store: waterfall sequencial | `Promise.all` paralelo |

---

## Endpoints API (total: 40+)

| Grupo | Endpoints |
|-------|-----------|
| Auth | 4 (login, logout, refresh, me) |
| Dashboard | 3 (stats, activities, logins) |
| Users | 9 (CRUD + search + block + unblock + reset-password) |
| Departments | 5 (CRUD) |
| Equipment | 9 (CRUD + stats + available + status changes) |
| Equipment Types | 1 (list) |
| Assignments | 7 (CRUD + return + lost + user/equipment queries) |
| Employee Profiles | 5 (CRUD + search) |
| Companies | 5 (CRUD) |
| Roles | 8 (CRUD + permissions CRUD + assign/remove) |
| Onboarding | 2 (onboard, offboard) |
| Audit | 4 (list, recent, suspicious, failed-logins) |
| Notifications | 4 (list, unread-count, read, mark-all-read) |

---

## Dados de Teste

Login: `admin@empresademo.com` / `123`

| Tabela | Registos |
|--------|----------|
| Utilizadores | 4 (admin + 3) |
| Departamentos | 3 |
| Tipos Equipamento | 8 |
| Equipamentos | 11 (5 atribuídos, 5 disponíveis, 1 manutenção) |
| Perfis Funcionários | 5 |
| Atribuições Ativas | 5 |
| Notificações | 4 de teste |

---

## Ficheiros Modificados/Criados (total: ~60+)

### Backend (PHP)
- 4 DTOs reordenados
- 4 Entities com parâmetros reordenados
- 6 Services com notificações + audit fixes
- 3 Controllers com audit + notificações
- 2 Repositories com correções
- 1 Novo Controller (EquipmentTypeController)
- Routes atualizadas
- 1 Seeder de teste
- Config .env corrigido

### Frontend (Vue)
- 10 Views com design system + toasts + detalhes + debounce
- 4 Stores novas (notifications, toast, confirm)
- 4 Componentes novos/melhorados (DashboardChart, ConfirmDialog, Header, Modal)
- Router + Sidebar com nova rota (notificações)
- CSS completo com design system
