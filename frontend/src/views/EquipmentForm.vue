<template>
  <div :class="['max-w-2xl mx-auto rounded-xl border p-8', theme.isDark ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200']">
    <h2 :class="['text-xl font-semibold mb-6', theme.isDark ? 'text-white' : 'text-gray-900']">
      {{ isEdit ? 'Editar Equipamento' : 'Novo Equipamento' }}
    </h2>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Marca</label>
          <input v-model="form.brand" required
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Modelo</label>
          <input v-model="form.model" required
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nº Série</label>
          <input v-model="form.serial_number" required
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Tipo</label>
          <select v-model="form.equipment_type_id" required
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
            <option value="">Selecionar...</option>
            <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Fornecedor</label>
          <input v-model="form.supplier"
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Compra</label>
          <input v-model="form.purchase_date" type="date"
            class="w-full px-4 py-2 rounded-lg border outline-none"
            :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Notas</label>
        <textarea v-model="form.notes" rows="3"
          class="w-full px-4 py-2 rounded-lg border outline-none"
          :class="theme.isDark ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'" />
      </div>

      <div class="flex gap-4">
        <button type="submit"
          class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">
          {{ isEdit ? 'Atualizar' : 'Criar Equipamento' }}
        </button>
        <router-link to="/equipment"
          class="px-6 py-2.5 border rounded-lg text-sm font-medium"
          :class="theme.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
          Cancelar
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useThemeStore } from '../stores/theme'
import api from '../lib/axios'

const route = useRoute()
const router = useRouter()
const theme = useThemeStore()

const isEdit = computed(() => !!route.params.id)
const types = ref([])
const form = ref({ brand: '', model: '', serial_number: '', equipment_type_id: '', supplier: '', purchase_date: '', notes: '' })

onMounted(async () => {
  try {
    const res = await api.get('/v1/roles/permissions') // placeholder - should be equipment types endpoint
    types.value = [
      { id: 1, name: 'Computador' }, { id: 2, name: 'Portátil' }, { id: 3, name: 'Telemóvel' },
      { id: 4, name: 'Impressora' }, { id: 5, name: 'Scanner' }, { id: 6, name: 'Tablet' },
      { id: 7, name: 'Monitor' }, { id: 8, name: 'Licença' },
    ]
  } catch (e) { console.error(e) }

  if (isEdit.value) {
    try {
      const res = await api.get(`/v1/equipment/${route.params.id}`)
      const eq = res.data.data
      form.value = {
        brand: eq.brand, model: eq.model, serial_number: eq.serial_number,
        equipment_type_id: eq.equipment_type_id || '', supplier: eq.supplier || '',
        purchase_date: eq.purchase_date || '', notes: eq.notes || ''
      }
    } catch (e) { console.error(e) }
  }
})

async function handleSubmit() {
  try {
    if (isEdit.value) {
      await api.put(`/v1/equipment/${route.params.id}`, form.value)
    } else {
      await api.post('/v1/equipment', form.value)
    }
    router.push('/equipment')
  } catch (e) { console.error(e) }
}
</script>
