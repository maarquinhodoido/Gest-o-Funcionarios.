<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Departamentos</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Novo Departamento</button>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Departamento' : 'Novo Departamento'" max-width="lg">
      <form @submit.prevent="handleSave" class="space-y-4">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
          <input v-model="form.name" required class="input" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Descrição</label>
          <textarea v-model="form.description" rows="3" class="input"></textarea>
        </div>
        <div v-if="editing" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <button type="button" @click="handleDelete(editingItem)" class="btn btn-sm btn-danger">Eliminar Departamento</button>
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
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in departments" :key="d.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ d.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ d.name }}</td>
            <td class="px-6 py-4">
              <span :class="['badge', d.is_active ? 'badge-success' : 'badge-neutral']">{{ d.is_active ? 'Ativo' : 'Inativo' }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetail(d)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openEditModal(d)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(d)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

    <Modal v-model="showDetailModal" title="Detalhes do Departamento" max-width="lg">
      <div v-if="detailItem" class="space-y-6">
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Referência</p><p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ detailItem.reference || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Nome</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.name }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Descrição</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.description || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Status</p><span :class="['badge', detailItem.is_active ? 'badge-success' : 'badge-neutral']">{{ detailItem.is_active ? 'Ativo' : 'Inativo' }}</span></div>
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
const departments = ref([])
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const editingItem = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const form = ref({ name: '', description: '' })
const showDetailModal = ref(false)
const detailItem = ref(null)

function openDetail(item) {
  detailItem.value = item
  showDetailModal.value = true
}

onMounted(loadDepartments)

async function loadDepartments() {
  try {
    const res = await api.get('/v1/departments')
    departments.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function openCreateModal() {
  editing.value = false
  editingItem.value = null
  errorMsg.value = ''
  form.value = { name: '', description: '' }
  showModal.value = true
}

function openEditModal(department) {
  editing.value = true
  editingId.value = department.id
  editingItem.value = department
  errorMsg.value = ''
  form.value = { name: department.name, description: department.description || '' }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/departments/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/departments', form.value)
    }
    showModal.value = false
    await loadDepartments()
    toast.success(editing.value ? 'Departamento atualizado' : 'Departamento criado')
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

async function handleDelete(department) {
  const ok = await confirm.show(`Tem a certeza que deseja eliminar o departamento "${department.name}"?`)
  if (!ok) return
  try {
    await api.delete(`/v1/departments/${department.id}`)
    showModal.value = false
    await loadDepartments()
    toast.success('Departamento eliminado')
  } catch (e) { console.error(e) }
}
</script>
