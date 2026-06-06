<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div class="flex gap-3">
        <input v-model="search" @input="loadEquipment" placeholder="Pesquisar equipamentos..."
          :class="['px-4 py-2 rounded-lg border text-sm outline-none',
            theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" />
        <select v-model="statusFilter" @change="loadEquipment"
          :class="['px-3 py-2 rounded-lg border text-sm outline-none',
            theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']">
          <option value="">Todos</option>
          <option value="available">Disponível</option>
          <option value="assigned">Atribuído</option>
          <option value="maintenance">Manutenção</option>
          <option value="lost">Perdido</option>
        </select>
      </div>
      <button @click="showModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        + Novo Equipamento
      </button>
    </div>

    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Serial</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Marca</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Modelo</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Status</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="item in equipment" :key="item.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ item.serial_number }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ item.brand }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ item.model }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                item.status === 'available' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
                item.status === 'assigned' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' :
                item.status === 'maintenance' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300' :
                'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300']">
                {{ item.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button v-if="item.status === 'available' || item.status === 'maintenance'"
                  @click="changeStatus(item.id, item.status === 'available' ? 'maintenance' : 'available')"
                  class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                  {{ item.status === 'available' ? 'Manutenção' : 'Disponível' }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal v-model="showModal" title="Novo Equipamento" max-width="2xl">
      <form @submit.prevent="handleCreate" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Marca</label>
            <input v-model="form.brand" required
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Modelo</label>
            <input v-model="form.model" required
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nº Série</label>
            <input v-model="form.serial_number" required
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Tipo</label>
            <select v-model="form.equipment_type_id" required
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
              <option value="">Selecionar...</option>
              <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Fornecedor</label>
            <input v-model="form.supplier"
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Compra</label>
            <input v-model="form.purchase_date" type="date"
              class="w-full px-4 py-2 rounded-lg border outline-none"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Notas</label>
          <textarea v-model="form.notes" rows="3"
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showModal = false"
            class="px-4 py-2 border rounded-lg text-sm font-medium"
            :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
            Cancelar
          </button>
          <button type="submit"
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
            Criar Equipamento
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'

const theme = useThemeStore()
const equipment = ref([])
const search = ref('')
const statusFilter = ref('')
const showModal = ref(false)
const types = ref([])
const form = ref({ brand: '', model: '', serial_number: '', equipment_type_id: '', supplier: '', purchase_date: '', notes: '' })

async function loadEquipment() {
  try {
    const res = await api.get('/v1/equipment', { params: { search: search.value, status: statusFilter.value } })
    equipment.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
}

async function loadTypes() {
  types.value = [
    { id: 1, name: 'Computador' }, { id: 2, name: 'Portátil' }, { id: 3, name: 'Telemóvel' },
    { id: 4, name: 'Impressora' }, { id: 5, name: 'Scanner' }, { id: 6, name: 'Tablet' },
    { id: 7, name: 'Monitor' }, { id: 8, name: 'Licença' },
  ]
}

async function handleCreate() {
  try {
    await api.post('/v1/equipment', form.value)
    form.value = { brand: '', model: '', serial_number: '', equipment_type_id: '', supplier: '', purchase_date: '', notes: '' }
    showModal.value = false
    await loadEquipment()
  } catch (e) { console.error(e) }
}

async function changeStatus(id, status) {
  try {
    const endpoint = status === 'maintenance' ? 'maintenance' : 'lost'
    await api.post(`/v1/equipment/${id}/${endpoint}`)
    await loadEquipment()
  } catch (e) { console.error(e) }
}

onMounted(() => {
  loadEquipment()
  loadTypes()
})
</script>
