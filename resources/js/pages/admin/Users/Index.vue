<script setup lang="ts">
import {
    destroy as destroyUser,
    resendInvitation as resendUserInvitation,
    store as storeUser,
    update as updateUser,
    updateAccess as updateUserAccess,
} from '@/actions/App/Http/Controllers/Admin/UserController';
import AppTablePagination from '@/components/AppTablePagination.vue';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import type { BreadcrumbItem } from '@/types';
import { useDebounceFn } from '@vueuse/core';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Mail, Pencil, Plus, Trash2, XCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type RoleOption = {
    id: number;
    name: string | null;
    label: string;
};

type SchoolOption = {
    id: number;
    name: string;
};

type PointOption = {
    id: number;
    school_id: number;
    name: string;
};

type UserRow = {
    id: number;
    role_id: number;
    school_id: number | null;
    name: string;
    email: string;
    email_verified_at: string | null;
    has_verified_email: boolean;
    role: string | null;
    role_name: string | null;
    school: string;
    status: string;
    point_of_schools_count: number;
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
    stats: {
        total: number;
        active: number;
        leaders: number;
        teachers: number;
        students: number;
    };
    roles: RoleOption[];
    schools: SchoolOption[];
    points: PointOption[];
    users: Paginated<UserRow>;
    filters: {
        search?: string;
        status?: string;
        role?: string;
        school_id?: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/admin' },
    { title: 'Usuarios', href: '/admin/users' },
];

const userDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedUser = ref<UserRow | null>(null);
const accessUpdatingId = ref<number | null>(null);
const resendingInvitationId = ref<number | null>(null);
const filtersForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    role: props.filters.role ?? 'all',
    school_id: props.filters.school_id ? String(props.filters.school_id) : 'all',
});
const suspendAutoFilters = ref(false);

const userForm = useForm({
    name: '',
    email: '',
    role_id: '',
    school_id: '',
    point_of_school_ids: [] as string[],
    status: 'active',
});

const deleteForm = useForm({});

const accessLabel: Record<string, string> = {
    active: 'Habilitado',
    inactive: 'Desabilitado',
};

