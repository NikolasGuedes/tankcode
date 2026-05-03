<script setup lang="ts">
import {
    destroy as destroyActivity,
    store as storeActivity,
    update as updateActivity,
} from '@/actions/App/Http/Controllers/Teacher/ActivityController';
import AppReveal from '@/components/AppReveal.vue';
import AppTablePagination from '@/components/AppTablePagination.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { Calculator, CircleDashed, ClipboardList, FileQuestion, ListChecks, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type ActivityLevel = 'facil' | 'media' | 'dificil';
type ActivityStatus = 'draft' | 'published';
type OptionKey = 'A' | 'B' | 'C' | 'D';
type QuestionType = 'multiple_choice' | 'drag_drop' | 'matching';

type ActivityQuestion = {
    id?: number;
    type: QuestionType;
    statement: string;
    correct_option?: OptionKey | '';
    options?: Record<OptionKey, string>;
    keywords?: string[];
    blank_answers?: Record<string, number>;
    left_column?: string[];
    right_column?: string[];
    pairs?: Record<string, string>;
};

type ActivityRow = {
    id: number;
    classroom_id: number;
    classroom: string;
    classroom_code: string | null;
    title: string;
    description: string | null;
    level: ActivityLevel;
    level_label: string;
    questions_count: number;
    points_per_question: string | number;
    total_points: string | number;
    due_date: string | null;
    due_date_label: string;
    status: ActivityStatus;
    questions: ActivityQuestion[];
};

type ClassroomOption = {
    id: number;
    name: string;
    code: string;
    point_of_school: string | null;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

const props = defineProps<{
    stats: {
        total: number;
        published: number;
        draft: number;
        questions: number;
        points: number;
    };
    activities: Paginated<ActivityRow>;
    classrooms: ClassroomOption[];
    filters: {
        search?: string;
        status?: ActivityStatus;
        classroom_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/teacher' },
    { title: 'Atividades', href: '/teacher/activities' },
];

const levelLabels: Record<ActivityLevel, string> = {
    facil: 'Facil',
    media: 'Media',
    dificil: 'Dificil',
};

const statusLabels: Record<ActivityStatus, string> = {
    draft: 'Rascunho',
    published: 'Publicado',
};

const questionTypeLabels: Record<QuestionType, string> = {
    multiple_choice: 'Multipla Escolha',
    drag_drop: 'Arrastar e Soltar',
    matching: 'Relacionar Colunas',
};

const pointsByLevel: Record<ActivityLevel, number> = {
    facil: 1,
    media: 2.5,
    dificil: 4,
};

const optionKeys: OptionKey[] = ['A', 'B', 'C', 'D'];

const activityDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const mode = ref<'create' | 'edit'>('create');
const selectedActivity = ref<ActivityRow | null>(null);
const activeQuestionIndex = ref(0);
const step = ref(1);
const suspendAutoFilters = ref(false);

const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    classroom_id: props.filters.classroom_id ? String(props.filters.classroom_id) : 'all',
});

const newQuestion = (type: QuestionType = 'multiple_choice'): ActivityQuestion => ({
    id: Date.now() + Math.floor(Math.random() * 1000),
    type,
    statement: '',
    correct_option: 'A',
    options: { A: '', B: '', C: '', D: '' },
    keywords: ['', ''],
    blank_answers: {},
    left_column: ['', ''],
    right_column: ['', ''],
    pairs: {},
});

const form = useForm({
    title: '',
    classroom_id: props.classrooms[0]?.id ? String(props.classrooms[0].id) : '',
    level: 'media' as ActivityLevel,
    description: '',
    due_date: '',
    status: 'draft' as ActivityStatus,
    questions: [newQuestion()] as ActivityQuestion[],
});

const deleteForm = useForm({});

const currentQuestion = computed(() => form.questions[activeQuestionIndex.value]);
const questionsCount = computed(() => form.questions.length);
const previewPointsPerQuestion = computed(() => pointsByLevel[form.level] ?? 0);
const previewTotalPoints = computed(() => questionsCount.value * previewPointsPerQuestion.value);

const formatPoints = (value: string | number) =>
    Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

const errorFor = (path: string) => (form.errors as Record<string, string | undefined>)[path];

