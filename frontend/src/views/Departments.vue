<template>
  <div class="space-y-6">
    <div class="flex justify-end">
      <button @click="showModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
        + Novo Departamento
      </button>
    </div>

    <Modal v-model="showModal" title="Novo Departamento" max-width="lg">
      <form @submit.prevent="handleCreate" class="flex gap-4">
        <input v-model="deptName" placeholder="Nome do departamento" autofocus
          :class="['flex-1 px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']" />
        <button type="submit"
          class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
          Criar
        </button>
      </form>
    </Modal>

    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Nome</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="d in departments" :key="d.id">
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ d.name }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                d.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-700']">
                {{ d.is_active ? 'Ativo' : 'Inativo' }}
              </span>
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
const departments = ref([])
const deptName = ref('')
const showModal = ref(false)

onMounted(async () => {
  try {
    const res = await api.get('/v1/departments')
    departments.value = res.data.data || []
  } catch (e) { console.error(e) }
})

async function handleCreate() {
  try {
    await api.post('/v1/departments', { name: deptName.value })
    deptName.value = ''
    showModal.value = false
    const res = await api.get('/v1/departments')
    departments.value = res.data.data || []
  } catch (e) { console.error(e) }
}
</script>
