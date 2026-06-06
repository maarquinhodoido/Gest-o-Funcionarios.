<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard title="Utilizadores Ativos" :value="stats.active_users" icon="users" color="blue" />
      <StatCard title="Equipamentos Atribuídos" :value="stats.equipment_assigned" icon="computer" color="green" />
      <StatCard title="Equipamentos Disponíveis" :value="stats.equipment_available" icon="cube" color="indigo" />
      <StatCard title="Alertas Críticos" :value="stats.critical_alerts" icon="exclamation" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div :class="['rounded-xl border p-6', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['text-lg font-semibold mb-4', theme.isDark ? 'text-white' : 'text-gray-900']">
          Atividades Recentes
        </h3>
        <div class="space-y-3">
          <div v-for="log in recentActivities" :key="log.id"
            :class="['flex items-center gap-3 p-3 rounded-lg text-sm', theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50']">
            <div :class="[
              'w-2 h-2 rounded-full flex-shrink-0',
              log.severity === 'critical' ? 'bg-red-500' : log.severity === 'warning' ? 'bg-yellow-500' : 'bg-green-500'
            ]" />
            <div class="flex-1 min-w-0">
              <p :class="['font-medium truncate', theme.isDark ? 'text-gray-200' : 'text-gray-700']">
                {{ log.description }}
              </p>
              <p :class="['text-xs', theme.isDark ? 'text-gray-400' : 'text-gray-500']">
                {{ log.created_at?.date ? new Date(log.created_at.date).toLocaleString('pt-PT') : '' }}
              </p>
            </div>
          </div>
          <p v-if="!recentActivities.length" :class="['text-sm', theme.isDark ? 'text-gray-400' : 'text-gray-500']">
            Nenhuma atividade recente.
          </p>
        </div>
      </div>

      <div :class="['rounded-xl border p-6', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
        <h3 :class="['text-lg font-semibold mb-4', theme.isDark ? 'text-white' : 'text-gray-900']">
          Equipamentos por Estado
        </h3>
        <div class="space-y-4">
          <ProgressBar label="Disponível" :value="stats.equipment_available" :total="stats.total_equipment" color="green" />
          <ProgressBar label="Atribuído" :value="stats.equipment_assigned" :total="stats.total_equipment" color="blue" />
          <ProgressBar label="Manutenção" :value="stats.equipment_maintenance" :total="stats.total_equipment" color="yellow" />
          <ProgressBar label="Perdido" :value="stats.equipment_lost" :total="stats.total_equipment" color="red" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'
import StatCard from '../components/StatCard.vue'
import ProgressBar from '../components/ProgressBar.vue'

const theme = useThemeStore()
const stats = ref({
  active_users: 0, total_users: 0, equipment_assigned: 0,
  equipment_maintenance: 0, equipment_lost: 0, equipment_available: 0,
  total_equipment: 0, failed_logins: 0, critical_alerts: 0
})
const recentActivities = ref([])

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
