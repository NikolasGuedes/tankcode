<script setup lang="ts">
import {
    destroy as destroyClassroom,
    store as storeClassroom,
    update as updateClassroom,
} from '@/actions/App/Http/Controllers/Director/ClassroomController';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type PointOption = { id: number; name: string };
type TeacherOption = { id: number; name: string; point_of_school_ids: number[] };
type StudentOption = { id: number; name: string; point_of_school_ids: number[] };
type ClassroomRow = { id: number; point_of_school_id: number; teacher_id: number | null; name: string; code: string; status: string; point_of_school: string | null; teacher: string; students_count: number; student_ids: number[] };

const props = defineProps<{
    stats: { total: number; active: number; students: number };
    points: PointOption[];
    teachers: TeacherOption[];
    students: StudentOption[];
    classrooms: ClassroomRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Turmas', href: '/director/classrooms' },
];

const statusLabel: Record<string, string> = { active: 'Ativo', inactive: 'Inativo' };
const dialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedClassroom = ref<ClassroomRow | null>(null);
const form = useForm({ point_of_school_id: '', teacher_id: '', name: '', code: '', student_ids: [] as string[], status: 'active' });
const deleteForm = useForm({});

const availableTeachers = computed(() => props.teachers.filter((teacher) => !form.point_of_school_id || teacher.point_of_school_ids.includes(Number(form.point_of_school_id))));
const availableStudents = computed(() => props.students.filter((student) => !form.point_of_school_id || student.point_of_school_ids.includes(Number(form.point_of_school_id))));

const resetForm = () => {
    form.clearErrors();
    form.point_of_school_id = props.points[0]?.id ? String(props.points[0].id) : '';
    form.teacher_id = '';
    form.name = '';
    form.code = '';
    form.student_ids = [];
    form.status = 'active';
};

const openCreateDialog = () => {
    selectedClassroom.value = null;
    resetForm();
    dialogOpen.value = true;
};

const openEditDialog = (classroom: ClassroomRow) => {
    selectedClassroom.value = classroom;
    form.clearErrors();
    form.point_of_school_id = String(classroom.point_of_school_id);
    form.teacher_id = classroom.teacher_id ? String(classroom.teacher_id) : '';
    form.name = classroom.name;
    form.code = classroom.code;
    form.student_ids = classroom.student_ids.map(String);
    form.status = classroom.status;
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    selectedClassroom.value = null;
    resetForm();
};

const toggleStudent = (studentId: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.student_ids.includes(studentId)) form.student_ids = [...form.student_ids, studentId];
        return;
    }
    form.student_ids = form.student_ids.filter((id) => id !== studentId);
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => closeDialog() };
    if (selectedClassroom.value) {
        form.put(updateClassroom(selectedClassroom.value.id).url, options);
        return;
    }
    form.post(storeClassroom().url, options);
};

const openDeleteDialog = (classroom: ClassroomRow) => {
    selectedClassroom.value = classroom;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedClassroom.value = null;
};

const submitDelete = () => {
    if (!selectedClassroom.value) return;
    deleteForm.delete(destroyClassroom(selectedClassroom.value.id).url, { preserveScroll: true, onSuccess: () => closeDeleteDialog() });
};

watch(() => form.point_of_school_id, (pointId) => {
    const allowedStudents = new Set(availableStudents.value.map((student) => String(student.id)));
    form.student_ids = form.student_ids.filter((studentId) => allowedStudents.has(studentId));

    if (form.teacher_id && !availableTeachers.value.some((teacher) => String(teacher.id) === form.teacher_id)) {
        form.teacher_id = '';
    }
});
</script>

