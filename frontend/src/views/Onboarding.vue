<template>
  <div class="space-y-6 animate-fade-in">
    <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Onboarding / Offboarding</h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="card">
      <h3 :class="['text-lg font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
        Onboarding - Novo Funcionário
      </h3>
      <form @submit.prevent="handleOnboard" class="space-y-4">
        <div v-if="onboardError" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ onboardError }}</div>
        <input v-model="form.name" placeholder="Nome" required class="input" />
        <input v-model="form.email" type="email" placeholder="Email" required class="input" />
        <input v-model="form.password" type="password" placeholder="Password" required class="input" />
        <button type="submit" :disabled="onboarding" class="btn btn-primary w-full btn-lg">
          {{ onboarding ? 'A processar...' : 'Iniciar Onboarding' }}
        </button>
      </form>
    </div>

      <div class="card">
      <h3 :class="['text-lg font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
        Offboarding - Desligar Funcionário
      </h3>
      <div class="space-y-4">
        <div v-if="offboardError" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ offboardError }}</div>
        <select v-model="offboardUserId" class="input">
          <option value="">Selecionar Utilizador</option>
          <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
        </select>
        <button @click="handleOffboard" :disabled="!offboardUserId || offboarding"
          class="btn btn-danger w-full btn-lg">
          {{ offboarding ? 'A processar...' : 'Desligar Funcionário' }}
        </button>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'
import { useToastStore } from '../stores/toast'

const theme = useThemeStore()
const users = ref([])
const offboardUserId = ref('')
const form = ref({ name: '', email: '', password: '' })
const onboarding = ref(false)
const offboarding = ref(false)
const onboardError = ref('')
const offboardError = ref('')
const toast = useToastStore()

onMounted(async () => {
  try {
    const res = await api.get('/v1/users')
    users.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
})

async function handleOnboard() {
  onboarding.value = true
  onboardError.value = ''
  try {
    await api.post('/v1/onboarding/onboard', form.value)
    form.value = { name: '', email: '', password: '' }
    toast.success('Onboarding realizado com sucesso!')
  } catch (e) {
    onboardError.value = e.response?.data?.message || e.response?.data?.error || 'Erro no onboarding. Verifique os dados e tente novamente.'
    if (e.response?.data?.errors) {
      const msgs = Object.values(e.response.data.errors).flat()
      onboardError.value = msgs.join('\n')
    }
  } finally {
    onboarding.value = false
  }
}

async function handleOffboard() {
  if (!offboardUserId.value) return
  offboarding.value = true
  offboardError.value = ''
  try {
    await api.post(`/v1/onboarding/${offboardUserId.value}/offboard`)
    offboardUserId.value = ''
    toast.success('Offboarding realizado com sucesso!')
  } catch (e) {
    offboardError.value = e.response?.data?.message || e.response?.data?.error || 'Erro no offboarding. Tente novamente.'
  } finally {
    offboarding.value = false
  }
}
</script>
