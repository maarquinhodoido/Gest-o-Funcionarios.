<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 :class="['text-lg font-semibold', theme.isDark ? 'text-white' : 'text-gray-900']">
        Atribuições de Equipamentos
      </h2>
      <button @click="showModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
        + Nova Atribuição
      </button>
    </div>

    <Modal v-model="showModal" title="Nova Atribuição" max-width="2xl">
      <form @submit.prevent="handleAssign" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <select v-model="assignForm.equipment_id" required
            :class="['px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']">
            <option value="">Selecionar Equipamento</option>
            <option v-for="eq in availableEquipment" :key="eq.id" :value="eq.id">{{ eq.serial_number }} - {{ eq.brand }} {{ eq.model }}</option>
          </select>
          <select v-model="assignForm.user_id" required
            :class="['px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']">
            <option value="">Selecionar Utilizador</option>
            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
          </select>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showModal = false"
            class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors"
            :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
            Cancelar
          </button>
          <button type="submit"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
            Atribuir
          </button>
        </div>
      </form>
    </Modal>

    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Equipamento</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Utilizador</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Data</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Status</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="a in assignments" :key="a.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ a.equipment_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ a.user_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ a.assigned_at }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                a.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300']">
                {{ a.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <button v-if="a.status === 'active'" @click="returnAssignment(a.id)"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                Devolver
              </button>
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
import api from '../lib/axios'
import Modal from '../components/Modal.vue'

const theme = useThemeStore()
const assignments = ref([])
const availableEquipment = ref([])
const users = ref([])
const assignForm = ref({ equipment_id: '', user_id: '' })
const showModal = ref(false)

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

async function handleAssign() {
  try {
    await api.post('/v1/assignments', assignForm.value)
    assignForm.value = { equipment_id: '', user_id: '' }
    showModal.value = false
    await loadData()
  } catch (e) { console.error(e) }
}

async function returnAssignment(id) {
  try {
    await api.post(`/v1/assignments/${id}/return`)
    await loadData()
  } catch (e) { console.error(e) }
}

onMounted(loadData)
</script>
