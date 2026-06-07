<template>
  <div class="min-h-screen flex items-center justify-center px-4"
    style="background: var(--ds-surface-secondary)">
    <div class="w-full max-w-sm animate-slide-up">
      <div class="text-center mb-8">
        <div class="mx-auto w-14 h-14 flex items-center justify-center mb-5"
          style="background: var(--ds-color-primary-600); border-radius: var(--ds-radius-lg); box-shadow: 0 4px 12px rgba(99,102,241,0.3)">
          <span class="text-white font-bold text-xl">GI</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Gestão Interna</h1>
        <p class="mt-1.5 text-sm" style="color: var(--ds-text-tertiary)">Sistema de gestão de utilizadores e equipamentos</p>
      </div>

      <div class="p-6"
        style="background: var(--ds-surface); border: 1px solid var(--ds-border); border-radius: var(--ds-radius-lg); box-shadow: var(--ds-shadow-xl)">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="label" style="margin-bottom: var(--ds-space-1)">Email</label>
            <input v-model="email" type="email" required class="input" placeholder="seu@email.com" />
          </div>
          <div>
            <label class="label" style="margin-bottom: var(--ds-space-1)">Password</label>
            <input v-model="password" type="password" required class="input" placeholder="••••••••" />
          </div>

          <div v-if="error" class="alert alert-error">{{ error }}</div>

          <button type="submit" :disabled="loading" class="btn btn-primary w-full btn-lg">
            {{ loading ? 'A entrar...' : 'Entrar' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()
const email = ref('admin@empresademo.com')
const password = ref('123')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.response?.data?.error || 'Erro ao fazer login. Verifique as credenciais.'
  } finally {
    loading.value = false
  }
}
</script>
