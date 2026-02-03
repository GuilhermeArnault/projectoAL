<template>
  <div class="min-h-screen flex flex-col font-sans text-dark">
    <Navbar />

    <!-- Hero Section -->
    <section class="relative h-[90vh]  flex items-center justify-center text-center text-white mt-20">
      <img
        src="/images/IMG_3528.JPG"
        alt="Paisagem de alojamento"
        class="absolute inset-0 w-full h-full object-cover brightness-75"
      />
      <div class="relative z-10">
        <h1 class="text-5xl font-bold mb-4">O Teu Refúgio Perfeito</h1>
        <p class="text-lg mb-6 max-w-2xl mx-auto">
          Desfruta de alojamentos únicos e acolhedores, onde conforto e natureza se encontram.
        </p>
        <a
          href="/alojamentos"
          class="bg-white text-[#603813] px-6 py-2 rounded-md font-semibold text-2xl hover:bg-black hover:text-white transition"
        >
          Reservar Agora
        </a>
      </div>
    </section>

    <!-- Sobre nós -->
    <section class="bg-white py-20 px-6 md:px-20 text-center md:text-left">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-4xl font-bold mb-6 text-dark">Alojamentos com Alma</h2>
        <p class="text-xl leading-relaxed text-dark/70">
          Descubra o lugar perfeito para desligar da rotina e respirar a tranquilidade da serra. No Marão à Vista, cada detalhe foi pensado para que desfrute de uma estadia acolhedora, confortável e com uma vista de cortar a respiração.
          Aqui, podes acordar com o silêncio da montanha, sentir o ar puro e apreciar o pôr-do sol sobre o Marão diretamente da varanda. A casa tem duas suítes e três quartos, uma
          cozinha, uma sala de estar com vista para a serra, e um pátio equipado com barbecue. Não esquecendo do jardim com piscina e com vista para a serra do marão.
          Pode ser alugada por quartos ou a casa toda. A casa fica situada em Poiares da Régua, entre a cidade do peso da régua e vila real. Fica muito perto dos miradouros de São Bernardo, Galafura e o da Formiga, Canelas.
          Seja para uma escapadinha romântica, uns dias em família ou simplesmente para recarregar energias, o Marão á Vista oferece o ambiente ideal. Próximo de trilhos, miradouros e pontos de interesse local, é a base perfeita para explorar o melhor da serra, ou simplesmente para não fazer nada e aproveitar a paz do lugar.
          Reserva já a tua estadia e deixa-te envolver pela magia da montanha.
          Marão à Vista ”onde a natureza entra pela janela.
        </p>
      </div>
    </section>

    <!-- Destaques -->
    <!-- <section class="bg-secondary text-dark py-20 px-6 md:px-20">
      <h2 class="text-3xl font-bold text-center mb-10">Os Nossos Destaques</h2>
      <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
        <div
          v-for="(item, index) in alojamentos"
          :key="index"
          class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition"
        >
          <img :src="item.imagem" alt="Alojamento" class="h-56 w-full object-cover" />
          <div class="p-6">
            <h3 class="text-xl font-semibold mb-2">{{ item.titulo }}</h3>
            <p class="text-dark/70 mb-4">{{ item.descricao }}</p>
            <p class="text-lg font-bold text-accent">
              {{ item.preco }}€/noite
            </p>
          </div>
        </div>
      </div>
    </section> -->
<div   class="
    relative
    w-full
    sm:w-[90%]
    md:w-[60%]
    lg:w-[28%]
    sm:h-[20%]
    md:h-[20%]
    lg:h-full
    mx-auto
    overflow-hidden
    rounded-xl
    shadow-lg
  ">

  <!-- ITEM ATUAL -->
  <div class="w-full h-full">
    <img
      :src="items[currentIndex].image"
      class="w-full h-full object-cover"
    />
  </div>

  <!-- BOTÃO ESQUERDA -->
  <button
    @click="prev"
    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/60 text-white p-3 rounded-full hover:bg-black"
  >
    ‹
  </button>

  <!-- BOTÃO DIREITA -->
  <button
    @click="next"
    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/60 text-white p-3 rounded-full hover:bg-black"
  >
    ›
  </button>

  <!-- INDICADORES -->
  <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
    <span
      v-for="(_, i) in items"
      :key="i"
      @click="currentIndex = i"
      class="w-3 h-3 rounded-full cursor-pointer"
      :class="currentIndex === i ? 'bg-white' : 'bg-white/50'"
    ></span>
  </div>

</div>

    <!-- Rodapé -->
    <footer class="bg-[#603813] text-white text-center py-6 text-sm">
      <p>© {{ new Date().getFullYear() }} Alojamento Local — Todos os direitos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
// Importa as funções do Vue
import { onMounted } from 'vue';
import axiosInstance from '../axiosFrontend';  // Importa a configuração do Axios
import Navbar from '../Components/NavBar.vue'; 

/* // Variáveis e dados diretamente no script setup
let alojamentos = [
  {
    titulo: "Quarto do Sol",
    descricao: "Vista deslumbrante sobre o vale e piscina privativa.",
    preco: 120,
    imagem: "/images/casa1.jpg",
  },
  {
    titulo: "Quarto Verde",
    descricao: "Ambiente natural e tranquilo, ideal para relaxar.",
    preco: 95,
    imagem: "/images/casa2.jpg",
  },
  {
    titulo: "Quarto Horizonte",
    descricao: "Design moderno e conforto com vistas incríveis.",
    preco: 150,
    imagem: "/images/casa3.jpg",
  },
]; */

// Função fetchData para fazer a chamada à API
const fetchData = async () => {
  try {
    const response = await axiosInstance.get('/home');
    message = response.data.message;  // Atualiza a variável
  } catch (error) {
    console.error('Erro ao obter dados:', error); // Exibe erros se houver falhas na requisição
  }
};

import { ref } from "vue"

const items = ref([
  { title: "Item 1", image: "/images/img1.jpg" },
  { title: "Item 2", image: "/images/img2.jpg" },
  { title: "Item 3", image: "/images/img3.jpg" },
])

const currentIndex = ref(0)

const next = () => {
  currentIndex.value =
    currentIndex.value < items.value.length - 1 ? currentIndex.value + 1 : 0
}

const prev = () => {
  currentIndex.value =
    currentIndex.value > 0 ? currentIndex.value - 1 : items.value.length - 1
}


// Usando onMounted para chamar fetchData quando o componente for montado
onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.text-dark {
  color: #603813;
}
.bg-dark {
  background-color: #616160;
}
.bg-primary {
  background-color: #603813;
}
.bg-secondary {
  background-color: #b9bda5;
}
.bg-accent {
  background-color: #ffffff;
}
</style>