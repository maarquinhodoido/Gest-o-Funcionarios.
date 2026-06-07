<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Notificações</h1>
      <button v-if="notif.unreadCount > 0" @click="notif.markAllAsRead()" class="btn btn-ghost btn-sm">Marcar todas como lidas</button>
    </div>

    <div class="space-y-2">
      <div v-for="note in notif.items" :key="note.id"
        class="card flex items-start gap-4 cursor-pointer transition-all duration-200"
        :class="[!note.is_read ? (theme.isDark ? 'border-indigo-500/30' : 'border-indigo-200') : 'card-hover']"
        @click="handleClick(note)">
        <div class="p-2 rounded-xl flex-shrink-0" :style="{ background: typeBg(note.type) }">
          <component :is="typeIcon(note.type)" class="w-5 h-5" :style="{ color: typeColor(note.type) }" />
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-sm font-semibold" :class="!note.is_read ? (theme.isDark ? 'text-white' : 'text-gray-900') : (theme.isDark ? 'text-gray-300' : 'text-gray-600')">
                {{ note.title }}
              </p>
              <p class="text-sm mt-1" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ note.message }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <span class="text-xs whitespace-nowrap" :class="theme.isDark ? 'text-gray-500' : 'text-gray-400'">{{ timeAgo(note.created_at) }}</span>
              <span v-if="!note.is_read" class="w-2 h-2 rounded-full bg-indigo-500"></span>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!notif.items.length" class="text-center py-16">
        <BellIcon class="w-12 h-12 mx-auto mb-4" :class="theme.isDark ? 'text-gray-600' : 'text-gray-300'" />
        <p class="text-sm font-medium" style="color: var(--ds-text-secondary)">Nenhuma notificação</p>
        <p class="text-xs mt-1" style="color: var(--ds-text-tertiary)">As notificações aparecerão aqui</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import { useNotificationStore } from '../stores/notifications'
import {
  BellIcon, InformationCircleIcon, ExclamationTriangleIcon,
  CheckCircleIcon, ShieldExclamationIcon
} from '@heroicons/vue/24/outline'

const theme = useThemeStore()
const notif = useNotificationStore()

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

function handleClick(note) {
  if (!note.is_read) notif.markAsRead(note.id)
}

onMounted(() => {
  notif.fetchNotifications()
})
</script>
