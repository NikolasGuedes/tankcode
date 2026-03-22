<script setup lang="ts">
import {
    destroy as destroyStudent,
    store as storeStudent,
    update as updateStudent,
} from '@/actions/App/Http/Controllers/Director/StudentController';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

type PointOption = { id: number; name: string };
type StudentRow = {
    id: number;
    name: string;
    email: string;
    status: string;
    point_of_school_id: number | null;
    point_of_school: string | null;
    classroom: string;
    last_login_at: string;
};

const props = defineProps<{
    stats: { total: number; active: number; points: number };
    points: PointOption[];
    students: StudentRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Alunos', href: '/director/students' },
];

const statusLabel: Record<string, string> = { active: 'Ativo', inactive: 'Inativo' };
const dialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedStudent = ref<StudentRow | null>(null);
const form = useForm({ name: '', email: '', point_of_school_id: '', status: 'active' });
const deleteForm = useForm({});

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
                        <p class="text-sm text-white/60">Cada aluno fica vinculado a um unico ponto de ensino.</p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog"><Plus class="size-4" />Novo Aluno</Button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead><tr class="text-left text-sm text-secondary"><th class="pb-4 font-medium">Aluno</th><th class="pb-4 font-medium">Ponto de Ensino</th><th class="pb-4 font-medium">Turma</th><th class="pb-4 font-medium">Status</th><th class="pb-4 font-medium">Ultimo acesso</th><th class="pb-4 text-right font-medium">Ações</th></tr></thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="student in props.students" :key="student.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ student.name }}</p><p class="text-white/55">{{ student.email }}</p></td>
                                <td class="py-4">{{ student.point_of_school ?? '-' }}</td>
                                <td class="py-4">{{ student.classroom }}</td>
                                <td class="py-4"><span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-white">{{ statusLabel[student.status] ?? student.status }}</span></td>
                                <td class="py-4">{{ student.last_login_at }}</td>
                                <td class="py-4"><div class="flex justify-end gap-2"><Button variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]" @click="openEditDialog(student)"><Pencil class="size-4" /></Button><Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(student)"><Trash2 class="size-4" /></Button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppReveal>
        </section>
    </AppLayout>

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
