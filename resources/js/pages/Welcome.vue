<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import CurvedLoop from '@/Components/ui/CurvedLoop/CurvedLoop.vue'

function goTo(selector: string) {
  const el = document.querySelector(selector)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const mobileOpen = ref(false)
function toggleMobile() { mobileOpen.value = !mobileOpen.value }
function onResize() { if (window.innerWidth >= 768) mobileOpen.value = false }

const activeSection = ref<string>('home')
let io: IntersectionObserver | null = null

function onScroll() {
  if (mobileOpen.value) mobileOpen.value = false
}

onMounted(() => {
  const header = document.querySelector('header') as HTMLElement | null
  const h = header?.offsetHeight ?? 64
  document.documentElement.style.setProperty('--nav-h', `${h}px`)

  window.addEventListener('resize', onResize)
  window.addEventListener('scroll', onScroll, { passive: true })

  io = new IntersectionObserver(
    (entries) => {
      for (const e of entries) {
        if (e.isIntersecting) {
          const id = e.target.getAttribute('id')
          if (id) activeSection.value = id
        }
      }
    },
    { threshold: 0.35, rootMargin: `-${h}px 0px 0px 0px` }
  )

    ;['home', 'sobre', 'beneficios', 'personas'].forEach((id) => {
      const el = document.getElementById(id)
      if (el) io!.observe(el)
    })
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  window.removeEventListener('scroll', onScroll)
  io?.disconnect()
})

const year = new Date().getFullYear()
</script>

<template>

  <Head title="TankCode — Home">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div class="min-h-screen bg-[#0a0a0a] text-white antialiased overflow-x-hidden">
    <!-- NAVBAR -->
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
            <a href="#beneficios" class="navlink" :class="{ 'navlink--active': activeSection === 'beneficios' }"
              @click.prevent="goTo('#beneficios')">benefícios</a>
            <a href="#personas" class="navlink" :class="{ 'navlink--active': activeSection === 'personas' }"
              @click.prevent="goTo('#personas')">personas</a>
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
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#beneficios"
              :class="{ 'navlink--active': activeSection === 'beneficios' }"
              @click.prevent="goTo('#beneficios'); mobileOpen = false">benefícios</a>
            <a class="block rounded-lg px-3 py-2 text-white/80 hover:bg-white/10" href="#personas"
              :class="{ 'navlink--active': activeSection === 'personas' }"
              @click.prevent="goTo('#personas'); mobileOpen = false">personas</a>
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

    <!-- HERO -->
    <section id="home"
      class="w-full min-h-screen pt-[calc(var(--nav-h,64px)+16px)] scroll-mt-[calc(var(--nav-h,64px)+12px)]">
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div
          class="relative overflow-hidden rounded-[36px] border border-white/10 ring-1 ring-white/10 shadow-[0_25px_80px_rgba(0,0,0,0.45)]">
          <img src="/imgs/Rectangle.png" alt="" class="absolute inset-0 h-full w-full object-cover" />

          <div
            class="pointer-events-none absolute -left-24 -top-20 z-10 hidden h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl sm:block">
          </div>
          <div
            class="pointer-events-none absolute -right-20 -bottom-16 z-10 hidden h-72 w-72 rounded-full bg-[#FF58BD]/25 blur-3xl sm:block">
          </div>

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

          <div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 px-3 sm:px-6 md:px-8 lg:px-10">
            <img src="/imgs/desafio exemplo.png" alt="Mock do card"
              class="mx-auto h-auto w-[clamp(320px,92vw,1080px)] max-w-none rounded-2xl ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)]" />
          </div>
        </div>
      </div>
    </section>

    <!-- SOBRE -->
    <section id="sobre" class="py-20 scroll-mt-[calc(var(--nav-h,64px)+12px)]">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="text-center text-white/80 text-base sm:text-lg">
          Aprenda linguagens como:
        </p>

        <!-- LOOP no topo -->
        <div class="relative mt-2 flex justify-center">
          <div class="relative w-[clamp(360px,92vw,1200px)] overflow-hidden mask-edges"
            style="--loop-1:#6a5cff; --loop-2:#ff58bd; --loop-3:#F9A8D4;">
            <CurvedLoop class="loop-color"
              marquee-text="Python ∘ JavaScript ∘ TypeScript ∘ SQL ∘ PHP ∘ Java ∘ C# ∘ Go ∘ Rust ∘ Vue ∘ Laravel ∘ Docker ∘ Git ∘"
              :speed="1.6" :curve-amount="0" direction="left" :interactive="false" />
          </div>
        </div>

        <div class="mt-10 grid items-center gap-y-12 gap-x-16 xl:gap-x-20 lg:grid-cols-12">
          <!-- texto -->
          <div class="min-w-0 lg:col-span-4 xl:col-span-4">
            <h2 class="text-3xl sm:text-4xl font-semibold grad-title">Sobre a plataforma</h2>
            <p class="mt-3 max-w-[990px] leading-relaxed copy-soft">
              Aqui você pode gerenciar alunos, salas e atividades, importar planilhas e acompanhar o progresso de cada
              turma.
            </p>
            <ul class="mt-6 space-y-2 list-tank">
              <li>Importação de alunos via Excel</li>
              <li>Habilitar/desabilitar acesso rapidamente</li>
              <li>Verificação de e-mail e códigos únicos</li>
            </ul>
          </div>

          <!-- imagem -->
          <div class="min-w-0 lg:col-span-8 xl:col-span-8 justify-self-end w-full">
            <div class="relative w-full">
              <div
                class="pointer-events-none absolute -left-24 -top-20 h-72 w-72 rounded-full bg-[#635BFF]/35 blur-3xl">
              </div>
              <div
                class="pointer-events-none absolute -right-24 -bottom-20 h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl">
              </div>
              <img src="/imgs/controle_alunos.png" alt="Visual da lista de estudantes"
                class="relative z-10 block w-full h-auto object-contain rounded-[28px] ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)]" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- BENEFÍCIOS -->
    <section id="beneficios" class="py-20 min-h-[60vh] scroll-mt-[calc(var(--nav-h,64px)+12px)]">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[36px] border border-white/10 ring-1 ring-white/10">
          <div class="absolute inset-0 bg-benefit-aurora"></div>

          <div class="relative z-10 grid gap-10 p-8 sm:p-10 lg:p-12 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-7">
              <h2 class="text-3xl sm:text-4xl font-semibold text-white">
                Benefícios para <span class="text-[#6a5cff]">escolas e universidades</span>
              </h2>

              <div class="mt-6 grid gap-8 sm:grid-cols-2">
                <div>
                  <h3 class="text-lg font-semibold text-white/95">Para professores & coordenação</h3>
                  <ul class="mt-3 space-y-2 text-white/85">
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div><strong>Dashboards personalizados</strong> por turma, disciplina e período.</div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div><strong>Controle de notas</strong> da sala, avaliações e entregas.</div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div><strong>Estudo do aluno e das turmas</strong> (evolução, dificuldades, engajamento).</div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div>Relatórios e exportações em 1 clique (Excel/CSV).</div>
                    </li>
                  </ul>
                </div>

                <div>
                  <h3 class="text-lg font-semibold text-white/95">Para alunos</h3>
                  <ul class="mt-3 space-y-2 text-white/85">
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div><strong>Gamificação</strong> do aprendizado (XP, badges, ranking) pra manter o engajamento.
                      </div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div>Trilhas guiadas com <strong>feedback imediato</strong> por desafio.</div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div>Portfólio de exercícios para entrevistas e estágio.</div>
                    </li>
                    <li class="flex items-start gap-3"><span class="dot"></span>
                      <div>Acesso web/mobile com notificações.</div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Card em CSS (sem imagem) -->
            <div class="min-w-0 lg:col-span-5">
              <div
                class="relative overflow-hidden rounded-[28px] ring-1 ring-white/10 shadow-[0_24px_90px_rgba(0,0,0,0.55)]">
                <div class="absolute inset-0 benefit-card-bg"></div>
                <div
                  class="pointer-events-none absolute -left-14 -top-14 h-56 w-56 rounded-full bg-[#6a5cff]/35 blur-3xl">
                </div>
                <div
                  class="pointer-events-none absolute -right-14 -bottom-16 h-60 w-60 rounded-full bg-[#ff58bd]/30 blur-3xl">
                </div>

                <div class="relative p-6 sm:p-8">
                  <h3 class="text-lg font-semibold text-white/95">Tudo em um só lugar</h3>
                  <p class="mt-2 text-sm text-white/80">Compare turmas, acompanhe a evolução e identifique gargalos em
                    tempo
                    real.</p>
                  <ul class="mt-4 space-y-2 text-white/90">
                    <li class="flex items-center gap-2"><span class="dot"></span> KPIs por turma e aluno</li>
                    <li class="flex items-center gap-2"><span class="dot"></span> Controle de avaliações e notas</li>
                    <li class="flex items-center gap-2"><span class="dot"></span> Exportação rápida para Excel/CSV</li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /Card -->
          </div>
        </div>
      </div>
    </section>

    <!-- PERSONAS (menos cards + ícones diferentes + título com brilho) -->
    <section id="personas" class="py-20 min-h-[60vh] scroll-mt-[calc(var(--nav-h,64px)+12px)]">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[36px] border border-white/10 ring-1 ring-white/10">
          <div class="absolute inset-0 bg-benefit-aurora opacity-90"></div>

          <div class="relative z-10 p-8 sm:p-10 lg:p-12">
            <h2 class="text-3xl sm:text-4xl font-semibold title-accent">Personas que se identificam com a TankCode</h2>
            <p class="mt-3 max-w-3xl text-white/75">Quem mais aproveita a plataforma e por quê.</p>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
              <!-- 1. Estudantes -->
              <article class="persona-card group">
                <div class="icon-bubble">
                  <!-- graduation cap -->
                  <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7 text-white/95">
                    <path d="M3 8.5L12 4l9 4.5-9 4.5L3 8.5Z" stroke="currentColor" stroke-width="1.5" />
                    <path d="M7 11v4.2c0 .9 2.2 2.3 5 2.3s5-1.4 5-2.3V11" stroke="currentColor" stroke-width="1.5" />
                  </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-white/95">Estudantes</h3>
                <p class="mt-2 text-sm text-white/75">Desafios práticos, ranking e portfólio para estágio/emprego.</p>
              </article>

              <!-- 2. Professores/Monitores -->
              <article class="persona-card group">
                <div class="icon-bubble">
                  <!-- chalkboard teacher -->
                  <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7 text-white/95">
                    <rect x="3" y="5" width="14" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                    <path d="M6 20v-2a2 2 0 0 1 2-2h8" stroke="currentColor" stroke-width="1.5" />
                    <circle cx="19" cy="9.5" r="1.7" stroke="currentColor" stroke-width="1.5" />
                  </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-white/95">Professores & Monitores</h3>
                <p class="mt-2 text-sm text-white/75">Listas autocorrigidas e visão instantânea do desempenho.</p>
              </article>

              <!-- 3. Coordenação -->
              <article class="persona-card group">
                <div class="icon-bubble">
                  <!-- chart up -->
                  <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7 text-white/95">
                    <path d="M4 19h16" stroke="currentColor" stroke-width="1.5" />
                    <path d="M6 15v3M11 11v7M16 8v10" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 10l4-4 4 2" stroke="currentColor" stroke-width="1.5" />
                  </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-white/95">Coordenação de Curso</h3>
                <p class="mt-2 text-sm text-white/75">KPIs por turma/semestre e relatórios para BI.</p>
              </article>

              <!-- 4. Empresas/RH -->
              <article class="persona-card group">
                <div class="icon-bubble">
                  <!-- briefcase -->
                  <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7 text-white/95">
                    <rect x="3" y="7" width="18" height="11" rx="2" stroke="currentColor" stroke-width="1.5" />
                    <path d="M9 7V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="1.5" />
                    <path d="M3 12h18" stroke="currentColor" stroke-width="1.5" />
                  </svg>
                </div>
                <h3 class="mt-5 text-lg font-semibold text-white/95">Empresas Parceiras / RH</h3>
                <p class="mt-2 text-sm text-white/75">Acesso a rankings e portfólios para parcerias e estágios.</p>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-white/10 bg-[#0b0b0b]">
      <div
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-6 w-auto opacity-90" />
          <span class="text-sm text-white/60">Plataforma de desafios de programação</span>
        </div>
        <div class="text-sm text-white/55">© {{ year }} TankCode. Todos os direitos reservados.</div>
      </div>
    </footer>
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

/* Desativa o scroll-behavior nativo (usamos JS) */
html {
  scroll-behavior: auto;
}

/* fade nas bordas do loop */
.mask-edges {
  -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}

/* LOOP — HTML e SVG com neon roxo-azulado */

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

/* ===== CurvedLoop – roxo-azulado com neon (HTML e SVG) ===== */


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

/* mantém as bordas com fade do container do loop */
.mask-edges {
  -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
  mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}



/* SOBRE: roxo fixo + bullets */
#sobre .grad-title {
  color: #6a5cff !important;
  text-shadow: 0 0 12px rgba(106, 92, 255, .45);
}

.copy-soft {
  color: rgba(232, 228, 255, .90);
}

.list-tank {
  color: rgba(232, 228, 255, .85);
}

#sobre .list-tank li {
  position: relative;
  padding-left: 1.25rem;
}

