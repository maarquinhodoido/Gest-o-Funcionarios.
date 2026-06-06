<template>
  <TransitionRoot appear :show="modelValue" as="template">
    <Dialog as="div" @close="close" class="relative z-50">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              :class="[
                'w-full rounded-xl border shadow-xl overflow-hidden',
                maxWidthClass,
                theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'
              ]"
            >
              <div v-if="title" :class="['flex items-center justify-between px-6 py-4 border-b', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
                <DialogTitle :class="['text-lg font-semibold', theme.isDark ? 'text-white' : 'text-gray-900']">
                  {{ title }}
                </DialogTitle>
                <button @click="close" :class="['p-1 rounded-lg transition-colors', theme.isDark ? 'hover:bg-gray-700 text-gray-400' : 'hover:bg-gray-100 text-gray-500']">
                  <XMarkIcon class="w-5 h-5" />
                </button>
              </div>
              <div class="p-6">
                <slot />
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed } from 'vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useThemeStore } from '../stores/theme'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: '' },
  maxWidth: { type: String, default: 'md' },
})

const emit = defineEmits(['update:modelValue'])

const theme = useThemeStore()

const maxWidthClass = computed(() => ({
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  '2xl': 'max-w-2xl',
  '3xl': 'max-w-3xl',
  '4xl': 'max-w-4xl',
  '5xl': 'max-w-5xl',
}[props.maxWidth] || 'max-w-md'))

function close() {
  emit('update:modelValue', false)
}
</script>
