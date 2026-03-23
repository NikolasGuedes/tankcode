<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Briefcase, MapPinned } from 'lucide-vue-next';

defineProps<{
    stats: {
        school: string;
        points: number;
        directors: number;
        active_directors: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/owner' },
];

const modules = [
    { title: 'Diretores', description: 'Cadastre diretores e distribua a atuacao deles entre os pontos de ensino da sua escola.', href: '/owner/directors', icon: Briefcase },
];
</script>

<template>
    <Head title="Portal do Owner" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Portal do Owner</h1>
                <p class="mt-2 text-lg text-white/70">Gerencie os diretores e acompanhe a distribuicao operacional da escola sob sua responsabilidade.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-4">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Escola</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ stats.school }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Pontos de Ensino</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.points }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Diretores</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.directors }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Diretores Ativos</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.active_directors }}</p>
                    </div>
                </div>
            </AppReveal>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <AppReveal
                    v-for="module in modules"
                    :key="module.title"
                    :delay="0.08"
                    class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_45px_rgba(0,0,0,0.25)] transition hover:-translate-y-0.5 hover:border-secondary"
                >
                    <a :href="module.href" class="block">
                        <component :is="module.icon" class="h-12 w-12 rounded-2xl bg-primary/15 p-3 text-secondary" />
                        <h2 class="mt-8 text-2xl font-semibold text-white">{{ module.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-white/70">{{ module.description }}</p>
                        <p class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-secondary">
                            <MapPinned class="size-4" />
                            Acessar
                        </p>
                    </a>
                </AppReveal>
            </div>
        </section>
    </AppLayout>
</template>
