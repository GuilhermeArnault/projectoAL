<script setup>
import { ref, onMounted } from "vue";
import axios from "@/axiosFrontend";
import Navbar from "@/Components/NavBar.vue";

const reservas = ref([]);
const erro = ref(null);

onMounted(async () => {
  try {
    const res = await axios.get("/reservas/me");
    reservas.value = res.data;
  } catch (e) {
    erro.value = "Erro ao obter reservas.";
  }
});
</script>

<template>
  <Navbar />

  <div class="max-w-4xl mx-auto mt-28 px-4">
    <h1 class="text-3xl font-bold text-dark mb-6">
      As minhas reservas
    </h1>

    <!-- ERRO -->
    <p v-if="erro" class="text-red-600">
      {{ erro }}
    </p>

    <!-- SEM RESERVAS -->
    <p v-else-if="reservas.length === 0" class="text-gray-600">
      Não tens reservas.
    </p>

    <!-- LISTA DE RESERVAS -->
    <div
      v-else
      v-for="reserva in reservas"
      :key="reserva.id"
      class="bg-white shadow rounded p-6 mb-4"
    >
      <h2
        v-if="reserva.alojamento"
        class="text-xl font-semibold text-dark"
      >
        {{ reserva.alojamento.titulo }}
      </h2>

      <p class="text-gray-600">
        {{ reserva.checkin }} → {{ reserva.checkout }}
      </p>

      <p class="mt-2 font-bold">
        Total: {{ reserva.total }} €
      </p>

      <p
        class="mt-1 font-medium"
        :class="{
          'text-yellow-600': reserva.estado === 'pendente',
          'text-green-600': reserva.estado === 'confirmada',
          'text-red-600': reserva.estado === 'cancelada'
        }"
      >
        Estado: {{ reserva.estado }}
      </p>
        <!-- BOTÃO PAGAMENTO -->
      <button
    v-if="reserva.estado === 'pendente'"
    @click="pagar(reserva.id)"
    class="mt-4 bg-accent text-dark px-5 py-2 rounded font-semibold hover:bg-yellow-300"
  >
    Pagar agora
  </button>

    </div>
  </div>
</template>

<style scoped>
.text-dark {
  color: #616160;
}
</style>
