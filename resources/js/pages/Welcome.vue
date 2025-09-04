<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount, ref } from 'vue'

// Header: sombra quando rolar
const scrolled = ref(false)
const onScroll = () => { scrolled.value = window.scrollY > 8 }
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
  <!-- SEO & fonts -->
  <Head title="TankCode — Desafios que viram oportunidades">
    <meta
      name="description"
      content="TankCode: plataforma universitária onde estudantes resolvem desafios de TI, acumulam pontos e desbloqueiam benefícios como bolsas, voluntariado e oportunidades."
    />
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <!-- Page wrapper -->
  <div class="min-h-screen bg-[#0b0b0b] text-[#EDEDEC]">
    <!-- Background: gradientes + partículas -->
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10">
      <!-- colorido -->
      <div
        class="absolute inset-0"
        style="
          background:
            radial-gradient(120% 90% at 20% 0%, #9333ea 0%, transparent 40%),
            radial-gradient(120% 80% at 80% 0%, #2563eb 0%, transparent 40%),
            radial-gradient(100% 80% at 50% 100%, #ec4899 0%, transparent 55%),
            linear-gradient(to bottom, #0b0b0b, #0b0b0b);
        "
      />
      <!-- partículas -->
      <div
        class="absolute inset-0 opacity-[0.08]"
        style="background-image: radial-gradient(#ffffff 1px, transparent 1.2px); background-size: 18px 18px;"
      />
    </div>

    <!-- Top nav (sticky + sombra on scroll) -->
    <header
      :class="[
        'sticky top-0 z-50 mx-auto flex max-w-6xl items-center justify-between px-6 py-3 lg:py-4 border-b bg-[#0b0b0b]/70 backdrop-blur transition-colors',
        scrolled ? 'shadow-md shadow-black/20 border-white/10' : 'border-white/10'
      ]"
    >
      <div class="flex items-center gap-2">
        <img src="/imgs/logo-tankcode.png" alt="Logotipo TankCode" class="h-8 w-auto lg:h-10" />
        <span class="hidden text-sm font-semibold tracking-tight sm:inline"></span>
      </div>
      <nav class="flex items-center gap-2 sm:gap-4 text-sm">
        <Link
          :href="route('register')"
          class="rounded-md border border-white/10 bg-gradient-to-r from-violet-500/30 to-blue-500/30 px-3 py-1.5 transition hover:from-violet-500/50 hover:to-blue-500/50"
        >Cadastrar</Link>
        <Link
          :href="route('login')"
          class="rounded-md border border-white/10 px-3 py-1.5 transition hover:bg-white/5"
        >Entrar</Link>
        <Link
          v-if="$page.props.auth?.user"
          :href="route('students')"
          class="hidden rounded-md border border-white/10 px-3 py-1.5 transition hover:bg-white/5 sm:inline"
        >Dashboard</Link>
      </nav>
    </header>

    <!-- Hero -->
    <section class="mx-auto grid max-w-6xl items-center gap-10 px-6 pb-20 pt-10 sm:pt-16 lg:grid-cols-2 lg:gap-14">
      <div class="space-y-6">
        <h1 class="fluid-h1 text-center font-extrabold leading-tight tracking-tight">
  <span class="typewriter-1line" style="--steps: 36;">
    Transforme desafios em
    <span class="bg-gradient-to-r from-violet-400 via-fuchsia-400 to-blue-400 bg-clip-text text-transparent">
      oportunidades
    </span>
  </span>
</h1>
        <p class="max-w-prose text-base/7 text-[#e2e2df]">
          No TankCode, estudantes completam desafios de TI, acumulam pontuação e desbloqueiam benefícios como bolsas,
          vagas de voluntariado e muito mais. Ideal para universidades que querem engajamento real e visibilidade do
          desempenho dos alunos.
        </p>
        <div class="flex flex-col gap-3 sm:flex-row">
          <Link
            :href="route('register')"
            class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-3 text-white shadow-md transition hover:from-violet-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-violet-400/50"
          >Começar agora</Link>
          <a
            href="#como-funciona"
            class="inline-flex items-center justify-center rounded-lg border border-white/15 px-5 py-3 transition hover:bg-white/5"
          >Como funciona</a>
        </div>
        <div class="flex items-center gap-6 pt-2 text-sm text-[#c8c8c3]">
          <div class="flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="opacity-80">
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-5-5 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 9z"
              />
            </svg>
            <span>Gamificado</span>
          </div>
          <div class="flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="opacity-80">
              <path d="M12 7a5 5 0 100 10 5 5 0 000-10zm7 5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Relatórios para a universidade</span>
          </div>
        </div>
      </div>
      <div class="relative">
        <div class="absolute -inset-4 -z-10 rounded-3xl bg-gradient-to-br from-violet-500/20 via-fuchsia-500/20 to-blue-500/20 blur-xl" aria-hidden="true" />
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f0f]/80 shadow-2xl">
          <img src="/imgs/hero-mock.png" alt="Exemplo do painel TankCode" class="h-auto w-full object-cover" />
        </div>
        <p class="mt-3 text-center text-xs text-[#d1d1d1]">*Mockup ilustrativo do painel de desafios e pontuação.</p>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="mx-auto max-w-6xl px-6 py-16 lg:py-20">
      <h2 class="text-center text-2xl font-bold tracking-tight sm:text-3xl">Por que usar o TankCode?</h2>
      <p class="mx-auto mt-3 max-w-2xl text-center text-sm text-[#cfcfcf]">
        Um ecossistema simples de usar para estudantes e universidades, com foco em resultados mensuráveis.
      </p>

      <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Card 1 com borda neon 1px -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-violet-500/40 via-fuchsia-500/40 to-blue-500/40 interactive-scale">
          <article class="rounded-[11px] bg-gradient-to-br from-violet-950/40 to-blue-950/30 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-violet-300">Desafios práticos</h3>
            <p class="mt-2 text-sm text-[#e0e0dd]">
              Programação, dados e infraestrutura, com validação automática e feedback.
            </p>
          </article>
        </div>

        <!-- Card 2 -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-blue-500/40 via-fuchsia-500/40 to-violet-500/40 interactive-scale">
          <article class="rounded-[11px] bg-gradient-to-br from-blue-950/30 to-pink-950/30 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-blue-300">Métricas e ranking</h3>
            <p class="mt-2 text-sm text-[#e0e0dd]">
              Pontuação por aluno, ranking por turma e relatórios para coordenação.
            </p>
          </article>
        </div>

        <!-- Card 3 -->
        <div class="relative rounded-xl p-[1px] bg-gradient-to-r from-fuchsia-500/40 via-violet-500/40 to-blue-500/40   interactive-scale">
          <article class="rounded-[11px] bg-gradient-to-br from-fuchsia-950/30 to-violet-950/30 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-pink-300">Benefícios desbloqueáveis</h3>
            <p class="mt-2 text-sm text-[#e0e0dd]">
              Acesso a bolsas, monitorias e ações de extensão conforme o desempenho.
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="mx-auto max-w-6xl px-6 pb-10">
      <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Aprovado por quem usa</h2>

      <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Testimonial 1 -->
        <div class="relative rounded-2xl p-[1px] bg-gradient-to-r from-violet-500/40 via-fuchsia-500/40 to-blue-500/40 interactive-scale">
          <article class="rounded-[15px] bg-gradient-to-br from-violet-950/30 to-blue-950/20 p-5 shadow-sm">
            <p class="text-sm text-[#ededed]">
              “Os desafios deram um gás na turma. O ranking motiva e os relatórios ajudam muito nas reuniões.”
            </p>
            <p class="mt-3 text-xs text-[#c7c7c0]">Ana — Coordenação</p>
          </article>
        </div>

        <!-- Testimonial 2 -->
        <div class="relative rounded-2xl p-[1px] bg-gradient-to-r from-blue-500/40 via-fuchsia-500/40 to-violet-500/40 interactive-scale">
          <article class="rounded-[15px] bg-gradient-to-br from-blue-950/30 to-fuchsia-950/20 p-5 shadow-sm">
            <p class="text-sm text-[#ededed]">
              “Gostei da validação automática e dos feedbacks. Fica claro o que estudar pra subir de nível.”
            </p>
            <p class="mt-3 text-xs text-[#c7c7c0]">Pedro — Estudante</p>
          </article>
        </div>

        <!-- Testimonial 3 -->
        <div class="relative rounded-2xl p-[1px] bg-gradient-to-r from-fuchsia-500/40 via-violet-500/40 to-blue-500/40 interactive-scale">
          <article class="rounded-[15px] bg-gradient-to-br from-violet-950/30 to-pink-950/20 p-5 shadow-sm">
            <p class="text-sm text-[#ededed]">
              “Conseguimos atrelar benefícios ao desempenho de forma transparente. O engajamento subiu.”
            </p>
            <p class="mt-3 text-xs text-[#c7c7c0]">Rafael — Extensão</p>
          </article>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section id="como-funciona" class="mx-auto max-w-6xl px-6 py-16 lg:py-20">
      <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-violet-950/40 to-blue-950/40 p-8 shadow-sm">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Como funciona</h2>
        <ol class="mt-6 grid gap-6 sm:grid-cols-3">
          <li class="rounded-lg border border-white/10 p-5 bg-[#0f0f0f]/60">
            <p class="text-sm font-semibold text-violet-300">1) Cadastre-se</p>
            <p class="mt-2 text-sm text-[#e0e0dd]">Crie sua conta com o e-mail institucional.</p>
          </li>
          <li class="rounded-lg border border-white/10 p-5 bg-[#0f0f0f]/60">
            <p class="text-sm font-semibold text-blue-300">2) Resolva desafios</p>
            <p class="mt-2 text-sm text-[#e0e0dd]">Escolha trilhas e suba sua solução. A validação é automática.</p>
          </li>
          <li class="rounded-lg border border-white/10 p-5 bg-[#0f0f0f]/60">
            <p class="text-sm font-semibold text-pink-300">3) Ganhe pontos e benefícios</p>
            <p class="mt-2 text-sm text-[#e0e0dd]">Suba no ranking e desbloqueie oportunidades na universidade.</p>
          </li>
        </ol>
        <div class="mt-6">
          <Link
            :href="route('register')"
            class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-3 text-white shadow-sm transition hover:from-violet-500 hover:to-blue-500"
          >Criar conta grátis</Link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-10">
      <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 sm:flex-row">
        <div class="flex items-center gap-2">
          <span class="text-xs">© {{ new Date().getFullYear() }} TankCode</span>
        </div>
      </div>
    </footer>
  </div>
</template>
<style scoped>  

</style>