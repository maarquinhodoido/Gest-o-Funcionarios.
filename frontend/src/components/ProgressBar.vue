<template>
  <div>
    <div class="flex justify-between text-sm mb-1">
      <span style="color: var(--ds-text-secondary)">{{ label }}</span>
      <span style="color: var(--ds-text-tertiary)">{{ value }}/{{ total }}</span>
    </div>
    <div style="background: var(--ds-surface-tertiary); height: 8px; border-radius: var(--ds-radius-full)">
      <div :class="colorClass" :style="{ width: percentage + '%', height: '8px', borderRadius: 'var(--ds-radius-full)', transition: 'width 0.5s ease' }" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ label: String, value: Number, total: Number, color: String })

const percentage = computed(() => props.total > 0 ? (props.value / props.total) * 100 : 0)
const colorClass = computed(() => ({
  green: 'bg-green-500', blue: 'bg-blue-500', yellow: 'bg-yellow-500', red: 'bg-red-500'
}[props.color] || 'bg-indigo-500'))
</script>
