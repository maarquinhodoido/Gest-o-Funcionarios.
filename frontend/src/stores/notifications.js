import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../lib/axios'

export const useNotificationStore = defineStore('notifications', () => {
  const items = ref([])
  const unreadCount = ref(0)
  const loading = ref(false)
  let pollTimer = null

  const hasUnread = computed(() => unreadCount.value > 0)

  async function fetchUnreadCount() {
    try {
      const res = await api.get('/v1/notifications/unread-count')
      unreadCount.value = res.data.data?.unread_count || 0
    } catch { /* ignore */ }
  }

  async function fetchNotifications(filters = {}) {
    loading.value = true
    try {
      const [listRes] = await Promise.all([
        api.get('/v1/notifications', { params: { per_page: 20, ...filters } }),
        fetchUnreadCount(),
      ])
      items.value = listRes.data.data || listRes.data.items || []
    } catch { /* ignore */ }
    finally { loading.value = false }
  }

  async function markAsRead(id) {
    try {
      await api.post(`/v1/notifications/${id}/read`)
      const note = items.value.find(n => n.id === id)
      if (note) { note.is_read = true; note.read_at = new Date().toISOString() }
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch { /* ignore */ }
  }

  async function markAllAsRead() {
    try {
      await api.post('/v1/notifications/mark-all-read')
      items.value.forEach(n => { n.is_read = true; n.read_at = new Date().toISOString() })
      unreadCount.value = 0
    } catch { /* ignore */ }
  }

  function startPolling(interval = 30000) {
    stopPolling()
    fetchUnreadCount()
    if (!document.hidden) pollTimer = setInterval(fetchUnreadCount, interval)
    document.addEventListener('visibilitychange', onVisibilityChange)
  }

  function onVisibilityChange() {
    if (document.hidden) { stopPolling() }
    else { fetchUnreadCount(); startPolling() }
  }

  function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null }
    document.removeEventListener('visibilitychange', onVisibilityChange)
  }

  return {
    items, unreadCount, loading, hasUnread,
    fetchUnreadCount, fetchNotifications, markAsRead, markAllAsRead,
    startPolling, stopPolling,
  }
})
