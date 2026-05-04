<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import type { ApexOptions } from 'apexcharts';
import {
    BarChart3,
    CheckCircle2,
    ClipboardList,
    Clock3,
    GraduationCap,
    LayoutDashboard,
    Medal,
    PieChart,
    Sparkles,
    TrendingUp,
    Trophy,
    Users,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

type ActivityLevel = 'facil' | 'media' | 'dificil';
type ActivityStatus = 'draft' | 'published';

type ChartDatum = {
    label: string;
    value: number;
    color?: string;
};

type MonthlyActivityDatum = {
    label: string;
    value: number;
};

type MonthlySubmissionMetric = {
    label: string;
    submitted: number;
    pending: number;
};

type RankedStudent = {
    name: string;
    points: number;
    performance: number;
};

type ClassroomPerformanceMetric = {
    name: string;
    performance: number;
    completed: number;
    pending: number;
};

type ClassroomSummary = {
    id: number;
    name: string;
    code: string;
    students: number;
    activities: number;
    availablePoints: number;
    submitted: number;
    pending: number;
    completionRate: number;
    performanceRate: number;
    status: string;
};

type UpcomingActivity = {
    id: number;
    title: string;
    classroom: string;
    level: ActivityLevel;
    levelLabel: string;
    dueDate: string | null;
    status: ActivityStatus;
    totalPoints: string | number;
};

const props = defineProps<{
    realMetrics: {
        points: number;
        classrooms: number;
        students: number;
        activities: number;
        publishedActivities: number;
        draftActivities: number;
        totalQuestions: number;
        availablePoints: number;
        averagePointsPerActivity: number;
    };
    performanceMetrics: {
        averageCompletionRate: number;
        averagePerformance: number;
        submittedActivities: number;
        pendingSubmissions: number;
        completedSubmissions: number;
        topStudent: string | null;
        topClassroom: string | null;
        topStudents: RankedStudent[];
        classroomPerformance: ClassroomPerformanceMetric[];
        monthlySubmissions: MonthlySubmissionMetric[];
        performanceDistribution: ChartDatum[];
    };
    charts: {
        activityStatus: ChartDatum[];
        activityDifficulty: ChartDatum[];
        activitiesByClassroom: ChartDatum[];
        studentsByClassroom: ChartDatum[];
        monthlyActivities: MonthlyActivityDatum[];
    };
    classroomSummary: ClassroomSummary[];
    upcomingActivities: UpcomingActivity[];
    insights: string[];
}>();

const chartReady = ref(false);

onMounted(() => {
    chartReady.value = true;
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visão geral', href: '/teacher' },
];

const palette = {
    purple: '#8b5cf6',
    violet: '#a78bfa',
    blue: '#38bdf8',
    green: '#34d399',
    amber: '#fbbf24',
    rose: '#fb7185',
    panel: '#0f0b1a',
    muted: '#94a3b8',
    text: '#e2e8f0',
};

const levelClasses: Record<ActivityLevel, string> = {
    facil: 'border-emerald-400/20 bg-emerald-500/15 text-emerald-300',
    media: 'border-amber-400/20 bg-amber-500/15 text-amber-300',
    dificil: 'border-rose-400/20 bg-rose-500/15 text-rose-300',
};

const statusLabels: Record<ActivityStatus, string> = {
    draft: 'Rascunho',
    published: 'Publicado',
};

const performanceCards = computed(() => [
    { label: 'Aproveitamento', value: `${props.performanceMetrics.averagePerformance}%`, description: 'Media atual', icon: Sparkles },
    {
        label: 'Conclusao',
        value: `${props.performanceMetrics.averageCompletionRate}%`,
        description: 'Taxa real',
        icon: CheckCircle2,
    },
    { label: 'Entregas', value: props.performanceMetrics.submittedActivities, description: 'Realizadas', icon: ClipboardList },
    { label: 'Pendentes', value: props.performanceMetrics.pendingSubmissions, description: 'Aguardando envio', icon: Clock3 },
    { label: 'Melhor turma', value: props.performanceMetrics.topClassroom || '-', description: 'Destaque atual', icon: Trophy },
    { label: 'Melhor aluno', value: props.performanceMetrics.topStudent || '-', description: 'Maior score atual', icon: Medal },
]);

const topPerformanceCards = computed(() => performanceCards.value.slice(0, 4));
const highlightPerformanceCards = computed(() => performanceCards.value.slice(4));

const hasClassrooms = computed(() => props.realMetrics.classrooms > 0);
const hasStatusData = computed(() => props.charts.activityStatus.some((item) => item.value > 0));
const hasDifficultyData = computed(() => props.charts.activityDifficulty.some((item) => item.value > 0));
const hasClassroomActivityData = computed(() => props.charts.activitiesByClassroom.some((item) => item.value > 0));
const hasStudentsByClassroomData = computed(() => props.charts.studentsByClassroom.some((item) => item.value > 0));
const hasMonthlyActivityData = computed(() => props.charts.monthlyActivities.some((item) => item.value > 0));

const statusSeries = computed(() => props.charts.activityStatus.map((item) => item.value));
const difficultySeries = computed(() => [
    {
        name: 'Atividades',
        data: props.charts.activityDifficulty.map((item) => item.value),
    },
]);
const monthlyActivitySeries = computed(() => [
    {
        name: 'Atividades criadas',
        data: props.charts.monthlyActivities.map((item) => item.value),
    },
]);
const activitiesByClassroomSeries = computed(() => [
    {
        name: 'Atividades',
        data: props.charts.activitiesByClassroom.map((item) => item.value),
    },
]);
const studentsByClassroomSeries = computed(() => [
    {
        name: 'Alunos',
        data: props.charts.studentsByClassroom.map((item) => item.value),
    },
]);
const performanceDistributionSeries = computed(() => props.performanceMetrics.performanceDistribution.map((item) => item.value));
const topStudentsSeries = computed(() => [
    {
        name: 'Pontos',
        data: props.performanceMetrics.topStudents.map((student) => student.points),
    },
]);
const classroomPerformanceSeries = computed(() => [
    {
        name: 'Desempenho',
        data: props.performanceMetrics.classroomPerformance.map((classroom) => classroom.performance),
    },
]);
const submissionsSeries = computed(() => [
    {
        name: 'Concluidas',
        data: props.performanceMetrics.monthlySubmissions.map((item) => item.submitted),
    },
    {
        name: 'Pendentes',
        data: props.performanceMetrics.monthlySubmissions.map((item) => item.pending),
    },
]);
const statusOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'donut' },
        labels: props.charts.activityStatus.map((item) => item.label),
        colors: props.charts.activityStatus.map((item) => item.color ?? palette.purple),
        stroke: { width: 2, colors: [palette.panel] },
        dataLabels: {
            enabled: true,
            style: { colors: ['#ffffff'] },
            dropShadow: { enabled: false },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { color: palette.muted },
                        value: {
                            color: '#ffffff',
                            fontSize: '28px',
                            fontWeight: 700,
                            formatter: (value: string) => formatNumber(value),
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            color: palette.muted,
                            formatter: () => formatNumber(props.realMetrics.activities),
                        },
                    },
                },
            },
        },
    }),
);

const difficultyOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar' },
        colors: props.charts.activityDifficulty.map((item) => item.color ?? palette.purple),
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '46%',
                distributed: true,
            },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.charts.activityDifficulty.map((item) => item.label),
            labels: axisLabelStyle(),
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                ...axisLabelStyle(),
                formatter: (value: number) => String(Math.round(value)),
            },
        },
    }),
);

const monthlyActivityOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'area' },
        colors: [palette.violet],
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.7,
                opacityFrom: 0.38,
                opacityTo: 0.04,
                stops: [0, 95, 100],
            },
        },
        markers: { size: 4, colors: [palette.violet], strokeColors: '#ffffff', strokeWidth: 2 },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.charts.monthlyActivities.map((item) => item.label),
            labels: axisLabelStyle(),
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                ...axisLabelStyle(),
                formatter: (value: number) => String(Math.round(value)),
            },
        },
    }),
);

const activitiesByClassroomOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar' },
        colors: [palette.purple],
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 7,
                barHeight: '56%',
            },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.charts.activitiesByClassroom.map((item) => item.label),
            labels: {
                ...axisLabelStyle(),
                formatter: (value: string | number) => String(value),
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: { labels: axisLabelStyle() },
    }),
);

const studentsByClassroomOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar' },
        colors: [palette.blue],
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '48%',
            },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.charts.studentsByClassroom.map((item) => item.label),
            labels: axisLabelStyle(),
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                ...axisLabelStyle(),
                formatter: (value: number) => String(Math.round(value)),
            },
        },
    }),
);

const performanceDistributionOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'donut' },
        labels: props.performanceMetrics.performanceDistribution.map((item) => item.label),
        colors: props.performanceMetrics.performanceDistribution.map((item) => item.color ?? palette.purple),
        stroke: { width: 2, colors: [palette.panel] },
        dataLabels: {
            enabled: true,
            formatter: (value: number) => `${Math.round(value)}%`,
            style: { colors: ['#ffffff'] },
            dropShadow: { enabled: false },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        name: { color: palette.muted },
                        value: {
                            color: '#ffffff',
                            fontSize: '28px',
                            fontWeight: 700,
                            formatter: (value: string) => `${Math.round(Number(value))}%`,
                        },
                        total: {
                            show: true,
                            label: 'Aproveit.',
                            color: palette.muted,
                            formatter: () => `${props.performanceMetrics.averagePerformance}%`,
                        },
                    },
                },
            },
        },
    }),
);

const topStudentsOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar' },
        colors: [palette.violet],
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '44%',
            },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.performanceMetrics.topStudents.map((student) => student.name),
            labels: axisLabelStyle(),
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                ...axisLabelStyle(),
                formatter: (value: number) => formatNumber(value),
            },
        },
    }),
);

const classroomPerformanceOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar' },
        colors: [palette.blue],
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 7,
                barHeight: '54%',
            },
        },
        dataLabels: {
            enabled: true,
            formatter: (value: number) => `${Math.round(value)}%`,
            style: { colors: ['#ffffff'] },
        },
        xaxis: {
            min: 0,
            max: 100,
            categories: props.performanceMetrics.classroomPerformance.map((classroom) => classroom.name),
            labels: {
                ...axisLabelStyle(),
                formatter: (value: string | number) => `${Math.round(Number(value))}%`,
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: { labels: axisLabelStyle() },
    }),
);

const submissionsOptions = computed<ApexOptions>(() =>
    withBaseOptions({
        chart: { type: 'bar', stacked: true },
        colors: [palette.green, palette.amber],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '52%',
            },
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.performanceMetrics.monthlySubmissions.map((item) => item.label),
            labels: axisLabelStyle(),
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                ...axisLabelStyle(),
                formatter: (value: number) => String(Math.round(value)),
            },
        },
    }),
);

function axisLabelStyle() {
    return {
        style: {
            colors: palette.muted,
            fontSize: '12px',
        },
    };
}

function withBaseOptions(options: ApexOptions): ApexOptions {
    return {
        ...options,
        chart: {
            background: 'transparent',
            foreColor: palette.text,
            fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: {
                enabled: true,
                speed: 550,
            },
            ...(options.chart ?? {}),
        },
        theme: {
            mode: 'dark',
            ...(options.theme ?? {}),
        },
        grid: {
            borderColor: 'rgba(148,163,184,0.14)',
            strokeDashArray: 4,
            padding: { left: 6, right: 12, top: 0, bottom: 0 },
            ...(options.grid ?? {}),
        },
        legend: {
            position: 'bottom',
            fontSize: '13px',
            labels: { colors: palette.text },
            markers: { strokeWidth: 0 },
            ...(options.legend ?? {}),
        },
        tooltip: {
            theme: 'dark',
            style: { fontSize: '13px' },
            ...(options.tooltip ?? {}),
        },
        states: {
            hover: { filter: { type: 'lighten', value: 0.08 } },
            active: { filter: { type: 'none' } },
            ...(options.states ?? {}),
        },
    };
}

function formatNumber(value: string | number): string {
    return Number(value).toLocaleString('pt-BR', {
        maximumFractionDigits: 2,
    });
}
</script>

