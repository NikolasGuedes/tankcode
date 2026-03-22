<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/teacher' },
    { title: 'Atividades', href: '/teacher/activities' },
];
</script>

<template>
    <Head title="Atividades" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Atividades</h1>
                <p class="mt-2 text-lg text-white/70">Esta area esta em construcao e sera conectada ao fluxo pedagógico nas proximas etapas.</p>
            </AppReveal>
        </section>
    </AppLayout>
</template>
