<template>
  <div class="w-full">
    <!-- Bar Chart -->
    <div v-if="type === 'bar'" class="space-y-3">
      <div v-for="(bar, i) in data" :key="i" class="flex items-center gap-3 relative"
        @mousemove="onBarMove($event, i)"
        @mouseleave="onBarLeave(i)">
        <div class="w-24 text-xs font-medium truncate flex-shrink-0" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">
          {{ bar.label }}
        </div>
        <div class="flex-1 h-7 rounded-lg relative overflow-hidden" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-100'">
          <div class="h-full rounded-lg transition-all duration-700 ease-out flex items-center justify-end px-2"
            :style="{ width: bar.percent + '%', background: bar.color }">
            <span v-if="bar.percent > 15" class="text-xs font-medium text-white">{{ bar.value }}</span>
          </div>
        </div>
        <div class="w-10 text-xs font-medium text-right flex-shrink-0" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">
          {{ bar.percent }}%
        </div>
        <div v-if="tip.visible && tip.index === i"
          class="fixed px-2 py-1 rounded-lg text-xs font-medium whitespace-nowrap pointer-events-none z-50"
          :class="theme.isDark ? 'bg-gray-700 text-white' : 'bg-gray-800 text-white'"
          :style="{ left: tip.x + 'px', top: (tip.y - 32) + 'px', boxShadow: '0 4px 12px rgba(0,0,0,0.15)' }">
          {{ bar.value }} de {{ bar.total }} ({{ bar.percent }}%)
        </div>
      </div>
    </div>

    <!-- Donut Chart -->
    <div v-if="type === 'donut'" class="flex flex-col items-center">
      <svg :width="size" :height="size" viewBox="0 0 100 100"
        @mousemove="onDonutMove"
        @mouseleave="onDonutLeave">
        <circle cx="50" cy="50" r="40" fill="none"
          :stroke="theme.isDark ? '#1e293b' : '#f1f5f9'"
          :stroke-width="strokeWidth" />
        <path v-for="(slice, i) in slices" :key="i"
          :d="slice.path"
          :stroke="slice.color"
          fill="none"
          :stroke-width="strokeWidth"
          stroke-linecap="butt"
          class="transition-all duration-700 ease-out cursor-pointer"
          @mousemove.stop="tip = { visible: true, x: $event.clientX, y: $event.clientY, index: i }" />
        <template v-if="!hasData">
          <path d="M 70 50 A 40 40 0 1 1 69.99 50"
            :stroke="theme.isDark ? '#334155' : '#e2e8f0'"
            fill="none"
            :stroke-width="strokeWidth"
            stroke-dasharray="4 4" />
        </template>
      </svg>
      <div v-if="tip.visible && tip.index >= 0 && tip.index < slices.length"
        class="fixed px-2 py-1 rounded-lg text-xs font-medium whitespace-nowrap pointer-events-none z-50"
        :class="theme.isDark ? 'bg-gray-700 text-white' : 'bg-gray-800 text-white'"
        :style="{ left: tip.x + 'px', top: (tip.y - 32) + 'px', boxShadow: '0 4px 12px rgba(0,0,0,0.15)' }">
        {{ slices[tip.index].label }}: {{ Math.round(slices[tip.index].pct * 100) }}%
      </div>
      <div class="flex flex-wrap justify-center gap-3 mt-4">
        <div v-for="(item, i) in slices" :key="i" class="flex items-center gap-1.5 text-xs">
          <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: item.color }"></span>
          <span :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ item.label }}</span>
          <span class="font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-700'">{{ Math.round(item.pct * 100) }}%</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useThemeStore } from '../stores/theme'

const theme = useThemeStore()
const tip = ref({ visible: false, x: 0, y: 0, index: -1 })

const props = defineProps({
  type: { type: String, default: 'bar' },
  data: { type: Array, default: () => [] },
  size: { type: Number, default: 180 },
  strokeWidth: { type: Number, default: 12 },
})

function onBarMove(e, i) {
  tip.value = { visible: true, x: e.clientX, y: e.clientY, index: i }
}
function onBarLeave(i) {
  if (tip.value.index === i) tip.value.visible = false
}
function onDonutMove() {
  if (!tip.value.visible) tip.value = { ...tip.value, visible: false }
}
function onDonutLeave() {
  tip.value.visible = false
}

const total = computed(() => props.data.reduce((s, d) => s + (d.value || 0), 0))
const hasData = computed(() => total.value > 0)

function polar(cx, cy, r, deg) {
  const rad = (deg - 90) * Math.PI / 180
  return { x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) }
}

function arcPath(cx, cy, r, startDeg, endDeg) {
  const s = polar(cx, cy, r, endDeg)
  const e = polar(cx, cy, r, startDeg)
  const large = endDeg - startDeg > 180 ? 1 : 0
  return `M ${s.x} ${s.y} A ${r} ${r} 0 ${large} 0 ${e.x} ${e.y}`
}

const slices = computed(() => {
  if (props.type !== 'donut') return props.data
  let startAngle = 0
  return props.data.map(d => {
    const pct = total.value > 0 ? d.value / total.value : 0
    const sweep = pct * 360
    const endAngle = startAngle + sweep
    const path = pct > 0 ? arcPath(50, 50, 40, startAngle, endAngle) : ''
    startAngle = endAngle
    return { ...d, path, pct }
  })
})
</script>
