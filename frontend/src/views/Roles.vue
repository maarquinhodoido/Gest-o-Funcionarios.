<template>
  <div class="space-y-6">
    <div class="flex justify-end">
      <button @click="showModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
        + Nova Role
      </button>
    </div>

    <Modal v-model="showModal" title="Nova Role" max-width="lg">
      <form @submit.prevent="handleCreateRole" class="flex gap-4">
        <input v-model="roleName" placeholder="Nome da role" autofocus
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
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Role</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Guard</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="role in roles" :key="role.id">
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ role.name }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ role.guard_name }}</td>
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
const roles = ref([])
const roleName = ref('')
const showModal = ref(false)

onMounted(async () => {
  try {
    const res = await api.get('/v1/roles')
    roles.value = res.data.data || []
  } catch (e) { console.error(e) }
})

async function handleCreateRole() {
  try {
    await api.post('/v1/roles', { name: roleName.value })
    roleName.value = ''
    showModal.value = false
    const res = await api.get('/v1/roles')
    roles.value = res.data.data || []
  } catch (e) { console.error(e) }
}
</script>
