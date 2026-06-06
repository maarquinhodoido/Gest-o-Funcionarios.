<template>
  <div :class="['min-h-screen', theme.isDark ? 'dark bg-gray-900' : 'bg-gray-50']">
    <template v-if="auth.isAuthenticated">
      <div class="flex h-screen overflow-hidden">
        <Sidebar />
        <div class="flex-1 flex flex-col overflow-hidden">
          <Header />
          <main class="flex-1 overflow-y-auto p-6">
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
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'
import Sidebar from './components/layout/Sidebar.vue'
import Header from './components/layout/Header.vue'

const auth = useAuthStore()
const theme = useThemeStore()
</script>
