# API — Gestão Interna

**Base URL:** `http://localhost:8000/api/v1`  
**Auth:** JWT Bearer Token (exceto login/refresh)  
**Content-Type:** `application/json`

---

## Autenticação

### `POST /auth/login`
Login do utilizador.

**Request:**
```json
{ "email": "admin@empresademo.com", "password": "123" }
```
**Response (200):**
```json
{
  "token": "eyJ...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { "id": 1, "name": "Administrador", "email": "admin@empresademo.com", "roles": ["super-admin"] },
  "requires_2fa": false
}
```
**Response (401):** `{ "error": "Invalid credentials or account blocked" }`

### `POST /auth/refresh`
Renova o token JWT atual.  
**Response (200):** `{ "token": "eyJ...", "token_type": "bearer", "expires_in": 3600 }`

### `POST /auth/logout`
Termina a sessão. Requer auth.  
**Response (200):** `{ "message": "Successfully logged out" }`

### `GET /auth/me`
Devolve o utilizador autenticado.  
**Response (200):** `{ "data": { "id": 1, "name": "...", "email": "...", "roles": [...] } }`

---

## Dashboard

### `GET /dashboard/stats`
Estatísticas principais do dashboard.  
**Response (200):**
```json
{
  "data": {
    "total_users": 4, "active_users": 4, "blocked_users": 0,
    "total_departments": 5,
    "total_equipment": 11, "equipment_available": 5, "equipment_assigned": 5,
    "equipment_maintenance": 1, "equipment_lost": 0,
    "usage_percentage": 45, "available_percentage": 45,
    "failed_logins": 6, "critical_alerts": 0
  }
}
```

### `GET /dashboard/recent-activities?limit=10`
Últimas atividades (audit logs).  
**Response (200):** `{ "data": [ { "id": 1, "action": "login", "description": "...", "severity": "info", "user_name": "Administrador", "created_at": "..." } ] }`

### `GET /dashboard/recent-logins?limit=10`
Últimos logins realizados.  
**Response (200):** `{ "data": [...] }`

---

## Utilizadores

### `GET /users?search=&status=&department_id=&per_page=15`
Lista utilizadores (paginado). Inclui roles.  
**Response (200):** `{ "items": [...], "total": 4, "page": 1, "per_page": 15, "last_page": 1 }`

### `POST /users`
Criar utilizador.  
**Request:**
```json
{
  "name": "Novo User", "email": "novo@email.com", "password": "123456",
  "employee_profile_id": null, "department_id": null, "hire_date": "2026-01-01",
  "roles": ["user"]
}
```
**Response (201):** `{ "data": { "id": 5, "name": "...", "roles": ["user"] } }`

### `GET /users/{id}`
Detalhes de um utilizador.  
**Response (200):** `{ "data": { "id": 1, "name": "...", "roles": ["super-admin"] } }`

### `PUT /users/{id}`
Atualizar utilizador. Campos opcionais.  
**Request:** `{ "name": "Novo Nome", "department_id": 2, "roles": ["admin", "user"] }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /users/{id}`
Eliminar utilizador (soft delete).  
**Response (200):** `{ "message": "User deleted" }`

### `GET /users/search?q=termo`
Pesquisar utilizadores.  
**Response (200):** `{ "data": [...] }`

### `POST /users/{id}/block`
Bloquear utilizador.  
**Response (200):** `{ "data": { "status": "blocked" } }`

### `POST /users/{id}/unblock`
Desbloquear utilizador.  
**Response (200):** `{ "data": { "status": "active" } }`

### `POST /users/{id}/reset-password`
**Request:** `{ "password": "nova123" }`  
**Response (200):** `{ "message": "Password reset successfully" }`

---

## Departamentos

### `GET /departments`
Lista departamentos.  
**Response (200):** `{ "data": [ { "id": 1, "name": "Tecnologia", "is_active": true } ] }`

### `POST /departments`
**Request:** `{ "name": "Novo Dept", "description": "..." }`  
**Response (201):** `{ "data": { "id": 4, "name": "Novo Dept" } }`

### `GET /departments/{id}`
**Response (200):** `{ "data": { ... } }`

### `PUT /departments/{id}`
**Request:** `{ "name": "Renomeado" }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /departments/{id}`
**Response (200):** `{ "message": "Department deleted" }`

