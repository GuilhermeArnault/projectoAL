<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
    <h1 class="text-5xl font-bold mb-4"> Bem-vindo ao Marão à Vista</h1>
    <p class="text-lg mb-6">Sistema de reservas online com Laravel, Vue 3 e Tailwind CSS</p>
    <a href="/reservas" class="bg-white text-indigo-700 px-6 py-3 rounded-lg shadow hover:bg-gray-100 transition">
      Fazer Reserva
    </a>
  </div>
    
</template>


<script setup>
function mostrarMensagem() {
  alert('Funcionalidade de reservas em breve ');
}
</script>
