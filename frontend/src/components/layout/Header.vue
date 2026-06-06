<template>
  <header :class="[
    'h-16 flex items-center justify-between px-6 border-b',
    theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
  ]">
    <h1 :class="['text-lg font-semibold', theme.isDark ? 'text-white' : 'text-gray-900']">
      {{ pageTitle }}
    </h1>

    <div class="flex items-center gap-4">
      <button @click="theme.toggle()"
        :class="['p-2 rounded-lg transition-colors', theme.isDark ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600']">
        <SunIcon v-if="theme.isDark" class="w-5 h-5" />
        <MoonIcon v-else class="w-5 h-5" />
      </button>

      <div class="relative">
        <button @click="showDropdown = !showDropdown"
          :class="['flex items-center gap-2 px-3 py-2 rounded-lg transition-colors', theme.isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-100']">
          <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
            <span class="text-white font-medium text-sm">{{ initials }}</span>
          </div>
          <span :class="['text-sm font-medium', theme.isDark ? 'text-gray-200' : 'text-gray-700']">
            {{ auth.user?.name }}
          </span>
        </button>

        <div v-if="showDropdown" @click.outside="showDropdown = false" :class="[
          'absolute right-0 mt-2 w-48 rounded-lg shadow-lg border py-1 z-50',
          theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
        ]">
          <button @click="handleLogout"
            :class="['w-full text-left px-4 py-2 text-sm transition-colors', theme.isDark ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-700']">
            Sair
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useThemeStore } from '../../stores/theme'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const theme = useThemeStore()
const showDropdown = ref(false)

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/users': 'Utilizadores',
    '/departments': 'Departamentos',
    '/equipment': 'Equipamentos',
    '/assignments': 'Atribuições',
    '/companies': 'Empresas',
    '/roles': 'Roles e Permissões',
    '/audit': 'Auditoria',
    '/onboarding': 'Onboarding / Offboarding',
  }
  return titles[route.path] || 'Gestão Interna'
})

const initials = computed(() => {
  return auth.user?.name?.charAt(0)?.toUpperCase() || 'U'
})

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>
