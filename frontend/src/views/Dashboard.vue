<template>
  <div class="space-y-8 animate-fade-in">
    <!-- Welcome -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight" style="color: var(--ds-text-primary)">
          {{ greeting }}, {{ userName }}
        </h1>
        <p class="mt-1 text-sm" style="color: var(--ds-text-tertiary)">
          {{ dateStr }}
        </p>
      </div>
      <div class="flex items-center gap-2 text-sm" style="color: var(--ds-text-tertiary)">
        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
        Sistema operacional
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
      <div v-for="card in statCards" :key="card.label"
        class="card card-hover p-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full opacity-5" :style="{ background: card.color }"></div>
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-xl flex-shrink-0" :style="{ background: card.bg }">
            <component :is="card.icon" class="w-4 h-4" :style="{ color: card.color }" />
          </div>
          <div class="min-w-0">
            <p class="text-xs font-medium" style="color: var(--ds-text-tertiary)">{{ card.label }}</p>
            <p class="text-xl font-bold mt-0.5" style="color: var(--ds-text-primary)">{{ card.value }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="card lg:col-span-2">
        <div class="card-header">
          <h3 class="card-title">Distribuição de Equipamentos</h3>
          <span class="badge badge-neutral">{{ stats.total_equipment }} total</span>
        </div>
        <DashboardChart type="bar" :data="equipmentBars" />
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Visão Geral</h3>
        </div>
        <DashboardChart type="donut" :data="donutData" :size="200" stroke-width="14" />
      </div>
    </div>

    <!-- Recent Activities with Detail Modal -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Atividades Recentes</h3>
        <span v-if="recentActivities.length" class="badge badge-neutral">{{ recentActivities.length }} eventos</span>
      </div>
      <div class="space-y-1">
        <div v-for="log in recentActivities" :key="log.id"
          class="flex items-start gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
          :class="theme.isDark ? 'hover:bg-gray-800' : 'hover:bg-gray-50'">
          <div class="w-2 h-2 mt-2 rounded-full flex-shrink-0" :class="severityDot(log.severity)"></div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <p class="text-sm font-medium truncate" style="color: var(--ds-text-primary)">
                {{ log.description || log.action }}
              </p>
              <span :class="['badge', severityBadge(log.severity)]">{{ log.severity }}</span>
              <span class="badge badge-neutral text-[10px]">{{ log.action }}</span>
            </div>
            <div class="flex items-center gap-3 mt-0.5">
              <span class="text-xs" style="color: var(--ds-text-tertiary)">
                {{ formatDate(log.created_at) }}
              </span>
              <span v-if="log.entity_type" class="text-xs" style="color: var(--ds-text-tertiary)">
                {{ log.entity_type }}{{ log.entity_id ? ' #' + log.entity_id : '' }}
              </span>
              <span v-if="log.user_name" class="text-xs font-medium" style="color: var(--ds-text-secondary)">
                {{ log.user_name }}
              </span>
              <span v-if="log.ip_address" class="text-xs font-mono" style="color: var(--ds-text-tertiary)">
                {{ log.ip_address }}
              </span>
            </div>
          </div>
          <button @click="openDetail(log)" class="btn btn-sm btn-ghost opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
            Detalhes
          </button>
        </div>
        <div v-if="!recentActivities.length" class="text-center py-10">
          <p class="text-sm" style="color: var(--ds-text-tertiary)">Nenhuma atividade recente.</p>
        </div>
      </div>
    </div>

    <!-- Activity Detail Modal -->
    <Modal v-model="showDetailModal" title="Detalhes da Atividade" max-width="lg">
      <div v-if="selectedLog" class="space-y-5">
        <!-- Header -->
        <div class="flex items-start gap-3">
          <div class="w-3 h-3 mt-1 rounded-full flex-shrink-0" :class="severityDot(selectedLog.severity)"></div>
          <div>
            <p class="text-base font-semibold" style="color: var(--ds-text-primary)">
              {{ selectedLog.description || selectedLog.action }}
            </p>
            <p class="text-sm mt-0.5" style="color: var(--ds-text-tertiary)">
              {{ formatDateTime(selectedLog.created_at) }}
            </p>
          </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Tipo de Ação</p>
            <p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ selectedLog.action }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Severidade</p>
            <span :class="['badge', severityBadge(selectedLog.severity)]">{{ selectedLog.severity }}</span>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Entidade</p>
            <p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ selectedLog.entity_type || '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">ID Entidade</p>
            <p class="text-sm font-medium font-mono" style="color: var(--ds-text-primary)">{{ selectedLog.entity_id ?? '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Utilizador</p>
            <p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ selectedLog.user_name || 'Sistema' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">IP</p>
            <p class="text-sm font-medium font-mono" style="color: var(--ds-text-primary)">{{ selectedLog.ip_address || '-' }}</p>
          </div>
          <div v-if="selectedLog.location" class="col-span-2">
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Localização</p>
            <p class="text-sm" style="color: var(--ds-text-primary)">{{ selectedLog.location }}</p>
          </div>
          <div v-if="selectedLog.user_agent" class="col-span-2">
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">User Agent</p>
            <p class="text-xs break-all font-mono" style="color: var(--ds-text-secondary)">{{ selectedLog.user_agent }}</p>
          </div>
        </div>

        <!-- Old / New Values -->
        <div v-if="selectedLog.old_values || selectedLog.new_values" class="grid grid-cols-2 gap-4">
          <div v-if="selectedLog.old_values">
            <p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Valores Anteriores</p>
            <pre class="text-xs p-3 rounded-xl overflow-x-auto font-mono"
              :class="theme.isDark ? 'bg-gray-800 text-gray-300' : 'bg-gray-50 text-gray-600'"
            >{{ formatJSON(selectedLog.old_values) }}</pre>
          </div>
          <div v-if="selectedLog.new_values">
            <p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Novos Valores</p>
            <pre class="text-xs p-3 rounded-xl overflow-x-auto font-mono"
              :class="theme.isDark ? 'bg-gray-800 text-gray-300' : 'bg-gray-50 text-gray-600'"
            >{{ formatJSON(selectedLog.new_values) }}</pre>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import { useAuthStore } from '../stores/auth'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'
import DashboardChart from '../components/DashboardChart.vue'
import {
  UsersIcon, ComputerDesktopIcon, CubeIcon,
  ExclamationTriangleIcon, BuildingOfficeIcon, WrenchScrewdriverIcon
} from '@heroicons/vue/24/outline'

const theme = useThemeStore()
const auth = useAuthStore()

const userName = computed(() => auth.user?.name || 'Utilizador')
const greeting = computed(() => {
  const h = new Date().getHours()
  if (h < 12) return 'Bom dia'
  if (h < 18) return 'Boa tarde'
  return 'Boa noite'
})
const dateStr = computed(() =>
  new Date().toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
    .replace(/^\w/, c => c.toUpperCase())
)

const stats = ref({
  active_users: 0, total_users: 0, blocked_users: 0,
  total_departments: 0,
  equipment_assigned: 0, equipment_maintenance: 0,
  equipment_lost: 0, equipment_available: 0, total_equipment: 0,
  usage_percentage: 0, available_percentage: 0,
  failed_logins: 0, critical_alerts: 0
})
const recentActivities = ref([])
const showDetailModal = ref(false)
const selectedLog = ref(null)

const statCards = computed(() => [
  { label: 'Utilizadores Ativos', value: stats.value.active_users, icon: UsersIcon, color: '#6366f1', bg: 'rgba(99,102,241,0.1)' },
  { label: 'Equipamentos', value: stats.value.total_equipment, icon: CubeIcon, color: '#3b82f6', bg: 'rgba(59,130,246,0.1)' },
  { label: 'Atribuições', value: stats.value.equipment_assigned, icon: ComputerDesktopIcon, color: '#22c55e', bg: 'rgba(34,197,94,0.1)' },
  { label: 'Departamentos', value: stats.value.total_departments, icon: BuildingOfficeIcon, color: '#a855f7', bg: 'rgba(168,85,247,0.1)' },
  { label: 'Manutenção', value: stats.value.equipment_maintenance, icon: WrenchScrewdriverIcon, color: '#eab308', bg: 'rgba(234,179,8,0.1)' },
  { label: 'Alertas', value: stats.value.critical_alerts, icon: ExclamationTriangleIcon, color: '#ef4444', bg: 'rgba(239,68,68,0.1)' },
])

const equipmentBars = computed(() => {
  const t = stats.value.total_equipment || 1
  return [
    { label: 'Disponível', value: stats.value.equipment_available, total: t, percent: Math.round(stats.value.equipment_available / t * 100), color: '#22c55e' },
    { label: 'Atribuído', value: stats.value.equipment_assigned, total: t, percent: Math.round(stats.value.equipment_assigned / t * 100), color: '#3b82f6' },
    { label: 'Manutenção', value: stats.value.equipment_maintenance, total: t, percent: Math.round(stats.value.equipment_maintenance / t * 100), color: '#eab308' },
    { label: 'Perdido', value: stats.value.equipment_lost, total: t, percent: Math.round(stats.value.equipment_lost / t * 100), color: '#ef4444' },
  ]
})

const donutData = computed(() => [
  { label: 'Disponível', value: stats.value.equipment_available, color: '#22c55e' },
  { label: 'Atribuído', value: stats.value.equipment_assigned, color: '#3b82f6' },
  { label: 'Manutenção', value: stats.value.equipment_maintenance, color: '#eab308' },
  { label: 'Perdido', value: stats.value.equipment_lost, color: '#ef4444' },
])

function openDetail(log) {
  selectedLog.value = log
  showDetailModal.value = true
}

function severityDot(s) {
  return s === 'critical' ? 'bg-red-500' : s === 'warning' ? 'bg-yellow-500' : 'bg-green-500'
}
function severityBadge(s) {
  return s === 'critical' ? 'badge-error' : s === 'warning' ? 'badge-warning' : 'badge-success'
}
function formatDate(date) {
  if (!date) return ''
  const d = date?.date ? new Date(date.date) : new Date(date)
  return d.toLocaleString('pt-PT', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
}
function formatDateTime(date) {
  if (!date) return ''
  const d = date?.date ? new Date(date.date) : new Date(date)
  return d.toLocaleString('pt-PT', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' })
}
function formatJSON(val) {
  if (!val) return '-'
  if (typeof val === 'string') {
    try { return JSON.stringify(JSON.parse(val), null, 2) } catch { return val }
  }
  return JSON.stringify(val, null, 2)
}

onMounted(async () => {
  try {
    const [statsRes, activitiesRes] = await Promise.all([
      api.get('/v1/dashboard/stats'),
      api.get('/v1/dashboard/recent-activities'),
    ])
    stats.value = statsRes.data.data
    recentActivities.value = activitiesRes.data.data
  } catch (e) {
    console.error('Failed to load dashboard:', e)
  }
})
</script>
