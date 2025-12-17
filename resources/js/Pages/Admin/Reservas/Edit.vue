<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import backend from '@/axiosBackend'

const props = defineProps({
  id: {
    type: [Number, String],
    required: true,
  },
})

const form = ref({
  user_id: '',
  alojamento_id: '',
  checkin: '',
  checkout: '',
  hospedes: 1,
  estado: 'pendente',
  observacoes: '',
})

const estados = [
  'pendente',
  'confirmado',
  'concluido',
  'cancelado',
  'expirado',
]

const users = ref([])
const alojamentos = ref([])

const errors = ref({})
const loading = ref(true)
const saving = ref(false)

const fetchOptions = async () => {
  try {
    const [usersRes, alojRes] = await Promise.all([
      backend.get('/utilizadores', { params: { per_page: 200 } }),
      backend.get('/alojamentos', { params: { per_page: 200 } }),
    ])

    users.value = Array.isArray(usersRes.data?.data)
      ? usersRes.data.data
      : []

    alojamentos.value = Array.isArray(alojRes.data?.data)
      ? alojRes.data.data
      : []
  } catch (error) {
    console.error('Erro ao carregar opções:', error)
  }
}

const fetchReserva = async () => {
  try {
    const res = await backend.get(`/reservas/${props.id}`)
    const r = res.data

    form.value = {
      user_id: r.user_id,
      alojamento_id: r.alojamento_id,
      checkin: r.checkin,
      checkout: r.checkout,
      hospedes: r.hospedes,
      estado: r.estado,
      observacoes: r.observacoes || '',
    }
  } catch (error) {
    console.error('Erro ao carregar reserva:', error)
  }
}

const loadAll = async () => {
  loading.value = true
  errors.value = {}

  try {
    await fetchOptions()
    await fetchReserva()
  } finally {
    loading.value = false
  }
}

const submit = async () => {
  saving.value = true
  errors.value = {}

  try {
    await backend.put(`/reservas/${props.id}`, {
      user_id: form.value.user_id,
      alojamento_id: form.value.alojamento_id,
      checkin: form.value.checkin,
      checkout: form.value.checkout,
      hospedes: form.value.hospedes,
      estado: form.value.estado,
      observacoes: form.value.observacoes,
    })

    // sem Ziggy: caminho direto
    router.visit('/admin/reservas')
  } catch (error) {
    console.error('Erro ao atualizar reserva:', error)
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    }
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadAll()
})
</script>

<template>
  <AdminLayout title="Editar Reserva">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800">
          Editar Reserva #{{ props.id }}
        </h1>

        <button
          type="button"
          class="text-sm text-gray-600 hover:text-gray-800"
          @click="router.visit('/admin/reservas')"
        >
          ← Voltar à lista
        </button>
      </div>

      <div v-if="loading" class="text-sm text-gray-500">
        A carregar dados da reserva...
      </div>

      <form
        v-else
        class="space-y-4"
        @submit.prevent="submit"
      >
        <!-- Cliente -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Cliente
          </label>
          <select
            v-model="form.user_id"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Selecione um utilizador</option>
            <option
              v-for="u in users"
              :key="u.id"
              :value="u.id"
            >
              {{ u.name }} ({{ u.email }})
            </option>
          </select>
          <p
            v-if="errors.user_id"
            class="text-xs text-red-600 mt-1"
          >
            {{ errors.user_id[0] }}
          </p>
        </div>

        <!-- Alojamento -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Alojamento
          </label>
          <select
            v-model="form.alojamento_id"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Selecione um alojamento</option>
            <option
              v-for="a in alojamentos"
              :key="a.id"
              :value="a.id"
            >
              {{ a.titulo }}
            </option>
          </select>
          <p
            v-if="errors.alojamento_id"
            class="text-xs text-red-600 mt-1"
          >
            {{ errors.alojamento_id[0] }}
          </p>
        </div>

        <!-- Datas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Check-in
            </label>
            <input
              v-model="form.checkin"
              type="date"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <p
              v-if="errors.checkin"
              class="text-xs text-red-600 mt-1"
            >
              {{ errors.checkin[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Check-out
            </label>
            <input
              v-model="form.checkout"
              type="date"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <p
              v-if="errors.checkout"
              class="text-xs text-red-600 mt-1"
            >
              {{ errors.checkout[0] }}
            </p>
          </div>
        </div>

        <!-- Hóspedes + Estado -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Nº de hóspedes
            </label>
            <input
              v-model.number="form.hospedes"
              type="number"
              min="1"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <p
              v-if="errors.hospedes"
              class="text-xs text-red-600 mt-1"
            >
              {{ errors.hospedes[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Estado
            </label>
            <select
              v-model="form.estado"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option
                v-for="e in estados"
                :key="e"
                :value="e"
              >
                {{ e }}
              </option>
            </select>
            <p
              v-if="errors.estado"
              class="text-xs text-red-600 mt-1"
            >
              {{ errors.estado[0] }}
            </p>
          </div>
        </div>

        <!-- Observações -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Observações
          </label>
          <textarea
            v-model="form.observacoes"
            rows="3"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          ></textarea>
          <p
            v-if="errors.observacoes"
            class="text-xs text-red-600 mt-1"
          >
            {{ errors.observacoes[0] }}
          </p>
        </div>

        <!-- Botões -->
        <div class="flex justify-end gap-2 pt-4 border-t mt-4">
          <button
            type="button"
            class="px-4 py-2 text-sm rounded border border-gray-300 text-gray-700 hover:bg-gray-100"
            @click="router.visit('/admin/reservas')"
          >
            Cancelar
          </button>

          <button
            type="submit"
            class="px-4 py-2 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60"
            :disabled="saving"
          >
            {{ saving ? 'A guardar...' : 'Guardar Alterações' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
