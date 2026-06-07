<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Empresas</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Nova Empresa</button>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Empresa' : 'Nova Empresa'" max-width="2xl">
      <form @submit.prevent="handleSave" class="space-y-4">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
            <input v-model="form.name" required class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome Legal</label>
            <input v-model="form.legal_name" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">NIF</label>
            <input v-model="form.tax_id" required class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Email</label>
            <input v-model="form.email" type="email" class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Telefone</label>
            <input v-model="form.phone" class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Plano</label>
            <select v-model="form.plan" class="input">
              <option value="free">Free</option>
              <option value="basic">Basic</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Máx. Utilizadores</label>
            <input v-model="form.max_users" type="number" min="1" class="input" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Morada</label>
          <input v-model="form.address" class="input" />
        </div>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Cidade</label>
            <input v-model="form.city" class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">País</label>
            <input v-model="form.country" class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Código Postal</label>
            <input v-model="form.postal_code" class="input" />
          </div>
        </div>
        <div v-if="editing" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <button type="button" @click="handleDelete(editingItem)" class="btn btn-sm btn-danger">
            Eliminar Empresa
          </button>
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="showModal = false" class="btn btn-ghost">Cancelar</button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'A guardar...' : (editing ? 'Atualizar' : 'Criar') }}
          </button>
        </div>
      </form>
    </Modal>

    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Referência</th>
            <th>Nome</th>
            <th>NIF</th>
            <th>Plano</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in companies" :key="c.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ c.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ c.name }}</td>
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ c.tax_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ c.plan }}</td>
            <td class="px-6 py-4">
              <span :class="['badge', c.status === 'active' ? 'badge-success' : 'badge-neutral']">{{ c.status }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetail(c)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openEditModal(c)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(c)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

    <Modal v-model="showDetailModal" title="Detalhes da Empresa" max-width="lg">
      <div v-if="detailItem" class="space-y-6">
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Referência</p><p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ detailItem.reference || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Nome</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.name }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Nome Legal</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.legal_name }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">NIF</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.tax_id }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Email</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.email || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Telefone</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.phone || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Plano</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.plan }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Máx. Utilizadores</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.max_users }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Status</p><span :class="['badge', detailItem.status === 'active' ? 'badge-success' : 'badge-neutral']">{{ detailItem.status }}</span></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Morada</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.address || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Cidade</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.city || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">País</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.country || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Código Postal</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.postal_code || '-' }}</p></div>
        </div>
        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import { useToastStore } from '../stores/toast'
import { useConfirmStore } from '../stores/confirm'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'

const theme = useThemeStore()
const toast = useToastStore()
const confirm = useConfirmStore()
const companies = ref([])
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const editingItem = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const form = ref({ name: '', legal_name: '', tax_id: '', email: '', phone: '', plan: 'free', max_users: 10, address: '', city: '', country: '', postal_code: '' })
const showDetailModal = ref(false)
const detailItem = ref(null)

function openDetail(item) {
  detailItem.value = item
  showDetailModal.value = true
}

onMounted(loadCompanies)

async function loadCompanies() {
  try {
    const res = await api.get('/v1/companies')
    companies.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function openCreateModal() {
  editing.value = false
  editingItem.value = null
  errorMsg.value = ''
  form.value = { name: '', legal_name: '', tax_id: '', email: '', phone: '', plan: 'free', max_users: 10, address: '', city: '', country: '', postal_code: '' }
  showModal.value = true
}

function openEditModal(company) {
  editing.value = true
  editingId.value = company.id
  editingItem.value = company
  errorMsg.value = ''
  form.value = {
    name: company.name, legal_name: company.legal_name, tax_id: company.tax_id,
    email: company.email || '', phone: company.phone || '', plan: company.plan,
    max_users: company.max_users || 10, address: company.address || '', city: company.city || '',
    country: company.country || '', postal_code: company.postal_code || '',
  }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/companies/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/companies', form.value)
    }
    showModal.value = false
    await loadCompanies()
    toast.success(editing.value ? 'Empresa atualizada' : 'Empresa criada')
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

async function handleDelete(company) {
  const ok = await confirm.show(`Tem a certeza que deseja eliminar a empresa "${company.name}"?`)
  if (!ok) return
  try {
    await api.delete(`/v1/companies/${company.id}`)
    showModal.value = false
    await loadCompanies()
    toast.success('Empresa eliminada')
  } catch (e) { console.error(e) }
}
</script>
