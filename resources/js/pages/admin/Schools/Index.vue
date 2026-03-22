<script setup lang="ts">
import {
    destroy as destroySchool,
    store as storeSchool,
    update as updateSchool,
} from '@/actions/App/Http/Controllers/Admin/SchoolController';
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
import { formatCnpj, isValidCnpj } from '@/lib/utils';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';

type SchoolRow = {
    id: number;
    name: string;
    cnpj: string | null;
    logo_url: string | null;
    status: string;
    point_of_schools_count: number;
};

const statusLabel: Record<string, string> = {
    active: 'Ativo',
    inactive: 'Inativo',
};

const props = defineProps<{
    stats: {
        total: number;
        active: number;
        pointOfSchools: number;
    };
    schools: SchoolRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/admin' },
    { title: 'Escolas', href: '/admin/schools' },
];

const schoolDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedSchool = ref<SchoolRow | null>(null);

const schoolForm = useForm({
    name: '',
    cnpj: '',
    logo: null as File | null,
    status: 'active',
});

const deleteForm = useForm({});

const resetSchoolForm = () => {
    schoolForm.clearErrors();
    schoolForm.name = '';
    schoolForm.cnpj = '';
    schoolForm.logo = null;
    schoolForm.status = 'active';
};

const logoPreviewUrl = computed(() => {
    if (schoolForm.logo) {
        return URL.createObjectURL(schoolForm.logo);
    }

    return selectedSchool.value?.logo_url ?? null;
});

const handleCnpjInput = (value: string | number) => {
    schoolForm.cnpj = formatCnpj(String(value ?? ''));
};

const closeSchoolDialog = () => {
    schoolDialogOpen.value = false;
    selectedSchool.value = null;
    resetSchoolForm();
};

const openCreateDialog = () => {
    selectedSchool.value = null;
    resetSchoolForm();
    schoolDialogOpen.value = true;
};

const openEditDialog = (school: SchoolRow) => {
    selectedSchool.value = school;
    schoolForm.clearErrors();
    schoolForm.name = school.name;
    schoolForm.cnpj = formatCnpj(school.cnpj ?? '');
    schoolForm.logo = null;
    schoolForm.status = school.status;
    schoolDialogOpen.value = true;
};

const submitSchool = () => {
    schoolForm.clearErrors();

    if (schoolForm.cnpj && !isValidCnpj(schoolForm.cnpj)) {
        const message = 'Informe um CNPJ valido.';

        schoolForm.setError('cnpj', message);
        toast.error(message);

        return;
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => closeSchoolDialog(),
    };

    if (selectedSchool.value) {
        schoolForm
            .transform((data) => ({
                ...data,
                _method: 'put',
            }))
            .post(updateSchool(selectedSchool.value.id).url, {
                ...options,
                forceFormData: true,
            });

        return;
    }

    schoolForm.transform((data) => data).post(storeSchool().url, {
        ...options,
        forceFormData: true,
    });
};

const openDeleteDialog = (school: SchoolRow) => {
    selectedSchool.value = school;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedSchool.value = null;
};

