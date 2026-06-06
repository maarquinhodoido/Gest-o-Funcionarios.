<template>
  <div :class="['rounded-xl border p-6 transition-colors', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
    <div class="flex items-center justify-between">
      <div>
        <p :class="['text-sm font-medium', theme.isDark ? 'text-gray-400' : 'text-gray-500']">{{ title }}</p>
        <p :class="['text-3xl font-bold mt-1', theme.isDark ? 'text-white' : 'text-gray-900']">{{ value }}</p>
      </div>
      <div :class="['p-3 rounded-lg', colorClasses]">
        <component :is="iconComponent" class="w-6 h-6 text-white" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '../stores/theme'
import { UsersIcon, ComputerDesktopIcon, CubeIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String, value: [Number, String], icon: String, color: String
})
const theme = useThemeStore()

const icons = { users: UsersIcon, computer: ComputerDesktopIcon, cube: CubeIcon, exclamation: ExclamationTriangleIcon }
const iconComponent = computed(() => icons[props.icon] || CubeIcon)

const colorClasses = computed(() => ({
  blue: 'bg-blue-500', green: 'bg-green-500', indigo: 'bg-indigo-500',
  red: 'bg-red-500', yellow: 'bg-yellow-500',
}[props.color] || 'bg-indigo-500'))
</script>
