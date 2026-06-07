<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="confirm.visible" class="fixed inset-0 z-[300] flex items-center justify-center p-4">
        <div class="fixed inset-0" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(4px)" @click="confirm.cancel()"></div>
        <div class="relative rounded-2xl shadow-xl border p-6 w-full max-w-sm animate-scale-in"
          style="background: var(--ds-surface); border-color: var(--ds-border); box-shadow: var(--ds-shadow-xl)">
          <div class="flex items-center gap-3 mb-4">
            <div class="p-2 rounded-xl" style="background: var(--ds-color-warning-bg)">
              <svg class="w-5 h-5" style="color: var(--ds-color-warning-text)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 class="text-lg font-semibold" style="color: var(--ds-text-primary)">{{ confirm.title }}</h3>
          </div>
          <p class="text-sm mb-6" style="color: var(--ds-text-secondary)">{{ confirm.message }}</p>
          <div class="flex justify-end gap-3">
            <button @click="confirm.cancel()" class="btn btn-ghost">Cancelar</button>
            <button @click="confirm.confirm()" class="btn btn-danger">Confirmar</button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useConfirmStore } from '../stores/confirm'
const confirm = useConfirmStore()
</script>
