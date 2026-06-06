<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <div class="flex gap-3">
        <input v-model="search" @input="loadUsers" placeholder="Pesquisar utilizadores..."
          :class="['px-4 py-2 rounded-lg border text-sm outline-none transition-colors',
            theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']" />
        <select v-model="statusFilter" @change="loadUsers"
          :class="['px-3 py-2 rounded-lg border text-sm outline-none',
            theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900']">
          <option value="">Todos</option>
          <option value="active">Ativo</option>
          <option value="inactive">Inativo</option>
          <option value="blocked">Bloqueado</option>
        </select>
      </div>
      <button @click="showModal = true"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
        + Novo Utilizador
      </button>
    </div>

    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Nome</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Email</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Status</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase tracking-wider" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ user.name }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ user.email }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                user.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
                user.status === 'blocked' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' :
                'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300']">
                {{ user.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <router-link :to="`/users/${user.id}/edit`"
                  class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-sm font-medium">
                  Editar
                </router-link>
                <button @click="toggleBlock(user)" class="text-sm font-medium"
                  :class="user.status === 'blocked' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'">
                  {{ user.status === 'blocked' ? 'Desbloquear' : 'Bloquear' }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal v-model="showModal" title="Novo Utilizador" max-width="2xl">
      <form @submit.prevent="handleCreate" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
            <input v-model="form.name" required
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Password</label>
          <input v-model="form.password" type="password" required
            class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">NIF</label>
            <input v-model="form.nif" maxlength="20"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Nascimento</label>
            <input v-model="form.birth_date" type="date"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Telefone</label>
            <input v-model="form.phone"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">NISS</label>
            <input v-model="form.niss" maxlength="20"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Cargo</label>
            <input v-model="form.position" maxlength="100"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Entrada</label>
            <input v-model="form.hire_date" type="date"
              class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
              :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" @click="showModal = false"
            class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors"
            :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
            Cancelar
          </button>
          <button type="submit"
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
            Criar Utilizador
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
const users = ref([])
const search = ref('')
const statusFilter = ref('')
const showModal = ref(false)
const form = ref({ name: '', email: '', password: '', nif: '', birth_date: '', phone: '', niss: '', position: '', hire_date: '' })

async function loadUsers() {
  try {
    const res = await api.get('/v1/users', { params: { search: search.value, status: statusFilter.value } })
    users.value = res.data.items || res.data.data || []
  } catch (e) {
    console.error(e)
  }
}

async function handleCreate() {
  try {
    await api.post('/v1/users', form.value)
    form.value = { name: '', email: '', password: '', nif: '', birth_date: '', phone: '', niss: '', position: '', hire_date: '' }
    showModal.value = false
    await loadUsers()
  } catch (e) {
    console.error(e)
  }
}

async function toggleBlock(user) {
  try {
    if (user.status === 'blocked') {
      await api.post(`/v1/users/${user.id}/unblock`)
    } else {
      await api.post(`/v1/users/${user.id}/block`)
    }
    await loadUsers()
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadUsers)
</script>
