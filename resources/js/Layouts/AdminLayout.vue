<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#603813] text-white flex flex-col">
      <div class="p-4 text-xl font-bold border-b border-white/20">
        Painel Administrativo
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <Link
          :href="route('admin.dashboard')"
          class="block p-2 rounded hover:bg-white/10 transition"
        >
          Dashboard
        </Link>

        <Link
          :href="route('admin.reservas')"
          class="block p-2 rounded hover:bg-white/10 transition"
        >
          Reservas
        </Link>

        <Link
          :href="route('admin.utilizadores')"
          class="block p-2 rounded hover:bg-white/10 transition"
        >
          Utilizadores
        </Link>

        <Link
          :href="route('admin.alojamentos')"
          class="block p-2 rounded hover:bg-white/10 transition"
        >
          Alojamento
        </Link>

        <Link
          :href="route('admin.comentarios')"
          class="block p-2 rounded hover:bg-white/10 transition"
        >
          Comentários
        </Link>
      </nav>
    </aside>

    <!-- Conteúdo principal -->
    <main class="flex-1 p-6 overflow-y-auto">
      <header class="flex justify-between items-center mb-6">
        <h1 class="font-semibold text-[#616160]">
          {{ title }}
        </h1>

        <!-- Botão Home -->
        <Link
          href="/"
          class="
            px-4
            py-2
            bg-[#603813]
            text-white
            font-bold
            rounded
            transition
            hover:bg-[#4e2f10]
          "
        >
          Home
        </Link>
      </header>

      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: 'Painel Admin',
  },
})
</script>
