import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(localStorage.getItem('theme') === 'dark')

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
