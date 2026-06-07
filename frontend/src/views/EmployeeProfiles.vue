<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold tracking-tight" style="color: var(--ds-text-primary)">Funcionários</h1>
      <button @click="openCreateModal" class="btn btn-primary">+ Novo Perfil</button>
    </div>

    <div class="table-wrap">
      <table class="ds">
        <thead>
          <tr>
            <th>Referência</th>
            <th>Nome</th>
            <th>NIF</th>
            <th>Documento</th>
            <th>Nº Documento</th>
            <th>Validade</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in profiles" :key="p.id">
            <td class="px-6 py-4 text-sm font-mono" :class="theme.isDark ? 'text-gray-400' : 'text-gray-500'">{{ p.reference || '-' }}</td>
            <td class="px-6 py-4 text-sm font-medium" :class="theme.isDark ? 'text-gray-200' : 'text-gray-900'">{{ p.name }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ p.nif || '-' }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ p.document_type === 'BI' ? 'Bilhete de Identificação' : p.document_type === 'AR' ? 'Autorização de Residência' : '-' }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ p.document_number || '-' }}</td>
            <td class="px-6 py-4 text-sm" :class="theme.isDark ? 'text-gray-300' : 'text-gray-600'">{{ p.document_expiry_date || '-' }}</td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button @click="openDetail(p)" class="btn btn-sm btn-ghost">Detalhes</button>
                <button @click="openEditModal(p)" class="btn btn-sm btn-secondary">Gerir</button>
                <button @click="handleDelete(p.id)" class="btn btn-sm btn-danger">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal v-model="showModal" :title="editing ? 'Editar Perfil' : 'Novo Perfil'" max-width="2xl">
      <form @submit.prevent="handleSave" class="space-y-6">
        <div v-if="errorMsg" class="p-3 rounded-lg text-sm bg-red-100 text-red-700 border border-red-300">{{ errorMsg }}</div>
        <div>
          <h4 :class="['text-md font-semibold mb-4', theme.isDark ? 'text-white' : 'text-gray-900']">Dados do Funcionário</h4>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nome</label>
              <input v-model="form.name" required class="input" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">NIF</label>
              <input v-model="form.nif" maxlength="20" class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Nascimento</label>
              <input v-model="form.birth_date" type="date" class="input" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Telefone</label>
              <input v-model="form.phone" maxlength="20" class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">NISS</label>
              <input v-model="form.niss" maxlength="20" class="input" />
            </div>
          </div>
        </div>

        <div :class="['border-t pt-6', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Cargo do Funcionário</label>
          <select v-model="form.position" class="input">
            <option value="">Selecionar...</option>
            <option value="Administrador">Administrador</option>
            <option value="Gestor de TI">Gestor de TI</option>
            <option value="Técnico de Informática">Técnico de Informática</option>
            <option value="Analista de Sistemas">Analista de Sistemas</option>
            <option value="Desenvolvedor">Desenvolvedor</option>
            <option value="Gestor de Equipamentos">Gestor de Equipamentos</option>
            <option value="Chefe de Departamento">Chefe de Departamento</option>
            <option value="Recursos Humanos">Recursos Humanos</option>
            <option value="Financeiro">Financeiro</option>
            <option value="Operador">Operador</option>
            <option value="Outro">Outro</option>
          </select>
        </div>

        <div :class="['border-t pt-6', theme.isDark ? 'border-gray-700' : 'border-gray-200']">
          <h4 :class="['text-md font-semibold mb-4', theme.isDark ? 'text-white' : 'text-gray-900']">Documento de Identificação</h4>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Tipo de Documento</label>
              <select v-model="form.document_type" class="input">
                <option value="">Selecionar...</option>
                <option value="BI">Bilhete de Identificação</option>
                <option value="AR">Autorização de Residência</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Nº do Documento</label>
              <input v-model="form.document_number" maxlength="50" class="input" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Emissão</label>
              <input v-model="form.document_issue_date" type="date" class="input" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" :class="theme.isDark ? 'text-gray-300' : 'text-gray-700'">Data de Validade</label>
              <input v-model="form.document_expiry_date" type="date" class="input" />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" @click="showModal = false" class="btn btn-ghost">Cancelar</button>
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'A guardar...' : (editing ? 'Atualizar' : 'Criar Perfil') }}
          </button>
        </div>
      </form>
    </Modal>
  </div>

    <Modal v-model="showDetailModal" title="Detalhes do Funcionário" max-width="lg">
      <div v-if="detailItem" class="space-y-6">
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl" :class="theme.isDark ? 'bg-gray-800' : 'bg-gray-50'">
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Referência</p><p class="text-sm font-medium" style="color: var(--ds-text-primary)">{{ detailItem.reference || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Nome</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.name }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">NIF</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.nif || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Data Nascimento</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.birth_date || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Telefone</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.phone || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">NISS</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.niss || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Cargo</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.position || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Tipo Documento</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.document_type === 'BI' ? 'Bilhete de Identificação' : detailItem.document_type === 'AR' ? 'Autorização de Residência' : '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Nº Documento</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.document_number || '-' }}</p></div>
          <div><p class="text-xs font-medium mb-1" style="color: var(--ds-text-tertiary)">Validade Documento</p><p class="text-sm" style="color: var(--ds-text-primary)">{{ detailItem.document_expiry_date || '-' }}</p></div>
        </div>
        <div class="flex justify-end pt-2">
          <button @click="showDetailModal = false" class="btn btn-ghost">Fechar</button>
        </div>
      </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useThemeStore } from '../stores/theme'