---

## Equipamentos

### `GET /equipment?search=&status=&equipment_type_id=&per_page=15`
Lista equipamentos (paginado).  
**Response (200):** `{ "items": [...], "total": 11, "page": 1, ... }`

### `POST /equipment`
Criar equipamento.  
**Request:**
```json
{
  "equipment_type_id": 1, "serial_number": "SN-001", "brand": "Dell",
  "model": "Latitude", "supplier": "Dell PT", "purchase_date": "2026-01-15",
  "notes": "..."
}
```
**Response (201):** `{ "data": { "id": 12, "serial_number": "SN-001", ... } }`

### `GET /equipment/{id}`
**Response (200):** `{ "data": { ... } }`

### `PUT /equipment/{id}`
Apenas `location`, `notes`, `supplier`, `purchase_date`, `purchase_price`, `warranty_end` são editáveis.  
**Request:** `{ "notes": "Atualizado", "supplier": "Novo Fornecedor" }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /equipment/{id}`
**Response (200):** `{ "message": "Equipment deleted" }`

### `GET /equipment/stats`
**Response (200):** `{ "data": { "total": 11, "available": 5, "assigned": 5, "maintenance": 1, "lost": 0 } }`

### `GET /equipment/available`
Equipamentos disponíveis para atribuição.  
**Response (200):** `{ "data": [...] }`

### `POST /equipment/{id}/maintenance`
Marcar como manutenção.  
**Response (200):** `{ "data": { "status": "maintenance" } }`

### `POST /equipment/{id}/available`
Marcar como disponível.  
**Response (200):** `{ "data": { "status": "available" } }`

### `POST /equipment/{id}/lost`
Marcar como perdido.  
**Response (200):** `{ "data": { "status": "lost" } }`

---

## Tipos de Equipamento

### `GET /equipment-types`
Lista tipos de equipamento (company-scoped).  
**Response (200):** `{ "data": [ { "id": 1, "name": "Computador" }, { "id": 2, "name": "Portátil" } ] }`

---

## Atribuições

### `GET /assignments?status=&user_id=&per_page=15`
Lista atribuições (paginado).  
**Response (200):** `{ "items": [...], "total": 5, ... }`

### `POST /assignments`
Criar atribuição.  
**Request:** `{ "equipment_id": 2, "user_id": 1 }`  
**Response (201):** `{ "data": { "id": 6, "status": "active" } }`

### `GET /assignments/{id}`
**Response (200):** `{ "data": { ... } }`

### `DELETE /assignments/{id}`
**Response (200):** `{ "message": "Assignment deleted" }`

### `POST /assignments/{id}/return`
Devolver equipamento.  
**Response (200):** `{ "data": { "status": "returned" } }`

### `POST /assignments/{id}/lost`
Marcar atribuição como perdida.  
**Response (200):** `{ "data": { ... } }`

### `GET /assignments/user/{userId}`
Atribuições ativas de um utilizador.  
**Response (200):** `{ "data": [...] }`

### `GET /assignments/equipment/{equipmentId}/history`
Histórico de atribuições de um equipamento.  
**Response (200):** `{ "data": [...] }`

---

## Perfis de Funcionários

### `GET /employee-profiles?search=&status=&per_page=15`
Lista perfis (paginado).  
**Response (200):** `{ "items": [...], "total": 5, ... }`

### `POST /employee-profiles`
Criar perfil.  
**Request:**
```json
{
  "name": "João Silva", "nif": "123456789", "birth_date": "1990-05-15",
  "phone": "+351 911111111", "position": "Desenvolvedor",
  "document_type": "BI", "document_number": "12345678",
  "document_issue_date": "2020-01-01", "document_expiry_date": "2030-01-01"
}
```
**Response (201):** `{ "data": { ... } }`

### `GET /employee-profiles/{id}`
**Response (200):** `{ "data": { ... } }`

### `PUT /employee-profiles/{id}`
**Request:** `{ "name": "Nome Atualizado", "position": "Senior" }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /employee-profiles/{id}`
**Response (200):** `{ "message": "Profile deleted" }`

### `GET /employee-profiles/search?q=termo`
**Response (200):** `{ "data": [...] }`

---

## Empresas

