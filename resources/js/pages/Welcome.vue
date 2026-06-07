<script setup lang="ts">
import { create as login } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import AppReveal from '@/components/AppReveal.vue';
import Grainient from '@/components/Grainient/Grainient.vue';
import RotatingText from '@/components/RotatingText/RotatingText.vue';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpenCheck, ChartNoAxesCombined, LayoutDashboard, Medal, Sparkles, Trophy, Users } from 'lucide-vue-next';
import { motion } from 'motion-v';
import type { Component } from 'vue';

type ShowcaseFeature = {
    icon: Component;
    title: string;
    description: string;
};

const professorFeatures: ShowcaseFeature[] = [
    {
        icon: LayoutDashboard,
        title: 'Dashboard com leitura imediata',
        description: 'O professor acompanha turmas, entregas, aproveitamento e distribuição das atividades em um só lugar.',
    },
    {
        icon: BookOpenCheck,
        title: 'Criação de atividades sem atrito',
        description: 'O fluxo de cadastro guia etapa por etapa, da estrutura da questão até a revisão final da pontuação.',
    },
    {
        icon: Users,
        title: 'Gestão de sala mais organizada',
        description: 'Status, filtros e acompanhamento visual ajudam a agir rápido sem perder o contexto pedagógico.',
    },
];

const studentFeatures: ShowcaseFeature[] = [
    {
        icon: Trophy,
        title: 'Ranking que puxa engajamento',
        description: 'A competição saudável aparece de forma clara, valorizando o progresso da turma e do aluno.',
    },
    {
        icon: Medal,
        title: 'Perfil com evolução visível',
        description: 'Pontuação, bio, conquistas e posição no ranking reforçam a sensação de avanço a cada desafio.',
    },
    {
        icon: Sparkles,
        title: 'Aprendizado com cara de jogo',
        description: 'A experiência visual conversa com o conteúdo e faz a programação parecer mais acessível e divertida.',
    },
];

const footerLinks = [
    { label: 'Professor', href: '#professores' },
    { label: 'Aluno', href: '#alunos' },
    { label: 'Login', href: login.url() },
];
</script>

