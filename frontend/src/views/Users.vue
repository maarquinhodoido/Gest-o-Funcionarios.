<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Utilizadores</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Novo Utilizador</button>
    </div>

    <div class="flex gap-3 items-center">
      <div class="relative flex-1 max-w-xs">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--ds-text-tertiary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input v-model="search" placeholder="Pesquisar utilizadores..." class="input" style="padding-left: 36px" />
      </div>
      <select v-model="statusFilter" @change="loadUsers" class="input !w-auto">
        <option value="">Todos</option>
        <option value="active">Ativo</option>
        <option value="inactive">Inativo</option>
        <option value="blocked">Bloqueado</option>
      </select>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Utilizador' : 'Novo Utilizador'" max-width="2xl">
      <form @submit.prevent="handleSave" class="space-y-4">
        <div v-if="errorMsg" class="alert alert-error">{{ errorMsg }}</div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
            <input v-model="form.name" required class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Email</label>
            <input v-model="form.email" type="email" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Password</label>
            <input v-model="form.password" type="password" :required="!editing" class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Perfil de Funcionário</label>
            <select v-model="form.employee_profile_id" class="input">
              <option value="">Sem perfil</option>
              <option v-for="p in profiles" :key="p.id" :value="p.id">{{ p.name }} {{ p.nif ? `(${p.nif})` : '' }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Entrada</label>
            <input v-model="form.hire_date" type="date" class="input" />
          </div>
        </div>

        <div :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <label class="block text-sm font-medium mb-2" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Roles</label>
          <div class="relative">
            <button type="button" @click="showRoleDropdown = !showRoleDropdown"
              class="input flex items-center justify-between gap-2 cursor-pointer">
              <div class="flex flex-wrap gap-1 flex-1 min-w-0">
                <span v-if="!form.roles.length" class="text-sm" style="color: var(--ds-text-tertiary)">Selecionar roles...</span>
                <span v-for="r in form.roles" :key="r" class="badge badge-info text-[11px]">{{ r }}</span>
              </div>
              <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': showRoleDropdown }" style="color: var(--ds-text-tertiary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <Transition name="slide-up">
              <div v-if="showRoleDropdown"
                class="absolute left-0 right-0 mt-1 rounded-xl border shadow-lg overflow-hidden z-50"
                :class="theme.isDark ? 'bg-gray-800 border-gray-700 shadow-black/20' : 'bg-white border-gray-100 shadow-gray-200/50'">
              <div v-for="role in rolesList" :key="role.name"
                @click="toggleRole(role.name)"
                class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors duration-150 text-sm"
                :class="[
                  form.roles.includes(role.name)
                    ? theme.isDark ? 'bg-indigo-500/10 text-indigo-400' : 'bg-indigo-50 text-indigo-700'
                    : theme.isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'
                ]">
                <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                  :class="form.roles.includes(role.name)
                    ? 'bg-indigo-600 border-indigo-600'
                    : theme.isDark ? 'border-gray-500' : 'border-gray-300'">
                  <svg v-if="form.roles.includes(role.name)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                {{ role.name }}
              </div>
            </div>
            </Transition>
            <div v-if="showRoleDropdown" class="fixed inset-0 z-40" @click="showRoleDropdown = false"></div>
          </div>
        </div>

        <div v-if="editing" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <label class="block text-sm font-medium mb-2" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Ações</label>
          <div class="flex gap-2">
            <button type="button" @click="toggleBlock(editingUser)"
              class="btn btn-sm"
              :class="editingUser?.status === 'blocked' ? 'btn-secondary' : 'btn-danger'">
              {{ editingUser?.status === 'blocked' ? 'Desbloquear' : 'Bloquear' }}
            </button>
            <button type="button" @click="handleDelete(editingUser)" class="btn btn-sm btn-danger">
              Eliminar
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="showModal = false" class="btn btn-ghost">
            Cancelar
          </button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'A guardar...' : (editing ? 'Atualizar' : 'Criar Utilizador') }}
          </button>
        </div>
      </form>
    </Modal>

    <!-- Detail Modal -->
    <Modal v-model="showDetailModal" :title="detailUser?.name || 'Detalhes do Utilizador'" max-width="2xl">
      <div v-if="detailUser" class="space-y-6">
        <!-- Header -->
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-lg font-bold text-indigo-700 dark:text-indigo-300 flex-shrink-0">
            {{ detailUser.name?.charAt(0) || '?' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-lg font-semibold" style="color: var(--ds-text-primary)">{{ detailUser.name }}</p>
            <p class="text-sm" style="color: var(--ds-text-secondary)">{{ detailUser.email }}</p>
            <div class="flex items-center gap-2 mt-1">
              <span :class="['badge', detailUser.status === 'active' ? 'badge-success' : detailUser.status === 'blocked' ? 'badge-error' : 'badge-neutral']">{{ detailUser.status }}</span>
              <span v-for="r in (detailUser.roles || [])" :key="r" class="badge badge-info">{{ r }}</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
            <p class="text-xs font-medium mb-2" style="color: var(--ds-text-tertiary)">Informação Geral</p>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between"><span style="color: var(--ds-text-tertiary)">Referência</span><span style="color: var(--ds-text-primary)">{{ detailUser.reference || '-' }}</span></div>
              <div class="flex justify-between"><span style="color: var(--ds-text-tertiary)">Telefone</span><span style="color: var(--ds-text-primary)">{{ detailUser.phone || '-' }}</span></div>
              <div class="flex justify-between"><span style="color: var(--ds-text-tertiary)">Data Entrada</span><span style="color: var(--ds-text-primary)">{{ detailUser.hire_date || '-' }}</span></div>
              <div class="flex justify-between"><span style="color: var(--ds-text-tertiary)">Departamento</span><span style="color: var(--ds-text-primary)">{{ detailDepartment || '-' }}</span></div>
              <div class="flex justify-between"><span style="color: var(--ds-text-tertiary)">Perfil</span><span style="color: var(--ds-text-primary)">{{ detailProfile || '-' }}</span></div>
            </div>
          </div>
          <div class="p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
            <p class="text-xs font-medium mb-2" style="color: var(--ds-text-tertiary)">Equipamentos Atribuídos</p>
            <div v-if="detailEquipment.length" class="space-y-2">
              <div v-for="eq in detailEquipment" :key="eq.id" class="flex items-center gap-2 text-sm">
                <div class="w-1.5 h-1.5 rounded-full" :class="eq.status === 'available' ? 'bg-green-500' : eq.status === 'assigned' ? 'bg-blue-500' : eq.status === 'maintenance' ? 'bg-yellow-500' : 'bg-red-500'"></div>
                <span style="color: var(--ds-text-primary)">{{ eq.brand }} {{ eq.model }}</span>
                <span class="text-xs" style="color: var(--ds-text-tertiary)">({{ eq.serial_number }})</span>
              </div>
            </div>
            <p v-else class="text-sm" style="color: var(--ds-text-tertiary)">Nenhum equipamento atribuído</p>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>

    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Referência</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ user.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ user.email }}</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span v-for="role in (user.roles || [])" :key="role" class="badge badge-info">{{ role }}</span>
                <span v-if="!user.roles?.length" class="text-xs" :class="theme.isDark ? 'text-gray-500' : 'text-gray-400'">—</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span :class="['badge',
                user.status === 'active' ? 'badge-success' :
                user.status === 'blocked' ? 'badge-error' :
                'badge-neutral']">
                {{ user.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetail(user)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openEditModal(user)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(user)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useThemeStore } from '../stores/theme'
import { useToastStore } from '../stores/toast'
import { useConfirmStore } from '../stores/confirm'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'

const theme = useThemeStore()
const toast = useToastStore()
const confirm = useConfirmStore()
const users = ref([])
const departments = ref([])
const profiles = ref([])
const rolesList = ref([])
const search = ref('')
const statusFilter = ref('')
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const editingUser = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const showRoleDropdown = ref(false)
const showDetailModal = ref(false)
const detailUser = ref(null)
const detailDepartment = ref('')
const detailProfile = ref('')
const detailEquipment = ref([])
const form = ref({ name: '', email: '', password: '', employee_profile_id: '', hire_date: '', roles: [] })

let searchTimer
watch(search, () => { clearTimeout(searchTimer); searchTimer = setTimeout(loadUsers, 300) })

onMounted(() => {
  loadUsers()
  loadDepartments()
  loadProfiles()
  loadRoles()
})

async function loadUsers() {
  try {
    const res = await api.get('/v1/users', { params: { search: search.value, status: statusFilter.value } })
    users.value = (res.data.items || res.data.data || []).map(u => ({
      ...u,
      roles: typeof u.roles?.[0] === 'string' ? u.roles : (u.roles || []).map(r => r.name || r)
    }))
  } catch (e) { console.error(e) }
}

async function loadDepartments() {
  try { const res = await api.get('/v1/departments'); departments.value = res.data.data || [] } catch {}
}

async function loadProfiles() {
  try { const res = await api.get('/v1/employee-profiles'); profiles.value = res.data.items || res.data.data || [] } catch {}
}

async function loadRoles() {
  try {
    const res = await api.get('/v1/roles')
    rolesList.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function toggleRole(name) {
  const idx = form.value.roles.indexOf(name)
  if (idx >= 0) form.value.roles.splice(idx, 1)
  else form.value.roles.push(name)
}

function openCreateModal() {
  editing.value = false
  editingUser.value = null
  errorMsg.value = ''
  form.value = { name: '', email: '', password: '', employee_profile_id: '', hire_date: '', roles: [] }
  showModal.value = true
}

async function openDetail(user) {
  detailUser.value = user
  showDetailModal.value = true
  detailEquipment.value = []

  const dep = departments.value.find(d => d.id === user.department_id)
  detailDepartment.value = dep?.name || '-'
  const prof = profiles.value.find(p => p.id === user.employee_profile_id)
  detailProfile.value = prof?.name || '-'

  try {
    const [assignRes] = await Promise.all([
      api.get('/v1/assignments', { params: { per_page: 50 } }),
      !departments.value.length ? api.get('/v1/departments') : Promise.resolve(),
      !profiles.value.length ? api.get('/v1/employee-profiles') : Promise.resolve(),
    ])
    const items = assignRes.data?.items || assignRes.data?.data || []
    const eqIds = items.filter(a => a.user_id === user.id && a.status === 'active').map(a => a.equipment_id)
    if (eqIds.length) {
      const eqRes = await api.get('/v1/equipment', { params: { per_page: 50 } })
      const allEq = eqRes.data.items || eqRes.data.data || []
      detailEquipment.value = allEq.filter(e => eqIds.includes(e.id))
    }
  } catch {}
}

function openEditModal(user) {
  editing.value = true
  editingId.value = user.id
  editingUser.value = user
  errorMsg.value = ''
  form.value = {
    name: user.name, email: user.email, password: '',
    employee_profile_id: user.employee_profile_id || '',
    hire_date: user.hire_date || '',
    roles: [...(user.roles || [])],
  }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/users/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/users', form.value)
    }
    showModal.value = false
    await loadUsers()
    toast.success(editing.value ? 'Utilizador atualizado com sucesso' : 'Utilizador criado com sucesso')
  } catch (e) {
    errorMsg.value = e.response?.data?.message || e.response?.data?.error || 'Erro ao guardar. Verifique os dados e tente novamente.'
    if (e.response?.data?.errors) {
      const msgs = Object.values(e.response.data.errors).flat()
      errorMsg.value = msgs.join('\n')
    }
  } finally {
    saving.value = false
  }
}

async function toggleBlock(user) {
  try {
    if (user.status === 'blocked') {
      await api.post(`/v1/users/${user.id}/unblock`)
      toast.success('Utilizador desbloqueado')
    } else {
      await api.post(`/v1/users/${user.id}/block`)
      toast.info('Utilizador bloqueado')
    }
    await loadUsers()
    editingUser.value = users.value.find(u => u.id === user.id)
  } catch (e) { console.error(e) }
}

async function handleDelete(user) {
  const ok = await confirm.show(`Tem a certeza que deseja eliminar o utilizador "${user.name}"?`)
  if (!ok) return
  try {
    await api.delete(`/v1/users/${user.id}`)
    showModal.value = false
    await loadUsers()
    toast.success('Utilizador eliminado')
  } catch (e) { console.error(e) }
}
</script>