import { useToastStore } from '../stores/toast'
import { useConfirmStore } from '../stores/confirm'
import api from '../lib/axios'
import Modal from '../components/Modal.vue'

const theme = useThemeStore()
const toast = useToastStore()
const confirm = useConfirmStore()
const profiles = ref([])
const showModal = ref(false)
const editing = ref(false)
const editingId = ref(null)
const saving = ref(false)
const errorMsg = ref('')
const form = ref({ name: '', nif: '', birth_date: '', phone: '', niss: '', document_type: '', document_number: '', document_issue_date: '', document_expiry_date: '', position: '' })
const showDetailModal = ref(false)
const detailItem = ref(null)

function openDetail(item) {
  detailItem.value = item
  showDetailModal.value = true
}

async function loadProfiles() {
  try {
    const res = await api.get('/v1/employee-profiles')
    profiles.value = res.data.items || res.data.data || []
  } catch (e) { console.error(e) }
}

function openCreateModal() {
  editing.value = false
  errorMsg.value = ''
  form.value = { name: '', nif: '', birth_date: '', phone: '', niss: '', document_type: '', document_number: '', document_issue_date: '', document_expiry_date: '', position: '' }
  showModal.value = true
}

function openEditModal(profile) {
  editing.value = true
  editingId.value = profile.id
  errorMsg.value = ''
  form.value = {
    name: profile.name, nif: profile.nif || '', birth_date: profile.birth_date || '',
    phone: profile.phone || '', niss: profile.niss || '',
    document_type: profile.document_type || '', document_number: profile.document_number || '',
    document_issue_date: profile.document_issue_date || '',
    document_expiry_date: profile.document_expiry_date || '',
    position: profile.position || '',
  }
  showModal.value = true
}

async function handleSave() {
  saving.value = true
  errorMsg.value = ''
  try {
    if (editing.value) {
      await api.put(`/v1/employee-profiles/${editingId.value}`, form.value)
    } else {
      await api.post('/v1/employee-profiles', form.value)
    }
    showModal.value = false
    await loadProfiles()
    toast.success(editing.value ? 'Perfil atualizado' : 'Perfil criado')
  } catch (e) {
    errorMsg.value = e.response?.data?.message || e.response?.data?.error || 'Erro ao guardar. Verifique os dados e tente novamente.'
    if (e.response?.data?.errors) {
      const msgs = Object.values(e.response.data.errors).flat()
      errorMsg.value = msgs.join('\n')
    }
  } finally {
    saving.value = false
  }
}

async function handleDelete(id) {
  const ok = await confirm.show('Tem a certeza que pretende eliminar este perfil?')
  if (!ok) return
  try {
    await api.delete(`/v1/employee-profiles/${id}`)
    await loadProfiles()
    toast.success('Perfil eliminado')
  } catch (e) { console.error(e) }
}

onMounted(loadProfiles)
</script>