<template>
    <Head title="TankCode — Home" />

    <div class="min-h-screen bg-background text-foreground">
        <section class="relative h-screen min-h-[100svh] overflow-hidden">
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

            <motion.div
                class="pointer-events-none absolute inset-x-0 bottom-0 z-20 h-[100vh] bg-black"
                :initial="{ opacity: 1 }"
                :animate="{ opacity: 0 }"
                :transition="{ duration: 1, delay: 0.5, ease: 'easeInOut' }"
            />

            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 h-[80vh] bg-gradient-to-b from-transparent via-black/50 to-black" />

            <header class="absolute inset-x-0 top-0 z-30">
                <AppReveal class-name="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8" :delay="0.05">
                    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-6">
                        <a href="/" class="inline-flex items-center">
                            <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-10 w-auto" />
                        </a>

                        <div class="flex items-center justify-end gap-3">
                            <Link
                                v-if="$page.props.auth?.user"
                                href="/dashboard"
                                class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(77,24,255,0.35)] transition hover:bg-[#5e2bff]"
                            >
                                Acessar portal
                            </Link>
                            <Link
                                v-else
                                :href="login.url()"
                                class="inline-flex items-center gap-2 rounded-full bg-black px-8 py-2.5 text-sm font-semibold text-white shadow-[0_16px_40px_rgba(77,24,255,0.35)] transition hover:bg-[#5e2bff]"
                            >
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
                            <h1 class="text-5xl leading-[0.95] font-light text-balance text-white uppercase sm:text-7xl lg:text-[6rem]">
                                <span class="inline-flex flex-wrap items-center justify-center gap-3">
                                    <span>VENHA</span>
                                    <RotatingText
                                        :texts="['TANKAR', 'TANKAR', 'TANKAR']"
                                        :animatePresenceMode="'wait'"
                                        :animatePresenceInitial="false"
                                        mainClassName="inline-flex items-center justify-center font-black px-2 sm:px-2 md:px-3 bg-[#8471FF] text-white overflow-hidden py-0.5 sm:py-1 md:py-2 rounded-lg align-middle"
                                        :staggerFrom="'last'"
                                        :initial="{ y: '100%' }"
                                        :animate="{ y: 0 }"
                                        :exit="{ y: '-120%' }"
                                        :staggerDuration="0.08"
                                        splitLevelClassName="overflow-hidden pb-0.5 sm:pb-1 md:pb-1"
                                        :transition="{ type: 'spring', damping: 30, stiffness: 400 }"
                                        :rotationInterval="5000"
                                    />
                                </span>
                                <span>ESTE DESAFIO.</span>
                            </h1>
                        </AppReveal>

                        <AppReveal class-name="mt-5" :delay="0.28">
                            <p class="max-w-4xl text-lg leading-tight font-light text-balance text-white/88 sm:text-2xl">
                                Aprenda programação se divertindo e faça exercícios com as linguagens mais requisitadas do mercado.
                            </p>
                        </AppReveal>

                        <AppReveal class-name="mt-10" :delay="0.38">
                            <Link
                                :href="$page.props.auth?.user ? '/dashboard' : login.url()"
                                class="inline-flex min-w-72 items-center justify-center rounded-2xl bg-white px-10 py-4 text-xl font-black text-black uppercase shadow-[0_20px_60px_rgba(255,255,255,0.16)] transition hover:-translate-y-0.5 hover:bg-white/92"
                            >
                                Iniciar agora
                            </Link>
                        </AppReveal>
                    </div>
                </div>
            </div>
        </section>

        <section id="professores" class="bg-background py-24 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AppReveal :delay="0.08">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold tracking-[0.28em] text-secondary uppercase">Área do professor</p>
                        <h2 class="mt-4 text-4xl font-semibold text-white sm:text-5xl">
                            Um painel que organiza a operação e deixa a leitura do desempenho mais clara.
                        </h2>
                        <p class="mt-5 text-lg leading-8 text-muted-foreground">
                            A linguagem visual do sistema valoriza informação útil, blocos bem definidos e ações rápidas. Na landing, trouxemos essa
                            mesma sensação para mostrar o produto como ele realmente acontece.
                        </p>
                    </div>
                </AppReveal>

                <div class="mt-14 grid gap-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] lg:items-start">
                    <AppReveal :delay="0.16">
                        <div class="rounded-[32px] border border-border bg-card p-4">
                            <img
                                src="/imgs/sistema/Area dashboards professor.png"
                                alt="Painel com métricas do professor"
                                class="w-full rounded-[24px] border border-white/6"
                            />
                        </div>
                    </AppReveal>

                    <div class="grid gap-6">
                        <AppReveal :delay="0.22">
                            <div class="rounded-[28px] border border-border bg-card p-7">
                                <div class="grid gap-4">
                                    <div
                                        v-for="feature in professorFeatures"
                                        :key="feature.title"
                                        class="rounded-2xl border border-border bg-[var(--surface-elevated)] p-5"
                                    >
                                        <component :is="feature.icon" class="h-5 w-5 text-secondary" />
                                        <h3 class="mt-4 text-xl font-semibold text-white">{{ feature.title }}</h3>
                                        <p class="mt-2 text-sm leading-7 text-muted-foreground">{{ feature.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </AppReveal>

                        <AppReveal :delay="0.3">
                            <div class="rounded-[28px] border border-border bg-card p-4">
                                <img
                                    src="/imgs/sistema/Area atividades professor.png"
                                    alt="Fluxo de criação de atividade no TankCode"
                                    class="w-full rounded-[22px] border border-white/6"
                                />
                            </div>
                        </AppReveal>
                    </div>
                </div>
            </div>
        </section>

        <section id="alunos" class="bg-background py-24 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AppReveal :delay="0.08">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold tracking-[0.28em] text-[var(--green_site)] uppercase">Experiência do aluno</p>
                        <h2 class="mt-4 text-4xl font-semibold text-white sm:text-5xl">
                            Perfil, ranking e atividades montados para manter o ritmo da aprendizagem.
                        </h2>
                        <p class="mt-5 text-lg leading-8 text-muted-foreground">
                            O aluno encontra clareza para agir e motivação para continuar. Pontos, conquistas e desafios aparecem como parte natural
                            da jornada.
                        </p>
                    </div>
                </AppReveal>

                <div class="mt-14 grid gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] lg:items-stretch">
                    <AppReveal :delay="0.16">
                        <div class="rounded-[28px] border border-border bg-card p-4">
                            <img
                                src="/imgs/sistema/Area perfil aluno.png"
                                alt="Perfil do aluno no TankCode"
                                class="w-full rounded-[22px] border border-white/6"
                            />
                        </div>
                    </AppReveal>

                    <AppReveal :delay="0.22">
                        <div class="flex h-full flex-col rounded-[28px] border border-border bg-card p-7">
                            <div
                                class="inline-flex w-fit items-center gap-2 rounded-full border border-border bg-[var(--surface-elevated)] px-4 py-2 text-xs font-semibold tracking-[0.24em] text-[var(--green_site)] uppercase"
                            >
                                <ChartNoAxesCombined class="h-4 w-4" />
                                Jornada ativa
                            </div>
                            <p class="mt-6 text-3xl font-semibold text-white">Aprender, competir e evoluir sem sair do fluxo.</p>
                            <p class="mt-4 text-sm leading-7 text-muted-foreground">
                                A landing agora mostra com mais honestidade como o produto sustenta o engajamento dos alunos no dia a dia, usando as
                                próprias telas do sistema como prova.
                            </p>
                            <div class="mt-8 grid gap-3 text-sm text-foreground/80">
                                <div class="rounded-2xl border border-border bg-[var(--surface-elevated)] px-4 py-3">
                                    Ranking da turma com pontuação visível
                                </div>
                                <div class="rounded-2xl border border-border bg-[var(--surface-elevated)] px-4 py-3">
                                    Atividades com status e prazos fáceis de ler
                                </div>
                                <div class="rounded-2xl border border-border bg-[var(--surface-elevated)] px-4 py-3">
                                    Perfil com conquistas, bio e progresso acumulado
                                </div>
                            </div>
                        </div>
                    </AppReveal>
                </div>

                <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] lg:items-start">
                    <AppReveal :delay="0.28">
                        <div class="grid gap-4">
                            <div v-for="feature in studentFeatures" :key="feature.title" class="rounded-[24px] border border-border bg-card p-6">
                                <component :is="feature.icon" class="h-5 w-5 text-[var(--green_site)]" />
                                <h3 class="mt-4 text-xl font-semibold text-white">{{ feature.title }}</h3>
                                <p class="mt-2 text-sm leading-7 text-muted-foreground">{{ feature.description }}</p>
                            </div>
                        </div>
                    </AppReveal>

                    <AppReveal :delay="0.34">
                        <div class="rounded-[28px] border border-border bg-card p-4">
                            <img
                                src="/imgs/sistema/Area perfil aluno 02.png"
                                alt="Atividades e ranking do aluno no TankCode"
                                class="h-auto w-full rounded-[22px] border border-border"
                            />
                        </div>
                    </AppReveal>
                </div>
            </div>
        </section>

        <footer class="bg-background py-10 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="mt-8 flex flex-col gap-5 border-t border-border/70 pt-6 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center gap-4">
                        <img src="/imgs/logo-tankcode.png" alt="TankCode" class="h-9 w-auto opacity-90" />
                        <p>Plataforma para aprender programação com desafios, ranking e acompanhamento real.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-5">
                        <a v-for="link in footerLinks" :key="link.label" :href="link.href" class="transition hover:text-white">
                            {{ link.label }}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
