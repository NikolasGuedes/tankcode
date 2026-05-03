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

const pointsByLevel: Record<ActivityLevel, number> = {
    facil: 1,
    media: 2.5,
    dificil: 4,
};

const activityDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const mode = ref<'create' | 'edit'>('create');
const selectedActivity = ref<ActivityRow | null>(null);
const suspendAutoFilters = ref(false);

const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    classroom_id: props.filters.classroom_id ? String(props.filters.classroom_id) : 'all',
});

const form = useForm({
    title: '',
    classroom_id: props.classrooms[0]?.id ? String(props.classrooms[0].id) : '',
    level: 'media' as ActivityLevel,
    questions_count: 1,
    description: '',
    due_date: '',
    status: 'draft' as ActivityStatus,
});

const deleteForm = useForm({});

const previewPointsPerQuestion = computed(() => pointsByLevel[form.level] ?? 0);
const previewTotalPoints = computed(() => Number(form.questions_count || 0) * previewPointsPerQuestion.value);

const formatPoints = (value: string | number) =>
    Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

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

const resetForm = () => {
    form.reset();
    form.clearErrors();
    form.classroom_id = props.classrooms[0]?.id ? String(props.classrooms[0].id) : '';
    form.level = 'media';
    form.questions_count = 1;
    form.status = 'draft';
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
    form.questions_count = activity.questions_count;
    form.description = activity.description ?? '';
    form.due_date = activity.due_date ?? '';
    form.status = activity.status;
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
                            <p class="mt-2 max-w-3xl text-lg text-white/70">Crie atividades para suas turmas com pontuacao calculada automaticamente pelo sistema.</p>
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
                        <p class="text-sm text-white/60">Acompanhe as atividades criadas e seus pontos oficiais salvos no banco.</p>
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
        <DialogContent class="max-h-[92vh] overflow-y-auto border-border bg-card text-white sm:max-w-3xl">
            <DialogHeader>
                <DialogTitle>{{ mode === 'edit' ? 'Editar atividade' : 'Nova atividade' }}</DialogTitle>
                <DialogDescription class="text-white/60">Os pontos oficiais sao calculados e salvos pelo backend.</DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submitActivity">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="activity-title">Titulo</Label>
                        <Input id="activity-title" v-model="form.title" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Avaliacao de SQL" />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
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

                    <div class="grid gap-2">
                        <Label for="activity-level">Nivel</Label>
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
                        <Label for="activity-questions">Quantidade de questoes</Label>
                        <Input id="activity-questions" v-model.number="form.questions_count" min="1" type="number" class="border-white/10 bg-[var(--surface-elevated)] text-white" />
                        <InputError :message="form.errors.questions_count" />
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

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="activity-description">Descricao</Label>
                        <textarea id="activity-description" v-model="form.description" class="min-h-28 rounded-xl border border-white/10 bg-[var(--surface-elevated)] px-4 py-3 text-sm text-white outline-none focus:border-secondary" placeholder="Descreva a proposta da atividade." />
                        <InputError :message="form.errors.description" />
                    </div>
                </div>

                <div class="grid gap-3 rounded-2xl border border-white/10 bg-black/20 p-4 text-sm text-white/70 sm:grid-cols-3">
                    <div>
                        <p class="text-white/50">Pontos por questao</p>
                        <p class="mt-1 text-xl font-semibold text-white">{{ formatPoints(previewPointsPerQuestion) }}</p>
                    </div>
                    <div>
                        <p class="text-white/50">Questoes</p>
                        <p class="mt-1 text-xl font-semibold text-white">{{ form.questions_count || 0 }}</p>
                    </div>
                    <div>
                        <p class="text-white/50">Total previsto</p>
                        <p class="mt-1 text-xl font-semibold text-secondary">{{ formatPoints(previewTotalPoints) }}</p>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="outline" class="border-white/10 bg-white/5 text-white hover:bg-white/10" @click="closeActivityDialog">Cancelar</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="form.processing || props.classrooms.length === 0">
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
