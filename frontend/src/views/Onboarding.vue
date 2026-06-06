<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div :class="['rounded-xl border p-6', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <h3 :class="['text-lg font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
        Onboarding - Novo Funcionário
      </h3>
      <form @submit.prevent="handleOnboard" class="space-y-4">
        <input v-model="form.name" placeholder="Nome" required
          :class="['w-full px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']" />
        <input v-model="form.email" type="email" placeholder="Email" required
          :class="['w-full px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']" />
        <input v-model="form.password" type="password" placeholder="Password" required
          :class="['w-full px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']" />
        <button type="submit"
          class="w-full px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
          Iniciar Onboarding
        </button>
      </form>
    </div>

    <div :class="['rounded-xl border p-6', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <h3 :class="['text-lg font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
        Offboarding - Desligar Funcionário
      </h3>
      <div class="space-y-4">
        <select v-model="offboardUserId"
          :class="['w-full px-4 py-2 rounded-lg border outline-none', theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300']">
          <option value="">Selecionar Utilizador</option>
          <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
        </select>
        <button @click="handleOffboard" :disabled="!offboardUserId"
          class="w-full px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg disabled:opacity-50">
          Desligar Funcionário
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'

const theme = useThemeStore()
const users = ref([])
const offboardUserId = ref('')
const form = ref({ name: '', email: '', password: '' })

onMounted(async () => {
  try {
    const res = await api.get('/v1/users')
    users.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
})

async function handleOnboard() {
  try {
    await api.post('/v1/onboarding/onboard', form.value)
    form.value = { name: '', email: '', password: '' }
    alert('Onboarding realizado com sucesso!')
  } catch (e) { console.error(e) }
}

async function handleOffboard() {
  if (!offboardUserId.value) return
  try {
    await api.post(`/v1/onboarding/${offboardUserId.value}/offboard`)
    offboardUserId.value = ''
    alert('Offboarding realizado com sucesso!')
  } catch (e) { console.error(e) }
}
</script>
