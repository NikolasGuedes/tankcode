<script setup lang="ts">
import {
    destroy as destroyPointOfSchool,
    store as storePointOfSchool,
    update as updatePointOfSchool,
} from '@/actions/App/Http/Controllers/Admin/PointOfSchoolController';
import AppTablePagination from '@/components/AppTablePagination.vue';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatCep, formatCnpj, isValidCep, isValidCnpj } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { ref } from 'vue';

type SchoolOption = {
    id: number;
    name: string;
};

type PointRow = {
    id: number;
    name: string;
    school: string | null;
    school_id: number;
    status: string;
    cnpj: string | null;
    zip_code: string | null;
    users_count: number;
    address_line: string | null;
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

const statusLabel: Record<string, string> = {
    active: 'Ativo',
    inactive: 'Inativo',
};

const props = defineProps<{
    stats: {
        total: number;
        active: number;
        schools: number;
        users: number;
    };
    schools: SchoolOption[];
    points: Paginated<PointRow>;
    filters: {
        search?: string;
        status?: string;
        school_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/admin' },
    { title: 'Pontos de Ensino', href: '/admin/point-of-schools' },
];

const pointDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedPoint = ref<PointRow | null>(null);
const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    school_id: props.filters.school_id ? String(props.filters.school_id) : 'all',
});

const pointForm = useForm({
    school_id: '',
    name: '',
    cnpj: '',
    zip_code: '',
    address_line: '',
    status: 'active',
});

const deleteForm = useForm({});

