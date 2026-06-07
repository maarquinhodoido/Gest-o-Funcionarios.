<template>
  <aside :class="[
    'w-64 flex-shrink-0 border-r transition-colors duration-300',
    theme.isDark ? 'bg-gray-900 border-gray-800' : 'bg-white border-gray-100'
  ]">
    <div :class="['flex items-center gap-3 px-6 h-16', theme.isDark ? '' : '']">
      <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-sm shadow-indigo-200">
        <span class="text-white font-bold text-sm">GI</span>
      </div>
      <span :class="['font-semibold text-base tracking-tight', theme.isDark ? 'text-white' : 'text-gray-900']">
        Gestão Interna
      </span>
    </div>

    <nav class="p-3 space-y-0.5">
      <router-link v-for="item in menuItems" :key="item.path" :to="item.path"
        :class="[
          'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
          isActive(item.path)
            ? theme.isDark
              ? 'bg-indigo-500/10 text-indigo-400'
              : 'bg-indigo-50 text-indigo-700'
            : theme.isDark
              ? 'text-gray-400 hover:bg-gray-800 hover:text-gray-200'
              : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'
        ]">
        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
        <span>{{ item.label }}</span>
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
  BuildingStorefrontIcon, UserPlusIcon, IdentificationIcon, BellIcon
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
  { path: '/employee-profiles', label: 'Funcionários', icon: IdentificationIcon },
  { path: '/onboarding', label: 'Onboarding', icon: UserPlusIcon },
  { path: '/notifications', label: 'Notificações', icon: BellIcon },
]
</script>
