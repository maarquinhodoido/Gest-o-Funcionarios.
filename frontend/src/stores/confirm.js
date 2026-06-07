import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useConfirmStore = defineStore('confirm', () => {
  const visible = ref(false)
  const message = ref('')
  const title = ref('Confirmar')
  let resolveCallback = null

  function show(msg, config = {}) {
    message.value = msg
    title.value = config.title || 'Confirmar'
    visible.value = true
    return new Promise((resolve) => {
      resolveCallback = resolve
    })
  }

  function confirm() {
    visible.value = false
    if (resolveCallback) resolveCallback(true)
  }

  function cancel() {
    visible.value = false
    if (resolveCallback) resolveCallback(false)
  }

  return { visible, message, title, show, confirm, cancel }
})
