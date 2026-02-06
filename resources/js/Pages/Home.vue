<template>
  <div class="min-h-screen flex flex-col font-sans text-dark">
    <Navbar />

    <!-- Hero Section -->
    <section class="relative h-[90vh] flex items-center justify-center text-center text-white mt-20">
      <img
        src="/images/IMG_3528.JPG"
        alt="Paisagem de alojamento"
        class="absolute inset-0 w-full h-full object-cover brightness-75"
      />
      <div class="relative z-10 px-4">
        <h1 class="text-5xl font-bold mb-4 reveal r1">O Teu Refúgio Perfeito</h1>
        <p class="text-lg mb-6 max-w-2xl mx-auto reveal r2">
          Desfruta de alojamentos únicos e acolhedores, onde conforto e natureza se encontram.
        </p>
        <a
          href="/alojamentos"
          class="bg-white text-[#603813] px-6 py-2 rounded-md font-semibold text-2xl hover:bg-black hover:text-white transition reveal r3"
        >
          Reservar Agora
        </a>
      </div>
    </section>

    <!-- Sobre nós -->
    <section class="bg-white py-20 px-6 md:px-20 text-center md:text-left">
      <div class="max-w-5xl mx-auto">
        <h2
          class="text-4xl font-bold mb-6 text-dark fade-up"
          :class="{ 'in-view': aboutInView }"
          ref="aboutTitleRef"
        >
          Alojamentos com Alma
        </h2>

        <p
          class="text-xl leading-relaxed text-dark/70 fade-up delay-150"
          :class="{ 'in-view': aboutInView }"
          ref="aboutTextRef"
        >
          Descubra o lugar perfeito para desligar da rotina e respirar a tranquilidade da serra. No Marão à Vista, cada detalhe foi pensado para que desfrute de uma estadia acolhedora, confortável e com uma vista de cortar a respiração.
          Aqui, podes acordar com o silêncio da montanha, sentir o ar puro e apreciar o pôr-do sol sobre o Marão diretamente da varanda. A casa tem duas suítes e três quartos, uma
          cozinha, uma sala de estar com vista para a serra, e um pátio equipado com barbecue. Não esquecendo do jardim com piscina e com vista para a serra do marão.
          Pode ser alugada por quartos ou a casa toda. A casa fica situada em Poiares da Régua, entre a cidade do peso da régua e vila real. Fica muito perto dos miradouros de São Leornardo, Galafura e o da Formiga, Canelas.
          Seja para uma escapadinha romântica, uns dias em família ou simplesmente para recarregar energias, o Marão á Vista oferece o ambiente ideal. Próximo de trilhos, miradouros e pontos de interesse local, é a base perfeita para explorar o
          melhor da serra, ou simplesmente para não fazer nada e aproveitar a paz do lugar. Reserva já a tua estadia e deixa-te envolver pela magia da montanha.
          Marão à Vista ”onde a natureza entra pela janela.
        </p>
      </div>
    </section>

    <!-- Slider elegante em modo CARD -->
    <section class="bg-white pb-24 px-6">
      <div class="max-w-6xl mx-auto flex justify-center">
        <!-- ✅ card aparece ao scroll, igual ao texto -->
        <div
          ref="cardRef"
          class="
            relative
            w-full
            max-w-[320px]
            sm:max-w-[380px]
            md:max-w-[480px]
            aspect-[4/5]
            rounded-3xl
            overflow-hidden
            shadow-xl
            transition
            duration-500
            hover:scale-[1.015]
            fade-up
          "
          :class="{ 'in-view': cardInView }"
        >
          <!-- ✅ SEM FADE entre imagens -->
          <img
            :src="items[currentIndex].image"
            alt="Imagem do alojamento"
            class="absolute inset-0 w-full h-full object-cover"
          />

          <!-- Overlay suave -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>

          <!-- BOTÃO ESQUERDA -->
          <button
            @click="prev"
            class="
              absolute
              left-4
              top-1/2
              -translate-y-1/2
              w-11
              h-11
              rounded-full
              bg-black/50
              backdrop-blur-sm
              text-white
              flex
              items-center
              justify-center
              text-xl
              hover:bg-black
              transition
            "
            aria-label="Anterior"
          >
            ‹
          </button>

          <!-- BOTÃO DIREITA -->
          <button
            @click="next"
            class="
              absolute
              right-4
              top-1/2
              -translate-y-1/2
              w-11
              h-11
              rounded-full
              bg-black/50
              backdrop-blur-sm
              text-white
              flex
              items-center
              justify-center
              text-xl
              hover:bg-black
              transition
            "
            aria-label="Seguinte"
          >
            ›
          </button>

          <!-- INDICADORES -->
          <div
            class="
              absolute
              bottom-5
              left-1/2
              -translate-x-1/2
              flex
              gap-2
              px-4
              py-2
              rounded-full
              bg-black/40
              backdrop-blur-sm
            "
          >
            <span
              v-for="(_, i) in items"
              :key="i"
              @click="currentIndex = i"
              class="w-2.5 h-2.5 rounded-full cursor-pointer transition"
              :class="currentIndex === i ? 'bg-white' : 'bg-white/50'"
            ></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Rodapé -->
    <footer class="bg-[#603813] text-white text-center py-6 text-sm">
      <p>© {{ new Date().getFullYear() }} Alojamento Local — Todos os direitos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue"
