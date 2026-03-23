<script setup lang="ts">
import {
    downloadTemplate as downloadStudentTemplate,
    destroy as destroyStudent,
    importStudents as importStudentsAction,
    resendInvitation as resendStudentInvitation,
    store as storeStudent,
    update as updateStudent,
    updateAccess as updateStudentAccess,
} from '@/actions/App/Http/Controllers/Director/StudentController';
import AppTablePagination from '@/components/AppTablePagination.vue';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useDebounceFn } from '@vueuse/core';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Download, Mail, Pencil, Plus, Trash2, Upload, XCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type PointOption = { id: number; name: string };
type ClassroomOption = { id: number; point_of_school_id: number; name: string; code: string };
type StudentRow = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    has_verified_email: boolean;
    status: string;
    point_of_school_id: number | null;
    point_of_school: string | null;
    classroom: string;
    last_login_at: string;
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
    stats: { total: number; active: number; points: number };
    points: PointOption[];
    classrooms: ClassroomOption[];
    students: Paginated<StudentRow>;
    filters: {
        search?: string;
        status?: string;
        point_of_school_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Alunos', href: '/director/students' },
];

const dialogOpen = ref(false);
const importDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedStudent = ref<StudentRow | null>(null);
const accessUpdatingId = ref<number | null>(null);
const resendingInvitationId = ref<number | null>(null);
const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    point_of_school_id: props.filters.point_of_school_id ? String(props.filters.point_of_school_id) : 'all',
});
const suspendAutoFilters = ref(false);
const form = useForm({ name: '', email: '', point_of_school_id: '', status: 'active' });
const importForm = useForm({
    point_of_school_id: props.points[0]?.id ? String(props.points[0].id) : '',
    classroom_id: '',
    file: null as File | null,
});
const deleteForm = useForm({});
const accessLabel: Record<string, string> = { active: 'Habilitado', inactive: 'Desabilitado' };
const availableImportClassrooms = computed(() =>
    props.classrooms.filter((classroom) => String(classroom.point_of_school_id) === importForm.point_of_school_id),
);

const applyFilters = () => {
    router.get(
        '/director/students',
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status !== 'all' ? filtersForm.status : undefined,
            point_of_school_id: filtersForm.point_of_school_id !== 'all' ? filtersForm.point_of_school_id : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const debouncedApplyFilters = useDebounceFn(() => {
    if (suspendAutoFilters.value) {
        return;
    }

    applyFilters();
}, 400);

const resetFilters = () => {
    suspendAutoFilters.value = true;
    filtersForm.search = '';
    filtersForm.status = 'all';
    filtersForm.point_of_school_id = 'all';
    applyFilters();
    suspendAutoFilters.value = false;
};

const resetForm = () => {
    form.clearErrors();
    form.name = '';
    form.email = '';
    form.point_of_school_id = props.points[0]?.id ? String(props.points[0].id) : '';
    form.status = 'active';
};

const closeDialog = () => {
    dialogOpen.value = false;
    selectedStudent.value = null;
    resetForm();
};

const resetImportForm = () => {
    importForm.clearErrors();
    importForm.point_of_school_id = props.points[0]?.id ? String(props.points[0].id) : '';
    importForm.classroom_id = '';
    importForm.file = null;
};

const openImportDialog = () => {
    resetImportForm();
    importDialogOpen.value = true;
};

const closeImportDialog = () => {
    importDialogOpen.value = false;
    resetImportForm();
};

const openCreateDialog = () => {
    selectedStudent.value = null;
    resetForm();
    dialogOpen.value = true;
};

const openEditDialog = (student: StudentRow) => {
    selectedStudent.value = student;
    form.clearErrors();
    form.name = student.name;
    form.email = student.email;
    form.point_of_school_id = student.point_of_school_id ? String(student.point_of_school_id) : '';
    form.status = student.status;
    dialogOpen.value = true;
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => closeDialog() };

    if (selectedStudent.value) {
        form.put(updateStudent(selectedStudent.value.id).url, options);
        return;
    }

    form.post(storeStudent().url, options);
};

const openDeleteDialog = (student: StudentRow) => {
    selectedStudent.value = student;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedStudent.value = null;
};

const submitDelete = () => {
    if (!selectedStudent.value) return;

    deleteForm.delete(destroyStudent(selectedStudent.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};

const handleImportFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    importForm.file = target.files?.[0] ?? null;
};

const submitImport = () => {
    importForm.post(importStudentsAction().url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => closeImportDialog(),
    });
};

const updateAccess = (student: StudentRow, value: unknown) => {
    const nextStatus = ['string', 'number', 'bigint'].includes(typeof value) ? String(value) : '';

    if (!['active', 'inactive'].includes(nextStatus) || nextStatus === student.status) {
        return;
    }

    accessUpdatingId.value = student.id;

    router.patch(
        updateStudentAccess(student.id).url,
        { status: nextStatus },
        {
            preserveScroll: true,
            onFinish: () => {
                accessUpdatingId.value = null;
            },
        },
    );
};

const resendInvitation = (student: StudentRow) => {
    if (student.has_verified_email) {
        return;
    }

    resendingInvitationId.value = student.id;

    router.post(
        resendStudentInvitation(student.id).url,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                resendingInvitationId.value = null;
            },
        },
    );
};

