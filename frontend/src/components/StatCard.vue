<template>
  <div class="card card-hover">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium" style="color: var(--ds-text-tertiary)">{{ title }}</p>
        <p class="text-2xl font-bold mt-1" style="color: var(--ds-text-primary)">{{ value }}</p>
        <p v-if="subtitle" class="text-xs mt-0.5" style="color: var(--ds-text-tertiary)">{{ subtitle }}</p>
      </div>
      <div :class="['p-2.5 rounded-xl', colorClasses]" style="box-shadow: 0 2px 8px rgba(0,0,0,0.04)">
        <component :is="iconComponent" class="w-5 h-5 text-white" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { UsersIcon, ComputerDesktopIcon, CubeIcon, ExclamationTriangleIcon, BuildingOfficeIcon, WrenchScrewdriverIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String, value: [Number, String], subtitle: String,
  icon: String, color: String
})

const icons = {
  users: UsersIcon, computer: ComputerDesktopIcon, cube: CubeIcon,
  exclamation: ExclamationTriangleIcon, building: BuildingOfficeIcon,
  wrench: WrenchScrewdriverIcon
}
const iconComponent = computed(() => icons[props.icon] || CubeIcon)

const colorClasses = computed(() => ({
  blue: 'bg-blue-500', green: 'bg-green-500', indigo: 'bg-indigo-500',
  red: 'bg-red-500', yellow: 'bg-yellow-500', purple: 'bg-purple-500'
}[props.color] || 'bg-indigo-500'))
</script>
