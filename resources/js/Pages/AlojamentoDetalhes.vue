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
    <h1 class="text-3xl md:text-4xl font-bold mb-6">
      {{ alojamento.titulo }}
    </h1>

    <!-- GALERIA -->
    <div
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-8"
    >
      <img
        v-for="(foto, i) in alojamento.fotos"
        :key="i"
        :src="`/storage/${foto.path}`"
        class="w-full h-56 object-cover rounded-lg"
      />
    </div>

    <!-- CONTEÚDO + RESERVA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- DESCRIÇÃO -->
      <div class="lg:col-span-2">
        <p class="text-gray-700 mb-6">
          {{ alojamento.descricao }}
        </p>
      </div>

      <!-- CAIXA RESERVA -->
      <div class="bg-white p-6 rounded-lg shadow h-fit">
        <h2 class="text-xl font-semibold mb-4">
          Reservar
        </h2>

        <!-- Datas -->
        <label class="block font-medium mb-1">Check-in</label>
        <input
          type="date"
          v-model="checkin"
          class="w-full border rounded px-3 py-2 mb-3"
        />

        <label class="block font-medium mb-1">Check-out</label>
        <input
          type="date"
          v-model="checkout"
          class="w-full border rounded px-3 py-2 mb-3"
        />

        <!-- HÓSPEDES -->
        <label class="block font-medium mb-1">Hóspedes</label>
        <select
          v-model="hospedes"
          class="w-full border rounded px-3 py-2 mb-4"
        >
          <option :value="1">1</option>
          <option :value="2">2</option>
        </select>

        <!-- BOTÕES -->
        <button
          @click="verificarDisponibilidade"
          class="w-full bg-primary text-white py-2 rounded mb-3"
        >
          Verificar disponibilidade
        </button>

        <p
          v-if="disponibilidadeMsg"
          class="text-green-600 font-semibold mb-3"
        >
          {{ disponibilidadeMsg }}
        </p>

        <p
          v-if="erroMsg"
          class="text-red-600 font-semibold mb-3"
        >
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

    <!-- COMENTÁRIOS -->
    <div class="mt-12">
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
<!-- ADICIONAR COMENTÁRIO -->
<div class="mt-10 bg-white p-6 rounded-lg shadow">

  <h3 class="text-xl font-semibold mb-4">
    Avaliar este alojamento
  </h3>

  <div v-if="page.props.auth?.user">

    <!-- TÍTULO -->
    <label class="block font-medium mb-1">Título</label>
    <input
      v-model="tituloComentario"
      type="text"
      class="w-full border rounded px-3 py-2 mb-3"
      placeholder="Ex: Excelente estadia!"
    />

    <!-- RATING -->
    <label class="block font-medium mb-1">Avaliação</label>
    <select
      v-model="rating"
      class="w-full border rounded px-3 py-2 mb-3"
    >
      <option v-for="n in 5" :key="n" :value="n">
        {{ n }} ⭐
      </option>
    </select>

    <!-- TEXTO -->
    <label class="block font-medium mb-1">Comentário</label>
    <textarea
      v-model="textoComentario"
      rows="4"
      class="w-full border rounded px-3 py-2 mb-3"
      placeholder="Descreve a tua experiência…"
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
      class="bg-primary text-white px-5 py-2 rounded hover:bg-secondary disabled:opacity-50"
    >
      Enviar avaliação
    </button>
  </div>

  <p v-else class="text-gray-600">
    Inicia sessão para deixar uma avaliação.
  </p>
</div>

    </div>

  </div>
</template>
