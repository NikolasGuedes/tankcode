<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { onMounted } from 'vue'

/**
 * Scrolls smoothly to the section with the given selector and updates the active nav link.
 */
function goTo(selector: string, section: string) {
  const el = document.querySelector(selector)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })

  }
}



</script>

<template>
  <Head title="TankCode — Home">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div class="min-h-screen bg-[#0a0a0a] text-white antialiased">
    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 border-b border-white/5 bg-[#0a0a0a]/70 backdrop-blur">
      <div class="mx-auto flex max-w-[1200px] items-center justify-between gap-3 px-4 py-3">
        <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-9 w-auto" />

        <nav class="hidden md:block">
          <ul class="flex items-center gap-6 text-sm text-white/70">
            <li>
              <a href="#home"
                 class="navlink"
                 @click.prevent="goTo('#home','home')">home</a>
            </li>
            <li>
              <a href="#sobre"
                 class="navlink"
                
                 @click.prevent="goTo('#sobre','sobre')">sobre</a>
            </li>
            <li>
              <a href="#features"
                 class="navlink"
                 
                 @click.prevent="goTo('#features','features')">features</a>
            </li>
            <li>
              <a href="#exemplos"
                 class="navlink"
                
                 @click.prevent="goTo('#exemplos','exemplos')">exemplos</a>
            </li>
          </ul>
        </nav>

        <div class="flex items-center gap-2">
          <Link v-if="$page.props.auth?.user" :href="route('students')" class="btn-ghost">ESTUDANTES</Link>
          <template v-else>
            <Link :href="route('register')" class="btn-outline">REGISTRAR-SE</Link>
            <Link :href="route('login')" class="btn-solid">LOGIN</Link>
          </template>
        </div>
      </div>
    </header>

    <!-- HERO (id=home) com card sobreposto -->
    <section id="home" class="relative mx-auto mt-8 max-w-[1200px] px-4">
      <div class="relative overflow-visible rounded-[28px] border border-white/10">
        <img src="/imgs/Rectangle.png" alt="" class="absolute inset-0 h-full w-full rounded-[28px] object-cover" />

        <!-- glows -->
        <div class="pointer-events-none absolute -left-24 -top-20 z-10 h-72 w-72 rounded-full bg-[#635BFF]/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 -bottom-16 z-10 h-72 w-72 rounded-full bg-[#FF58BD]/25 blur-3xl"></div>

        <!-- conteúdo -->
        <div class="relative z-20 flex h-[560px] flex-col items-center justify-center px-6 text-center md:h-[600px] lg:h-[640px] pb-24 md:pb-28 lg:pb-32">
          <h1 class="hero-title mx-auto max-w-5xl text-4xl md:text-6xl lg:text-[64px] leading-[1.05]">
            VENHA <span class="text-white">TANKAR</span> ESTE DESAFIO!
          </h1>
          <p class="mx-auto mt-4 max-w-3xl text-sm text-white/80 md:text-base">
            Aprenda programação se divertindo e faça exercícios com as linguagens mais requisitadas do mercado.
          </p>

          <!-- CTA sempre vai para login -->
          <Link :href="route('login')" class="btn-cta mx-auto mt-7 inline-flex items-center justify-center">
            Iniciar agora
          </Link>
        </div>

        <!-- CARD (imagem do Figma) -->
        <div class="pointer-events-none absolute left-1/2 z-30 -translate-x-1/2 bottom-[-88px] md:bottom-[-96px] lg:bottom-[-104px]">
          <img
            src="/imgs/desafio exemplo.png"
            alt="Mock do card"
            class="w-[92vw] max-w-[1050px] rounded-2xl ring-1 ring-white/10 shadow-fig" />
        </div>
      </div>
    </section>

    <!-- espaçador para compensar o card -->
    <div class="h-[200px] md:h-[220px] lg:h-[240px]"></div>

    <!-- seções -->
    <section id="sobre" class="mx-auto max-w-[1200px] px-4">
      <h2 class="mb-2 text-xl font-semibold text-white/90">Sobre</h2>
      <p class="text-sm text-white/70">Texto de exemplo…</p>
    </section>

    <section id="features" class="mx-auto mt-16 max-w-[1200px] px-4">
      <h2 class="mb-2 text-xl font-semibold text-white/90">Features</h2>
      <p class="text-sm text-white/70">Texto de exemplo…</p>
    </section>

    <section id="exemplos" class="mx-auto mt-16 max-w-[1200px] px-4 pb-24">
      <h2 class="mb-2 text-xl font-semibold text-white/90">Exemplos</h2>
      <p class="text-sm text-white/70">Texto de exemplo…</p>
    </section>
  </div>
</template>

<style scoped>
/* nav */
.navlink { position: relative; padding:.25rem .25rem; transition: color .2s; }
.navlink:hover, .navlink--active { color:#fff; }
.navlink--active::after{
  content:""; position:absolute; left:50%; bottom:-8px; transform:translateX(-50%);
  width:22px; height:3px; border-radius:3px; background:linear-gradient(90deg,#6a5cff,#ff58bd);
}

/* tipografia do hero */
.hero-title{
  font-family: "Inter var", Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  font-weight: 800;
  letter-spacing: -0.02em; /* tracking mais fechado, igual Figma */
}

/* buttons */
.btn-solid{ background:#4000ff; color:#fff; padding:.55rem 1.1rem; border-radius:9999px; font-weight:600; font-size:.85rem; border:1px solid rgba(255,255,255,.12); transition:.2s; }
.btn-solid:hover{ background:#2a00cc; }
.btn-outline{ background:transparent; color:#8ea0ff; padding:.55rem 1rem; border-radius:9999px; font-weight:600; font-size:.85rem; border:1px solid #635bff66; }
.btn-outline:hover{ background:#ffffff10; }
.btn-ghost{ background:transparent; color:#fff; padding:.55rem 1rem; border-radius:10px; border:1px solid #ffffff22; }
.btn-ghost:hover{ background:#ffffff10; }

/* mock/fig */
.shadow-fig{ box-shadow: 0 24px 90px rgba(0,0,0,.55); border-radius: 16px; }

/* CTA */
.btn-cta{
  border:1.5px solid rgba(255,255,255,.7);
  padding:.7rem 1.25rem; border-radius:12px; font-weight:600; font-size:.95rem;
  backdrop-filter: blur(2px);
}
.btn-cta:hover{ background:rgba(255,255,255,.08); }

/* desativa o scroll CSS para usar nosso JS */
html{ scroll-behavior: auto; }
</style>
