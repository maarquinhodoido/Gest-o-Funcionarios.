<template>
  <div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Auditoria</h1>
      <button @click="currentTab === 'all' ? loadAll() : currentTab === 'suspicious' ? loadSuspicious() : loadFailed()" class="btn btn-secondary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        Atualizar
      </button>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 p-1 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-100'">
      <button v-for="tab in tabs" :key="tab.key"
        @click="switchTab(tab.key)"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
        :class="currentTab === tab.key
          ? theme.isDark ? 'bg-gray-700 text-white shadow-sm' : 'bg-white text-gray-900 shadow-sm'
          : theme.isDark ? 'text-gray-400 hover:text-gray-200' : 'text-gray-500 hover:text-gray-700'">
        <component :is="tab.icon" class="w-4 h-4" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Logs Table -->
    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Data</th>
            <th>Utilizador</th>
            <th>Ação</th>
            <th>Entidade</th>
            <th>Descrição</th>
            <th>Severidade</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs" :key="log.id">
            <td class="text-xs whitespace-nowrap font-mono" style="color: var(--ds-text-tertiary)">
              {{ formatDate(log.created_at) }}
            </td>
            <td>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
                  :class="log.user_id ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500'">
                  {{ log.user_name ? log.user_name.charAt(0).toUpperCase() : 'S' }}
                </div>
                <span class="text-sm" style="color: var(--ds-text-primary)">{{ log.user_name || 'Sistema' }}</span>
              </div>
            </td>
            <td>
              <span class="badge badge-neutral text-[10px]">{{ log.action }}</span>
            </td>
            <td class="text-sm" style="color: var(--ds-text-secondary)">
              {{ log.entity_type }}{{ log.entity_id ? ' #' + log.entity_id : '' }}
            </td>
            <td class="text-sm max-w-xs truncate" style="color: var(--ds-text-primary)">
              {{ log.description || '-' }}
            </td>
            <td>
              <span :class="['badge', severityBadge(log.severity)]">{{ log.severity }}</span>
            </td>
            <td>
              <button @click="openDetail(log)" class="btn btn-sm btn-ghost">Detalhes</button>
            </td>
          </tr>
          <tr v-if="!logs.length">
            <td colspan="7" class="text-center py-10 text-sm" style="color: var(--ds-text-tertiary)">Nenhum registo encontrado.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Detail Modal -->
    <Modal v-model="showDetail" title="Detalhes do Registo" max-width="lg">
      <div v-if="selectedLog" class="space-y-5">
        <div class="flex items-start gap-3">
          <div class="w-3 h-3 mt-1 rounded-full flex-shrink-0" :class="severityDot(selectedLog.severity)"></div>
          <div>
            <p class="text-base font-semibold" style="color: var(--ds-text-primary)">
              {{ selectedLog.description || selectedLog.action }}
            </p>
            <p class="text-sm mt-0.5" style="color: var(--ds-text-tertiary)">{{ formatDateTime(selectedLog.created_at) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Utilizador</p>
            <p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ selectedLog.user_name || 'Sistema' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">Ação</p>
            <span class="badge badge-neutral text-[10px]">{{ selectedLog.action }}</span>
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
            <p class="text-sm font-mono" style="color: var(--ds-text-primary)">{{ selectedLog.entity_id ?? '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-medium mb-0.5" style="color: var(--ds-text-tertiary)">IP</p>
            <p class="text-sm font-mono" style="color: var(--ds-text-primary)">{{ selectedLog.ip_address || '-' }}</p>
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

        <div v-if="selectedLog.old_values || selectedLog.new_values" class="grid grid-cols-2 gap-4">
          <div v-if="selectedLog.old_values">
            <p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Valores Anteriores</p>
            <pre class="text-xs p-3 rounded-xl overflow-x-auto font-mono" :class="theme.isDark ? 'bg-gray-800 text-gray-300' : 'bg-gray-50 text-gray-600'">{{ formatJSON(selectedLog.old_values) }}</pre>
          </div>
          <div v-if="selectedLog.new_values">
            <p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Novos Valores</p>
            <pre class="text-xs p-3 rounded-xl overflow-x-auto font-mono" :class="theme.isDark ? 'bg-gray-800 text-gray-300' : 'bg-gray-50 text-gray-600'">{{ formatJSON(selectedLog.new_values) }}</pre>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button @click="showDetail = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'
import { ClockIcon, ShieldExclamationIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const theme = useThemeStore()
const logs = ref([])
const currentTab = ref('all')
const showDetail = ref(false)
const selectedLog = ref(null)

const tabs = [
  { key: 'all', label: 'Todos', icon: ClockIcon },
  { key: 'suspicious', label: 'Suspeitas', icon: ShieldExclamationIcon },
  { key: 'failed', label: 'Logins Falhados', icon: ExclamationTriangleIcon },
]

function switchTab(tab) {
  currentTab.value = tab
  if (tab === 'all') loadAll()
  else if (tab === 'suspicious') loadSuspicious()
  else loadFailed()
}

async function loadAll() {
  try {
    const res = await api.get('/v1/audit')
    logs.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
}
async function loadSuspicious() {
  try {
    const res = await api.get('/v1/audit/suspicious')
    logs.value = res.data.data || []
  } catch (e) { console.error(e) }
}
async function loadFailed() {
  try {
    const res = await api.get('/v1/audit/failed-logins')
    logs.value = res.data.data || []
  } catch (e) { console.error(e) }
}

function openDetail(log) {
  selectedLog.value = log
  showDetail.value = true
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
  if (typeof val === 'string') { try { return JSON.stringify(JSON.parse(val), null, 2) } catch { return val } }
  return JSON.stringify(val, null, 2)
}

onMounted(loadAll)
</script>
