<template>
  <header :class="[
    'h-16 flex items-center justify-between px-6 lg:px-8 border-b transition-colors duration-300 relative z-40',
    theme.isDark ? 'bg-gray-900/80 backdrop-blur-md border-gray-800' : 'bg-white/80 backdrop-blur-md border-gray-100'
  ]">
    <h1 class="text-lg font-semibold tracking-tight" :class="theme.isDark ? 'text-white' : 'text-gray-900'">
      {{ pageTitle }}
    </h1>

    <div class="flex items-center gap-2">
      <!-- Notification Bell -->
      <div class="relative">
        <button @click="toggleNotifications"
          class="relative p-2 rounded-xl transition-all duration-200"
          :class="theme.isDark ? 'hover:bg-gray-800 text-gray-400 hover:text-gray-200' : 'hover:bg-gray-100 text-gray-400 hover:text-gray-600'">
          <BellIcon class="w-5 h-5" />
          <span v-if="notif.hasUnread"
            class="absolute -top-0.5 -right-0.5 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] min-h-[18px]"
            style="box-shadow: 0 0 0 2px var(--ds-surface)">
            {{ notif.unreadCount > 99 ? '99+' : notif.unreadCount }}
          </span>
        </button>

        <!-- Dropdown backdrop -->
        <div v-if="showNotifications" class="fixed inset-0 z-[100]" @click="showNotifications = false"></div>

        <!-- Dropdown -->
        <div v-if="showNotifications" :class="[
          'absolute right-0 mt-2 w-96 rounded-2xl shadow-xl border overflow-hidden z-[110] animate-slide-down',
          theme.isDark ? 'bg-gray-800 border-gray-700 shadow-black/20' : 'bg-white border-gray-100 shadow-gray-200/50'
        ]">
          <div class="flex items-center justify-between px-4 py-3 border-b" :class="theme.isDark ? 'border-gray-700' : 'border-gray-100'">
            <span class="text-sm font-semibold" :class="theme.isDark ? 'text-white' : 'text-gray-900'">Notificações</span>
            <button v-if="notif.unreadCount > 0" @click="notif.markAllAsRead()" class="text-xs font-medium hover:underline" style="color: var(--ds-color-primary-600)">Marcar todas lidas</button>
          </div>

          <div class="max-h-80 overflow-y-auto">
            <div v-for="note in notif.items" :key="note.id"
              @click="handleNotificationClick(note)"
              class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors duration-150"
              :class="[
                !note.is_read
                  ? theme.isDark ? 'bg-indigo-500/5 hover:bg-indigo-500/10' : 'bg-indigo-50/50 hover:bg-indigo-50'
                  : theme.isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-50'
              ]">
              <div class="p-1.5 rounded-lg flex-shrink-0" :style="{ background: typeBg(note.type) }">
                <component :is="typeIcon(note.type)" class="w-4 h-4" :style="{ color: typeColor(note.type) }" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <p class="text-sm font-medium truncate" :class="!note.is_read ? (theme.isDark ? 'text-white' : 'text-gray-900') : (theme.isDark ? 'text-gray-300' : 'text-gray-600')">
                    {{ note.title }}
                  </p>
                  <span v-if="!note.is_read" class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                </div>
                <p class="text-xs mt-0.5 line-clamp-2" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ note.message }}</p>
                <p class="text-[10px] mt-1" :class="theme.isDark ? 'text-gray-500' : 'text-gray-400'">{{ timeAgo(note.created_at) }}</p>
              </div>
            </div>

            <div v-if="!notif.items.length" class="flex flex-col items-center py-10 text-sm" :class="theme.isDark ? 'text-gray-400' : 'text-gray-400'">
              <BellIcon class="w-8 h-8 mb-2 opacity-40" />
              Sem notificações
            </div>
          </div>

          <div v-if="notif.items.length" class="border-t px-4 py-2.5" :class="theme.isDark ? 'border-gray-700' : 'border-gray-100'">
            <button @click="showAll" class="text-xs font-medium w-full text-center hover:underline" style="color: var(--ds-color-primary-600)">Ver todas</button>
          </div>
        </div>
      </div>

      <button @click="theme.toggle()"
        :class="['p-2 rounded-xl transition-all duration-200', theme.isDark ? 'hover:bg-gray-800 text-gray-400 hover:text-gray-200' : 'hover:bg-gray-100 text-gray-400 hover:text-gray-600']">
        <SunIcon v-if="theme.isDark" class="w-5 h-5" />
        <MoonIcon v-else class="w-5 h-5" />
      </button>

      <div class="relative">
        <button @click="showDropdown = !showDropdown"
          :class="['flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all duration-200', theme.isDark ? 'hover:bg-gray-800' : 'hover:bg-gray-100']">
          <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center text-xs font-medium text-white shadow-sm">
            {{ initials }}
          </div>
          <span :class="['text-sm font-medium', theme.isDark ? 'text-gray-200' : 'text-gray-700']">
            {{ auth.user?.name }}
          </span>
        </button>

        <div v-if="showDropdown" class="fixed inset-0 z-[100]" @click="showDropdown = false"></div>
        <div v-if="showDropdown" :class="[
          'absolute right-0 mt-2 w-48 rounded-xl shadow-lg border py-1 z-[110] animate-fade-in',
          theme.isDark ? 'bg-gray-800 border-gray-700 shadow-black/20' : 'bg-white border-gray-100 shadow-gray-200/50'
        ]">
          <button @click="handleLogout"
            :class="['w-full text-left px-4 py-2.5 text-sm transition-colors duration-150', theme.isDark ? 'hover:bg-gray-700 text-gray-300 hover:text-white' : 'hover:bg-gray-50 text-gray-600 hover:text-gray-900']">
            Sair
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Detail Modal -->
    <Modal v-model="showDetailModal" title="Notificação" max-width="md">
      <div v-if="selectedNote" class="space-y-4">
        <div class="flex items-start gap-3">
          <div class="p-2 rounded-xl flex-shrink-0" :style="{ background: typeBg(selectedNote.type) }">
            <component :is="typeIcon(selectedNote.type)" class="w-5 h-5" :style="{ color: typeColor(selectedNote.type) }" />
          </div>
          <div>
            <p class="text-base font-semibold" style="color: var(--ds-text-primary)">{{ selectedNote.title }}</p>
            <p class="text-sm mt-1" style="color: var(--ds-text-secondary)">{{ selectedNote.message }}</p>
            <p class="text-xs mt-2" style="color: var(--ds-text-tertiary)">{{ formatDateTime(selectedNote.created_at) }}</p>
          </div>
        </div>
        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useThemeStore } from '../../stores/theme'
