<script setup>
import { useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
  email: String,
  postUrl: String,
})

const form = useForm({
  phone: '',
  nif: '',
})

const onlyDigits = (v) => (v || '').replace(/\D/g, '')

const onNifInput = (e) => {
  const digits = onlyDigits(e.target.value).slice(0, 9)
  form.nif = digits
}

const onPhoneInput = (e) => {
  // deixa só dígitos; se quiseres permitir +, diz-me que eu ajusto
  form.phone = (e.target.value || '').replace(/[^\d]/g, '').slice(0, 20)
}

const submit = () => {
  if (!props.postUrl) return
  form.post(props.postUrl, {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">
    <!-- Fundo rústico -->
    <div
      class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1529692236671-f1d6f09c09b0?auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-30">
    </div>

    <!-- Card principal -->
    <div class="relative z-10 w-full max-w-md bg-white shadow-xl rounded-xl p-8 border border-primary">
      <h2 class="text-3xl font-semibold text-center mb-4 text-dark">
        Concluir Registo
      </h2>

      <p class="text-center text-dark/70 mb-8">
        Confirmação feita para:
        <span class="font-semibold">{{ email }}</span>
      </p>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-dark mb-1">Telemóvel</label>
          <input
            v-model="form.phone"
            @input="onPhoneInput"
            inputmode="numeric"
            autocomplete="tel"
            type="text"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            placeholder="Ex: 912345678"
            required
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <div>
          <label class="block text-dark mb-1">NIF</label>
          <input
            :value="form.nif"
            @input="onNifInput"
            inputmode="numeric"
            autocomplete="off"
            type="text"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            placeholder="9 dígitos"
            required
          />
          <InputError class="mt-2" :message="form.errors.nif" />
        </div>

        <button
          type="submit"
          class="w-full py-2 bg-primary hover:bg-dark transition text-white rounded-md shadow-md"
          :disabled="form.processing || !postUrl"
          :class="{ 'opacity-60 cursor-not-allowed': form.processing || !postUrl }"
        >
          <span v-if="form.processing">A enviar...</span>
          <span v-else>Concluir Registo</span>
        </button>

        <p v-if="form.recentlySuccessful" class="text-center text-dark/70 text-sm">
          Registo concluído com sucesso.
        </p>
      </form>
    </div>
  </div>
</template>