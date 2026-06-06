import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../lib/axios'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token'))
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(email, password) {
    const response = await api.post('/v1/auth/login', { email, password })
    token.value = response.data.token
    user.value = response.data.user
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    return response.data
  }

  async function logout() {
    try {
      await api.post('/v1/auth/logout')
    } catch {
      // ignore
    }
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  async function fetchMe() {
    const response = await api.get('/v1/auth/me')
    user.value = response.data.data
    localStorage.setItem('user', JSON.stringify(response.data.data))
    return response.data.data
  }

  return { token, user, isAuthenticated, login, logout, fetchMe }
})
