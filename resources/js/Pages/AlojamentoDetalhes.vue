<script setup>
import { ref } from "vue"
import api from "@/axiosFrontend"
import Navbar from "@/Components/NavBar.vue"
import { usePage } from "@inertiajs/vue3"

const page = usePage()

const props = defineProps({
  alojamento: Object,
})

const checkin = ref("")
const checkout = ref("")
const hospedes = ref(1)

const disponibilidadeMsg = ref(null)
const erroMsg = ref(null)


const tituloComentario = ref("")
const textoComentario = ref("")
const rating = ref(5)

const comentarioErro = ref(null)
const comentarioSucesso = ref(null)
const aEnviar = ref(false)

const fotoAtual = ref(0)

const nextFoto = () => {
  if (fotoAtual.value < props.alojamento.fotos.length - 1) {
    fotoAtual.value++
  } else {
    fotoAtual.value = 0
  }
}

const prevFoto = () => {
  if (fotoAtual.value > 0) {
    fotoAtual.value--
  } else {
    fotoAtual.value = props.alojamento.fotos.length - 1
  }
} 


const enviarComentario = async () => {

  console.log({
  alojamento_id: props.alojamento.id,
  titulo: tituloComentario.value,
  texto: textoComentario.value,
  rating: rating.value
})

  comentarioErro.value = null
  comentarioSucesso.value = null

  if (!tituloComentario.value || !textoComentario.value) {
    comentarioErro.value = "Preenche o título e o comentário."
    return
  }

  try{
    await api.post("/comentarios",{
      alojamento_id: props.alojamento.id,
      titulo: tituloComentario.value,
      texto: textoComentario.value,
      rating: rating.value,
    })

    comentarioSucesso.value = "Comentario enviado para aprovação"

    // limpar formulário
    tituloComentario.value = ""
    textoComentario.value = ""
    rating.value = 5

    } catch (e) {
      comentarioErro.value =
        e.response?.data?.message || "Erro ao enviar comentário."
    }

  }

const verificarDisponibilidade = async () => {
  disponibilidadeMsg.value = null
  erroMsg.value = null

  if (!checkin.value || !checkout.value) {
    erroMsg.value = "Seleciona as datas."
    return
  }

  try {
    const res = await api.post(
      `/reservas/available/${props.alojamento.id}`,
      {
        checkin: checkin.value,
        checkout: checkout.value,
      }
    )

    if (res.data.available) {
      disponibilidadeMsg.value = `Disponível — Total: ${res.data.total} €`
    } else {
      erroMsg.value = "Indisponível para estas datas."
    }
  } catch {
    erroMsg.value = "Erro ao verificar disponibilidade."
  }
}

const reservar = async () => {
  erroMsg.value = null

  try {
    const res = await api.post("/reservas", {
      alojamento_id: props.alojamento.id,
      checkin: checkin.value,
      checkout: checkout.value,
      hospedes: hospedes.value,
    })

    window.location.href = res.data.redirect
  } catch (e) {
    erroMsg.value = e.response?.data?.error || "Erro ao criar reserva."
  }
  aEnviar.value = true

  try {
    await api.post("/comentarios", {
      alojamento_id: props.alojamento.id,
      titulo: tituloComentario.value,
      texto: textoComentario.value,
      rating: rating.value,
    })

    comentarioSucesso.value =
      "Comentário enviado com sucesso. Aguarda aprovação."
    tituloComentario.value = ""
    textoComentario.value = ""
    rating.value = 5
  } catch (e) {
    comentarioErro.value =
      e.response?.data?.error || "Erro ao enviar comentário."
  } finally {
    aEnviar.value = false
  }
}
</script>
<template>
  <Navbar />

  <div class="max-w-6xl mx-auto mt-28 px-4">
    <pre>{{ alojamento.comentarios }}</pre>

    <!-- TÍTULO -->
    <h1 class="text-3xl md:text-4xl font-bold mb-6 text-[#603813] text-center">
      {{ alojamento.titulo }}
    </h1>

    <!-- GALERIA -->
