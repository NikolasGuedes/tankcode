<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'

/** Navegação com smooth-scrolling (o offset é resolvido via scroll-margin-top no CSS) */
function goTo(selector: string) {
  const el = document.querySelector(selector)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

/** Estado do menu mobile */
const mobileOpen = ref(false)
function toggleMobile() { mobileOpen.value = !mobileOpen.value }
function onResize() { if (window.innerWidth >= 768) mobileOpen.value = false }

/** Mostrar/ocultar navbar conforme direção de scroll */
const showNav = ref(true)
let lastY = 0
let ticking = false

/** Link ativo conforme seção visível */
const activeSection = ref<string>('home')
let io: IntersectionObserver | null = null

onMounted(() => {
  // medir altura do header e expor como CSS var para compensação de âncora
  const header = document.querySelector('header') as HTMLElement | null
  const h = header?.offsetHeight ?? 64
  document.documentElement.style.setProperty('--nav-h', `${h}px`)

  // ouve resize (já existia)
  window.addEventListener('resize', onResize)

  // scroll inteligente
  const onScroll = () => {
    const y = window.scrollY || 0
    // evita toggles em micro movimentos:
    const delta = Math.abs(y - lastY)

    if (!ticking && delta > 4) {
      window.requestAnimationFrame(() => {
        // se desceu e passou do header, esconde; se subiu, mostra
        if (y > h) showNav.value = y < lastY
        else showNav.value = true
        lastY = y
        ticking = false
      })
      ticking = true
    }

    // se rolar, fecha o menu mobile
    if (mobileOpen.value) mobileOpen.value = false
  }
  window.addEventListener('scroll', onScroll, { passive: true })

  // observar quais seções estão visíveis pra ativar o link
  io = new IntersectionObserver(
    (entries) => {
      for (const e of entries) {
        if (e.isIntersecting) {
          const id = e.target.getAttribute('id')
          if (id) activeSection.value = id
        }
      }
    },
    {
      // dispara quando 35% da seção estiver visível
      threshold: 0.35,
      // compensa a altura do header para o cálculo de viewport
      rootMargin: `-${h}px 0px 0px 0px`,
    }
  )
    ;['home', 'sobre', 'features', 'exemplos'].forEach((id) => {
      const el = document.getElementById(id)
      if (el) io!.observe(el)
    })

  // força estado inicial do lastY (em caso de reload no meio da página)
  lastY = window.scrollY || 0
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  io?.disconnect()
})
</script>

<template>

  <Head title="TankCode — Home">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <!-- trava overflow-x para evitar barra horizontal -->
  <div class="min-h-screen bg-[#0a0a0a] text-white antialiased overflow-x-hidden">
    <!-- NAVBAR: sticky + animação de esconder/mostrar -->
    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-[#0a0a0a]/70 backdrop-blur">
      
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
        <a href="#home" @click.prevent="goTo('#home')" class="inline-flex items-center">
          <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-9 w-auto" />
        </a>

        <!-- desktop -->
        <nav class="hidden md:block">
          <ul class="flex items-center gap-6 text-sm text-white/70">
            <a href="#home" class="navlink" :class="{ 'navlink--active': activeSection === 'home' }"
              @click.prevent="goTo('#home')">home</a>
            <a href="#sobre" class="navlink" :class="{ 'navlink--active': activeSection === 'sobre' }"
              @click.prevent="goTo('#sobre')">sobre</a>
            <a href="#features" class="navlink" :class="{ 'navlink--active': activeSection === 'features' }"
              @click.prevent="goTo('#features')">features</a>
            <a href="#exemplos" class="navlink" :class="{ 'navlink--active': activeSection === 'exemplos' }"
              @click.prevent="goTo('#exemplos')">exemplos</a>
          </ul>
        </nav>

        <!-- ações desktop -->
        <div class="hidden items-center gap-2 md:flex">
          <Link v-if="$page.props.auth?.user" :href="route('students')"
            class="rounded-xl border border-white/20 px-3 py-2 text-sm font-semibold text-white hover:bg-white/10">
          ESTUDANTES
          </Link>
          <template v-else>
            <Link :href="route('register')"
              class="rounded-full border border-indigo-400/40 px-3 py-2 text-sm font-semibold text-indigo-300 hover:bg-white/5">
            REGISTRAR-SE
            </Link>
            <Link :href="route('student.login')"
              class="rounded-full border border-white/15 bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            Área do Estudante
            </Link>
            <Link :href="route('login')"
              class="rounded-full border border-indigo-400/40 px-3 py-2 text-sm font-semibold text-indigo-300 hover:bg-white/5">
            Área Administrativa
            </Link>
          </template>
        </div>

        <!-- botão mobile -->
        <button @click="toggleMobile"
          class="inline-flex items-center justify-center rounded-md border border-white/15 p-2 md:hidden"
          aria-label="Abrir menu">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
          </svg>
        </button>
      </div>

      <!-- drawer mobile -->
      <div v-if="mobileOpen" class="md:hidden">
        <div class="mx-auto max-w-7xl px-4 pb-4">
          <nav class="rounded-xl border border-white/10 bg-black/40 p-3">
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#home"
              :class="{ 'navlink--active': activeSection === 'home' }"
              @click.prevent="goTo('#home'); mobileOpen = false">home</a>
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#sobre"
              :class="{ 'navlink--active': activeSection === 'sobre' }"
              @click.prevent="goTo('#sobre'); mobileOpen = false">sobre</a>
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#features"
              :class="{ 'navlink--active': activeSection === 'features' }"
              @click.prevent="goTo('#features'); mobileOpen = false">features</a>
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#exemplos"
              :class="{ 'navlink--active': activeSection === 'exemplos' }"
              @click.prevent="goTo('#exemplos'); mobileOpen = false">exemplos</a>
            <div class="mt-3 grid grid-cols-1 gap-2">
              <Link :href="route('register')"
                class="rounded-full border border-indigo-400/40 px-3 py-2 text-center text-sm font-semibold text-indigo-300 hover:bg-white/5">
              REGISTRAR-SE</Link>
              <Link :href="route('student.login')"
                class="rounded-full border border-white/15 bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-500">
              Área do Estudante</Link>
              <Link :href="route('login')"
                class="rounded-full border border-indigo-400/40 px-3 py-2 text-center text-sm font-semibold text-indigo-300 hover:bg-white/5">
              Área Administrativa</Link>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <!-- HERO ocupa a tela (menos o header) graças à scroll-margin-top e à min-h-screen nas seções seguintes -->
    <section id="home" class="section mx-auto mt-8 max-w-7xl px-4">
      <!-- container clipado -->
      <div class="relative overflow-hidden rounded-[28px] border border-white/10">
        <!-- bg do figma -->
        <img src="/imgs/Rectangle.png" alt="" class="absolute inset-0 h-full w-full rounded-[28px] object-cover" />

        <!-- glows só em >= sm para não vazar -->
        <div
          class="pointer-events-none absolute -left-24 -top-20 z-10 hidden h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl sm:block">
        </div>
        <div
          class="pointer-events-none absolute -right-20 -bottom-16 z-10 hidden h-72 w-72 rounded-full bg-[#FF58BD]/25 blur-3xl sm:block">
        </div>

        <!-- conteúdo: altura fluida + reserva do card -->
        <div class="relative z-20 flex min-h-[58vh] flex-col items-center justify-center px-4 text-center
                 sm:min-h-[62vh] md:min-h-[66vh] lg:min-h-[70vh] xl:min-h-[74vh]
                 pb-40 sm:pb-48 md:pb-52 lg:pb-56 xl:pb-64">
          <h1
            class="hero-title mx-auto max-w-6xl text-3xl leading-tight sm:text-4xl md:text-6xl lg:text-[64px] lg:leading-[1.05]">
            VENHA <span class="text-white">TANKAR</span> ESTE DESAFIO!
          </h1>
          <p class="mx-auto mt-4 max-w-3xl text-sm text-white/80 sm:text-base">
            Aprenda programação se divertindo e faça exercícios com as linguagens mais requisitadas do mercado.
          </p>
          <Link :href="route('login')"
            class="mx-auto mt-6 inline-flex items-center justify-center rounded-xl border border-white/60 px-5 py-3 text-sm font-semibold backdrop-blur hover:bg-white/10 sm:mt-7 sm:text-base">
          Iniciar agora
          </Link>
        </div>

        <!-- CARD DO DESAFIO — maior e responsivo, DENTRO do container -->
        <div class="pointer-events-none absolute inset-x-0 bottom-0 z-30 px-3 sm:px-6 md:px-8 lg:px-10">
          <img src="/imgs/desafio exemplo.png" alt="Mock do card" class="mx-auto w-[92%] max-w-[520px] sm:max-w-[680px] md:max-w-[860px] lg:max-w-[980px] xl:max-w-[1080px]
                   rounded-2xl ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)]" />
        </div>
      </div>
    </section>

    <!-- espaçador menor (card está dentro) -->
    <div class="h-12 sm:h-14 md:h-16 lg:h-20"></div>

    <!-- Seções “tela cheia” -->
    <section id="sobre" class="section mx-auto max-w-7xl px-4">
      <h2 class="mb-4 text-2xl font-semibold text-white/90">Sobre</h2>
      <p class="max-w-3xl text-sm text-white/70">Texto de exemplo…</p>
    </section>

    <section id="features" class="section mx-auto max-w-7xl px-4">
      <h2 class="mb-4 text-2xl font-semibold text-white/90">Features</h2>
      <p class="max-w-3xl text-sm text-white/70">Texto de exemplo…</p>
    </section>

    <section id="exemplos" class="section mx-auto max-w-7xl px-4">
      <h2 class="mb-4 text-2xl font-semibold text-white/90">Exemplos</h2>
      <p class="max-w-3xl text-sm text-white/70">Texto de exemplo…</p>
    </section>

    <!-- respiro final -->
    <div class="h-24"></div>
  </div>