#sobre .list-tank li::before {
  content: "";
  position: absolute;
  left: 0;
  top: .6em;
  width: .45rem;
  height: .45rem;
  border-radius: 9999px;
  background: #6a5cff;
  filter: drop-shadow(0 0 8px rgba(106, 92, 255, .55));
}

/* Aurora roxo/rosa */
.bg-benefit-aurora {
  background:
    radial-gradient(58% 58% at 16% 22%, rgba(106, 92, 255, .25), transparent 60%),
    radial-gradient(54% 54% at 84% 78%, rgba(255, 88, 189, .20), transparent 60%),
    radial-gradient(70% 70% at 88% 12%, rgba(132, 113, 255, .18), transparent 60%),
    linear-gradient(180deg, #101024 0%, #0a0a0a 100%);
  filter: saturate(1.08);
}

/* Card do benefício */
.benefit-card-bg {
  background:
    radial-gradient(60% 60% at 20% 15%, rgba(106, 92, 255, .28), transparent 60%),
    radial-gradient(55% 55% at 85% 80%, rgba(255, 88, 189, .22), transparent 60%),
    linear-gradient(180deg, #141226 0%, #0a0a0a 100%);
  filter: saturate(1.06) contrast(1.04);
}

/* Bolinhas idênticas */
.dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 9999px;
  background: #6a5cff;
  box-shadow: 0 0 8px rgba(106, 92, 255, .6);
  flex: none;
  margin-top: .5rem;
}

/* PERSONAS */
.title-accent {
  background-image: linear-gradient(90deg, #bca8ff, #6a5cff 40%, #ff58bd 90%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  filter: drop-shadow(0 2px 10px rgba(110, 48, 255, .35));
}

.persona-card {
  border: 1px solid rgba(255, 255, 255, .10);
  border-radius: 28px;
  padding: 1.5rem;
  background: linear-gradient(180deg, #15142a 0%, #0b0b12 100%);
  box-shadow: 0 18px 60px rgba(0, 0, 0, .35);
  transition: transform .18s ease, box-shadow .18s ease;
}

.persona-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 24px 80px rgba(0, 0, 0, .45);
}

/* Bolha do ícone (tamanho idêntico) */
.icon-bubble {
  height: 72px;
  width: 72px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(60% 60% at 30% 30%, rgba(106, 92, 255, .35), rgba(106, 92, 255, .12));
  border: 1px solid rgba(255, 255, 255, .12);
  box-shadow: inset 0 0 18px rgba(106, 92, 255, .25), 0 0 18px rgba(106, 92, 255, .25);
}
</style>