const applyFilters = () => {
    router.get(
        '/admin/users',
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status !== 'all' ? filtersForm.status : undefined,
            role: filtersForm.role !== 'all' ? filtersForm.role : undefined,
            school_id: filtersForm.school_id !== 'all' ? filtersForm.school_id : undefined,
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
    filtersForm.role = 'all';
    filtersForm.school_id = 'all';
    applyFilters();
    suspendAutoFilters.value = false;
};

const selectedRole = computed(() => props.roles.find((role) => String(role.id) === userForm.role_id) ?? null);
const isTankAdminRole = computed(() => selectedRole.value?.name === 'tank_admin');
const isStudentRole = computed(() => selectedRole.value?.name === 'student');
const shouldShowSchoolField = computed(() => !isTankAdminRole.value);
const availablePoints = computed(() => {
    if (!userForm.school_id) {
        return [];
    }

    return props.points.filter((point) => String(point.school_id) === userForm.school_id);
});

const resetUserForm = () => {
    userForm.clearErrors();
    userForm.name = '';
    userForm.email = '';
    userForm.role_id = props.roles[0]?.id ? String(props.roles[0].id) : '';
    userForm.school_id = '';
    userForm.point_of_school_ids = [];
    userForm.status = 'active';
};

const closeUserDialog = () => {
    userDialogOpen.value = false;
    selectedUser.value = null;
    resetUserForm();
};

const openCreateDialog = () => {
    selectedUser.value = null;
    resetUserForm();
    userDialogOpen.value = true;
};

const openEditDialog = (user: UserRow) => {
    selectedUser.value = user;
    userForm.clearErrors();
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.role_id = String(user.role_id);
    userForm.school_id = user.school_id ? String(user.school_id) : '';
    userForm.point_of_school_ids = user.point_of_school_ids.map(String);
    userForm.status = user.status;
    userDialogOpen.value = true;
};

const handleRoleChange = (value: unknown) => {
    userForm.role_id = ['string', 'number', 'bigint'].includes(typeof value) ? String(value) : '';

    if (!shouldShowSchoolField.value) {
        userForm.school_id = '';
        userForm.point_of_school_ids = [];
    }
};

const togglePoint = (pointId: string, checked: boolean | 'indeterminate') => {
    if (checked !== true) {
        userForm.point_of_school_ids = userForm.point_of_school_ids.filter((id) => id !== pointId);

        return;
    }

    if (isStudentRole.value) {
        userForm.point_of_school_ids = [pointId];

        return;
    }

    if (!userForm.point_of_school_ids.includes(pointId)) {
        userForm.point_of_school_ids = [...userForm.point_of_school_ids, pointId];
    }
};

const submitUser = () => {
    userForm.clearErrors();

    if (!shouldShowSchoolField.value) {
        userForm.school_id = '';
        userForm.point_of_school_ids = [];
    }

    const options = {
        preserveScroll: true,
        onSuccess: () => closeUserDialog(),
    };

    if (selectedUser.value) {
        userForm.put(updateUser(selectedUser.value.id).url, options);

        return;
    }

    userForm.post(storeUser().url, options);
};

const openDeleteDialog = (user: UserRow) => {
    selectedUser.value = user;
    deleteDialogOpen.value = true;
};

const closeDeleteDialog = () => {
    deleteDialogOpen.value = false;
    selectedUser.value = null;
};

const submitDelete = () => {
    if (!selectedUser.value) {
        return;
    }

    deleteForm.delete(destroyUser(selectedUser.value.id).url, {
        preserveScroll: true,
        onSuccess: () => closeDeleteDialog(),
    });
};

const updateAccess = (user: UserRow, value: unknown) => {
    const nextStatus = ['string', 'number', 'bigint'].includes(typeof value) ? String(value) : '';

    if (!['active', 'inactive'].includes(nextStatus) || nextStatus === user.status) {
        return;
    }

    accessUpdatingId.value = user.id;

    router.patch(
        updateUserAccess(user.id).url,
        { status: nextStatus },
        {
            preserveScroll: true,
            onFinish: () => {
                accessUpdatingId.value = null;
            },
        },
    );
};

const resendInvitation = (user: UserRow) => {
    if (user.has_verified_email) {
        return;
    }

    resendingInvitationId.value = user.id;

    router.post(
        resendUserInvitation(user.id).url,
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
    () => [filtersForm.search, filtersForm.status, filtersForm.role, filtersForm.school_id],
    () => {
        debouncedApplyFilters();
    },
);

watch(
    () => userForm.school_id,
    (schoolId) => {
        if (!schoolId) {
            userForm.point_of_school_ids = [];

            return;
        }

        const validPointIds = new Set(
            props.points
                .filter((point) => String(point.school_id) === schoolId)
                .map((point) => String(point.id)),
        );

        userForm.point_of_school_ids = userForm.point_of_school_ids.filter((pointId) => validPointIds.has(pointId));
    },
);
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <h1 class="text-4xl font-semibold tracking-tight text-white">Usuarios</h1>
                <p class="mt-2 text-lg text-white/70">Centralize acessos, papeis e distribuicao dos usuarios entre escolas e pontos de ensino.</p>

                <div class="mt-8 grid gap-4 md:grid-cols-5">
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Usuarios</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.total }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Ativos</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.active }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Liderancas</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.leaders }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Professores</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.teachers }}</p>
                    </div>
                    <div class="rounded-3xl border border-border bg-black/40 p-5">
                        <p class="text-sm text-white/60">Alunos</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ props.stats.students }}</p>
                    </div>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-white">Usuarios cadastrados</h1>
                        <p class="text-sm text-white/60">
                            Gerencie validacao de e-mail, acesso a plataforma e vinculacao por escola dentro da plataforma.
                        </p>
                    </div>
                    <Button class="rounded-2xl" @click="openCreateDialog">
                        <Plus class="size-4" />
                        Novo Usuario
                    </Button>
                </div>

                <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1.2fr)_220px_220px_220px_auto]" @submit.prevent="applyFilters">
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
                    <Select v-model="filtersForm.role">
                        <SelectTrigger class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectValue placeholder="Perfil" />
                        </SelectTrigger>
                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                            <SelectItem value="all">Todos os perfis</SelectItem>
                            <SelectItem v-for="role in props.roles" :key="role.id" :value="role.name ?? ''">
                                {{ role.label }}
                            </SelectItem>
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
                        <Button type="button" class="rounded-2xl" @click="resetFilters">Limpar</Button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead>
                            <tr class="text-left text-sm text-secondary">
                                <th class="pb-4 font-medium">Usuario</th>
                                <th class="pb-4 font-medium">Perfil</th>
                                <th class="pb-4 font-medium">Escola</th>
                                <th class="pb-4 font-medium">Unidades</th>
                                <th class="pb-4 font-medium">E-mail validado</th>
                                <th class="pb-4 font-medium">Acesso a plataforma</th>
                                <th class="pb-4 font-medium">Ultimo acesso</th>
                                <th class="pb-4 text-right font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm text-white/75">
                            <tr v-for="user in props.users.data" :key="user.id">
                                <td class="py-4">
                                    <p class="font-semibold text-white">{{ user.name }}</p>
                                    <p class="text-white/55">{{ user.email }}</p>
                                </td>
                                <td class="py-4">{{ user.role ?? '-' }}</td>
                                <td class="py-4">{{ user.school }}</td>
                                <td class="py-4">{{ user.point_of_schools_count }}</td>
                                <td class="py-4">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="
                                            user.has_verified_email
                                                ? 'bg-emerald-500/15 text-emerald-300 ring-1 ring-emerald-500/30'
                                                : 'bg-rose-500/15 text-rose-300 ring-1 ring-rose-500/30'
                                        "
                                    >
                                        <CheckCircle2 v-if="user.has_verified_email" class="size-3.5" />
                                        <XCircle v-else class="size-3.5" />
                                        {{ user.has_verified_email ? 'Validado' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="py-4">
                                    <Select :model-value="user.status" :disabled="accessUpdatingId === user.id" @update:model-value="(value) => updateAccess(user, value)">
                                        <SelectTrigger class="h-10 w-[170px] border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectValue :placeholder="accessLabel[user.status] ?? user.status" />
                                        </SelectTrigger>
                                        <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                            <SelectItem value="active">{{ accessLabel.active }}</SelectItem>
                                            <SelectItem value="inactive">{{ accessLabel.inactive }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </td>
                                <td class="py-4">{{ user.last_login_at }}</td>
                                <td class="py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            v-if="!user.has_verified_email"
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-emerald-500/70 !bg-emerald-500/15 !text-emerald-200 hover:!border-emerald-400 hover:!bg-emerald-500/25 hover:!text-emerald-100"
                                            :disabled="resendingInvitationId === user.id"
                                            @click="resendInvitation(user)"
                                        >
                                            <Mail class="size-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)] hover:!text-white"
                                            @click="openEditDialog(user)"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon-sm"
                                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                                            @click="openDeleteDialog(user)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <AppTablePagination :meta="props.users" :links="props.users.links" />
            </AppReveal>
        </section>
    </AppLayout>

    <Dialog
        :open="userDialogOpen"
        @update:open="
            (value) => {
                if (!value) {
                    closeUserDialog();
                } else {
                    userDialogOpen = value;
                }
            }
        "
    >
        <DialogContent class="border-border bg-card text-white sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ selectedUser ? 'Editar Usuario' : 'Novo Usuario' }}</DialogTitle>
                <DialogDescription class="text-white/60">
                    Preencha os dados de acesso do usuario para manter o controle de perfis da plataforma.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submitUser">
                <div class="grid gap-2">
                    <Label for="user-name">Nome</Label>
                    <Input id="user-name" v-model="userForm.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Nikolas Guedes" />
                </div>

                <div class="grid gap-2">
                    <Label for="user-email">E-mail</Label>
                    <Input
                        id="user-email"
                        v-model="userForm.email"
                        type="email"
                        class="border-white/10 bg-[var(--surface-elevated)] text-white"
                        placeholder="usuario@tankcode.com"
                    />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="user-role">Perfil</Label>
                        <Select :model-value="userForm.role_id" @update:model-value="handleRoleChange">
                            <SelectTrigger id="user-role" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectValue placeholder="Selecione o perfil" />
                            </SelectTrigger>
                            <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectItem v-for="role in props.roles" :key="role.id" :value="String(role.id)">
                                    {{ role.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div v-if="shouldShowSchoolField" class="grid gap-2">
                        <Label for="user-school">Escola</Label>
                        <Select v-model="userForm.school_id">
                            <SelectTrigger id="user-school" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectValue placeholder="Selecione a escola" />
                            </SelectTrigger>
                            <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectItem v-for="school in props.schools" :key="school.id" :value="String(school.id)">
                                    {{ school.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div v-if="shouldShowSchoolField" class="grid gap-2">
                    <div class="flex items-center justify-between gap-3">
                        <Label>Pontos de Ensino vinculados</Label>
                        <span class="text-xs text-white/50">
                            {{ userForm.point_of_school_ids.length }} selecionado(s)
                        </span>
                    </div>
                    <p class="text-xs text-white/55">
                        {{ isStudentRole ? 'Usuarios do tipo Student podem ser vinculados a apenas um ponto de ensino.' : 'Selecione ao menos um ponto de ensino para este usuario.' }}
                    </p>

                    <div
                        class="max-h-64 space-y-3 overflow-y-auto rounded-2xl border border-white/10 bg-[var(--surface-elevated)] p-3"
                    >
                        <template v-if="availablePoints.length">
                            <div
                                v-for="point in availablePoints"
                                :key="point.id"
                                class="flex items-center gap-3 rounded-2xl border border-white/10 bg-black/20 px-4 py-3"
                            >
                                <Checkbox
                                    :model-value="userForm.point_of_school_ids.includes(String(point.id))"
                                    class="border-white/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary"
                                    @update:model-value="(checked) => togglePoint(String(point.id), checked)"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium text-white">{{ point.name }}</p>
                                    <p class="text-xs text-white/50">Vinculado a escola selecionada</p>
                                </div>
                            </div>
                        </template>

                        <div v-else class="rounded-2xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-white/50">
                            Selecione uma escola para liberar os Pontos de Ensino disponiveis.
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="user-status">Status</Label>
                        <Select v-model="userForm.status">
                            <SelectTrigger id="user-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectValue placeholder="Selecione o status" />
                            </SelectTrigger>
                            <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                <SelectItem value="active">Ativo</SelectItem>
                                <SelectItem value="inactive">Inativo</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button
                            type="button"
                            variant="outline"
                            class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)] hover:!text-white"
                            @click="closeUserDialog"
                        >
                            Cancelar
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="userForm.processing">
                        {{ selectedUser ? 'Salvar alteracoes' : 'Criar usuario' }}
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
                    Esta operacao nao pode ser desfeita e ira remover os vinculos de unidades associados a
                    <span class="font-semibold text-white">{{ selectedUser?.name }}</span>.
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
                    Excluir usuario
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