<!-- CARROSSEL -->
<div class="relative w-full h-[840px] mb-10 rounded-xl overflow-hidden shadow-lg">

  <!-- IMAGEM -->
  <img
    :src="`/storage/${alojamento.fotos[fotoAtual].path}`"
    class="w-full h-full object-cover transition-all duration-300"
  />

  <!-- BOTÃO ESQUERDA -->
  <button
    @click="prevFoto"
    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/60 text-white p-3 rounded-full hover:bg-black transition"
  >
    ‹
  </button>

  <!-- BOTÃO DIREITA -->
  <button
    @click="nextFoto"
    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/60 text-white p-3 rounded-full hover:bg-black transition"
  >
    ›
  </button>

  <!-- INDICADORES -->
  <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
    <span
      v-for="(foto, i) in alojamento.fotos"
      :key="i"
      @click="fotoAtual = i"
      class="w-3 h-3 rounded-full cursor-pointer"
      :class="fotoAtual === i ? 'bg-white' : 'bg-white/50'"
    ></span>
  </div>

</div>
<!-- DESCRIÇÃO -->
<div class="mb-10 text-lg text-gray-700">
  <p>
    {{ alojamento.descricao }}
  </p>
</div>


<!-- COMENTÁRIOS + RESERVA -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-lg">

  <!-- COMENTÁRIOS -->
  <div class="lg:col-span-2">

    <!-- LISTA DE COMENTÁRIOS -->
    <div class="mb-12">
      <h2 class="text-2xl font-semibold mb-4">
        Avaliações
      </h2>

      <div
        v-if="alojamento.comentarios?.length"
        class="space-y-4"
      >
        <div
          v-for="c in alojamento.comentarios"
          :key="c.id"
          class="bg-white p-4 rounded shadow"
        >
          <p class="font-semibold">
            {{ c.user.name }}
          </p>
          <p class="text-gray-600 text-sm mb-2">
            {{ c.created_at }}
          </p>
          <p>{{ c.texto }}</p>
        </div>
      </div>
    </div>

    <!-- ADICIONAR COMENTÁRIO -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-xl font-semibold mb-4">
        Avaliar este alojamento
      </h3>

      <div v-if="page.props.auth?.user">

        <label class="block font-medium mb-1">Título</label>
        <input
          v-model="tituloComentario"
          type="text"
          class="w-full border rounded px-3 py-2 mb-3"
        />

        <label class="block font-medium mb-1">Avaliação</label>
        <select
          v-model="rating"
          class="w-full border rounded px-3 py-2 mb-3"
        >
          <option v-for="n in 5" :key="n" :value="n">
            {{ n }} ⭐
          </option>
        </select>

        <label class="block font-medium mb-1">Comentário</label>
        <textarea
          v-model="textoComentario"
          rows="4"
          class="w-full border rounded px-3 py-2 mb-3"
        ></textarea>

        <p v-if="comentarioErro" class="text-red-600 mb-2">
          {{ comentarioErro }}
        </p>

        <p v-if="comentarioSucesso" class="text-green-600 mb-2">
          {{ comentarioSucesso }}
        </p>

        <button
          @click="enviarComentario"
          :disabled="aEnviar"
          class="bg-primary text-white px-5 py-2 rounded hover:bg-black disabled:opacity-50"
        >
          Enviar avaliação
        </button>
      </div>

      <p v-else class="text-gray-600">
        Inicia sessão para deixar uma avaliação.
      </p>
    </div>

  </div>

  <!-- CAIXA RESERVA -->
  <div class="bg-white p-6 rounded-lg shadow h-fit sticky top-28">
    <h2 class="text-xl font-semibold mb-4">
      Reservar
    </h2>

    <label class="block font-medium mb-1">Check-in</label>
    <input type="date" v-model="checkin" class="w-full border rounded px-3 py-2 mb-3" />

    <label class="block font-medium mb-1">Check-out</label>
    <input type="date" v-model="checkout" class="w-full border rounded px-3 py-2 mb-3" />

    <label class="block font-medium mb-1">Hóspedes</label>
    <select v-model="hospedes" class="w-full border rounded px-3 py-2 mb-4">
      <option :value="1">1</option>
      <option :value="2">2</option>
    </select>

    <button
      @click="verificarDisponibilidade"
      class="w-full bg-primary text-white py-2 rounded mb-3 hover:bg-black"
    >
      Verificar disponibilidade
    </button>

    <p v-if="disponibilidadeMsg" class="text-green-600 font-semibold mb-3">
      {{ disponibilidadeMsg }}
    </p>

    <p v-if="erroMsg" class="text-red-600 font-semibold mb-3">
      {{ erroMsg }}
    </p>

    <button
      v-if="disponibilidadeMsg"
      @click="reservar"
      class="w-full bg-accent text-dark py-2 rounded font-semibold"
    >
      Reservar agora
    </button>
  </div>

</div>
    </div>
</template>
