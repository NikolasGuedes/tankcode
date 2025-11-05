<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import CurvedLoop from '@/Components/ui/CurvedLoop/CurvedLoop.vue'

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
    <section id="home" class="w-full min-h-screen
         pt-[calc(var(--nav-h,64px)+16px)]
         scroll-mt-[calc(var(--nav-h,64px)+12px)]">
      <!-- wrapper central com margem responsiva -->
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- container clipado: AGORA relative -->
        <div
          class="relative overflow-hidden rounded-[36px] border border-white/10 ring-1 ring-white/10 shadow-[0_25px_80px_rgba(0,0,0,0.45)]">

          <!-- BG do figma preso ao container, não à section -->
          <img src="/imgs/Rectangle.png" alt="" class="absolute inset-0 h-full w-full object-cover" />

          <!-- glows -->
          <div
            class="pointer-events-none absolute -left-24 -top-20 z-10 hidden h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl sm:block">
          </div>
          <div
            class="pointer-events-none absolute -right-20 -bottom-16 z-10 hidden h-72 w-72 rounded-full bg-[#FF58BD]/25 blur-3xl sm:block">
          </div>

          <!-- conteúdo -->
          <div class="relative z-20 flex min-h-[58vh] flex-col items-center justify-center px-4 text-center
                  sm:min-h-[62vh] md:min-h-[66vh] lg:min-h-[80vh] xl:min-h-[80vh]
                  pb-[clamp(120px,18vw,260px)]">
            <h1
              class="hero-title mx-auto max-w-6xl text-3xl leading-tight sm:text-4xl md:text-6xl lg:text-[64px] lg:leading-[1.05]">
              VENHA <span class="text-white">TANKAR</span> ESTE DESAFIO!
            </h1>
            <p class="mx-auto mt-5 max-w-3xl text-sm text-white/80 sm:text-base">
              Aprenda programação se divertindo e faça exercícios com as linguagens mais requisitadas do mercado.
            </p>
            <Link :href="route('login')"
              class="mx-auto mt-6 inline-flex items-center justify-center rounded-xl border border-white/60 px-5 py-3 text-sm font-semibold backdrop-blur hover:bg-white/10 sm:mt-7 sm:text-base">
            Iniciar agora
            </Link>
          </div>

          <!-- card do desafio (continua absoluto, mas agora relativo ao container) -->
          <div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 px-3 sm:px-6 md:px-8 lg:px-10">
            <img src="/imgs/desafio exemplo.png" alt="Mock do card"
              class="mx-auto h-auto w-[clamp(320px,92vw,1080px)] max-w-none rounded-2xl ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)]" />
          </div>
        </div>
      </div>
    </section>


    <!-- Seções “tela cheia” -->
<section id="sobre" class="min-h-screen">
  <p class="mt-6 text-center text-white/80 text-base sm:text-lg">
    Aprenda linguagens como:
  </p>

  <!-- LOOP no topo -->
  <div class="relative mt-0 flex justify-center">
    <div class="relative w-[clamp(360px,92vw,1200px)] overflow-hidden mask-edges"
      style="--loop-1:#6a5cff; --loop-2:#ff58bd; --loop-3:#F9A8D4;">
      <CurvedLoop class="loop-color"
        marquee-text="Python ∘ JavaScript ∘ TypeScript ∘ SQL ∘ PHP ∘ Java ∘ C# ∘ Go ∘ Rust ∘ Vue ∘ Laravel ∘ Docker ∘ Git ∘"
        :speed="1.6" :curve-amount="0" direction="left" :interactive="false" />
    </div>
  </div>

  <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- container principal -->
    <div class="relative overflow-hidden rounded-[36px] border border-white/10 ring-1 ring-white/10 shadow-[0_25px_80px_rgba(0,0,0,0.45)]">

      <!-- BG flipado + correção de bordas -->
      <img
        src="/imgs/Rectangle.png" alt=""
        class="absolute inset-0 h-full w-full object-cover transform-gpu scale-x-[-1] scale-[1.02] rounded-[inherit] [transform:translateZ(0)] [backface-visibility:hidden]" />

      <!-- glows -->
      <div class="pointer-events-none absolute -left-24 -top-20 z-10 hidden h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl sm:block"></div>
      <div class="pointer-events-none absolute -right-20 -bottom-16 z-10 hidden h-72 w-72 rounded-full bg-[#FF58BD]/25 blur-3xl sm:block"></div>

      <!-- conteúdo em 2 colunas -->
      <div class="relative z-20 grid min-h-[60vh] gap-8 px-6 py-10 sm:py-12 md:py-16 lg:py-20 lg:grid-cols-2">
        <!-- Coluna ESQUERDA: texto -->
        <div class="flex flex-col justify-center text-left">
          <h2 class="text-2xl sm:text-3xl font-semibold text-white">Sobre a plataforma</h2>
          <p class="mt-3 text-white/80 leading-relaxed">
            Aqui você pode gerenciar alunos, salas e atividades, importar planilhas e acompanhar o progresso de cada turma.
          </p>
          <ul class="mt-5 space-y-2 text-white/75">
            <li>• Importação de alunos via Excel</li>
            <li>• Habilitar/desabilitar acesso rapidamente</li>
            <li>• Verificação de e-mail e códigos únicos</li>
          </ul>
        </div>

        <!-- Coluna DIREITA: mock menor e alinhado à direita -->
        <div class="relative flex items-center justify-end">
          <img
            src="/imgs/controle_alunos.png" alt="Mock do card"
            class="h-auto w-[min(560px,46vw)] max-h-[70vh] object-contain rounded-2xl
                   ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)] [transform:translateZ(0)]" />
        </div>
      </div>
    </div>
  </div>
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

.mask-edges {
  -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}

/* Estilo do texto dentro do CurvedLoop (gradiente + glow)  */
.loop-color :deep(span),
.loop-color :deep(.marquee-item),
.loop-color :deep(.marquee),
.loop-color :deep(svg text) {
  font-weight: 800;
  letter-spacing: .02em;
  font-size: clamp(28px, 6vw, 56px);
  /* gradiente controlado por variáveis */
  background-image: linear-gradient(90deg, var(--loop-1), var(--loop-2) 45%, var(--loop-3));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent !important;
  /* força usar o gradiente */
  opacity: .96;
  filter: drop-shadow(0 2px 8px rgb(117, 4, 255));
  /* leve glow */
}

/* fade nas bordas */
.mask-edges {
  -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}

/* ---- COR E ESTÉTICA DO TEXTO DO LOOP ---- */
/* Caso o texto seja HTML (spans/divs) */
.loop-color :deep(span),
.loop-color :deep(.marquee-item),
.loop-color :deep(.cl-char),
.loop-color :deep(.cl-text) {
  font-weight: 800;
  letter-spacing: .01em;
  font-size: clamp(28px, 6vw, 56px);

  /* degrade bonito */
  background-image: linear-gradient(90deg, #000000 0%, #000000 45%, #ff008c 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent !important;
  -webkit-text-fill-color: transparent !important;

  /* leve glow */
  filter: drop-shadow(0 2px 8px rgba(110, 48, 255, 0.35));
}

/* Caso o texto seja SVG <text> (background-clip não funciona em SVG) */
.loop-color :deep(svg text) {
  fill: #8471FF !important;
  /* cor sólida */
  /* se preferir outra cor: #EDEBFE, #B793FF, etc. */
}
</style>