import { useNotificationStore } from '../../stores/notifications'
import Modal from '../Modal.vue'
import { SunIcon, MoonIcon, BellIcon, InformationCircleIcon, ExclamationTriangleIcon, CheckCircleIcon, ShieldExclamationIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const theme = useThemeStore()
const notif = useNotificationStore()
const showDropdown = ref(false)
const showNotifications = ref(false)
const showDetailModal = ref(false)
const selectedNote = ref(null)

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/users': 'Utilizadores',
    '/employee-profiles': 'Funcionários',
    '/departments': 'Departamentos',
    '/equipment': 'Equipamentos',
    '/assignments': 'Atribuições',
    '/companies': 'Empresas',
    '/roles': 'Roles e Permissões',
    '/audit': 'Auditoria',
    '/onboarding': 'Onboarding / Offboarding',
    '/notifications': 'Notificações',
  }
  return titles[route.path] || 'Gestão Interna'
})

const initials = computed(() => auth.user?.name?.charAt(0)?.toUpperCase() || 'U')

function typeIcon(type) {
  return type === 'alert' ? ShieldExclamationIcon
    : type === 'warning' ? ExclamationTriangleIcon
    : type === 'success' ? CheckCircleIcon
    : InformationCircleIcon
}
function typeColor(type) {
  return type === 'alert' ? '#ef4444'
    : type === 'warning' ? '#eab308'
    : type === 'success' ? '#22c55e'
    : '#3b82f6'
}
function typeBg(type) {
  return type === 'alert' ? 'rgba(239,68,68,0.1)'
    : type === 'warning' ? 'rgba(234,179,8,0.1)'
    : type === 'success' ? 'rgba(34,197,94,0.1)'
    : 'rgba(59,130,246,0.1)'
}
function timeAgo(date) {
  if (!date) return ''
  const d = date?.date ? new Date(date.date) : new Date(date)
  const diff = Date.now() - d.getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'agora'
  if (mins < 60) return mins + 'm'
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return hrs + 'h'
  const days = Math.floor(hrs / 24)
  if (days < 7) return days + 'd'
  return d.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit' })
}
function formatDateTime(date) {
  if (!date) return ''
  const d = date?.date ? new Date(date.date) : new Date(date)
  return d.toLocaleString('pt-PT', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function toggleNotifications() {
  showNotifications.value = !showNotifications.value
  if (showNotifications.value) notif.fetchNotifications()
}

function handleNotificationClick(note) {
  selectedNote.value = note
  showDetailModal.value = true
  if (!note.is_read) notif.markAsRead(note.id)
}

function showAll() {
  showNotifications.value = false
  router.push('/notifications')
}

function handleLogout() {
  auth.logout()
  router.push('/login')
}

onMounted(() => notif.startPolling())
onUnmounted(() => notif.stopPolling())
</script>