import axiosInstance from "../axiosFrontend"
import Navbar from "../Components/NavBar.vue"

const message = ref("")

const fetchData = async () => {
  try {
    const response = await axiosInstance.get("/home")
    message.value = response.data.message
  } catch (error) {
    console.error("Erro ao obter dados:", error)
  }
}

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

/* =========
   REVEAL AO SCROLL (Sobre nós + Card)
   ========= */
const aboutInView = ref(false)
const cardInView = ref(false)

const aboutTitleRef = ref(null)
const aboutTextRef = ref(null)
const cardRef = ref(null)

let observer

onMounted(() => {
  fetchData()

  observer = new IntersectionObserver(
    (entries) => {
      for (const e of entries) {
        if (!e.isIntersecting) continue

        if (e.target === aboutTitleRef.value || e.target === aboutTextRef.value) {
          aboutInView.value = true
        }
        if (e.target === cardRef.value) {
          cardInView.value = true
        }
      }
    },
    { threshold: 0.15, rootMargin: "0px 0px -10% 0px" }
  )

  if (aboutTitleRef.value) observer.observe(aboutTitleRef.value)
  if (aboutTextRef.value) observer.observe(aboutTextRef.value)
  if (cardRef.value) observer.observe(cardRef.value)
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})
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

/* =========================
   HERO REVEAL (igual)
========================= */
.reveal {
  opacity: 0;
  transform: translateY(14px);
  filter: blur(6px);
  animation: heroReveal 1.05s cubic-bezier(.2,.8,.2,1) forwards;
  will-change: opacity, transform, filter;
}
.reveal.r1 { animation-delay: .45s; }
.reveal.r2 { animation-delay: .75s; }
.reveal.r3 { animation-delay: 1.05s; }

@keyframes heroReveal {
  to { opacity: 1; transform: translateY(0); filter: blur(0); }
}

/* =========================
   REVEAL AO SCROLL (texto + card)
========================= */
.fade-up {
  opacity: 0;
  transform: translateY(18px);
  filter: blur(2px);
  transition: opacity .7s ease, transform .7s ease, filter .7s ease;
  will-change: opacity, transform, filter;
}
.fade-up.in-view {
  opacity: 1;
  transform: translateY(0);
  filter: blur(0);
}
.delay-150 { transition-delay: 150ms; }

/* =========================
   REDUCED MOTION
========================= */
@media (prefers-reduced-motion: reduce) {
  .reveal {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
  }
  .fade-up {
    transition: none !important;
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
  }
}
</style>