const getLevelClass = (level: ActivityLevel) => {
    const classes: Record<ActivityLevel, string> = {
        facil: 'border-emerald-400/20 bg-emerald-500/15 text-emerald-300',
        media: 'border-amber-400/20 bg-amber-500/15 text-amber-300',
        dificil: 'border-rose-400/20 bg-rose-500/15 text-rose-300',
    };

    return classes[level];
};

const getStatusClass = (status: ActivityStatus) =>
    status === 'published'
        ? 'border-emerald-400/20 bg-emerald-500/15 text-emerald-300'
        : 'border-secondary/30 bg-secondary/15 text-secondary';

const classroomLabel = (classroom: ClassroomOption) => {
    const point = classroom.point_of_school ? ` - ${classroom.point_of_school}` : '';

    return `${classroom.name}${classroom.code ? ` (${classroom.code})` : ''}${point}`;
};

const normalizedQuestion = (question: ActivityQuestion): ActivityQuestion => {
    if (question.type === 'multiple_choice') {
        return {
            type: question.type,
            statement: question.statement,
            correct_option: question.correct_option || 'A',
            options: question.options ?? { A: '', B: '', C: '', D: '' },
        };
    }

    if (question.type === 'drag_drop') {
        return {
            type: question.type,
            statement: question.statement,
            keywords: question.keywords ?? [],
            blank_answers: question.blank_answers ?? {},
        };
    }

    return {
        type: question.type,
        statement: question.statement,
        left_column: question.left_column ?? [],
        right_column: question.right_column ?? [],
        pairs: question.pairs ?? {},
    };
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.title = '';
    form.classroom_id = props.classrooms[0]?.id ? String(props.classrooms[0].id) : '';
    form.level = 'media';
    form.description = '';
    form.due_date = '';
    form.status = 'draft';
    form.questions = [newQuestion()];
    activeQuestionIndex.value = 0;
    step.value = 1;
};

const openCreateDialog = () => {
    mode.value = 'create';
    selectedActivity.value = null;
    resetForm();
    activityDialogOpen.value = true;
};

const openEditDialog = (activity: ActivityRow) => {
    mode.value = 'edit';
    selectedActivity.value = activity;
    form.clearErrors();
    form.title = activity.title;
    form.classroom_id = String(activity.classroom_id);
    form.level = activity.level;
    form.description = activity.description ?? '';
    form.due_date = activity.due_date ?? '';
    form.status = activity.status;
    form.questions = activity.questions.length
        ? activity.questions.map((question) => ({
              ...newQuestion(question.type),
              ...question,
              options: question.options ?? { A: '', B: '', C: '', D: '' },
              keywords: question.keywords ?? ['', ''],
              blank_answers: question.blank_answers ?? {},
              left_column: question.left_column ?? ['', ''],
              right_column: question.right_column ?? ['', ''],
              pairs: question.pairs ?? {},
          }))
        : [newQuestion()];
    activeQuestionIndex.value = 0;
    step.value = 1;
    activityDialogOpen.value = true;
};

const closeActivityDialog = () => {
    activityDialogOpen.value = false;
    selectedActivity.value = null;
    resetForm();
};

const submitActivity = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeActivityDialog(),
    };

    form.transform((data) => ({
        ...data,
        questions: data.questions.map((question) => normalizedQuestion(question)),
    }));

    if (mode.value === 'edit' && selectedActivity.value) {
        form.put(updateActivity(selectedActivity.value.id).url, options);
        return;
    }

    form.post(storeActivity().url, options);
};

const openDeleteDialog = (activity: ActivityRow) => {
    selectedActivity.value = activity;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedActivity.value = null;
};

