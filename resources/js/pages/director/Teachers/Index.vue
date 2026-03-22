<script setup lang="ts">
import {
    destroy as destroyTeacher,
    store as storeTeacher,
    update as updateTeacher,
} from '@/actions/App/Http/Controllers/Director/TeacherController';
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
import { ref } from 'vue';

type PointOption = { id: number; name: string };
type TeacherRow = {
    id: number;
    name: string;
    email: string;
    status: string;
    point_of_school_ids: number[];
    point_of_schools: PointOption[];
    classrooms_count: number;
    last_login_at: string;
};

const props = defineProps<{
    stats: { total: number; active: number; points: number };
    points: PointOption[];
    teachers: TeacherRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Professores', href: '/director/teachers' },
];

const statusLabel: Record<string, string> = { active: 'Ativo', inactive: 'Inativo' };
const dialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedTeacher = ref<TeacherRow | null>(null);
const form = useForm({ name: '', email: '', point_of_school_ids: [] as string[], status: 'active' });
const deleteForm = useForm({});

const resetForm = () => {
    form.clearErrors();
    form.name = '';
    form.email = '';
    form.point_of_school_ids = [];
    form.status = 'active';
};

const openCreateDialog = () => {
    selectedTeacher.value = null;
    resetForm();
    dialogOpen.value = true;
};

const openEditDialog = (teacher: TeacherRow) => {
    selectedTeacher.value = teacher;
    form.clearErrors();
    form.name = teacher.name;
    form.email = teacher.email;
    form.point_of_school_ids = teacher.point_of_school_ids.map(String);
    form.status = teacher.status;
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    selectedTeacher.value = null;
    resetForm();
};

const togglePoint = (pointId: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.point_of_school_ids.includes(pointId)) {
            form.point_of_school_ids = [...form.point_of_school_ids, pointId];
        }
        return;
    }

    form.point_of_school_ids = form.point_of_school_ids.filter((id) => id !== pointId);
};

const submit = () => {
    const options = { preserveScroll: true, onSuccess: () => closeDialog() };

    if (selectedTeacher.value) {
        form.put(updateTeacher(selectedTeacher.value.id).url, options);
        return;
    }

    form.post(storeTeacher().url, options);
};

const openDeleteDialog = (teacher: TeacherRow) => {
    selectedTeacher.value = teacher;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedTeacher.value = null;
};

const submitDelete = () => {
    if (!selectedTeacher.value) return;

    deleteForm.delete(destroyTeacher(selectedTeacher.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};
</script>

<template>
    <Head title="Professores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Professores</h1>
                <p class="mt-2 text-lg text-white/70">Cadastre professores e distribua o acesso deles pelos pontos de ensino da sua diretoria.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Total</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Ativos</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Pontos de Ensino</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.points }}</p></div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Professores cadastrados</h2>
                        <p class="text-sm text-white/60">Cada professor pode atuar em um ou mais pontos de ensino.</p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog"><Plus class="size-4" />Novo Professor</Button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Professor</th>
                                <th class="pb-4 font-medium">Pontos de Ensino</th>
                                <th class="pb-4 font-medium">Turmas</th>
                                <th class="pb-4 font-medium">Status</th>
                                <th class="pb-4 font-medium">Ultimo acesso</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="teacher in props.teachers" :key="teacher.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ teacher.name }}</p><p class="text-white/55">{{ teacher.email }}</p></td>
                                <td class="py-4">{{ teacher.point_of_schools.map((point) => point.name).join(', ') || '-' }}</td>
                                <td class="py-4">{{ teacher.classrooms_count }}</td>
                                <td class="py-4"><span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-white">{{ statusLabel[teacher.status] ?? teacher.status }}</span></td>
                                <td class="py-4">{{ teacher.last_login_at }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]" @click="openEditDialog(teacher)"><Pencil class="size-4" /></Button>
                                        <Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(teacher)"><Trash2 class="size-4" /></Button>
                                    </div>
                                </td>
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
                <DialogTitle>{{ selectedTeacher ? 'Editar Professor' : 'Novo Professor' }}</DialogTitle>
                <DialogDescription class="text-white/60">O professor recebera um convite por e-mail para definir a senha no primeiro acesso.</DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="teacher-name">Nome</Label>
                    <Input id="teacher-name" v-model="form.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Ana Souza" />
                </div>

                <div class="grid gap-2">
                    <Label for="teacher-email">E-mail</Label>
                    <Input id="teacher-email" v-model="form.email" type="email" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="professor@escola.com" />
                </div>

                <div class="grid gap-3">
                    <div class="flex items-center justify-between gap-3">
                        <Label>Pontos de Ensino vinculados</Label>
                        <span class="text-xs text-white/50">{{ form.point_of_school_ids.length }} selecionado(s)</span>
                    </div>
                    <div class="max-h-64 space-y-3 overflow-y-auto rounded-2xl border border-white/10 bg-[var(--surface-elevated)] p-3">
                        <div v-for="point in props.points" :key="point.id" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3">
                            <Checkbox :model-value="form.point_of_school_ids.includes(String(point.id))" class="border-white/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary" @update:model-value="(checked) => togglePoint(String(point.id), checked)" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-white">{{ point.name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="teacher-status">Status</Label>
                    <Select v-model="form.status">
                        <SelectTrigger id="teacher-status" class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectValue placeholder="Selecione o status" /></SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white"><SelectItem value="active">Ativo</SelectItem><SelectItem value="inactive">Inativo</SelectItem></SelectContent>
                    </Select>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDialog">Cancelar</Button></DialogClose>
                    <Button type="submit" :disabled="form.processing">{{ selectedTeacher ? 'Salvar alteracoes' : 'Criar professor' }}</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => !value ? closeDeleteDialog() : (deleteDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Confirmar exclusao</DialogTitle>
                <DialogDescription class="text-white/60">Esta operacao nao pode ser desfeita e removera o professor <span class="font-semibold text-white">{{ selectedTeacher?.name }}</span>.</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDeleteDialog">Cancelar</Button></DialogClose>
                <Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir professor</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