<template>
    <Head title="Turmas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Turmas</h1>
                <p class="mt-2 text-lg text-white/70">Estruture turmas, associe professores e distribua os alunos pelos pontos de ensino da diretoria.</p>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Total</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Ativas</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Alunos vinculados</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.students }}</p></div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Turmas cadastradas</h2>
                        <p class="text-sm text-white/60">Cada turma pertence a um ponto de ensino e pode ter professor responsavel.</p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog"><Plus class="size-4" />Nova Turma</Button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead><tr class="text-left text-sm text-secondary"><th class="pb-4 font-medium">Turma</th><th class="pb-4 font-medium">Ponto de Ensino</th><th class="pb-4 font-medium">Professor</th><th class="pb-4 font-medium">Alunos</th><th class="pb-4 font-medium">Status</th><th class="pb-4 text-right font-medium">Ações</th></tr></thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="classroom in props.classrooms" :key="classroom.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ classroom.name }}</p><p class="text-white/55">{{ classroom.code }}</p></td>
                                <td class="py-4">{{ classroom.point_of_school ?? '-' }}</td>
                                <td class="py-4">{{ classroom.teacher }}</td>
                                <td class="py-4">{{ classroom.students_count }}</td>
                                <td class="py-4"><span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-white">{{ statusLabel[classroom.status] ?? classroom.status }}</span></td>
                                <td class="py-4"><div class="flex justify-end gap-2"><Button variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]" @click="openEditDialog(classroom)"><Pencil class="size-4" /></Button><Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(classroom)"><Trash2 class="size-4" /></Button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog :open="dialogOpen" @update:open="(value) => !value ? closeDialog() : (dialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-2xl">
            <DialogHeader><DialogTitle>{{ selectedClassroom ? 'Editar Turma' : 'Nova Turma' }}</DialogTitle><DialogDescription class="text-white/60">Defina o ponto de ensino, o professor responsavel e os alunos vinculados a turma.</DialogDescription></DialogHeader>
            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2"><Label for="classroom-point">Ponto de Ensino</Label><Select v-model="form.point_of_school_id"><SelectTrigger id="classroom-point" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione o ponto de ensino" /></SelectTrigger><SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem v-for="point in props.points" :key="point.id" :value="String(point.id)">{{ point.name }}</SelectItem></SelectContent></Select></div>
                    <div class="grid gap-2"><Label for="classroom-teacher">Professor responsavel</Label><Select v-model="form.teacher_id"><SelectTrigger id="classroom-teacher" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione um professor" /></SelectTrigger><SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem v-for="teacher in availableTeachers" :key="teacher.id" :value="String(teacher.id)">{{ teacher.name }}</SelectItem></SelectContent></Select></div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2"><Label for="classroom-name">Nome</Label><Input id="classroom-name" v-model="form.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: 3A - Manha" /></div>
                    <div class="grid gap-2"><Label for="classroom-code">Codigo</Label><Input id="classroom-code" v-model="form.code" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: 3A-MNH" /></div>
                </div>
                <div class="grid gap-3">
                    <div class="flex items-center justify-between gap-3"><Label>Alunos vinculados</Label><span class="text-xs text-white/50">{{ form.student_ids.length }} selecionado(s)</span></div>
                    <div class="max-h-64 space-y-3 overflow-y-auto rounded-2xl border border-white/10 bg-[var(--surface-elevated)] p-3">
                        <div v-if="availableStudents.length" v-for="student in availableStudents" :key="student.id" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3">
                            <Checkbox :model-value="form.student_ids.includes(String(student.id))" class="border-white/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary" @update:model-value="(checked) => toggleStudent(String(student.id), checked)" />
                            <div class="min-w-0 flex-1"><p class="truncate font-medium text-white">{{ student.name }}</p></div>
                        </div>
                        <div v-else class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-white/50">Selecione um ponto de ensino para listar os alunos disponiveis.</div>
                    </div>
                </div>
                <div class="grid gap-2"><Label for="classroom-status">Status</Label><Select v-model="form.status"><SelectTrigger id="classroom-status" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione o status" /></SelectTrigger><SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem value="active">Ativa</SelectItem><SelectItem value="inactive">Inativa</SelectItem></SelectContent></Select></div>
                <DialogFooter class="gap-2"><DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDialog">Cancelar</Button></DialogClose><Button type="submit" :disabled="form.processing">{{ selectedClassroom ? 'Salvar alteracoes' : 'Criar turma' }}</Button></DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => !value ? closeDeleteDialog() : (deleteDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader><DialogTitle>Confirmar exclusao</DialogTitle><DialogDescription class="text-white/60">Esta operacao nao pode ser desfeita e removera a turma <span class="font-semibold text-white">{{ selectedClassroom?.name }}</span>.</DialogDescription></DialogHeader>
            <DialogFooter class="gap-2"><DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDeleteDialog">Cancelar</Button></DialogClose><Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir turma</Button></DialogFooter>
        </DialogContent>
    </Dialog>
</template>
