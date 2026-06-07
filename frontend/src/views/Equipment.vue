<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Equipamentos</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Novo Equipamento</button>
    </div>

    <div class="flex gap-3 items-center">
      <div class="relative flex-1 max-w-xs">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--ds-text-tertiary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input v-model="search" placeholder="Pesquisar equipamentos..." class="input" style="padding-left: 36px" />
      </div>
      <select v-model="statusFilter" @change="loadEquipment" class="input !w-auto">
        <option value="">Todos</option>
        <option value="available">Disponível</option>
        <option value="assigned">Atribuído</option>
        <option value="maintenance">Manutenção</option>
        <option value="lost">Perdido</option>
      </select>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Equipamento' : 'Novo Equipamento'" max-width="2xl">
      <form @submit.prevent="handleSave" class="space-y-4">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Marca</label>
            <input v-model="form.brand" required class="input" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Modelo</label>
            <input v-model="form.model" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nº Série</label>
            <input v-model="form.serial_number" required class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Tipo</label>
            <select v-model="form.equipment_type_id" required class="input">
              <option value="">Selecionar...</option>
              <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Fornecedor</label>
            <input v-model="form.supplier" class="input" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Compra</label>
            <input v-model="form.purchase_date" type="date" class="input" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Notas</label>
          <textarea v-model="form.notes" rows="3" class="input"></textarea>
        </div>
        <div v-if="editing" :class="['border-t pt-4', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <label class="block text-sm font-medium mb-2" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Estado</label>
          <div class="flex gap-2">
            <button v-if="editingItem?.status === 'available'" type="button" @click="changeStatus(editingItem.id, 'maintenance')"
              class="btn btn-sm btn-secondary">
              Marcar Manutenção
            </button>
            <button v-if="editingItem?.status === 'maintenance'" type="button" @click="changeStatus(editingItem.id, 'available')"
              class="btn btn-sm btn-secondary">
              Marcar Disponível
            </button>
            <button type="button" @click="handleDelete(editingItem)"
              class="btn btn-sm btn-danger">
              Eliminar
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="showModal = false" class="btn btn-ghost">Cancelar</button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'A guardar...' : (editing ? 'Atualizar' : 'Criar Equipamento') }}
          </button>
        </div>
      </form>
    </Modal>

    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Referência</th>
            <th>Serial</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in equipment" :key="item.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ item.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ item.serial_number }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ item.brand }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ item.model }}</td>
            <td class="px-6 py-4">
              <span :class="['badge',
                item.status === 'available' ? 'badge-success' :
                item.status === 'assigned' ? 'badge-info' :
                item.status === 'maintenance' ? 'badge-warning' :
                'badge-error']">
                {{ item.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetail(item)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openEditModal(item)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(item)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

    <Modal v-model="showDetailModal" :title="detailItem?.brand + ' ' + detailItem?.model || 'Detalhes'" max-width="lg">
      <div v-if="detailItem" class="space-y-6">
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Serial</p><p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ detailItem.serial_number }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Status</p><span :class="['badge', detailItem.status === 'available' ? 'badge-success' : detailItem.status === 'assigned' ? 'badge-info' : detailItem.status === 'maintenance' ? 'badge-warning' : 'badge-error']">{{ detailItem.status }}</span></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Marca</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.brand }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Modelo</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.model }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Fornecedor</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.supplier || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Data Compra</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.purchase_date || '-' }}</p></div>
          <div class="col-span-2"><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Notas</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.notes || '-' }}</p></div>
        </div>
        <div v-if="detailAssignments.length" class="p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <p class="text-xs font-medium mb-2" style="color: var(--ds-text-tertiary)">Atribuições Ativas</p>
          <div v-for="a in detailAssignments" :key="a.id" class="text-sm py-1" style="color: var(--ds-text-primary)">Utilizador #{{ a.user_id }} — {{ a.assigned_at || '-' }}</div>
        </div>
        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
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
const equipment = ref([])
const search = ref('')
const statusFilter = ref('')
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const editingItem = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const types = ref([])
const form = ref({ brand: '', model: '', serial_number: '', equipment_type_id: '', supplier: '', purchase_date: '', notes: '' })
const showDetailModal = ref(false)
const detailItem = ref(null)
const detailAssignments = ref([])

async function loadEquipment() {
  try {
    const res = await api.get('/v1/equipment', { params: { search: search.value, status: statusFilter.value } })
    equipment.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
}

async function loadTypes() {
  try {
    const res = await api.get('/v1/equipment-types')
    types.value = res.data.data || []
  } catch (e) { console.error(e) }
}

async function openCreateModal() {
  editing.value = false
  editingItem.value = null
  errorMsg.value = ''
  form.value = { brand: '', model: '', serial_number: '', equipment_type_id: '', supplier: '', purchase_date: '', notes: '' }
  if (!types.value.length) await loadTypes()
  showModal.value = true
}

function openEditModal(item) {
  editing.value = true
  editingId.value = item.id
  editingItem.value = item
  errorMsg.value = ''
  form.value = {
    brand: item.brand, model: item.model, serial_number: item.serial_number,
    equipment_type_id: item.equipment_type_id || '', supplier: item.supplier || '',
    purchase_date: item.purchase_date || '', notes: item.notes || '',
  }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/equipment/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/equipment', form.value)
    }
    showModal.value = false
    await loadEquipment()
    toast.success(editing.value ? 'Equipamento atualizado' : 'Equipamento criado')
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

async function changeStatus(id, status) {
  try {
    const endpoint = status === 'maintenance' ? 'maintenance' : 'available'
    await api.post(`/v1/equipment/${id}/${endpoint}`)
    await loadEquipment()
    toast.info('Estado do equipamento alterado')
    editingItem.value = equipment.value.find(e => e.id === id)
  } catch (e) { console.error(e) }
}

async function handleDelete(item) {
  const ok = await confirm.show(`Tem a certeza que deseja eliminar o equipamento "${item.brand} ${item.model}" (${item.serial_number})?`)
  if (!ok) return
  try {
    await api.delete(`/v1/equipment/${item.id}`)
    showModal.value = false
    await loadEquipment()
    toast.success('Equipamento eliminado')
  } catch (e) { console.error(e) }
}

async function openDetail(item) {
  detailItem.value = item
  showDetailModal.value = true
  try {
    const [assignRes] = await Promise.all([
      api.get(`/v1/assignments/equipment/${item.id}/history`),
    ])
    detailAssignments.value = assignRes.data.data || []
  } catch { detailAssignments.value = [] }
}

let searchTimer
watch(search, () => { clearTimeout(searchTimer); searchTimer = setTimeout(loadEquipment, 300) })

onMounted(() => {
  loadEquipment()
})
</script>
