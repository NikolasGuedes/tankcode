<script setup lang="ts">
import {
    destroy as destroyDirector,
    resendInvitation as resendDirectorInvitation,
    updateAccess as updateDirectorAccess,
} from '@/actions/App/Http/Controllers/Owner/DirectorController';
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
type DirectorRow = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    has_verified_email: boolean;
    status: string;
    point_of_school_ids: number[];
    point_of_schools: PointOption[];
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
    directors: Paginated<DirectorRow>;
    points: PointOption[];
    filters: {
        search?: string;
        status?: string;
        point_of_school_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/owner' },
    { title: 'Diretores', href: '/owner/directors' },
];

const deleteDialogOpen = ref(false);
const selectedDirector = ref<DirectorRow | null>(null);
const accessUpdatingId = ref<number | null>(null);
const resendingInvitationId = ref<number | null>(null);
const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    point_of_school_id: props.filters.point_of_school_id ? String(props.filters.point_of_school_id) : 'all',
});
const suspendAutoFilters = ref(false);
const deleteForm = useForm({});
const accessLabel: Record<string, string> = { active: 'Habilitado', inactive: 'Desabilitado' };

const applyFilters = () => {
    router.get(
        '/owner/directors',
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

const openDeleteDialog = (director: DirectorRow) => {
    selectedDirector.value = director;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedDirector.value = null;
};

const submitDelete = () => {
    if (!selectedDirector.value) return;

    deleteForm.delete(destroyDirector(selectedDirector.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};

const updateAccess = (director: DirectorRow, value: unknown) => {
    const nextStatus = ['string', 'number', 'bigint'].includes(typeof value) ? String(value) : '';

    if (!['active', 'inactive'].includes(nextStatus) || nextStatus === director.status) {
        return;
    }

    accessUpdatingId.value = director.id;

    router.patch(
        updateDirectorAccess(director.id).url,
        { status: nextStatus },
        {
            preserveScroll: true,
            onFinish: () => {
                accessUpdatingId.value = null;
            },
        },
    );
};

const resendInvitation = (director: DirectorRow) => {
    if (director.has_verified_email) {
        return;
    }

    resendingInvitationId.value = director.id;

    router.post(
        resendDirectorInvitation(director.id).url,
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
</script>

<template>
    <Head title="Diretores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Diretores</h1>
                <p class="mt-2 text-lg text-white/70">Gerencie diretores e distribua a atuacao deles entre os pontos de ensino da sua escola.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Total</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Ativos</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p></div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5"><p class="text-sm text-white/60">Pontos de Ensino</p><p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.points }}</p></div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Diretores cadastrados</h2>
                        <p class="text-sm text-white/60">Cadastre e edite diretores em pagina dedicada, mantendo o controle de acesso e vinculacao.</p>
                    </div>
                    <Button as-child class="rounded-2xl">
                        <a href="/owner/directors/create"><Plus class="size-4" />Novo Diretor</a>
                    </Button>
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
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Diretor</th>
                                <th class="pb-4 font-medium">Pontos de Ensino</th>
                                <th class="pb-4 font-medium">E-mail validado</th>
                                <th class="pb-4 font-medium">Acesso a plataforma</th>
                                <th class="pb-4 font-medium">Ultimo acesso</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="director in props.directors.data" :key="director.id">
                                <td class="py-4"><p class="font-semibold text-white">{{ director.name }}</p><p class="text-white/55">{{ director.email }}</p></td>
                                <td class="py-4">{{ director.point_of_schools.map((point) => point.name).join(', ') || '-' }}</td>
                                <td class="py-4">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="director.has_verified_email ? 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-rose-500/15 text-rose-300 ring-1 ring-rose-500/30'"
                                    >
                                        <CheckCircle2 v-if="director.has_verified_email" class="size-3.5" />
                                        <XCircle v-else class="size-3.5" />
                                        {{ director.has_verified_email ? 'Validado' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <Select :model-value="director.status" :disabled="accessUpdatingId === director.id" @update:model-value="(value) => updateAccess(director, value)">
                                        <SelectTrigger class="h-10 w-[170px] border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectValue :placeholder="accessLabel[director.status] ?? director.status" />
                                        </SelectTrigger>
                                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectItem value="active">{{ accessLabel.active }}</SelectItem>
                                            <SelectItem value="inactive">{{ accessLabel.inactive }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </td>
                                <td class="py-4">{{ director.last_login_at }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            v-if="!director.has_verified_email"
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-emerald-500/70 !bg-emerald-500/15 !text-emerald-200 hover:!border-emerald-400 hover:!bg-emerald-500/25 hover:!text-emerald-100"
                                            :disabled="resendingInvitationId === director.id"
                                            @click="resendInvitation(director)"
                                        ><Mail class="size-4" /></Button>
                                        <Button as-child variant="outline" size="icon-sm" class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]"><a :href="`/owner/directors/${director.id}/edit`"><Pencil class="size-4" /></a></Button>
                                        <Button variant="outline" size="icon-sm" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="openDeleteDialog(director)"><Trash2 class="size-4" /></Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.directors" :links="props.directors.links" />
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog :open="deleteDialogOpen" @update:open="(value) => !value ? closeDeleteDialog() : (deleteDialogOpen = value)">
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Confirmar exclusao</DialogTitle>
                <DialogDescription class="text-white/60">Esta operacao nao pode ser desfeita e removera o diretor <span class="font-semibold text-white">{{ selectedDirector?.name }}</span>.</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child><Button type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]" @click="closeDeleteDialog">Cancelar</Button></DialogClose>
                <Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">Excluir diretor</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
