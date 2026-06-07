import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

function getInitialTheme() {
  const stored = localStorage.getItem('theme')
  if (stored === 'dark' || stored === 'light') return stored === 'dark'
  return window.matchMedia('(prefers-color-scheme: dark)').matches
}

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(getInitialTheme())

  watch(isDark, (val) => {
    localStorage.setItem('theme', val ? 'dark' : 'light')
    document.documentElement.classList.toggle('dark', val)
  })

  function toggle() {
    isDark.value = !isDark.value
  }

  if (isDark.value) {
    document.documentElement.classList.add('dark')
  }

  return { isDark, toggle }
})
