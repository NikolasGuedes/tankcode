<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { BookOpen, ClipboardList } from 'lucide-vue-next';

defineProps<{
    stats: {
        points: number;
        classrooms: number;
        students: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/teacher' },
];

const modules = [
    { title: 'Atividades', description: 'Acompanhe e gerencie as atividades pedagogicas vinculadas as suas turmas.', href: '/teacher/activities', icon: ClipboardList },
];
</script>

<template>
    <Head title="Portal do Professor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Portal do Professor</h1>
                <p class="mt-2 text-lg text-white/70">Visualize suas turmas, acompanhe os alunos vinculados e acesse suas atividades.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Pontos de Ensino</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.points }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Turmas</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.classrooms }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Alunos</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.students }}</p>
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
                            <BookOpen class="size-4" />
                            Acessar
                        </p>
                    </a>
                </AppReveal>
            </div>
        </section>
    </AppLayout>
</template>
