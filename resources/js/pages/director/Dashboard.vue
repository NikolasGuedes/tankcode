<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { BookOpen, GraduationCap, Users } from 'lucide-vue-next';

defineProps<{
    stats: {
        classrooms: number;
        teachers: number;
        students: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
];

const modules = [
    { title: 'Turmas', description: 'Gerencie as turmas da escola e acompanhe os alunos vinculados.', href: '/director/classrooms', icon: BookOpen },
    { title: 'Professores', description: 'Cadastre professores e distribua o acesso por ponto de ensino.', href: '/director/teachers', icon: Users },
    { title: 'Alunos', description: 'Cadastre alunos, acompanhe a vinculacao e organize por unidade.', href: '/director/students', icon: GraduationCap },
];
</script>

<template>
    <Head title="Portal da Diretoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Portal da Diretoria</h1>
                <p class="mt-2 text-lg text-white/70">Gerencie turmas, professores e alunos vinculados aos pontos de ensino sob sua responsabilidade.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Turmas</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.classrooms }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Professores</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ stats.teachers }}</p>
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
                        <p class="mt-8 text-sm font-semibold text-secondary">Acessar</p>
                    </a>
                </AppReveal>
            </div>
        </section>
    </AppLayout>
</template>
