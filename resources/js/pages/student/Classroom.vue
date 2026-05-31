<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppTablePagination from '@/components/AppTablePagination.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useInitials } from '@/composables/useInitials';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ArrowUpRight, CalendarClock, ChevronRight, Medal, Users } from 'lucide-vue-next';
import { watch } from 'vue';

type ActivityRow = {
    id: number;
    title: string;
    description: string;
    due_date: string | null;
    deadline_label: string;
    deadline_group: string;
    state: 'pendente' | 'vence_hoje' | 'vence_semana' | 'atrasada' | 'respondida';
    href: string;
    submitted_at: string | null;
    score: string | null;
    total_points: string | number;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedActivities = {
    data: ActivityRow[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

const props = defineProps<{
    classroom: {
        name: string;
        code: string;
        point_of_school: string;
        teacher: string;
    };
    score: {
        student_points: number;
        classroom_rank: number;
    };
    activities: PaginatedActivities;
    activity_filters: {
        search: string;
        state: string;
        sort: string;
    };
    activity_summary: Array<{
        label: string;
        count: number;
    }>;
    classmates: Array<{
        id: number;
        name: string;
        email: string;
        avatar: string | null;
        score: number;
        ranking_position: number;
        href: string;
        is_current_user: boolean;
    }>;
}>();

const { getInitials } = useInitials();

const filtersForm = useForm({
    search: props.activity_filters.search ?? '',
    state: props.activity_filters.state ?? 'all',
    sort: props.activity_filters.sort ?? 'deadline_asc',
});

const applyFilters = () => {
    router.get(
        '/student/minha-sala',
        {
            search: filtersForm.search || undefined,
            state: filtersForm.state !== 'all' ? filtersForm.state : undefined,
            sort: filtersForm.sort !== 'deadline_asc' ? filtersForm.sort : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const debouncedApplyFilters = useDebounceFn(() => {
    applyFilters();
}, 350);

const resetFilters = () => {
    filtersForm.search = '';
    filtersForm.state = 'all';
    filtersForm.sort = 'deadline_asc';
    applyFilters();
};

watch(
    () => filtersForm.search,
    () => debouncedApplyFilters(),
);

watch(
    () => [filtersForm.state, filtersForm.sort],
    () => applyFilters(),
);

const deadlineClass = (state: string) => {
    if (state === 'atrasada' || state === 'vence_hoje')
        return 'border-rose-200/70 bg-rose-500/35 text-white shadow-[0_0_0_1px_rgba(251,191,191,0.12)]';
    if (state === 'vence_semana') return 'border-amber-200/70 bg-amber-400/35 text-white shadow-[0_0_0_1px_rgba(253,230,138,0.12)]';
    if (state === 'respondida') return 'border-emerald-200/70 bg-emerald-500/35 text-white shadow-[0_0_0_1px_rgba(167,243,208,0.12)]';

    return 'border-white/35 bg-[#5a48ff]/45 text-white shadow-[0_0_0_1px_rgba(255,255,255,0.08)]';
};

const formatPoints = (value: string | number | null) =>
    Number(value ?? 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
</script>

<template>
    <Head title="Minha Sala" />

    <StudentLayout>
        <section class="grid gap-8 xl:grid-cols-[minmax(0,1.35fr)_380px] xl:items-start">
            <AppReveal class-name="order-2 rounded-[2rem] border border-white/10 bg-[#271D67] p-6 xl:order-1">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Minhas Atividades</p>
                        <h1 class="mt-4 text-3xl font-semibold text-white">{{ classroom.name }}</h1>
                        <p class="mt-2 text-white/60">Professor: {{ classroom.teacher }}</p>
                    </div>
                    <CalendarClock class="mt-1 size-6 text-[#8f7bff]" />
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div
                        v-for="summary in activity_summary"
                        :key="summary.label"
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-white/85"
                    >
                        <span>{{ summary.label }}</span>
                        <span class="rounded-full bg-[#8f7bff] px-2 py-0.5 text-xs font-semibold text-white">{{ summary.count }}</span>
                    </div>
                </div>

                <div class="mt-8 grid gap-3 lg:grid-cols-[minmax(0,1.2fr)_220px_220px_auto] lg:items-end">
                    <div class="space-y-2">
                        <label class="text-xs tracking-[0.18em] text-white/45 uppercase">Buscar atividade</label>
                        <Input
                            v-model="filtersForm.search"
                            placeholder="Título ou descrição"
                            class="border-white/10 bg-white/6 text-white placeholder:text-white/35"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs tracking-[0.18em] text-white/45 uppercase">Situação</label>
                        <Select v-model="filtersForm.state">
                            <SelectTrigger class="border-white/10 bg-white/6 text-white">
                                <SelectValue placeholder="Todas" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todas</SelectItem>
                                <SelectItem value="respondida">Respondidas</SelectItem>
                                <SelectItem value="vence_hoje">Vencem hoje</SelectItem>
                                <SelectItem value="vence_semana">Vencem nesta semana</SelectItem>
                                <SelectItem value="atrasada">Atrasadas</SelectItem>
                                <SelectItem value="pendente">Pendentes</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs tracking-[0.18em] text-white/45 uppercase">Ordenar por</label>
                        <Select v-model="filtersForm.sort">
                            <SelectTrigger class="border-white/10 bg-white/6 text-white">
                                <SelectValue placeholder="Selecione" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="deadline_asc">Prazo mais próximo</SelectItem>
                                <SelectItem value="deadline_desc">Prazo mais distante</SelectItem>
                                <SelectItem value="title_asc">Título A-Z</SelectItem>
                                <SelectItem value="title_desc">Título Z-A</SelectItem>
                                <SelectItem value="newest">Mais recentes</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <Button type="button" variant="outline" class="border-white/10 bg-white/6 text-white hover:bg-white/10" @click="resetFilters">
                        Limpar filtros
                    </Button>
                </div>

                <div class="mt-8 grid gap-4 2xl:grid-cols-2">
                    <article
                        v-for="activity in activities.data"
                        :key="activity.id"
                        class="group relative overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/6 p-5 transition duration-200 hover:-translate-y-1 hover:border-white/20 hover:bg-white/8"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-[#8471ff]"></div>

                        <span
                            class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold tracking-[0.01em]"
                            :class="deadlineClass(activity.state)"
                        >
                            {{ activity.deadline_label }}
                        </span>

                        <h2 class="mt-5 text-2xl font-semibold text-white">{{ activity.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-white/70">{{ activity.description }}</p>

                        <div class="mt-6 flex items-center justify-between text-sm">
                            <span class="text-white/55">{{ classroom.teacher }}</span>
                            <span class="font-medium text-[#d9d2ff]">{{ classroom.code }}</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span class="text-white/55">Valor total</span>
                            <span class="font-medium text-[#d9d2ff]">{{ formatPoints(activity.total_points) }} pts</span>
                        </div>

                        <div v-if="activity.score !== null" class="mt-2 flex items-center justify-between text-sm">
                            <span class="text-emerald-200/80">Sua nota</span>
                            <span class="font-medium text-emerald-200">{{ formatPoints(activity.score) }} pts</span>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-3">
                            <span class="text-sm text-white/50 transition group-hover:text-white/70">Abrir atividade</span>
                            <Button as-child class="border-[var(--primary)]/40 bg-[var(--secondary)] text-white transition">
                                <Link :href="activity.href">
                                    Ver detalhes
                                    <ArrowUpRight class="size-4" />
                                </Link>
                            </Button>
                        </div>
                    </article>
                </div>

                <div
                    v-if="!activities.data.length"
                    class="mt-6 rounded-[1.5rem] border border-dashed border-white/12 bg-white/4 px-5 py-10 text-center text-sm text-white/60"
                >
                    Nenhuma atividade publicada foi encontrada para os filtros atuais.
                </div>

                <AppTablePagination :meta="activities" :links="activities.links" />
            </AppReveal>

            <AppReveal class-name="order-1 rounded-[2rem] border border-white/10 bg-[#271D67] p-6 xl:sticky xl:top-6 xl:order-2" :delay="0.06">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between xl:flex-col xl:items-start">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Colegas da sala</p>
                        <h2 class="mt-4 text-3xl font-semibold text-white">Ranking da turma</h2>
                        <p class="mt-2 text-white/60">
                            Seu score atual é {{ score.student_points }} pts. Hoje ele coloca você na posição #{{ score.classroom_rank }} da turma.
                        </p>
                    </div>
                    <Users class="size-6 text-[#8f7bff]" />
                </div>

                <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/5">
                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-4 text-xs tracking-[0.18em] text-white/45 uppercase">
                        <span>Posição</span>
                        <span>Aluno</span>
                    </div>

                    <Link
                        v-for="classmate in classmates"
                        :key="classmate.id"
                        :href="classmate.href"
                        class="flex items-start gap-3 border-b border-white/8 px-4 py-4 transition last:border-b-0 hover:bg-white/8"
                    >
                        <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#8f7bff]/15 text-sm font-semibold text-[#d8d1ff]">
                            {{ classmate.ranking_position }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 flex-col items-center text-center">
                                    <Avatar class="mt-0.5 h-11 w-11 shrink-0 overflow-hidden border border-white/10">
                                        <AvatarImage v-if="classmate.avatar" :src="classmate.avatar" :alt="classmate.name" class="object-cover" />
                                        <AvatarFallback class="bg-[#8f7bff]/20 text-sm font-semibold text-[#efeaff]">
                                            {{ getInitials(classmate.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="mt-3 min-w-0">
                                        <p class="break-words text-sm font-semibold leading-5 text-white">{{ classmate.name }}</p>
                                        <div class="mt-2 flex justify-center">
                                            <span
                                                v-if="classmate.is_current_user"
                                                class="shrink-0 rounded-full border border-[#8f7bff]/25 bg-[#8f7bff]/12 px-2 py-1 text-[10px] tracking-[0.18em] text-[#d8d1ff] uppercase"
                                            >
                                                Voce
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-[#201657]">
                                        <Medal class="size-4 text-[#2f1ef4]" />
                                        {{ classmate.score }} pts
                                    </div>
                                    <ChevronRight class="size-5 text-white/45" />
                                </div>
                            </div>
                        </div>
                    </Link>

                    <div v-if="!classmates.length" class="px-5 py-12 text-center text-sm text-white/55">
                        Nenhum colega foi encontrado para esta sala no momento.
                    </div>
                </div>
            </AppReveal>
        </section>
    </StudentLayout>
</template>
