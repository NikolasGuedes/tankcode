<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Building2, School, Users } from 'lucide-vue-next';

const props = defineProps<{
    headline: string;
    description: string;
    role?: string | null;
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.headline,
        href: page.url,
    },
];

const cards = [
    {
        title: 'Escopo atual',
        description: 'Seu acesso sera refinado conforme avancarmos nas proximas etapas do projeto.',
        icon: Building2,
    },
    {
        title: 'Estrutura escolar',
        description: 'A base agora considera escola e ponto de ensino como pilares do dominio.',
        icon: School,
    },
    {
        title: 'Roles',
        description: 'O login e compartilhado e o redirecionamento depende do papel vinculado ao usuario.',
        icon: Users,
    },
];
</script>

<template>
    <Head :title="headline" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <div class="rounded-3xl border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-secondary">{{ role ?? 'Portal' }}</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-white">{{ headline }}</h1>
                <p class="mt-3 max-w-2xl text-base text-white/70">{{ description }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <article v-for="card in cards" :key="card.title" class="rounded-3xl border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]">
                    <component :is="card.icon" class="h-10 w-10 rounded-2xl bg-primary/15 p-2 text-secondary" />
                    <h2 class="mt-4 text-lg font-semibold text-white">{{ card.title }}</h2>
                    <p class="mt-2 text-sm leading-6 text-white/70">{{ card.description }}</p>
                </article>
            </div>
        </section>
    </AppLayout>
</template>
