<script setup lang="ts">
import { create as login } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import Grainient from '@/components/Grainient/Grainient.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
</script>

<template>

  <Head title="TankCode — Home">
  </Head>

  <div class="min-h-screen bg-black text-white">
    <section class="relative h-screen min-h-[100svh] overflow-hidden">
      <Grainient class-name="absolute inset-0 h-full w-full" color1="#FF9FFC" color2="#6A3BFF" color3="#B19EEF"
        :time-speed="0.5" :color-balance="0.0" :warp-strength="1.0" :warp-frequency="5.0" :warp-speed="2.0"
        :warp-amplitude="50.0" :blend-angle="0.0" :blend-softness="0.05" :rotation-amount="500.0" :noise-scale="2.0"
        :grain-amount="0.1" :grain-scale="2.0" :grain-animated="false" :contrast="1.5" :gamma="1.0" :saturation="1.0"
        :center-x="0.0" :center-y="0.0" :zoom="0.9" />

      <div class="absolute inset-x-0 bottom-0 h-[80vh] bg-gradient-to-b from-transparent via-black/50 to-black" />

     

      <header class="absolute inset-x-0 top-0 z-20">
        <div class="mx-auto grid max-w-7xl grid-cols-[auto_1fr_auto] items-center gap-6 px-4 py-5 sm:px-6 lg:px-8">
          <a href="/" class="inline-flex items-center">
            <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-10 w-auto" />
          </a>

          <nav class="hidden justify-center md:flex">
            <div class="flex items-center gap-10 text-sm text-white/80">
              <a href="#" class="border-b-2 border-white pb-1 font-medium text-white">Sobre</a>
              <a href="#" class="transition hover:text-white">Features</a>
              <a href="#" class="transition hover:text-white">Exemplos</a>
            </div>
          </nav>

          <div class="flex items-center justify-end gap-3">
            <Link v-if="$page.props.auth?.user" href="/dashboard"
              class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur hover:bg-white/15">
              Acessar portal
            </Link>
            <Link v-else :href="login.url()"
              class="inline-flex items-center gap-2 rounded-full bg-[#4d18ff] px-8 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(77,24,255,0.35)] transition hover:bg-[#5e2bff]">
              Login
            </Link>
          </div>
        </div>
      </header>

      <div class="relative z-10 flex h-full items-center">
        <div class="mx-auto flex h-full w-full max-w-7xl items-center px-4 sm:px-6 lg:px-8">
          <div class="flex w-full flex-col items-center pt-24 text-center sm:pt-28 lg:pt-20">
            <div class="w-full max-w-[1080px]">
              <h1
                class="text-left text-[3.6rem] font-[700] uppercase leading-[0.94] tracking-[-0.06em] text-white sm:text-[5.2rem] lg:text-[6.8rem]">
                Venha
                <span
                  class="mx-2 inline-block rounded-[0.9rem] bg-white/16 px-3 py-1 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,0.12)]">
                  tankar
                </span>
                <br />
                este desafio.
              </h1>

              <p class="mx-auto mt-14 max-w-4xl text-center text-lg leading-[1.45] text-white/82 sm:text-2xl">
                Aprenda programacao se divertindo e faca exercicios com as linguagens mais requisitadas do mercado.
              </p>

              <div class="mt-10 flex justify-center">
                <Link :href="login.url()"
                  class="inline-flex items-center gap-2 rounded-2xl bg-white px-10 py-4 text-lg font-[800] uppercase tracking-[-0.03em] text-black shadow-[0_20px_60px_rgba(255,255,255,0.18)] transition hover:bg-white/90">
                  Iniciar agora
                  <ArrowRight class="size-4" />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
