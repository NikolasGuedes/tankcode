<script setup lang="ts">
import { create as login } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import AppReveal from '@/components/AppReveal.vue';
import Grainient from '@/components/Grainient/Grainient.vue';
import RotatingText from '@/components/RotatingText/RotatingText.vue';
import { Head, Link } from '@inertiajs/vue3';
import { motion } from 'motion-v';
</script>

<template>

  <Head title="TankCode — Home" />

  <div class="min-h-screen bg-black text-white">
    <section class="relative h-screen min-h-[100svh] overflow-hidden">
      <Grainient class-name="pointer-events-none absolute inset-0 z-0 h-full w-full" color1="#FF9FFC" color2="#6A3BFF"
        color3="#B19EEF" :time-speed="0.5" :color-balance="0.0" :warp-strength="1.0" :warp-frequency="5.0"
        :warp-speed="2.0" :warp-amplitude="50.0" :blend-angle="0.0" :blend-softness="0.05" :rotation-amount="500.0"
        :noise-scale="2.0" :grain-amount="0.1" :grain-scale="2.0" :grain-animated="false" :contrast="1.5" :gamma="1.0"
        :saturation="1.0" :center-x="0.0" :center-y="0.0" :zoom="0.9" />

      <motion.div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 h-[100vh] bg-black"
        :initial="{ opacity: 1 }" :animate="{ opacity: 0 }"
        :transition="{ duration: 1, delay: 0.5, ease: 'easeInOut' }" />

      <div
        class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-[80vh] bg-gradient-to-b from-transparent via-black/50 to-black" />

      <header class="absolute inset-x-0 top-0 z-30">
        <AppReveal class-name="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8" :delay="0.05">
          <div class="grid grid-cols-[auto_1fr_auto] items-center gap-6">
            <a href="/" class="inline-flex items-center">
              <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-10 w-auto" />
            </a>

            <div class="flex items-center justify-end gap-3">
              <Link v-if="$page.props.auth?.user" href="/dashboard"
                class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(77,24,255,0.35)] transition hover:bg-[#5e2bff]">
                Acessar portal
              </Link>
              <Link v-else :href="login.url()"
                class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(77,24,255,0.35)] transition hover:bg-[#5e2bff]">
                Login
              </Link>
            </div>
          </div>
        </AppReveal>
      </header>

      <div class="absolute inset-0 z-20">
        <div class="mx-auto flex h-full max-w-7xl items-center justify-center px-4 sm:px-6 lg:px-8">
          <div class="mt-4 flex max-w-5xl flex-col items-center text-center sm:mt-8">
            <AppReveal :delay="0.18">
              <h1
                class="text-balance text-5xl font-light uppercase leading-[0.95] text-white sm:text-7xl lg:text-[6rem]">
                <span class="inline-flex flex-wrap items-center justify-center gap-3">
                  <span>VENHA</span>
                  <RotatingText :texts="['TANKAR', 'TANKAR', 'TANKAR']" :animatePresenceMode="'wait'"
                    :animatePresenceInitial="false"
                    mainClassName="inline-flex items-center justify-center font-black px-2 sm:px-2 md:px-3 bg-[#8471FF] text-white overflow-hidden py-0.5 sm:py-1 md:py-2 rounded-lg align-middle"
                    :staggerFrom="'last'" :initial="{ y: '100%' }" :animate="{ y: 0 }" :exit="{ y: '-120%' }"
                    :staggerDuration="0.08" splitLevelClassName="overflow-hidden pb-0.5 sm:pb-1 md:pb-1"
                    :transition="{ type: 'spring', damping: 30, stiffness: 400 }" :rotationInterval="5000" />
                </span>
                <span>ESTE DESAFIO.</span>
              </h1>
            </AppReveal>

            <AppReveal class-name="mt-5" :delay="0.28">
              <p class="max-w-4xl text-balance text-lg font-light leading-tight text-white/88 sm:text-2xl">
                Aprenda programação se divertindo e faça exercícios com as linguagens mais requisitadas do mercado.
              </p>
            </AppReveal>

            <AppReveal class-name="mt-10" :delay="0.38">
              <Link :href="$page.props.auth?.user ? '/dashboard' : login.url()"
                class="inline-flex min-w-72 items-center justify-center rounded-2xl bg-white px-10 py-4 text-xl font-black uppercase text-black shadow-[0_20px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-white/92">
                Iniciar agora
              </Link>
            </AppReveal>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