const submitDelete = () => {
    if (!selectedSchool.value) {
        return;
    }

    deleteForm.delete(destroySchool(selectedSchool.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    schoolForm.logo = target.files?.[0] ?? null;
};

const currentLogoLabel = computed(() => {
    if (schoolForm.logo) {
        return schoolForm.logo.name;
    }

    if (selectedSchool.value?.logo_url) {
        return 'Logo atual carregada';
    }

    return 'Nenhum arquivo escolhido';
});
</script>

<template>
    <Head title="Escolas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Escolas</h1>
                <p class="mt-2 text-lg text-white/70">Cadastre as escolas principais da plataforma e acompanhe a estrutura de unidades vinculadas a cada uma.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Total</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Ativas</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Pontos de Ensino</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.pointOfSchools }}</p>
                    </div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-white">Escolas cadastradas</h1>
                        <p class="text-sm text-white/60">Cada escola funciona como entidade principal da operacao.</p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog">
                        <Plus class="size-4" />
                        Nova Escola
                    </Button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Logo</th>
                                <th class="pb-4 font-medium">Nome</th>
                                <th class="pb-4 font-medium">CNPJ</th>
                                <th class="pb-4 font-medium">Status</th>
                                <th class="pb-4 font-medium">Unidades</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="school in props.schools" :key="school.id">
                                <td class="py-4">
                                    <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-white p-2 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
                                        <img v-if="school.logo_url" :src="school.logo_url" :alt="`Logo ${school.name}`" class="h-full w-full object-contain" />
                                        <span v-else class="text-lg font-semibold text-primary">{{ school.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                </td>
                                <td class="py-4 font-semibold text-white">{{ school.name }}</td>
                                <td class="py-4">{{ school.cnpj ? formatCnpj(school.cnpj) : '-' }}</td>
                                <td class="py-4">
                                    <span class="rounded-full bg-secondary px-3 py-1 text-xs font-semibold text-white">
                                        {{ statusLabel[school.status] ?? school.status }}
                                    </span>
                                </td>
                                <td class="py-4">{{ school.point_of_schools_count }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)] hover:!text-white"
                                            @click="openEditDialog(school)"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                                            @click="openDeleteDialog(school)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog
        :open="schoolDialogOpen"
        @update:open="
            (value) => {
                if (!value) {
                    closeSchoolDialog();
                } else {
                    schoolDialogOpen = value;
                }
            }
        "
    >
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ selectedSchool ? 'Editar Escola' : 'Nova Escola' }}</DialogTitle>
                <DialogDescription class="text-white/60">
                    Preencha os dados principais da escola para manter a estrutura da plataforma organizada.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submitSchool">
                <div class="grid gap-2">
                    <Label for="school-name">Nome</Label>
                    <Input id="school-name" v-model="schoolForm.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Escola TankCode" />
                </div>

                <div class="grid gap-2">
                    <Label for="school-cnpj">CNPJ</Label>
                    <Input
                        id="school-cnpj"
                        :model-value="schoolForm.cnpj"
                        placeholder="00.000.000/0000-00"
                        class="border-white/10 bg-[var(--surface-elevated)] text-white"
                        inputmode="numeric"
                        maxlength="18"
                        @update:model-value="handleCnpjInput"
                    />
                </div>

                <div class="grid gap-3">
                    <Label for="school-logo">Logo</Label>
                    <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-[var(--surface-elevated)] p-4">
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-white p-3 shadow-[0_12px_24px_rgba(0,0,0,0.18)]">
                            <img v-if="logoPreviewUrl" :src="logoPreviewUrl" alt="Preview da logo" class="h-full w-full object-contain" />
                            <span v-else class="text-center text-xs font-semibold uppercase tracking-[0.24em] text-primary">Sem logo</span>
                        </div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <Input
                                id="school-logo"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,.svg"
                                class="border-white/10 bg-transparent text-white file:mr-4 file:cursor-pointer file:rounded-xl file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--primary-hover)]"
                                @change="handleLogoChange"
                            />
                            <p class="truncate text-sm font-medium text-white/80">{{ currentLogoLabel }}</p>
                            <p class="text-xs text-white/50">Use JPG, PNG, WEBP ou SVG com ate 2 MB.</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="school-status">Status</Label>
                    <Select v-model="schoolForm.status">
                        <SelectTrigger id="school-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Selecione o status" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="active">Ativa</SelectItem>
                            <SelectItem value="inactive">Inativa</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                            @click="closeSchoolDialog"
                        >
                            Cancelar
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="schoolForm.processing">
                        {{ selectedSchool ? 'Salvar alteracoes' : 'Criar escola' }}
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
                    Esta operacao nao pode ser desfeita e ira remover todos os Pontos de ensino e usuarios associados a
                    <span class="font-semibold text-white">{{ selectedSchool?.name }}</span>.
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
                    Excluir escola
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
