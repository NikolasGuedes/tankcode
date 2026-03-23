<script setup lang="ts">
import {
    destroy as destroyTeacher,
    resendInvitation as resendTeacherInvitation,
    updateAccess as updateTeacherAccess,
} from '@/actions/App/Http/Controllers/Director/TeacherController';
import AppTablePagination from '@/components/AppTablePagination.vue';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useDebounceFn } from '@vueuse/core';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Mail, Pencil, Plus, Trash2, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

type PointOption = { id: number; name: string };

type TeacherRow = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    has_verified_email: boolean;
    status: string;
    point_of_school_ids: number[];
    point_of_schools: PointOption[];
    classrooms_count: number;
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
    teachers: Paginated<TeacherRow>;
    filters: {
        search?: string;
        status?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Professores', href: '/director/teachers' },
];

const deleteDialogOpen = ref(false);
const selectedTeacher = ref<TeacherRow | null>(null);
const accessUpdatingId = ref<number | null>(null);
const resendingInvitationId = ref<number | null>(null);
const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
});
const suspendAutoFilters = ref(false);
const deleteForm = useForm({});
const accessLabel: Record<string, string> = { active: 'Habilitado', inactive: 'Desabilitado' };

const applyFilters = () => {
    router.get(
        '/director/teachers',
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status !== 'all' ? filtersForm.status : undefined,
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
    applyFilters();
    suspendAutoFilters.value = false;
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

const updateAccess = (teacher: TeacherRow, value: unknown) => {
    const nextStatus = ['string', 'number', 'bigint'].includes(typeof value) ? String(value) : '';

    if (!['active', 'inactive'].includes(nextStatus) || nextStatus === teacher.status) {
        return;
    }

    accessUpdatingId.value = teacher.id;

    router.patch(
        updateTeacherAccess(teacher.id).url,
        { status: nextStatus },
        {
            preserveScroll: true,
            onFinish: () => {
                accessUpdatingId.value = null;
            },
        },
    );
};

const resendInvitation = (teacher: TeacherRow) => {
    if (teacher.has_verified_email) {
        return;
    }

    resendingInvitationId.value = teacher.id;

    router.post(
        resendTeacherInvitation(teacher.id).url,
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
    () => [filtersForm.search, filtersForm.status],
    () => {
        debouncedApplyFilters();
    },
);
</script>

<template>
    <Head title="Professores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Professores</h1>
                <p class="mt-2 text-lg text-white/70">Gerencie professores com um fluxo dedicado, mais claro para revisar pontos de ensino e dados de acesso.</p>

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
                        <p class="text-sm text-white/60">Crie e edite professores em pagina dedicada, mantendo o controle de acesso e vinculacao.</p>
                    </div>
                    <Button as-child class="rounded-2xl">
                        <a href="/director/teachers/create"><Plus class="size-4" />Novo Professor</a>
                    </Button>
                </div>

                <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 md:grid-cols-[minmax(0,1fr)_220px_auto]" @submit.prevent="applyFilters">
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
                    <div class="flex gap-3">
                        <Button type="button" class="rounded-2xl" @click="resetFilters">Limpar</Button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Professor</th>
                                <th class="pb-4 font-medium">Pontos de Ensino</th>
                                <th class="pb-4 font-medium">Turmas</th>
                                <th class="pb-4 font-medium">E-mail validado</th>
                                <th class="pb-4 font-medium">Acesso a plataforma</th>
                                <th class="pb-4 font-medium">Ultimo acesso</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="teacher in props.teachers.data" :key="teacher.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ teacher.name }}</p><p class="text-white/55">{{ teacher.email }}</p></td>
                                <td class="py-4">{{ teacher.point_of_schools.map((point) => point.name).join(', ') || '-' }}</td>
                                <td class="py-4">{{ teacher.classrooms_count }}</td>
                                <td class="py-4">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="teacher.has_verified_email ? 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-rose-500/15 text-rose-300 ring-1 ring-rose-500/30'"
                                    >
                                        <CheckCircle2 v-if="teacher.has_verified_email" class="size-3.5" />
                                        <XCircle v-else class="size-3.5" />
                                        {{ teacher.has_verified_email ? 'Validado' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <Select :model-value="teacher.status" :disabled="accessUpdatingId === teacher.id" @update:model-value="(value) => updateAccess(teacher, value)">
                                        <SelectTrigger class="h-10 w-[170px] border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectValue :placeholder="accessLabel[teacher.status] ?? teacher.status" />
                                        </SelectTrigger>
                                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectItem value="active">{{ accessLabel.active }}</SelectItem>
                                            <SelectItem value="inactive">{{ accessLabel.inactive }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </td>
                                <td class="py-4">{{ teacher.last_login_at }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            v-if="!teacher.has_verified_email"
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-emerald-500/70 !bg-emerald-500/15 !text-emerald-200 hover:!border-emerald-400 hover:!bg-emerald-500/25 hover:!text-emerald-100"
                                            :disabled="resendingInvitationId === teacher.id"
                                            @click="resendInvitation(teacher)"
                                        ><Mail class="size-4" /></Button>
                                        <Button as-child variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]"><a :href="`/director/teachers/${teacher.id}/edit`"><Pencil class="size-4" /></a></Button>
                                        <Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(teacher)"><Trash2 class="size-4" /></Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.teachers" :links="props.teachers.links" />
            </AppReveal>
        </section>
    </AppLayout>

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
