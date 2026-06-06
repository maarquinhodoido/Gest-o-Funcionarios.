<template>
  <div>
    <div class="flex gap-3 mb-6">
      <button @click="loadAudit"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
        Atualizar Logs
      </button>
      <button @click="loadSuspicious"
        class="px-4 py-2 border text-sm font-medium rounded-lg"
        :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
        Atividades Suspeitas
      </button>
      <button @click="loadFailedLogins"
        class="px-4 py-2 border text-sm font-medium rounded-lg"
        :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
        Logins Falhados
      </button>
    </div>

    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Data</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Ação</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Entidade</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Descrição</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Severidade</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="log in logs" :key="log.id">
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">
              {{ log.created_at?.date ? new Date(log.created_at.date).toLocaleString('pt-PT') : '' }}
            </td>
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ log.action }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ log.entity_type }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ log.description }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                log.severity === 'critical' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' :
                log.severity === 'warning' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300' :
                'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300']">
                {{ log.severity }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'

const theme = useThemeStore()
const logs = ref([])

async function loadAudit() {
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

async function loadFailedLogins() {
  try {
    const res = await api.get('/v1/audit/failed-logins')
    logs.value = res.data.data || []
  } catch (e) { console.error(e) }
}

onMounted(loadAudit)
</script>
