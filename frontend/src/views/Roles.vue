<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Roles e Permissões</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Nova Role</button>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Role' : 'Nova Role'" max-width="lg">
      <form @submit.prevent="handleSave" class="space-y-4">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
          <input v-model="form.name" required class="input" />
        </div>

        <div v-if="editing" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <button type="button" @click="handleDelete(editingItem)" class="btn btn-sm btn-danger">Eliminar Role</button>
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
            <th>Role</th>
            <th>Guard</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in roles" :key="role.id">
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ role.name }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ role.guard_name }}</td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openEditModal(role)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(role)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
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
const roles = ref([])
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const editingItem = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const form = ref({ name: '' })

onMounted(loadRoles)

async function loadRoles() {
  try {
    const res = await api.get('/v1/roles')
    roles.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function openCreateModal() {
  editing.value = false
  editingItem.value = null
  errorMsg.value = ''
  form.value = { name: '' }
  showModal.value = true
}

function openEditModal(role) {
  editing.value = true
  editingId.value = role.id
  editingItem.value = role
  errorMsg.value = ''
  form.value = { name: role.name }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/roles/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/roles', form.value)
    }
    showModal.value = false
    await loadRoles()
    toast.success(editing.value ? 'Role atualizada' : 'Role criada')
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

async function handleDelete(role) {
  const ok = await confirm.show(`Tem a certeza que deseja eliminar a role "${role.name}"?`)
  if (!ok) return
  try {
    await api.delete(`/v1/roles/${role.id}`)
    showModal.value = false
    await loadRoles()
    toast.success('Role eliminada')
  } catch (e) { console.error(e) }
}
</script>
