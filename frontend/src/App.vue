<template>
  <div :class="['min-h-screen transition-colors duration-300', theme.isDark ? 'dark bg-gray-950' : 'bg-gray-50']">
    <template v-if="auth.isAuthenticated">
      <div class="flex h-screen overflow-hidden">
        <Sidebar />
        <div class="flex-1 flex flex-col overflow-hidden">
          <Header />
          <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <router-view v-slot="{ Component }">
              <Transition name="fade" mode="out-in">
                <component :is="Component" />
              </Transition>
            </router-view>
          </main>
        </div>
      </div>
    </template>
    <template v-else>
      <router-view v-slot="{ Component }">
        <Transition name="fade" mode="out-in">
          <component :is="Component" />
        </Transition>
      </router-view>
    </template>

    <!-- Toast Container -->
    <div class="fixed bottom-6 right-6 z-[200] flex flex-col gap-2 pointer-events-none">
      <TransitionGroup name="toast">
        <div v-for="t in toast.toasts" :key="t.id"
          class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border backdrop-blur-md min-w-[300px] max-w-[420px] animate-slide-up"
          :class="[t.type === 'error' ? 'bg-red-50 border-red-200 dark:bg-red-900/80 dark:border-red-800' : t.type === 'info' ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/80 dark:border-blue-800' : 'bg-green-50 border-green-200 dark:bg-green-900/80 dark:border-green-800']">
          <div class="p-1 rounded-lg flex-shrink-0"
            :class="[t.type === 'error' ? 'bg-red-100 dark:bg-red-800' : t.type === 'info' ? 'bg-blue-100 dark:bg-blue-800' : 'bg-green-100 dark:bg-green-800']">
            <svg v-if="t.type === 'success'" class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <svg v-else-if="t.type === 'error'" class="w-4 h-4 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            <svg v-else class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <p class="text-sm font-medium flex-1" :class="[t.type === 'error' ? 'text-red-800 dark:text-red-200' : t.type === 'info' ? 'text-blue-800 dark:text-blue-200' : 'text-green-800 dark:text-green-200']">{{ t.message }}</p>
          <button @click="toast.remove(t.id)" class="p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" :class="[t.type === 'error' ? 'text-red-400' : t.type === 'info' ? 'text-blue-400' : 'text-green-400']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'
import { useToastStore } from './stores/toast'
import Sidebar from './components/layout/Sidebar.vue'
import Header from './components/layout/Header.vue'
import ConfirmDialog from './components/ConfirmDialog.vue'

const auth = useAuthStore()
const theme = useThemeStore()
const toast = useToastStore()
</script>