<template>
    <Head title="Dashboard do Professor" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 overflow-hidden p-4 sm:p-6">
            <AppReveal class-name="overflow-hidden rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex min-w-0 gap-4">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/15 text-secondary">
                            <LayoutDashboard class="size-6" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold tracking-[0.22em] text-secondary uppercase">Metricas do professor</p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Dashboard</h1>
                            <p class="mt-2 text-sm text-white/60 sm:text-base">Metricas das turmas, atividades e alunos.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-300"
                            >Dados reais</span
                        >
                    </div>
                </div>
            </AppReveal>

            <section class="grid gap-5 xl:grid-cols-[minmax(0,1.12fr)_minmax(320px,0.88fr)]">
                <AppReveal
                    class-name="rounded-[2rem] border border-secondary/25 bg-[linear-gradient(135deg,rgba(139,92,246,0.18),rgba(10,10,16,0.95))] p-5 shadow-[0_22px_55px_rgba(88,28,135,0.2)] sm:p-6"
                    :delay="0.04"
                >
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-xl font-semibold text-white sm:text-2xl">Faixa de desempenho</h2>
                            <p class="mt-1 text-sm text-white/55">Distribuição real de performance das turmas.</p>
                        </div>
                        <span class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                            >Real</span
                        >
                    </div>

                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px] lg:items-center">
                        <VueApexCharts
                            v-if="chartReady"
                            type="donut"
                            height="292"
                            :options="performanceDistributionOptions"
                            :series="performanceDistributionSeries"
                        />
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
                            <div
                                v-for="item in props.performanceMetrics.performanceDistribution"
                                :key="item.label"
                                class="rounded-2xl border border-white/10 bg-black/20 p-3"
                            >
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="flex min-w-0 items-center gap-2 font-medium text-white/75">
                                        <span class="size-2.5 shrink-0 rounded-full" :style="{ backgroundColor: item.color }" />
                                        {{ item.label }}
                                    </span>
                                    <span class="font-semibold text-white">{{ item.value }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </AppReveal>

                <div class="grid gap-4 sm:grid-cols-2">
                    <AppReveal
                        v-for="card in topPerformanceCards"
                        :key="card.label"
                        class-name="rounded-[1.5rem] border border-secondary/20 bg-card p-5 shadow-[0_18px_38px_rgba(88,28,135,0.16)]"
                        :delay="0.05"
                    >
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-300"
                                >Real</span
                            >
                            <component :is="card.icon" class="size-9 rounded-2xl bg-primary/15 p-2 text-secondary" />
                        </div>
                        <p class="text-sm text-white/55">{{ card.label }}</p>
                        <p class="mt-2 truncate text-3xl font-semibold text-white">{{ card.value }}</p>
                        <p class="mt-2 text-sm text-white/45">{{ card.description }}</p>
                    </AppReveal>
                </div>
            </section>

            <div
                v-if="!hasClassrooms"
                class="rounded-[2rem] border border-dashed border-secondary/30 bg-card p-8 text-center shadow-[0_20px_45px_rgba(0,0,0,0.25)]"
            >
                <GraduationCap class="mx-auto size-12 text-secondary" />
                <h2 class="mt-4 text-2xl font-semibold text-white">Voce ainda nao possui turmas vinculadas</h2>
                <p class="mx-auto mt-2 max-w-2xl text-white/60">
                    Quando suas turmas forem atribuidas, este dashboard exibira alunos, atividades, entregas, score e indicadores de desempenho em tempo real.
                </p>
            </div>

            <section class="space-y-5 pt-2">
                <div>
                    <p class="text-sm font-semibold tracking-[0.22em] text-secondary uppercase">Atividades</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Distribuicao e evolucao</h2>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <AppReveal
                        class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6"
                        :delay="0.08"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Atividades por status</h2>
                                <p class="text-sm text-white/55">Publicadas x rascunhos.</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <VueApexCharts v-if="chartReady && hasStatusData" type="donut" height="310" :options="statusOptions" :series="statusSeries" />
                        <div
                            v-else
                            class="flex h-[310px] flex-col items-center justify-center rounded-3xl border border-dashed border-white/10 bg-black/20 text-center"
                        >
                            <PieChart class="size-10 text-secondary" />
                            <p class="mt-3 font-semibold text-white">Sem atividades para agrupar.</p>
                            <p class="mt-1 text-sm text-white/50">Crie atividades para visualizar este grafico.</p>
                        </div>
                    </AppReveal>

                    <AppReveal class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6" :delay="0.1">
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Atividades por dificuldade</h2>
                                <p class="text-sm text-white/55">Facil, media e dificil.</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <VueApexCharts
                            v-if="chartReady && hasDifficultyData"
                            type="bar"
                            height="310"
                            :options="difficultyOptions"
                            :series="difficultySeries"
                        />
                        <div
                            v-else
                            class="flex h-[310px] flex-col items-center justify-center rounded-3xl border border-dashed border-white/10 bg-black/20 text-center"
                        >
                            <BarChart3 class="size-10 text-secondary" />
                            <p class="mt-3 font-semibold text-white">Ainda nao ha dificuldades cadastradas.</p>
                            <p class="mt-1 text-sm text-white/50">As atividades aparecerao aqui por nivel.</p>
                        </div>
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6"
                        :delay="0.12"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Evolucao de atividades</h2>
                                <p class="text-sm text-white/55">Criacao mensal.</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <VueApexCharts
                            v-if="chartReady && hasMonthlyActivityData"
                            type="area"
                            height="310"
                            :options="monthlyActivityOptions"
                            :series="monthlyActivitySeries"
                        />
                        <div
                            v-else
                            class="flex h-[310px] flex-col items-center justify-center rounded-3xl border border-dashed border-white/10 bg-black/20 text-center"
                        >
                            <TrendingUp class="size-10 text-secondary" />
                            <p class="mt-3 font-semibold text-white">Sem historico recente.</p>
                            <p class="mt-1 text-sm text-white/50">O grafico sera preenchido conforme novas atividades forem criadas.</p>
                        </div>
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6"
                        :delay="0.14"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Atividades por turma</h2>
                                <p class="text-sm text-white/55">Distribuicao por sala.</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <VueApexCharts
                            v-if="chartReady && hasClassroomActivityData"
                            type="bar"
                            height="340"
                            :options="activitiesByClassroomOptions"
                            :series="activitiesByClassroomSeries"
                        />
                        <div
                            v-else
                            class="flex h-[340px] flex-col items-center justify-center rounded-3xl border border-dashed border-white/10 bg-black/20 text-center"
                        >
                            <ClipboardList class="size-10 text-secondary" />
                            <p class="mt-3 font-semibold text-white">Voce ainda nao possui atividades cadastradas.</p>
                            <Link
                                href="/teacher/activities"
                                class="mt-4 inline-flex rounded-2xl bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-[var(--primary-hover)]"
                                >Criar atividade</Link
                            >
                        </div>
                    </AppReveal>
                </div>
            </section>

            <section class="space-y-5 pt-2">
                <div>
                    <p class="text-sm font-semibold tracking-[0.22em] text-secondary uppercase">Alunos e desempenho</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Rankings e turmas</h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <AppReveal
                        v-for="card in highlightPerformanceCards"
                        :key="card.label"
                        class-name="rounded-[1.5rem] border border-secondary/20 bg-[linear-gradient(135deg,rgba(139,92,246,0.15),rgba(10,10,16,0.94))] p-5 shadow-[0_18px_38px_rgba(88,28,135,0.16)]"
                        :delay="0.12"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-300">Real</span>
                                <p class="mt-4 text-sm text-white/55">{{ card.label }}</p>
                                <p class="mt-2 truncate text-2xl font-semibold text-white">{{ card.value }}</p>
                                <p class="mt-2 text-sm text-white/45">{{ card.description }}</p>
                            </div>
                            <component :is="card.icon" class="size-10 rounded-2xl bg-white/10 p-2.5 text-white" />
                        </div>
                    </AppReveal>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <AppReveal
                        class-name="rounded-[2rem] border border-secondary/20 bg-card p-5 shadow-[0_20px_45px_rgba(88,28,135,0.18)] sm:p-6"
                        :delay="0.14"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Ranking de alunos</h2>
                                <p class="text-sm text-white/55">Pontuação acumulada pelas submissões reais.</p>
                            </div>
                            <span class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Real</span>
                        </div>
                        <VueApexCharts v-if="chartReady" type="bar" height="300" :options="topStudentsOptions" :series="topStudentsSeries" />
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-secondary/20 bg-card p-5 shadow-[0_20px_45px_rgba(88,28,135,0.18)] sm:p-6"
                        :delay="0.16"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Desempenho por turma</h2>
                                <p class="text-sm text-white/55">Aproveitamento médio por turma.</p>
                            </div>
                            <span class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Real</span>
                        </div>
                        <VueApexCharts
                            v-if="chartReady"
                            type="bar"
                            height="300"
                            :options="classroomPerformanceOptions"
                            :series="classroomPerformanceSeries"
                        />
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6"
                        :delay="0.18"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Alunos por turma</h2>
                                <p class="text-sm text-white/55">Distribuicao real.</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <VueApexCharts
                            v-if="chartReady && hasStudentsByClassroomData"
                            type="bar"
                            height="310"
                            :options="studentsByClassroomOptions"
                            :series="studentsByClassroomSeries"
                        />
                        <div
                            v-else
                            class="flex h-[310px] flex-col items-center justify-center rounded-3xl border border-dashed border-white/10 bg-black/20 text-center"
                        >
                            <Users class="size-10 text-secondary" />
                            <p class="mt-3 font-semibold text-white">Sem alunos vinculados nas turmas.</p>
                            <p class="mt-1 text-sm text-white/50">Quando houver alunos, eles aparecerao neste grafico.</p>
                        </div>
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-secondary/20 bg-card p-5 shadow-[0_20px_45px_rgba(88,28,135,0.18)] sm:p-6"
                        :delay="0.2"
                    >
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Entregas por mes</h2>
                                <p class="text-sm text-white/55">Concluidas x pendentes.</p>
                            </div>
                            <span class="shrink-0 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Real</span>
                        </div>
                        <VueApexCharts v-if="chartReady" type="bar" height="310" :options="submissionsOptions" :series="submissionsSeries" />
                    </AppReveal>
                </div>
            </section>

            <section class="space-y-5 pt-2">
                <div>
                    <p class="text-sm font-semibold tracking-[0.22em] text-secondary uppercase">Resumo</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Turmas e proximos prazos</h2>
                </div>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
                    <AppReveal class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6" :delay="0.3">
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Resumo por turma</h2>
                                <p class="text-sm text-white/55">Alunos, entregas, pendências e aproveitamento.</p>
                            </div>
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-border">
                                <thead>
                                    <tr class="text-left text-sm text-secondary">
                                        <th class="pb-4 font-medium">Turma</th>
                                        <th class="pb-4 font-medium">Alunos</th>
                                        <th class="pb-4 font-medium">Atividades</th>
                                        <th class="pb-4 font-medium">Entregas</th>
                                        <th class="pb-4 font-medium">Conclusão</th>
                                        <th class="pb-4 font-medium">Aproveitamento</th>
                                        <th class="pb-4 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-sm text-white/75">
                                    <tr v-for="classroom in props.classroomSummary" :key="classroom.id">
                                        <td class="py-4 pr-6">
                                            <p class="font-semibold text-white">{{ classroom.name }}</p>
                                            <p class="text-white/45">{{ classroom.code }}</p>
                                        </td>
                                        <td class="py-4 pr-6">{{ classroom.students }}</td>
                                        <td class="py-4 pr-6">{{ classroom.activities }}</td>
                                        <td class="py-4 pr-6">{{ classroom.submitted }} / {{ classroom.submitted + classroom.pending }}</td>
                                        <td class="py-4 pr-6">{{ classroom.completionRate }}%</td>
                                        <td class="py-4 pr-6">{{ classroom.performanceRate }}%</td>
                                        <td class="py-4 pr-6">
                                            <span
                                                class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-white/70"
                                                >{{ classroom.status }}</span
                                            >
                                        </td>
                                    </tr>
                                    <tr v-if="props.classroomSummary.length === 0">
                                        <td colspan="7" class="py-12 text-center text-white/55">Nenhuma turma encontrada para o contexto atual.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </AppReveal>

                    <AppReveal
                        class-name="rounded-[2rem] border border-border bg-card p-5 shadow-[0_20px_45px_rgba(0,0,0,0.25)] sm:p-6"
                        :delay="0.32"
                    >
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold text-white sm:text-2xl">Proximas atividades</h2>
                                <p class="text-sm text-white/55">Prazos cadastrados.</p>
                            </div>
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300"
                                >Real</span
                            >
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="activity in props.upcomingActivities"
                                :key="activity.id"
                                class="rounded-2xl border border-white/10 bg-black/20 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-white">{{ activity.title }}</p>
                                        <p class="mt-1 text-sm text-white/50">{{ activity.classroom }} · {{ activity.dueDate ?? '-' }}</p>
                                    </div>
                                    <span
                                        class="shrink-0 rounded-full border px-3 py-1 text-xs font-semibold"
                                        :class="levelClasses[activity.level]"
                                        >{{ activity.levelLabel }}</span
                                    >
                                </div>
                                <div class="mt-3 flex items-center justify-between text-sm text-white/60">
                                    <span>{{ statusLabels[activity.status] }}</span>
                                    <span class="font-semibold text-secondary">{{ formatNumber(activity.totalPoints) }} pts</span>
                                </div>
                            </div>
                            <div
                                v-if="props.upcomingActivities.length === 0"
                                class="rounded-2xl border border-dashed border-white/10 bg-black/20 p-6 text-center"
                            >
                                <Clock3 class="mx-auto size-9 text-secondary" />
                                <p class="mt-3 text-sm text-white/60">Nenhuma atividade futura com prazo definido.</p>
                            </div>
                        </div>
                    </AppReveal>
                </div>
            </section>
        </section>
    </AppLayout>
</template>