watch(
    () => [filtersForm.search, filtersForm.status, filtersForm.point_of_school_id],
    () => {
        debouncedApplyFilters();
    },
);

watch(
    () => importForm.point_of_school_id,
    (pointId) => {
        const matchingClassrooms = props.classrooms.filter((classroom) => String(classroom.point_of_school_id) === pointId);

        if (!matchingClassrooms.some((classroom) => String(classroom.id) === importForm.classroom_id)) {
            importForm.classroom_id = matchingClassrooms[0]?.id ? String(matchingClassrooms[0].id) : '';
        }
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Alunos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Alunos</h1>
                <p class="mt-2 text-lg text-white/70">Cadastre alunos e vincule cada um ao ponto de ensino correto dentro da diretoria.</p>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Total</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Ativos</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Pontos de Ensino</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.points }}</p></div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Alunos cadastrados</h2>
                        <p class="text-sm text-white/60">Controle validacao de e-mail, acesso a plataforma e vinculacao de cada aluno.</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <Button as-child class="rounded-2xl !bg-primary !text-white hover:!bg-[var(--primary-hover)]">
                            <a :href="downloadStudentTemplate().url">
                                <Download class="size-4" />
                                Baixar template
                            </a>
                        </Button>
                        <Button class="rounded-2xl !bg-primary !text-white hover:!bg-[var(--primary-hover)]" @click="openImportDialog">
                            <Upload class="size-4" />
                            Importar alunos
                        </Button>
                        <Button class="rounded-2xl" @click="openCreateDialog"><Plus class="size-4" />Novo Aluno</Button>
                    </div>
                </div>

                <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 md:grid-cols-[minmax(0,1fr)_220px_240px_auto]" @submit.prevent="applyFilters">
                    <Input v-model="filtersForm.search" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Buscar por nome ou e-mail" />
                    <Select v-model="filtersForm.status">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todos os status</SelectItem>
                            <SelectItem value="active">Ativos</SelectItem>
                            <SelectItem value="inactive">Inativos</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="filtersForm.point_of_school_id">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Ponto de Ensino" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todos os pontos</SelectItem>
                            <SelectItem v-for="point in props.points" :key="point.id" :value="String(point.id)">
                                {{ point.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div class="flex gap-3">
                        <Button type="button" class="rounded-2xl" @click="resetFilters">Limpar</Button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead><tr class="text-left text-sm text-secondary"><th class="pb-4 font-medium">Aluno</th><th class="pb-4 font-medium">Ponto de Ensino</th><th class="pb-4 font-medium">Turma</th><th class="pb-4 font-medium">E-mail validado</th><th class="pb-4 font-medium">Acesso a plataforma</th><th class="pb-4 font-medium">Ultimo acesso</th><th class="pb-4 text-right font-medium">Ações</th></tr></thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="student in props.students.data" :key="student.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ student.name }}</p><p class="text-white/55">{{ student.email }}</p></td>
                                <td class="py-4">{{ student.point_of_school ?? '-' }}</td>
                                <td class="py-4">{{ student.classroom }}</td>
                                <td class="py-4">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="student.has_verified_email ? 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-rose-500/15 text-rose-300 ring-1 ring-rose-500/30'"
                                    >
                                        <CheckCircle2 v-if="student.has_verified_email" class="size-3.5" />
                                        <XCircle v-else class="size-3.5" />
                                        {{ student.has_verified_email ? 'Validado' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <Select :model-value="student.status" :disabled="accessUpdatingId === student.id" @update:model-value="(value) => updateAccess(student, value)">
                                        <SelectTrigger class="h-10 w-[170px] border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue :placeholder="accessLabel[student.status] ?? student.status" /></SelectTrigger>
                                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem value="active">{{ accessLabel.active }}</SelectItem><SelectItem value="inactive">{{ accessLabel.inactive }}</SelectItem></SelectContent>
                                    </Select>
                                </td>
                                <td class="py-4">{{ student.last_login_at }}</td>
                                <td class="py-4"><div class="flex justify-end gap-2"><Button v-if="!student.has_verified_email" variant="outline" size="icon-sm" class="!border-emerald-500/70 !bg-emerald-500/15 !text-emerald-200 hover:!border-emerald-400 hover:!bg-emerald-500/25 hover:!text-emerald-100" :disabled="resendingInvitationId === student.id" @click="resendInvitation(student)"><Mail class="size-4" /></Button><Button variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]" @click="openEditDialog(student)"><Pencil class="size-4" /></Button><Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(student)"><Trash2 class="size-4" /></Button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.students" :links="props.students.links" />
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog :open="importDialogOpen" @update:open="(value) => !value ? closeImportDialog() : (importDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Importar alunos</DialogTitle>
                <DialogDescription class="text-white/60">Use o template padrao com as colunas NOME e EMAIL e escolha o ponto de ensino que recebera os alunos importados.</DialogDescription>
            </DialogHeader>
            <form class="space-y-5" @submit.prevent="submitImport">
                <div class="grid gap-2">
                    <Label for="import-student-point">Ponto de Ensino</Label>
                    <Select v-model="importForm.point_of_school_id">
                        <SelectTrigger id="import-student-point" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Selecione o ponto de ensino" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem v-for="point in props.points" :key="point.id" :value="String(point.id)">
                                {{ point.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-2">
                    <Label for="import-student-classroom">Turma</Label>
                    <Select v-model="importForm.classroom_id">
                        <SelectTrigger id="import-student-classroom" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Selecione a turma" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem v-for="classroom in availableImportClassrooms" :key="classroom.id" :value="String(classroom.id)">
                                {{ classroom.name }}<span v-if="classroom.code"> - {{ classroom.code }}</span>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p class="text-xs text-white/50">A lista de turmas acompanha o ponto de ensino selecionado.</p>
                </div>

                <div class="grid gap-2">
                    <Label for="import-student-file">Arquivo</Label>
                    <Input id="import-student-file" type="file" class="border-white/10 bg-[var(--surface-elevated)] text-white file:text-white" accept=".xlsx,.xls,.csv" @change="handleImportFileChange" />
                    <p class="text-xs text-white/50">Formatos aceitos: XLSX, XLS e CSV.</p>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeImportDialog">Cancelar</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="importForm.processing">
                        <Upload class="size-4" />
                        Importar planilha
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="dialogOpen" @update:open="(value) => !value ? closeDialog() : (dialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ selectedStudent ? 'Editar Aluno' : 'Novo Aluno' }}</DialogTitle>
                <DialogDescription class="text-white/60">O aluno recebera um convite por e-mail para definir a senha no primeiro acesso.</DialogDescription>
            </DialogHeader>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-2"><Label for="student-name">Nome</Label><Input id="student-name" v-model="form.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Julia Lima" /></div>
                <div class="grid gap-2"><Label for="student-email">E-mail</Label><Input id="student-email" v-model="form.email" type="email" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="aluno@escola.com" /></div>
                <div class="grid gap-2"><Label for="student-point">Ponto de Ensino</Label><Select v-model="form.point_of_school_id"><SelectTrigger id="student-point" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione o ponto de ensino" /></SelectTrigger><SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem v-for="point in props.points" :key="point.id" :value="String(point.id)">{{ point.name }}</SelectItem></SelectContent></Select></div>
                <div class="grid gap-2"><Label for="student-status">Status</Label><Select v-model="form.status"><SelectTrigger id="student-status" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione o status" /></SelectTrigger><SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem value="active">Ativo</SelectItem><SelectItem value="inactive">Inativo</SelectItem></SelectContent></Select></div>
                <DialogFooter class="gap-2"><DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDialog">Cancelar</Button></DialogClose><Button type="submit" :disabled="form.processing">{{ selectedStudent ? 'Salvar alteracoes' : 'Criar aluno' }}</Button></DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => !value ? closeDeleteDialog() : (deleteDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader><DialogTitle>Confirmar exclusao</DialogTitle><DialogDescription class="text-white/60">Esta operacao nao pode ser desfeita e removera o aluno <span class="font-semibold text-white">{{ selectedStudent?.name }}</span>.</DialogDescription></DialogHeader>
            <DialogFooter class="gap-2"><DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDeleteDialog">Cancelar</Button></DialogClose><Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir aluno</Button></DialogFooter>
        </DialogContent>
    </Dialog>
</template>