const submitDelete = () => {
    if (!selectedActivity.value) return;

    deleteForm.delete(destroyActivity(selectedActivity.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};

const nextStep = () => {
    if (step.value < 3) {
        step.value += 1;
    }
};

const prevStep = () => {
    if (step.value > 1) {
        step.value -= 1;
    }
};

const addQuestion = () => {
    form.questions = [...form.questions, newQuestion()];
    activeQuestionIndex.value = form.questions.length - 1;
};

const selectQuestion = (index: number) => {
    activeQuestionIndex.value = index;
};

const deleteQuestion = (index: number) => {
    if (form.questions.length === 1) {
        return;
    }

    form.questions.splice(index, 1);

    if (activeQuestionIndex.value >= form.questions.length) {
        activeQuestionIndex.value = form.questions.length - 1;
    }
};

const changeQuestionType = (type: QuestionType) => {
    if (!currentQuestion.value) return;

    const statement = currentQuestion.value.statement;
    form.questions[activeQuestionIndex.value] = {
        ...newQuestion(type),
        id: currentQuestion.value.id,
        statement,
        type,
    };
};

const setCorrectOption = (option: OptionKey) => {
    if (currentQuestion.value) {
        currentQuestion.value.correct_option = option;
    }
};

const extractBlanksFromStatement = (statement: string): string[] => {
    const regex = /\[blank_(\d+)\]/g;
    const blanks: string[] = [];
    let match;

    while ((match = regex.exec(statement)) !== null) {
        blanks.push(`blank_${match[1]}`);
    }

    return [...new Set(blanks)];
};

const initializeBlanks = (question: ActivityQuestion) => {
    const blanks = extractBlanksFromStatement(question.statement || '');
    const answers: Record<string, number> = {};

    blanks.forEach((blank) => {
        answers[blank] = question.blank_answers?.[blank] ?? -1;
    });

    question.blank_answers = answers;
};

const addKeyword = (question: ActivityQuestion) => {
    question.keywords = [...(question.keywords ?? []), ''];
};

const removeKeyword = (question: ActivityQuestion, index: number) => {
    question.keywords = (question.keywords ?? []).filter((_, keywordIndex) => keywordIndex !== index);
};

const addColumnItem = (question: ActivityQuestion, column: 'left_column' | 'right_column') => {
    question[column] = [...(question[column] ?? []), ''];
};

const removeColumnItem = (question: ActivityQuestion, column: 'left_column' | 'right_column', index: number) => {
    question[column] = (question[column] ?? []).filter((_, itemIndex) => itemIndex !== index);
};

const applyFilters = () => {
    router.get(
        '/teacher/activities',
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status !== 'all' ? filtersForm.status : undefined,
            classroom_id: filtersForm.classroom_id !== 'all' ? filtersForm.classroom_id : undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const debouncedApplyFilters = useDebounceFn(() => {
    if (!suspendAutoFilters.value) {
        applyFilters();
    }
}, 400);

const resetFilters = () => {
    suspendAutoFilters.value = true;
    filtersForm.search = '';
    filtersForm.status = 'all';
    filtersForm.classroom_id = 'all';
    applyFilters();
    suspendAutoFilters.value = false;
};

watch(
    () => [filtersForm.search, filtersForm.status, filtersForm.classroom_id],
    () => debouncedApplyFilters(),
);
</script>

<template>
    <Head title="Atividades" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex gap-4">
                        <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-primary/15 text-secondary">
                            <ClipboardList class="size-7" />
                        </div>
                        <div>
                            <h1 class="text-4xl font-semibold tracking-tight text-white">Atividades</h1>
                            <p class="mt-2 max-w-3xl text-lg text-white/70">Crie atividades para suas turmas com questoes e pontuacao calculada automaticamente pelo sistema.</p>
                        </div>
                    </div>
                    <Button type="button" class="rounded-2xl !bg-primary !text-white hover:!bg-[var(--primary-hover)]" @click="openCreateDialog">
                        <Plus class="size-4" />
                        Nova atividade
                    </Button>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm text-white/60">Total</p><ClipboardList class="size-5 text-secondary" /></div>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm text-white/60">Publicadas</p><ListChecks class="size-5 text-secondary" /></div>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.published }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm text-white/60">Rascunhos</p><CircleDashed class="size-5 text-secondary" /></div>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.draft }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm text-white/60">Questoes</p><FileQuestion class="size-5 text-secondary" /></div>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.questions }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm text-white/60">Pontos</p><Calculator class="size-5 text-secondary" /></div>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ formatPoints(props.stats.points) }}</p>
                    </div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Lista de atividades</h2>
                        <p class="text-sm text-white/60">Acompanhe as atividades criadas, suas questoes e os pontos oficiais salvos no banco.</p>
                    </div>
                </div>

                <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 lg:grid-cols-[minmax(0,1fr)_220px_260px_auto]" @submit.prevent="applyFilters">
                    <div class="relative">
                        <Search class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-white/40" />
                        <Input v-model="filtersForm.search" class="border-white/10 bg-[var(--surface-elevated)] pl-10 text-white placeholder:text-white/35" placeholder="Buscar por titulo ou turma" />
                    </div>
                    <Select v-model="filtersForm.status">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Status" /></SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todos os status</SelectItem>
                            <SelectItem value="draft">Rascunhos</SelectItem>
                            <SelectItem value="published">Publicadas</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="filtersForm.classroom_id">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Turma" /></SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todas as turmas</SelectItem>
                            <SelectItem v-for="classroom in props.classrooms" :key="classroom.id" :value="String(classroom.id)">
                                {{ classroom.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Button type="button" class="rounded-2xl" @click="resetFilters">Limpar</Button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Atividade</th>
                                <th class="pb-4 font-medium">Turma</th>
                                <th class="pb-4 font-medium">Nivel</th>
                                <th class="pb-4 font-medium">Questoes</th>
                                <th class="pb-4 font-medium">Pontos/questao</th>
                                <th class="pb-4 font-medium">Total</th>
                                <th class="pb-4 font-medium">Entrega</th>
                                <th class="pb-4 font-medium">Status</th>
                                <th class="pb-4 text-right font-medium">Acoes</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="activity in props.activities.data" :key="activity.id" class="transition hover:bg-white/[0.03]">
                                <td class="py-4 pr-6">
                                    <p class="font-semibold text-white">{{ activity.title }}</p>
                                    <p class="mt-1 max-w-xs truncate text-white/50">{{ activity.description || 'Sem descricao' }}</p>
                                </td>
                                <td class="py-4 pr-6">
                                    <p class="font-medium text-white/85">{{ activity.classroom }}</p>
                                    <p class="text-xs text-white/45">{{ activity.classroom_code ?? '-' }}</p>
                                </td>
                                <td class="py-4 pr-6">
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold" :class="getLevelClass(activity.level)">
                                        {{ levelLabels[activity.level] }}
                                    </span>
                                </td>
                                <td class="py-4 pr-6 text-white/85">{{ activity.questions_count }}</td>
                                <td class="py-4 pr-6 text-white/85">{{ formatPoints(activity.points_per_question) }}</td>
                                <td class="py-4 pr-6 font-semibold text-white">{{ formatPoints(activity.total_points) }}</td>
                                <td class="py-4 pr-6 text-white/85">{{ activity.due_date_label }}</td>
                                <td class="py-4 pr-6">
                                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold" :class="getStatusClass(activity.status)">
                                        {{ statusLabels[activity.status] }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button type="button" variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]" title="Editar atividade" @click="openEditDialog(activity)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button type="button" variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" title="Excluir atividade" @click="openDeleteDialog(activity)">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="props.activities.data.length === 0">
                                <td colspan="9" class="py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center">
                                        <FileQuestion class="size-10 text-secondary" />
                                        <p class="mt-4 font-semibold text-white">Nenhuma atividade encontrada</p>
                                        <p class="mt-1 text-sm text-white/55">Crie uma atividade ou ajuste os filtros para visualizar resultados.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.activities" :links="props.activities.links" />
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog :open="activityDialogOpen" @update:open="(value) => (value ? (activityDialogOpen = value) : closeActivityDialog())">
        <DialogContent class="flex max-h-[94vh] w-[96vw] max-w-[1400px] flex-col overflow-hidden border-border bg-card p-0 text-white sm:max-w-[1400px]">
            <DialogHeader class="shrink-0 border-b border-white/5 px-5 py-5 sm:px-6">
                <DialogTitle class="text-2xl font-semibold text-white">{{ mode === 'edit' ? 'Editar atividade' : 'Nova atividade' }}</DialogTitle>
                <DialogDescription class="text-white/60">Cadastre os dados, adicione questoes e revise os pontos antes de salvar.</DialogDescription>
            </DialogHeader>

            <form class="flex min-h-0 flex-1 flex-col" @submit.prevent="submitActivity">
                <div class="shrink-0 px-5 pt-5 sm:px-6">
                    <div class="grid gap-3 md:grid-cols-3">
                        <button type="button" class="rounded-2xl border px-4 py-3 text-left text-sm font-medium" :class="step === 1 ? 'border-secondary bg-secondary/15 text-white' : step > 1 ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-white/10 bg-white/[0.03] text-white/45'" @click="step = 1">
                            1. Dados da atividade
                        </button>
                        <button type="button" class="rounded-2xl border px-4 py-3 text-left text-sm font-medium" :class="step === 2 ? 'border-secondary bg-secondary/15 text-white' : step > 2 ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-white/10 bg-white/[0.03] text-white/45'" @click="step = 2">
                            2. Adicionar questoes
                        </button>
                        <button type="button" class="rounded-2xl border px-4 py-3 text-left text-sm font-medium" :class="step === 3 ? 'border-secondary bg-secondary/15 text-white' : 'border-white/10 bg-white/[0.03] text-white/45'" @click="step = 3">
                            3. Revisao final
                        </button>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto p-5 sm:p-6">
                    <div v-if="step === 1" class="space-y-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="activity-title">Titulo</Label>
                                <Input id="activity-title" v-model="form.title" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Avaliacao de SQL" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="activity-classroom">Turma</Label>
                                <Select v-model="form.classroom_id">
                                    <SelectTrigger id="activity-classroom" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione a turma" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem v-for="classroom in props.classrooms" :key="classroom.id" :value="String(classroom.id)">
                                            {{ classroomLabel(classroom) }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.classroom_id" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="activity-level">Dificuldade</Label>
                                <Select v-model="form.level">
                                    <SelectTrigger id="activity-level" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione o nivel" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem value="facil">Facil</SelectItem>
                                        <SelectItem value="media">Media</SelectItem>
                                        <SelectItem value="dificil">Dificil</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.level" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="activity-due-date">Data de entrega</Label>
                                <Input id="activity-due-date" v-model="form.due_date" type="date" class="border-white/10 bg-[var(--surface-elevated)] text-white" />
                                <InputError :message="form.errors.due_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="activity-status">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger id="activity-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione o status" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem value="draft">Rascunho</SelectItem>
                                        <SelectItem value="published">Publicado</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.status" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="activity-description">Descricao</Label>
                            <textarea id="activity-description" v-model="form.description" class="min-h-32 rounded-xl border border-white/10 bg-[var(--surface-elevated)] px-4 py-3 text-sm text-white outline-none focus:border-secondary" placeholder="Descreva a proposta da atividade." />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-3 rounded-2xl border border-white/10 bg-black/20 p-4 text-sm text-white/70 sm:grid-cols-3">
                            <div>
                                <p class="text-white/50">Pontos por questao</p>
                                <p class="mt-1 text-xl font-semibold text-white">{{ formatPoints(previewPointsPerQuestion) }}</p>
                            </div>
                            <div>
                                <p class="text-white/50">Questoes cadastradas</p>
                                <p class="mt-1 text-xl font-semibold text-white">{{ questionsCount }}</p>
                            </div>
                            <div>
                                <p class="text-white/50">Total previsto</p>
                                <p class="mt-1 text-xl font-semibold text-secondary">{{ formatPoints(previewTotalPoints) }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="step === 2" class="grid min-w-0 gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
                        <div class="min-w-0 rounded-2xl border border-white/10 bg-black/30 p-4 lg:self-start">
                            <div class="mb-4 flex items-center justify-between gap-2">
                                <div>
                                    <h4 class="text-lg font-semibold">Questoes</h4>
                                    <InputError :message="form.errors.questions" />
                                </div>
                                <Button type="button" size="sm" class="rounded-xl" @click="addQuestion">
                                    <Plus class="size-3.5" />
                                    Nova
                                </Button>
                            </div>

                            <div class="space-y-2">
                                <button
                                    v-for="(question, index) in form.questions"
                                    :key="question.id ?? index"
                                    type="button"
                                    class="w-full cursor-pointer rounded-xl border text-left"
                                    :class="activeQuestionIndex === index ? 'border-secondary bg-secondary/15 text-white' : 'border-white/10 bg-black/20 text-white/70 hover:bg-white/[0.04]'"
                                    @click="selectQuestion(index)"
                                >
                                    <div class="flex items-start justify-between gap-3 px-4 py-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium">Questao {{ index + 1 }}</div>
                                            <div class="mt-1 truncate text-xs text-white/45">{{ question.statement || questionTypeLabels[question.type] }}</div>
                                        </div>
                                        <Button v-if="form.questions.length > 1" type="button" variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click.stop="deleteQuestion(index)">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div v-if="currentQuestion" class="min-w-0 space-y-5 rounded-2xl border border-white/10 bg-black/20 p-4 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-xl font-semibold">Questao {{ activeQuestionIndex + 1 }}</h4>
                                    <p class="mt-1 text-sm text-white/45">{{ questionTypeLabels[currentQuestion.type] }}</p>
                                </div>
                                <Select v-model="currentQuestion.type" @update:model-value="(value) => changeQuestionType(value as QuestionType)">
                                    <SelectTrigger class="w-[240px] border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Tipo de questao" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem value="multiple_choice">Multipla Escolha</SelectItem>
                                        <SelectItem value="drag_drop">Arrastar e Soltar</SelectItem>
                                        <SelectItem value="matching">Relacionar Colunas</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <InputError :message="errorFor(`questions.${activeQuestionIndex}.type`)" />

                            <div class="grid gap-2">
                                <Label :for="`question-${activeQuestionIndex}-statement`">Enunciado</Label>
                                <textarea
                                    :id="`question-${activeQuestionIndex}-statement`"
                                    v-model="currentQuestion.statement"
                                    class="min-h-28 rounded-xl border border-white/10 bg-[var(--surface-elevated)] px-4 py-3 text-sm text-white outline-none focus:border-secondary"
                                    placeholder="Digite o enunciado da questao."
                                    @input="currentQuestion.type === 'drag_drop' ? initializeBlanks(currentQuestion) : undefined"
                                />
                                <InputError :message="errorFor(`questions.${activeQuestionIndex}.statement`)" />
                            </div>

                            <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div v-for="option in optionKeys" :key="option" class="rounded-2xl border p-4" :class="currentQuestion.correct_option === option ? 'border-emerald-400/30 bg-emerald-500/10' : 'border-white/10 bg-white/[0.03]'">
                                        <div class="flex items-start gap-3">
                                            <button type="button" class="mt-1 flex h-5 w-5 items-center justify-center rounded-full border text-[10px] font-bold" :class="currentQuestion.correct_option === option ? 'border-emerald-400 bg-emerald-400 text-black' : 'border-white/20 text-white/40'" @click="setCorrectOption(option)">
                                                <span v-if="currentQuestion.correct_option === option">●</span>
                                            </button>
                                            <div class="min-w-0 flex-1">
                                                <Label :for="`question-${activeQuestionIndex}-option-${option}`">Alternativa {{ option }}</Label>
                                                <Input :id="`question-${activeQuestionIndex}-option-${option}`" v-model="currentQuestion.options![option]" class="mt-2 border-white/10 bg-[var(--surface-elevated)] text-white" :placeholder="`Alternativa ${option}`" />
                                                <InputError :message="errorFor(`questions.${activeQuestionIndex}.options.${option}`)" />
                                                <Button type="button" size="sm" class="mt-3 rounded-xl" variant="outline" @click="setCorrectOption(option)">
                                                    {{ currentQuestion.correct_option === option ? 'Correta' : 'Marcar correta' }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="errorFor(`questions.${activeQuestionIndex}.correct_option`)" />
                            </div>

                            <div v-else-if="currentQuestion.type === 'drag_drop'" class="space-y-4">
                                <div class="rounded-2xl border border-secondary/20 bg-secondary/10 p-4 text-sm text-white/70">
                                    Use marcadores como <code class="rounded bg-black/30 px-2 py-1 text-white">[blank_1]</code> no enunciado e relacione cada lacuna a uma palavra-chave.
                                </div>

                                <div>
                                    <Label>Palavras-chave</Label>
                                    <div class="mt-2 space-y-2">
                                        <div v-for="(_, keywordIndex) in currentQuestion.keywords" :key="keywordIndex" class="flex min-w-0 gap-2">
                                            <Input v-model="currentQuestion.keywords![keywordIndex]" class="min-w-0 border-white/10 bg-[var(--surface-elevated)] text-white" :placeholder="`Palavra-chave ${keywordIndex + 1}`" />
                                            <Button v-if="(currentQuestion.keywords?.length ?? 0) > 1" type="button" variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white" @click="removeKeyword(currentQuestion, keywordIndex)">
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </div>
                                    </div>
                                    <Button type="button" size="sm" class="mt-3 rounded-xl" @click="addKeyword(currentQuestion)">
                                        <Plus class="size-3.5" />
                                        Adicionar palavra
                                    </Button>
                                    <InputError :message="errorFor(`questions.${activeQuestionIndex}.keywords`)" />
                                </div>

                                <div v-if="extractBlanksFromStatement(currentQuestion.statement).length > 0">
                                    <Label>Mapeamento das lacunas</Label>
                                    <div class="mt-2 space-y-2">
                                        <div v-for="blank in extractBlanksFromStatement(currentQuestion.statement)" :key="blank" class="grid gap-2 rounded-xl border border-white/10 bg-white/5 p-3 sm:grid-cols-[140px_minmax(0,1fr)] sm:items-center">
                                            <span class="text-sm font-medium text-white/85">{{ blank }}</span>
                                            <select v-model.number="currentQuestion.blank_answers![blank]" class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white outline-none focus:border-secondary">
                                                <option :value="-1">Selecionar palavra-chave</option>
                                                <option v-for="(keyword, keywordIndex) in currentQuestion.keywords" :key="keywordIndex" :value="keywordIndex">
                                                    {{ keyword || `Palavra ${keywordIndex + 1}` }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="rounded-2xl border border-dashed border-amber-500/20 bg-amber-500/5 p-4 text-sm text-amber-300">
                                    Adicione [blank_1], [blank_2] no enunciado para mapear as lacunas.
                                </div>
                                <InputError :message="errorFor(`questions.${activeQuestionIndex}.blank_answers`)" />
                            </div>

                            <div v-else class="space-y-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <Label>Coluna esquerda</Label>
                                        <div class="mt-2 space-y-2">
                                            <div v-for="(_, leftIndex) in currentQuestion.left_column" :key="leftIndex" class="flex min-w-0 gap-2">
                                                <Input v-model="currentQuestion.left_column![leftIndex]" class="min-w-0 border-white/10 bg-[var(--surface-elevated)] text-white" :placeholder="`Item ${leftIndex + 1}`" />
                                                <Button v-if="(currentQuestion.left_column?.length ?? 0) > 1" type="button" variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white" @click="removeColumnItem(currentQuestion, 'left_column', leftIndex)">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </div>
                                        </div>
                                        <Button type="button" size="sm" class="mt-3 rounded-xl" @click="addColumnItem(currentQuestion, 'left_column')">
                                            <Plus class="size-3.5" />
                                            Adicionar item
                                        </Button>
                                        <InputError :message="errorFor(`questions.${activeQuestionIndex}.left_column`)" />
                                    </div>

                                    <div>
                                        <Label>Coluna direita</Label>
                                        <div class="mt-2 space-y-2">
                                            <div v-for="(_, rightIndex) in currentQuestion.right_column" :key="rightIndex" class="flex min-w-0 gap-2">
                                                <Input v-model="currentQuestion.right_column![rightIndex]" class="min-w-0 border-white/10 bg-[var(--surface-elevated)] text-white" :placeholder="`Item ${rightIndex + 1}`" />
                                                <Button v-if="(currentQuestion.right_column?.length ?? 0) > 1" type="button" variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white" @click="removeColumnItem(currentQuestion, 'right_column', rightIndex)">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </div>
                                        </div>
                                        <Button type="button" size="sm" class="mt-3 rounded-xl" @click="addColumnItem(currentQuestion, 'right_column')">
                                            <Plus class="size-3.5" />
                                            Adicionar item
                                        </Button>
                                        <InputError :message="errorFor(`questions.${activeQuestionIndex}.right_column`)" />
                                    </div>
                                </div>

                                <div>
                                    <Label>Pares corretos</Label>
                                    <div class="mt-2 space-y-2">
                                        <div v-for="(left, leftIndex) in currentQuestion.left_column" :key="leftIndex" class="grid min-w-0 gap-2 rounded-xl border border-white/10 bg-white/5 p-3 sm:grid-cols-[minmax(0,1fr)_minmax(220px,1fr)] sm:items-center">
                                            <span class="truncate text-sm text-white/85">{{ left || `Item ${leftIndex + 1}` }}</span>
                                            <select v-model="currentQuestion.pairs![leftIndex.toString()]" class="w-full min-w-0 rounded-xl border border-white/10 bg-black/30 px-4 py-2 text-sm text-white outline-none focus:border-secondary">
                                                <option value="">Selecionar correspondente</option>
                                                <option v-for="(right, rightIndex) in currentQuestion.right_column" :key="rightIndex" :value="rightIndex.toString()">
                                                    {{ right || `Item ${rightIndex + 1}` }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <InputError :message="errorFor(`questions.${activeQuestionIndex}.pairs`)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="grid gap-5 lg:grid-cols-[360px_minmax(0,1fr)]">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                            <h4 class="text-lg font-semibold">Resumo da atividade</h4>
                            <div class="mt-4 space-y-3 text-sm text-white/65">
                                <div class="flex justify-between gap-4"><span>Titulo</span><span class="text-right text-white">{{ form.title || 'Sem titulo' }}</span></div>
                                <div class="flex justify-between gap-4"><span>Turma</span><span class="text-right text-white">{{ props.classrooms.find((classroom) => String(classroom.id) === form.classroom_id)?.name ?? 'Sem turma' }}</span></div>
                                <div class="flex justify-between gap-4"><span>Dificuldade</span><span class="text-white">{{ levelLabels[form.level] }}</span></div>
                                <div class="flex justify-between gap-4"><span>Status</span><span class="text-white">{{ statusLabels[form.status] }}</span></div>
                                <div class="flex justify-between gap-4"><span>Questoes</span><span class="text-white">{{ questionsCount }}</span></div>
                                <div class="flex justify-between gap-4"><span>Pontos/questao</span><span class="text-white">{{ formatPoints(previewPointsPerQuestion) }}</span></div>
                                <div class="flex justify-between gap-4 border-t border-white/10 pt-3"><span>Total previsto</span><span class="font-semibold text-secondary">{{ formatPoints(previewTotalPoints) }}</span></div>
                            </div>
                        </div>

                        <div class="space-y-3 rounded-2xl border border-white/10 bg-black/20 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h4 class="text-lg font-semibold">Preview das questoes</h4>
                                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">{{ questionsCount }} questao(oes)</span>
                            </div>

                            <div class="max-h-[540px] space-y-3 overflow-y-auto pr-1">
                                <div v-for="(question, index) in form.questions" :key="question.id ?? index" class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="font-semibold text-white">Questao {{ index + 1 }}</p>
                                        <span class="rounded-full border border-secondary/30 bg-secondary/15 px-3 py-1 text-xs font-semibold text-secondary">{{ questionTypeLabels[question.type] }}</span>
                                    </div>
                                    <p class="mt-3 text-sm text-white/70">{{ question.statement || 'Sem enunciado' }}</p>

                                    <div v-if="question.type === 'multiple_choice'" class="mt-3 grid gap-2 sm:grid-cols-2">
                                        <div v-for="option in optionKeys" :key="option" class="rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-sm" :class="question.correct_option === option ? 'text-emerald-300' : 'text-white/70'">
                                            {{ option }}. {{ question.options?.[option] || 'Sem alternativa' }}
                                        </div>
                                    </div>

                                    <div v-else-if="question.type === 'drag_drop'" class="mt-3 text-sm text-white/65">
                                        <p>Palavras: {{ (question.keywords ?? []).filter(Boolean).join(', ') || 'Sem palavras' }}</p>
                                        <p class="mt-1">Lacunas: {{ Object.keys(question.blank_answers ?? {}).join(', ') || 'Sem lacunas mapeadas' }}</p>
                                    </div>

                                    <div v-else class="mt-3 grid gap-3 text-sm text-white/65 md:grid-cols-2">
                                        <div>
                                            <p class="font-medium text-white/85">Esquerda</p>
                                            <p>{{ (question.left_column ?? []).filter(Boolean).join(', ') || 'Sem itens' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-white/85">Direita</p>
                                            <p>{{ (question.right_column ?? []).filter(Boolean).join(', ') || 'Sem itens' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter class="shrink-0 gap-2 border-t border-white/5 px-5 py-5 sm:px-6">
                    <DialogClose as-child>
                        <Button type="button" variant="outline" class="border-white/10 bg-white/5 text-white hover:bg-white/10" @click="closeActivityDialog">Cancelar</Button>
                    </DialogClose>
                    <Button v-if="step > 1" type="button" variant="outline" class="border-white/10 bg-white/5 text-white hover:bg-white/10" @click="prevStep">Voltar</Button>
                    <Button v-if="step < 3" type="button" @click="nextStep">Continuar</Button>
                    <Button v-else type="submit" :disabled="form.processing || props.classrooms.length === 0">
                        {{ mode === 'edit' ? 'Salvar alteracoes' : 'Criar atividade' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => (value ? (deleteDialogOpen = value) : closeDeleteDialog())">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Confirmar exclusao</DialogTitle>
                <DialogDescription class="text-white/60">
                    Esta operacao nao pode ser desfeita e removera a atividade <span class="font-semibold text-white">{{ selectedActivity?.title }}</span>.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button type="button" variant="outline" class="border-white/10 bg-white/5 text-white hover:bg-white/10" @click="closeDeleteDialog">Cancelar</Button>
                </DialogClose>
                <Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir atividade</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
