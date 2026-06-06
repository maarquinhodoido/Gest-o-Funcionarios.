<template>
  <div>
    <div :class="['rounded-xl border overflow-hidden', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
      <table class="w-full">
        <thead>
          <tr :class="theme.isDark ? 'bg-gray-700/50' : 'bg-gray-50'">
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Nome</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">NIF</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Plano</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Status</th>
            <th class="text-left px-6 py-3 text-xs font-medium uppercase" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">Máx. Utilizadores</th>
          </tr>
        </thead>
        <tbody class="divide-y" :class="theme.isDark ? 'divide-gray-700' : 'divide-gray-200'">
          <tr v-for="c in companies" :key="c.id">
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ c.name }}</td>
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ c.tax_id }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ c.plan }}</td>
            <td class="px-6 py-4">
              <span :class="['inline-flex px-2 py-1 text-xs font-medium rounded-full',
                c.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300']">
                {{ c.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ c.max_users }}</td>
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
const companies = ref([])

onMounted(async () => {
  try {
    const res = await api.get('/v1/companies')
    companies.value = res.data.data || []
  } catch (e) { console.error(e) }
})
</script>
