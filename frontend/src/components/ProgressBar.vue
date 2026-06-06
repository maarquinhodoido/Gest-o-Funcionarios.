<template>
  <div>
    <div class="flex justify-between text-sm mb-1">
      <span :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ label }}</span>
      <span :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ value }}/{{ total }}</span>
    </div>
    <div :class="['w-full h-2 rounded-full', theme.isDark ? 'bg-gray-700' : 'bg-gray-200']">
      <div class="h-2 rounded-full transition-all duration-500" :class="colorClass" :style="{ width: percentage + '%' }" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '../stores/theme'

const props = defineProps({ label: String, value: Number, total: Number, color: String })
const theme = useThemeStore()

const percentage = computed(() => props.total > 0 ? (props.value / props.total) * 100 : 0)
const colorClass = computed(() => ({
  green: 'bg-green-500', blue: 'bg-blue-500', yellow: 'bg-yellow-500', red: 'bg-red-500'
}[props.color] || 'bg-indigo-500'))
</script>
