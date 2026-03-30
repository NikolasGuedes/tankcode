<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, CalendarClock, ChevronRight, Medal, Users } from 'lucide-vue-next';
import { computed } from 'vue';

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
    activities: Array<{
        id: string;
        title: string;
        description: string;
        deadline_label: string;
        deadline_group: string;
        status: string;
    }>;
    classmates: Array<{
        id: number;
        name: string;
        email: string;
        score: number;
        ranking_position: number;
        href: string;
        is_current_user: boolean;
    }>;
}>();

const activityFilters = computed(() => {
    const groups = ['Hoje', 'Essa semana', 'Proximas'];

    return groups.map((group) => ({
        label: group,
        count: props.activities.filter((activity) => activity.deadline_group === group).length,
    }));
});

const deadlineClass = (status: string) => {
    if (status === 'urgent') return 'border-rose-200/70 bg-rose-500/35 text-white shadow-[0_0_0_1px_rgba(251,191,191,0.12)]';
    if (status === 'warning') return 'border-amber-200/70 bg-amber-400/35 text-white shadow-[0_0_0_1px_rgba(253,230,138,0.12)]';

    return 'border-white/35 bg-[#5a48ff]/45 text-white shadow-[0_0_0_1px_rgba(255,255,255,0.08)]';
};
</script>

<template>
    <Head title="Minha Sala" />

    <StudentLayout>
        <section>
            <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Minhas Atividades</p>
                        <h1 class="mt-4 text-3xl font-semibold text-white">{{ classroom.name }}</h1>
                        <p class="mt-2 max-w-2xl text-white/60">Professor: {{ classroom.teacher }}</p>
                    </div>
                    <CalendarClock class="mt-1 size-6 text-[#8f7bff]" />
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div
                        v-for="filter in activityFilters"
                        :key="filter.label"
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/6 px-4 py-2 text-sm text-white/85"
                    >
                        <span>{{ filter.label }}</span>
                        <span class="rounded-full bg-[#8f7bff] px-2 py-0.5 text-xs font-semibold text-white">{{ filter.count }}</span>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-3">
                    <article
                        v-for="activity in activities"
                        :key="activity.id"
                        class="group relative overflow-hidden rounded-[1.5rem] border border-white/10 bg-white/6 p-5 transition duration-200 hover:-translate-y-1.5 hover:border-white/20 hover:bg-white/8"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-[#8471ff]"></div>

                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold tracking-[0.01em]"
                                :class="deadlineClass(activity.status)"
                            >
                                {{ activity.deadline_label }}
                            </span>
                        </div>

                        <h2 class="mt-5 text-2xl font-semibold text-white">{{ activity.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-white/70">{{ activity.description }}</p>

                        <div class="mt-6 flex items-center justify-between text-sm">
                            <span class="text-white/55">{{ classroom.teacher }}</span>
                            <span class="font-medium text-[#d9d2ff]">{{ classroom.code }}</span>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-3">
                            <span class="text-sm text-white/50 transition group-hover:text-white/70">Abrir atividade</span>
                            <Button
                                type="button"
                                class="border-[var(--primary)]/40 bg-[var(--secondary)] text-white transition"
                            >
                                Ver detalhes
                                <ArrowUpRight class="size-4" />
                            </Button>
                        </div>
                    </article>
                </div>

                <div class="mt-6 rounded-[1.5rem] border border-dashed border-white/12 bg-white/4 px-5 py-4 text-sm text-white/60">
                    Prazo em destaque para ajudar o aluno a priorizar entregas urgentes. Os dados ainda estao mockados nesta etapa.
                </div>
            </AppReveal>
        </section>

        <section class="mt-8">
            <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.06">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Colegas da sala</p>
                        <h2 class="mt-4 text-3xl font-semibold text-white">Ranking da turma</h2>
                        <p class="mt-2 max-w-2xl text-white/60">Seu score atual e {{ score.student_points }} pts. Hoje ele coloca voce na posicao #{{ score.classroom_rank }} da turma.</p>
                    </div>
                    <Users class="size-6 text-[#8f7bff]" />
                </div>

                <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/5">
                    <div class="grid grid-cols-[64px_minmax(0,1fr)_120px_52px] gap-3 border-b border-white/10 px-5 py-4 text-xs uppercase tracking-[0.18em] text-white/45">
                        <span>#</span>
                        <span>Aluno</span>
                        <span>Score</span>
                        <span></span>
                    </div>

                    <Link
                        v-for="classmate in classmates"
                        :key="classmate.id"
                        :href="classmate.href"
                        class="grid grid-cols-[64px_minmax(0,1fr)_120px_52px] items-center gap-3 border-b border-white/8 px-5 py-4 transition last:border-b-0 hover:bg-white/8"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8f7bff]/15 text-sm font-semibold text-[#d8d1ff]">
                            {{ classmate.ranking_position }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <p class="truncate text-base font-semibold text-white">{{ classmate.name }}</p>
                                <span
                                    v-if="classmate.is_current_user"
                                    class="rounded-full border border-[#8f7bff]/25 bg-[#8f7bff]/12 px-2 py-1 text-[10px] uppercase tracking-[0.18em] text-[#d8d1ff]"
                                >
                                    Voce
                                </span>
                            </div>
                            <p class="truncate text-sm text-white/50">{{ classmate.email }}</p>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-[#201657]">
                            <Medal class="size-4 text-[#2f1ef4]" />
                            {{ classmate.score }} pts
                        </div>
                        <div class="flex justify-end text-white/45">
                            <ChevronRight class="size-5" />
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
