<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-10">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Reserva o teu alojamento</h1>

    <form @submit.prevent="verificarDisponibilidade" class="bg-white p-6 rounded shadow-md w-full max-w-md space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Check-in</label>
        <input type="date" v-model="checkin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Check-out</label>
        <input type="date" v-model="checkout" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Hóspedes</label>
        <input type="number" v-model="hospedes" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
        Verificar Disponibilidade
      </button>
    </form>

    <div v-if="mensagem" class="mt-6 p-4 rounded text-center" :class="disponivel ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
      {{ mensagem }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const checkin = ref('');
const checkout = ref('');
const hospedes = ref(1);
const mensagem = ref('');
const disponivel = ref(false);

async function verificarDisponibilidade() {
  try {
    const alojamentoId = 1; // ID fixo por agora, podes mudar mais tarde
    const response = await axios.post(`/api/alojamentos/${alojamentoId}/available`, {
      checkin: checkin.value,
      checkout: checkout.value,
      hospedes: hospedes.value
    });

    disponivel.value = response.data.disponivel;
    mensagem.value = disponivel.value
      ? ' O alojamento está disponível!'
      : ' O alojamento não está disponível nas datas selecionadas.';
  } catch (error) {
    mensagem.value = ' Ocorreu um erro ao verificar a disponibilidade.';
    disponivel.value = false;
  }
}
</script>
