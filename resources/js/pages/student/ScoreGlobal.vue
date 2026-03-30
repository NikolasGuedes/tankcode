<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { useDebounceFn } from '@vueuse/core';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ChevronRight, Medal, School, Trophy } from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps<{
    score: {
        student_points: number;
        classroom_rank: number;
        point_rank: number;
    };
    classroom: {
        name: string;
    };
    point: {
        name: string;
    };
    school: {
        name: string;
    };
    filters: {
        search: string;
        classroom: string;
        unit: string;
    };
    filter_options: {
        classrooms: Array<{
            id: number;
            name: string;
        }>;
        units: Array<{
            id: number;
            name: string;
        }>;
    };
    students: Array<{
        id: number;
        name: string;
        email: string;
        score: number;
        ranking_position: number;
        href: string;
        is_current_user: boolean;
        classroom_name: string;
        unit_name: string;
    }>;
}>();

const filtersForm = useForm({
    search: props.filters.search ?? '',
    classroom: props.filters.classroom ?? 'all',
    unit: props.filters.unit ?? 'all',
});

const cards = computed(() => [
    {
        title: 'Seu score',
        value: `${props.score.student_points} pts`,
        description: 'Pontuacao individual.',
        icon: Trophy,
    },
    {
        title: 'Sala',
        value: `#${props.score.classroom_rank}`,
        description: props.classroom.name,
        icon: Medal,
    },
    {
        title: 'Unidade',
        value: `#${props.score.point_rank}`,
        description: props.point.name,
        icon: School,
    },
]);

const applyFilters = () => {
    router.get(
        '/student/score-global',
        {
            search: filtersForm.search || undefined,
            classroom: filtersForm.classroom !== 'all' ? filtersForm.classroom : undefined,
            unit: filtersForm.unit !== 'all' ? filtersForm.unit : undefined,
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
    filtersForm.classroom = 'all';
    filtersForm.unit = 'all';
    applyFilters();
};

watch(
    () => filtersForm.search,
    () => debouncedApplyFilters(),
);

watch(
    () => [filtersForm.classroom, filtersForm.unit],
    () => applyFilters(),
);
</script>

<template>
    <Head title="Score Global" />

    <StudentLayout>
        <section>
            <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.06">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Ranking global</p>
                        <h2 class="mt-4 text-3xl font-semibold text-white">Ranking da escola</h2>
                    </div>
                    <School class="size-6 text-[#8f7bff]" />
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <div
                        v-for="card in cards"
                        :key="card.title"
                        class="rounded-[1.35rem] border border-white/10 bg-white/5 px-4 py-3"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-white/45">{{ card.title }}</p>
                                <p class="mt-2 text-2xl font-semibold text-white">{{ card.value }}</p>
                                <p class="mt-1 text-sm text-white/55">{{ card.description }}</p>
                            </div>
                            <component :is="card.icon" class="mt-0.5 size-8 rounded-xl bg-[#8f7bff]/18 p-1.5 text-[#cfc6ff]" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-3 md:grid-cols-[minmax(0,1.3fr)_220px_220px_auto] md:items-end">
                    <div class="space-y-2">
                        <label class="text-xs uppercase tracking-[0.18em] text-white/45">Buscar aluno</label>
                        <Input v-model="filtersForm.search" placeholder="Nome ou email" class="border-white/10 bg-white/6 text-white placeholder:text-white/35" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs uppercase tracking-[0.18em] text-white/45">Sala</label>
                        <Select v-model="filtersForm.classroom">
                            <SelectTrigger class="border-white/10 bg-white/6 text-white">
                                <SelectValue placeholder="Todas as salas" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todas as salas</SelectItem>
                                <SelectItem v-for="classroomOption in filter_options.classrooms" :key="classroomOption.id" :value="String(classroomOption.id)">
                                    {{ classroomOption.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs uppercase tracking-[0.18em] text-white/45">Unidade</label>
                        <Select v-model="filtersForm.unit">
                            <SelectTrigger class="border-white/10 bg-white/6 text-white">
                                <SelectValue placeholder="Todas as unidades" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Todas as unidades</SelectItem>
                                <SelectItem v-for="unitOption in filter_options.units" :key="unitOption.id" :value="String(unitOption.id)">
                                    {{ unitOption.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <Button type="button" variant="outline" class="border-white/10 bg-white/6 text-white hover:bg-white/10" @click="resetFilters">
                        Limpar filtros
                    </Button>
                </div>

                <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white/5">
                    <div class="overflow-x-auto">
                        <div class="min-w-[920px]">
                            <div class="grid grid-cols-[64px_minmax(0,1.4fr)_minmax(140px,0.8fr)_minmax(140px,0.8fr)_120px_52px] gap-3 border-b border-white/10 px-5 py-4 text-xs uppercase tracking-[0.18em] text-white/45">
                                <span>#</span>
                                <span>Aluno</span>
                                <span>Sala</span>
                                <span>Unidade</span>
                                <span>Score</span>
                                <span></span>
                            </div>

                            <Link
                                v-for="student in students"
                                :key="student.id"
                                :href="student.href"
                                class="grid grid-cols-[64px_minmax(0,1.4fr)_minmax(140px,0.8fr)_minmax(140px,0.8fr)_120px_52px] items-center gap-3 border-b border-white/8 px-5 py-4 transition last:border-b-0 hover:bg-white/8"
                            >
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8f7bff]/15 text-sm font-semibold text-[#d8d1ff]">
                                    {{ student.ranking_position }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-3">
                                        <p class="truncate text-base font-semibold text-white">{{ student.name }}</p>
                                        <span
                                            v-if="student.is_current_user"
                                            class="rounded-full border border-[#8f7bff]/25 bg-[#8f7bff]/12 px-2 py-1 text-[10px] uppercase tracking-[0.18em] text-[#d8d1ff]"
                                        >
                                            Voce
                                        </span>
                                    </div>
                                    <p class="truncate text-sm text-white/50">{{ student.email }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-white/80">{{ student.classroom_name }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-white/80">{{ student.unit_name }}</p>
                                </div>
                                <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-sm font-semibold text-[#201657]">
                                    <Medal class="size-4 text-[#2f1ef4]" />
                                    {{ student.score }} pts
                                </div>
                                <div class="flex justify-end text-white/45">
                                    <ChevronRight class="size-5" />
                                </div>
                            </Link>
                        </div>
                    </div>

                    <div v-if="!students.length" class="px-5 py-12 text-center text-sm text-white/55">
                        Nenhum aluno foi encontrado para esta escola no momento.
                    </div>
                </div>
            </AppReveal>
        </section>
    </StudentLayout>
</template>
