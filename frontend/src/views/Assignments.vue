<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Atribuições</h1>
      <button @click="showCreateModal = true" class="btn btn-primary">+ Nova Atribuição</button>
    </div>

    <Modal v-model="showCreateModal" title="Nova Atribuição" max-width="2xl">
      <form @submit.prevent="handleAssign" class="space-y-4">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
        <div class="grid grid-cols-2 gap-4">
          <select v-model="assignForm.equipment_id" required class="input">
            <option value="">Selecionar Equipamento</option>
            <option v-for="eq in availableEquipment" :key="eq.id" :value="eq.id">{{ eq.serial_number }} - {{ eq.brand }} {{ eq.model }}</option>
          </select>
          <select v-model="assignForm.user_id" required class="input">
            <option value="">Selecionar Utilizador</option>
            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showCreateModal = false" class="btn btn-ghost">Cancelar</button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'A atribuir...' : 'Atribuir' }}
          </button>
        </div>
      </form>
    </Modal>

    <Modal v-model="showDetailModal" title="Detalhes da Atribuição" max-width="lg">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Referência</label>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-white' : 'text-gray-900']">{{ detail?.reference || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Equipamento ID</label>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-white' : 'text-gray-900']">{{ detail?.equipment_id }}</p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Utilizador ID</label>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-white' : 'text-gray-900']">{{ detail?.user_id }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Estado</label>
            <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
              detail?.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
              'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300']">
              {{ detail?.status }}
            </span>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Data de Atribuição</label>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-white' : 'text-gray-900']">{{ detail?.assigned_at }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Data de Devolução</label>
            <p :class="['text-sm font-medium', theme.isDark ? 'text-white' : 'text-gray-900']">{{ detail?.returned_at || '-' }}</p>
          </div>
        </div>
        <div v-if="detail?.status === 'active'" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <div class="flex gap-2">
            <button type="button" @click="handleReturn(detail.id)" class="btn btn-primary">
              Devolver Equipamento
            </button>
            <button type="button" @click="handleDelete(detail)" class="btn btn-sm btn-danger">Eliminar</button>
          </div>
        </div>
        <div v-else-if="detail?.status === 'returned'" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <button type="button" @click="handleDelete(detail)" class="btn btn-sm btn-danger">Eliminar Registo</button>
        </div>
        <div class="flex justify-end pt-2">
          <button type="button" @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>

    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Referência</th>
            <th>Equipamento</th>
            <th>Utilizador</th>
            <th>Data</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in assignments" :key="a.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ a.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ a.equipment_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ a.user_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ a.assigned_at }}</td>
            <td class="px-6 py-4">
              <span :class="['badge', a.status === 'active' ? 'badge-success' : 'badge-neutral']">{{ a.status }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetailModal(a)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openDetailModal(a)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(a)" class="btn btn-sm btn-danger">Eliminar</button>
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
const assignments = ref([])
const availableEquipment = ref([])
const users = ref([])
const assignForm = ref({ equipment_id: '', user_id: '' })
const showCreateModal = ref(false)
const showDetailModal = ref(false)
const detail = ref(null)
const saving = ref(false)
const errorMsg = ref('')

async function loadData() {
  try {
    const [assignRes, equipRes, usersRes] = await Promise.all([
      api.get('/v1/assignments'),
      api.get('/v1/equipment/available'),
      api.get('/v1/users'),
    ])
    assignments.value = assignRes.data.items || assignRes.data.data || []
    availableEquipment.value = equipRes.data.data || []
    users.value = usersRes.data.items || usersRes.data.data || []
  } catch (e) { console.error(e) }
}

function openDetailModal(a) {
  detail.value = a
  showDetailModal.value = true
}

async function handleAssign() {
  saving.value = true
  errorMsg.value = ''
  try {
    await api.post('/v1/assignments', assignForm.value)
    assignForm.value = { equipment_id: '', user_id: '' }
    showCreateModal.value = false
    await loadData()
    toast.success('Equipamento atribuído com sucesso')
  } catch (e) {
    errorMsg.value = e.response?.data?.message || e.response?.data?.error || 'Erro ao atribuir. Verifique os dados e tente novamente.'
    if (e.response?.data?.errors) {
      const msgs = Object.values(e.response.data.errors).flat()
      errorMsg.value = msgs.join('\n')
    }
  } finally {
    saving.value = false
  }
}

async function handleReturn(id) {
  try {
    await api.post(`/v1/assignments/${id}/return`)
    showDetailModal.value = false
    await loadData()
    toast.success('Equipamento devolvido com sucesso')
  } catch (e) { console.error(e) }
}

async function handleDelete(assignment) {
  const ok = await confirm.show('Tem a certeza que deseja eliminar esta atribuição?')
  if (!ok) return
  try {
    await api.delete(`/v1/assignments/${assignment.id}`)
    showDetailModal.value = false
    await loadData()
    toast.success('Atribuição eliminada')
  } catch (e) { console.error(e) }
}

onMounted(loadData)
</script>