### `GET /companies`
Lista empresas.  
**Response (200):** `{ "data": [ { "id": 1, "name": "Empresa Demo", "tax_id": "00.000.000/0001-00" } ] }`

### `POST /companies`
Criar empresa.  
**Request:**
```json
{
  "name": "Empresa XYZ", "legal_name": "XYZ Ltda", "tax_id": "12.345.678/0001-90",
  "email": "contato@xyz.com", "phone": "+351 900000000", "plan": "enterprise",
  "max_users": 50, "address": "Rua Principal", "city": "Lisboa", "country": "Portugal"
}
```
**Response (201):** `{ "data": { ... } }`

### `GET /companies/{id}`
**Response (200):** `{ "data": { ... } }`

### `PUT /companies/{id}`
**Request:** `{ "name": "Novo Nome", "plan": "basic" }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /companies/{id}`
**Response (200):** `{ "message": "Company deleted" }`

---

## Roles e Permissões

### `GET /roles`
Lista roles.  
**Response (200):** `{ "data": [ { "id": 1, "name": "super-admin" }, { "id": 2, "name": "admin" } ] }`

### `POST /roles`
**Request:** `{ "name": "nova-role" }`  
**Response (201):** `{ "data": { "id": 5, "name": "nova-role" } }`

### `GET /roles/{id}`
**Response (200):** `{ "data": { ... } }`

### `PUT /roles/{id}`
**Request:** `{ "name": "renomeada" }`  
**Response (200):** `{ "data": { ... } }`

### `DELETE /roles/{id}`
**Response (200):** `{ "message": "Role deleted" }`

### `GET /roles/permissions`
Lista todas as permissões.  
**Response (200):** `{ "data": [...] }`

### `POST /roles/permissions`
Criar permissão.  
**Request:** `{ "name": "users.export", "module": "users", "action": "export" }`  
**Response (201):** `{ "data": { ... } }`

### `POST /roles/{roleId}/permissions/assign`
Atribuir permissão a role.  
**Request:** `{ "permission_id": 1 }`  
**Response (200):** `{ "message": "Permission assigned to role" }`

### `POST /roles/{roleId}/permissions/remove`
Remover permissão de role.  
**Request:** `{ "permission_id": 1 }`

### `GET /roles/{roleId}/permissions`
Permissões de uma role.  
**Response (200):** `{ "data": [...] }`

---

## Onboarding / Offboarding

### `POST /onboarding/onboard`
Criar utilizador + atribuir equipamentos + roles.  
**Request:**
```json
{
  "name": "Novo", "email": "novo@empresa.com", "password": "12345678",
  "department_id": 1, "hire_date": "2026-06-01",
  "equipment_ids": [2, 5], "roles": ["user"]
}
```
**Response (201):** `{ "data": { ... } }`

### `POST /onboarding/{userId}/offboard`
Desligar funcionário (liberta equipamentos, desativa user).  
**Response (200):** `{ "message": "User offboarded successfully" }`

---

## Auditoria

### `GET /audit?action=&entity_type=&severity=&user_id=&from=&to=&per_page=25`
Logs de auditoria (paginado). Inclui `user_name`.  
**Response (200):** `{ "items": [...], "total": 10, ... }`

### `GET /audit/recent?limit=20`
Últimos logs.  
**Response (200):** `{ "data": [...] }`

### `GET /audit/suspicious`
Atividades críticas (severity = critical, últimos 7 dias).  
**Response (200):** `{ "data": [...] }`

### `GET /audit/failed-logins?minutes=30`
Tentativas de login falhadas.  
**Response (200):** `{ "data": [...] }`

---

## Notificações

### `GET /notifications?is_read=&type=&per_page=20`
Notificações do utilizador + notificações globais da empresa.  
**Response (200):** `{ "data": [ { "id": 1, "type": "success", "title": "Novo Utilizador", "message": "...", "is_read": false, "created_at": "..." } ] }`

### `GET /notifications/unread-count`
**Response (200):** `{ "data": { "unread_count": 3 } }`

### `POST /notifications/{id}/read`
Marcar como lida.  
**Response (200):** `{ "message": "Notification marked as read" }`

### `POST /notifications/mark-all-read`
Marcar todas como lidas.  
**Response (200):** `{ "message": "All notifications marked as read" }`
