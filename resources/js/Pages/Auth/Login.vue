<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>


<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">

    <!-- Fundo rústico em imagem + cor base -->
    <div
      class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-30">
    </div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md bg-white shadow-xl rounded-xl p-8 border border-primary">
      
      <h2 class="text-3xl font-semibold text-center mb-4 text-dark">
        Bem-vindo(a)
      </h2>

      <p class="text-center text-dark/70 mb-8">
        Entre para gerir ou reservar o seu alojamento
      </p>

      <form @submit.prevent="submit" class="space-y-5">

        <div>
          <label class="block text-dark mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
          />
        </div>

        <div>
          <label class="block text-dark mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
          />
        </div>

        <div class="flex items-center justify-between text-dark">
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.remember" />
            <span>Lembrar-me</span>
          </label>
          <a href="/forgot-password" class="hover:underline text-dark">
            Esqueceu a password?
          </a>
        </div>

        <button
          type="submit"
          class="w-full py-2 bg-primary hover:bg-dark transition text-white rounded-md shadow-md"
        >
          Entrar
        </button>
      </form>

      <p class="text-center mt-6 text-dark">
        Ainda não tem conta?
        <a href="/register" class="text-accent font-semibold hover:underline">
          Criar conta
        </a>
      </p>
    </div>

  </div>
</template>
