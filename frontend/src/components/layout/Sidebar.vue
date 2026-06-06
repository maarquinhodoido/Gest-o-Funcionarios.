<template>
  <aside :class="[
    'w-64 flex-shrink-0 border-r transition-colors',
    theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
  ]">
    <div :class="['flex items-center gap-2 px-6 h-16 border-b', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
      <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
        <span class="text-white font-bold text-sm">GI</span>
      </div>
      <span :class="['font-semibold text-lg', theme.isDark ? 'text-white' : 'text-gray-900']">
        Gestão Interna
      </span>
    </div>

    <nav class="p-4 space-y-1">
      <router-link v-for="item in menuItems" :key="item.path" :to="item.path"
        :class="[
          'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors',
          isActive(item.path)
            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300'
            : theme.isDark
              ? 'text-gray-300 hover:bg-gray-700 hover:text-white'
              : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
        ]">
        <component :is="item.icon" class="w-5 h-5" />
        {{ item.label }}
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { useThemeStore } from '../../stores/theme'
import {
  HomeIcon, UsersIcon, ComputerDesktopIcon, ArrowRightOnRectangleIcon,
  BuildingOfficeIcon, ShieldCheckIcon, ClipboardDocumentListIcon,
  BuildingStorefrontIcon, UserPlusIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const theme = useThemeStore()

function isActive(path) {
  return route.path.startsWith(path)
}

const menuItems = [
  { path: '/dashboard', label: 'Dashboard', icon: HomeIcon },
  { path: '/users', label: 'Utilizadores', icon: UsersIcon },
  { path: '/departments', label: 'Departamentos', icon: BuildingStorefrontIcon },
  { path: '/equipment', label: 'Equipamentos', icon: ComputerDesktopIcon },
  { path: '/assignments', label: 'Atribuições', icon: ArrowRightOnRectangleIcon },
  { path: '/companies', label: 'Empresas', icon: BuildingOfficeIcon },
  { path: '/roles', label: 'Roles', icon: ShieldCheckIcon },
  { path: '/audit', label: 'Auditoria', icon: ClipboardDocumentListIcon },
  { path: '/onboarding', label: 'Onboarding', icon: UserPlusIcon },
]
</script>