</template>

<style scoped>
/* Links da navbar */
.navlink {
  position: relative;
  padding: .25rem .25rem;
  transition: color .2s;
}

.navlink:hover,
.navlink--active {
  color: #fff;
}

.navlink--active::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -8px;
  transform: translateX(-50%);
  width: 22px;
  height: 3px;
  border-radius: 3px;
  background: linear-gradient(90deg, #6a5cff, #ff58bd);
}

/* Título */
.hero-title {
  font-family: "Inter var", Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  font-weight: 800;
  letter-spacing: -0.02em;
}

/* Compensa o header fixo ao rolar até um #id (âncora) e garante “tela cheia” */
.section {
  /* ocupa a altura da viewport, com um respiro de 16px para não “colar” no rodapé visual */
  min-height: calc(100vh - var(--section-pad, 0px));
  /* padding vertical da seção */
  padding-top: 4rem;
  padding-bottom: 4rem;

  /* MUITO IMPORTANTE: ao usar anchors com header sticky, evita que o topo seja coberto */
  scroll-margin-top: calc(var(--nav-h, 64px) + 12px);
}

/* Ajuste opcional: em telas muito pequenas, reduz um pouco o alvo de altura */
@media (max-width: 640px) {
  :root {
    --section-pad: 0px;
  }
}

/* Desativa o scroll-behavior nativo (usamos JS) */
html {
  scroll-behavior: auto;
}
</style>
