<script setup lang="ts">
import {
    destroy as destroyClassroom,
} from '@/actions/App/Http/Controllers/Director/ClassroomController';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

type ClassroomRow = {
    id: number;
    point_of_school_id: number;
    teacher_id: number | null;
    name: string;
    code: string;
    status: string;
    point_of_school: string | null;
    teacher: string;
    students_count: number;
    student_ids: number[];
};

const props = defineProps<{
    stats: { total: number; active: number; students: number };
    classrooms: ClassroomRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Turmas', href: '/director/classrooms' },
];

const statusLabel: Record<string, string> = { active: 'Ativo', inactive: 'Inativo' };
const deleteDialogOpen = ref(false);
const selectedClassroom = ref<ClassroomRow | null>(null);
const deleteForm = useForm({});

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
    deleteForm.delete(destroyClassroom(selectedClassroom.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};
</script>

<template>
    <Head title="Turmas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Turmas</h1>
                <p class="mt-2 text-lg text-white/70">Estruture turmas com um fluxo dedicado, mais espaco para distribuicao de alunos e visibilidade clara dos vinculos.</p>
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
                        <p class="text-sm text-white/60">Crie e edite turmas em uma pagina dedicada, com foco maior na selecao dos alunos.</p>
                    </div>
                    <Button as-child class="rounded-2xl">
                        <a href="/director/classrooms/create"><Plus class="size-4" />Nova Turma</a>
                    </Button>
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
                                <td class="py-4"><div class="flex justify-end gap-2"><Button as-child variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]"><a :href="`/director/classrooms/${classroom.id}/edit`"><Pencil class="size-4" /></a></Button><Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(classroom)"><Trash2 class="size-4" /></Button></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => !value ? closeDeleteDialog() : (deleteDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader><DialogTitle>Confirmar exclusao</DialogTitle><DialogDescription class="text-white/60">Esta operacao nao pode ser desfeita e removera a turma <span class="font-semibold text-white">{{ selectedClassroom?.name }}</span>.</DialogDescription></DialogHeader>
            <DialogFooter class="gap-2"><DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDeleteDialog">Cancelar</Button></DialogClose><Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir turma</Button></DialogFooter>
        </DialogContent>
    </Dialog>
</template>
