<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import Grainient from '@/components/Grainient/Grainient.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-dvh bg-black lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <div class="relative hidden overflow-hidden lg:flex">
            <Grainient
                class-name="pointer-events-none absolute inset-0 z-0 h-full w-full"
                color1="#FF9FFC"
                color2="#6A3BFF"
                color3="#B19EEF"
                :time-speed="0.5"
                :color-balance="0.0"
                :warp-strength="1.0"
                :warp-frequency="5.0"
                :warp-speed="2.0"
                :warp-amplitude="50.0"
                :blend-angle="0.0"
                :blend-softness="0.05"
                :rotation-amount="500.0"
                :noise-scale="2.0"
                :grain-amount="0.1"
                :grain-scale="2.0"
                :grain-animated="false"
                :contrast="1.5"
                :gamma="1.0"
                :saturation="1.0"
                :center-x="0.0"
                :center-y="0.0"
                :zoom="0.9"
            />

            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-[70%] bg-gradient-to-b from-transparent via-black/45 to-black" />

            <div class="absolute left-8 top-8 z-20 xl:left-10 xl:top-10">
                <AppReveal :delay="0.05">
                    <Link :href="home.url()" class="inline-flex items-center gap-3 text-lg font-medium text-white">
                        <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-10 w-auto" />
                    </Link>
                </AppReveal>
            </div>
        </div>

        <div class="flex min-h-dvh items-center justify-center px-6 py-10 sm:px-8 lg:px-12">
            <div class="mx-auto flex w-full max-w-xl flex-col justify-center">
                <AppReveal class-name="mb-8 flex flex-col space-y-2 text-center" :delay="0.1">
                    <h1 class="text-3xl font-semibold tracking-tight text-white" v-if="title">{{ title }}</h1>
                    <p class="text-base text-white/72" v-if="description">{{ description }}</p>
                </AppReveal>

                <AppReveal :delay="0.2">
                    <div class="rounded-[1.5rem] p-6 sm:p-8">
                        <slot />
                    </div>
                </AppReveal>
            </div>
        </div>
    </div>
</template>