const applyFilters = () => {
    router.get(
        '/admin/point-of-schools',
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status !== 'all' ? filtersForm.status : undefined,
            school_id: filtersForm.school_id !== 'all' ? filtersForm.school_id : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const resetFilters = () => {
    filtersForm.search = '';
    filtersForm.status = 'all';
    filtersForm.school_id = 'all';
    applyFilters();
};

const resetPointForm = () => {
    pointForm.clearErrors();
    pointForm.school_id = props.schools[0]?.id ? String(props.schools[0].id) : '';
    pointForm.name = '';
    pointForm.cnpj = '';
    pointForm.zip_code = '';
    pointForm.address_line = '';
    pointForm.status = 'active';
};

const openCreateDialog = () => {
    selectedPoint.value = null;
    resetPointForm();
    pointDialogOpen.value = true;
};

const openEditDialog = (point: PointRow) => {
    selectedPoint.value = point;
    pointForm.clearErrors();
    pointForm.school_id = String(point.school_id);
    pointForm.name = point.name;
    pointForm.cnpj = point.cnpj ?? '';
    pointForm.zip_code = point.zip_code ?? '';
    pointForm.address_line = point.address_line ?? '';
    pointForm.status = point.status;
    pointDialogOpen.value = true;
};

const closePointDialog = () => {
    pointDialogOpen.value = false;
    selectedPoint.value = null;
    resetPointForm();
};

const openDeleteDialog = (point: PointRow) => {
    selectedPoint.value = point;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedPoint.value = null;
};

const handleTaxIdInput = (value: string | number) => {
    pointForm.cnpj = formatCnpj(String(value ?? ''));
};

const handleZipCodeInput = (value: string | number) => {
    pointForm.zip_code = formatCep(String(value ?? ''));
};

const submitPoint = () => {
    pointForm.clearErrors();

    if (pointForm.cnpj && !isValidCnpj(pointForm.cnpj)) {
        const message = 'Informe um CNPJ valido.';

        pointForm.setError('cnpj', message);
        toast.error(message);

        return;
    }

    if (pointForm.zip_code && !isValidCep(pointForm.zip_code)) {
        const message = 'Informe um CEP valido.';

        pointForm.setError('zip_code', message);
        toast.error(message);

        return;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => closePointDialog(),
    };

    if (selectedPoint.value) {
        pointForm.put(updatePointOfSchool(selectedPoint.value.id).url, options);

        return;
    }

    pointForm.post(storePointOfSchool().url, options);
};

const submitDelete = () => {
    if (!selectedPoint.value) {
        return;
    }

    deleteForm.delete(destroyPointOfSchool(selectedPoint.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};
</script>

<template>
    <Head title="Pontos de Ensino" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Pontos de Ensino</h1>
                <p class="mt-2 text-lg text-white/70">Cadastre as unidades vinculadas a cada escola e acompanhe seus dados operacionais.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-4">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Total</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Ativos</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Escolas atendidas</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.schools }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Usuarios vinculados</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.users }}</p>
                    </div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Pontos de ensino cadastrados</h2>
                        <p class="text-sm text-white/60">Cada ponto de ensino representa uma unidade operacional vinculada a uma escola.</p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog">
                        <Plus class="size-4" />
                        Novo Ponto de Ensino
                    </Button>
                </div>

                <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 md:grid-cols-[minmax(0,1fr)_220px_240px_auto]" @submit.prevent="applyFilters">
                    <Input v-model="filtersForm.search" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Buscar por nome, CNPJ, CEP ou endereco" />
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
                    <Select v-model="filtersForm.school_id">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Escola" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todas as escolas</SelectItem>
                            <SelectItem v-for="school in props.schools" :key="school.id" :value="String(school.id)">
                                {{ school.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <div class="flex gap-3">
                        <Button type="submit" class="rounded-2xl">Filtrar</Button>
                        <Button type="button" variant="outline" class="rounded-2xl border-white/10 bg-white/5 text-white hover:bg-white/10" @click="resetFilters">Limpar</Button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Nome</th>
                                <th class="pb-4 font-medium">Escola</th>
                                <th class="pb-4 font-medium">CNPJ</th>
                                <th class="pb-4 font-medium">CEP</th>
                                <th class="pb-4 font-medium">Endereco</th>
                                <th class="pb-4 font-medium">Usuarios</th>
                                <th class="pb-4 font-medium">Status</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="point in props.points.data" :key="point.id">
                                <td class="py-4 font-semibold text-white">{{ point.name }}</td>
                                <td class="py-4">{{ point.school ?? '-' }}</td>
                                <td class="py-4">{{ point.cnpj ? formatCnpj(point.cnpj) : '-' }}</td>
                                <td class="py-4">{{ point.zip_code ? formatCep(point.zip_code) : '-' }}</td>
                                <td class="py-4">{{ point.address_line ?? '-' }}</td>
                                <td class="py-4">{{ point.users_count }}</td>
                                <td class="py-4">
                                    <span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-white">
                                        {{ statusLabel[point.status] ?? point.status }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)] hover:!text-white"
                                            @click="openEditDialog(point)"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                                            @click="openDeleteDialog(point)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.points" :links="props.points.links" />
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog
        :open="pointDialogOpen"
        @update:open="
            (value) => {
                if (!value) {
                    closePointDialog();
                } else {
                    pointDialogOpen = value;
                }
            }
        "
    >
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ selectedPoint ? 'Editar Ponto de Ensino' : 'Novo Ponto de Ensino' }}</DialogTitle>
                <DialogDescription class="text-white/60">
                    Preencha os dados principais da unidade para manter a estrutura da escola organizada.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submitPoint">
                <div class="grid gap-2">
                    <Label for="school-id">Escola</Label>
                    <Select v-model="pointForm.school_id">
                        <SelectTrigger id="school-id" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Selecione a escola" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem v-for="school in props.schools" :key="school.id" :value="String(school.id)">
                                {{ school.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-2">
                    <Label for="point-name">Nome</Label>
                    <Input id="point-name" v-model="pointForm.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Unidade Centro" />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="point-cnpj">CNPJ</Label>
                        <Input
                            id="point-cnpj"
                            :model-value="pointForm.cnpj"
                            class="border-white/10 bg-[var(--surface-elevated)] text-white"
                            placeholder="00.000.000/0000-00"
                            inputmode="numeric"
                            maxlength="18"
                            @update:model-value="handleTaxIdInput"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="point-zip-code">CEP</Label>
                        <Input
                            id="point-zip-code"
                            :model-value="pointForm.zip_code"
                            class="border-white/10 bg-[var(--surface-elevated)] text-white"
                            placeholder="00000-000"
                            inputmode="numeric"
                            maxlength="9"
                            @update:model-value="handleZipCodeInput"
                        />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="point-address-line">Endereco</Label>
                    <Input
                        id="point-address-line"
                        v-model="pointForm.address_line"
                        class="border-white/10 bg-[var(--surface-elevated)] text-white"
                        placeholder="Rua, numero, bairro"
                        maxlength="255"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="point-status">Status</Label>
                    <Select v-model="pointForm.status">
                        <SelectTrigger id="point-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Selecione o status" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="active">Ativo</SelectItem>
                            <SelectItem value="inactive">Inativo</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                            @click="closePointDialog"
                        >
                            Cancelar
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="pointForm.processing">
                        {{ selectedPoint ? 'Salvar alteracoes' : 'Criar ponto de ensino' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog
        :open="deleteDialogOpen"
        @update:open="
            (value) => {
                if (!value) {
                    closeDeleteDialog();
                } else {
                    deleteDialogOpen = value;
                }
            }
        "
    >
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Confirmar exclusao</DialogTitle>
                <DialogDescription class="text-white/60">
                    Esta operacao nao pode ser desfeita e ira remover os vinculos de usuarios associados a
                    <span class="font-semibold text-white">{{ selectedPoint?.name }}</span>.
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button
                        type="button"
                        variant="outline"
                        class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                        @click="closeDeleteDialog"
                    >
                        Cancelar
                    </Button>
                </DialogClose>
                <Button type="button" variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">
                    Excluir ponto de ensino
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
