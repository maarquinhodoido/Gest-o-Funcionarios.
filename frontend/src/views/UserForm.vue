<template>
  <div :class="['max-w-2xl mx-auto rounded-xl border p-8', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
    <h2 :class="['text-xl font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
      {{ isEdit ? 'Editar Utilizador' : 'Novo Utilizador' }}
    </h2>

    <form @submit.prevent="handleSubmit" class="space-y-6">
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

      <div v-if="!isEdit">
        <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Password</label>
        <input v-model="form.password" type="password" required
          class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
          :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Perfil de Funcionário</label>
          <select v-model="form.employee_profile_id"
            class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
            <option value="">Sem perfil</option>
            <option v-for="p in profiles" :key="p.id" :value="p.id">{{ p.name }} {{ p.nif ? `(${p.nif})` : '' }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Entrada</label>
          <input v-model="form.hire_date" type="date"
            class="w-full px-4 py-2 rounded-lg border outline-none transition-colors"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
      </div>

      <div class="flex gap-4">
        <button type="submit"
          class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
          {{ isEdit ? 'Atualizar' : 'Criar Utilizador' }}
        </button>
        <router-link to="/users"
          class="px-6 py-2.5 border rounded-lg text-sm font-medium transition-colors"
          :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
          Cancelar
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'

const route = useRoute()
const router = useRouter()
const theme = useThemeStore()

const isEdit = computed(() => !!route.params.id)
const profiles = ref([])
const form = ref({ name: '', email: '', password: '', employee_profile_id: '', hire_date: '' })

async function loadProfiles() {
  try {
    const res = await api.get('/v1/employee-profiles')
    profiles.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
}

onMounted(async () => {
  await loadProfiles()
  if (isEdit.value) {
    try {
      const res = await api.get(`/v1/users/${route.params.id}`)
      const user = res.data.data
      form.value = {
        name: user.name, email: user.email,
        employee_profile_id: user.employee_profile_id || '',
        hire_date: user.hire_date || '',
      }
    } catch (e) { console.error(e) }
  }
})

async function handleSubmit() {
  try {
    if (isEdit.value) {
      await api.put(`/v1/users/${route.params.id}`, form.value)
    } else {
      await api.post('/v1/users', form.value)
    }
    router.push('/users')
  } catch (e) {
    console.error(e)
  }
}
</script>
