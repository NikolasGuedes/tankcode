<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, Plus, Edit, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Alunos',
        href: '/students',
    },
];

const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    students: {
        type: Array as () => Array<{
            id: number;
            name: string;
            email: string;
            cod: string;
        }>,
        required: true,
    }
});

const search = ref(props.search);
const isDialogOpen = ref(false);
const isDialogOpenEdit = ref(false);
const selectStudentId = ref<number | null>(null);
const isDeleting = ref(false);
const isUpdating = ref(false);

watch(() => props.search, (newSearch) => {
    search.value = newSearch;
}, { immediate: true });

const openDialog = () => {
    isDialogOpen.value = true;
    studentForm.reset();
};

const openDialogEdit = (id: number) => {
    selectStudentId.value = id;
    const selectedStudent = props.students.find(s => s.id === id);
    if (selectedStudent) {
        editForm.name = selectedStudent.name;
        editForm.email = selectedStudent.email;
    }
    isDialogOpenEdit.value = true;
};

const studentForm = useForm({
    name: '',
    email: '',
});

const editForm = useForm({
    name: '',
    email: '',
});

const saveStudent = () => {
    studentForm.post(route('students.store'), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpen.value = false;
            studentForm.reset();
            toast.success('Estudante adicionado com sucesso.');
        },
        onError: () => {
            toast.error('Erro ao adicionar estudante.');
        },
    });
};

const editStudent = () => {
    isUpdating.value = true;
    editForm.put(route('students.update', selectStudentId.value), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isUpdating.value = false;
            toast.success('Estudante editado com sucesso.');
        },
        onError: () => {
            isUpdating.value = false;
            toast.error('Erro ao editar estudante.');
        },
    });
};

const deleteStudent = () => {
    isDeleting.value = true;
    editForm.delete(route('students.destroy', selectStudentId.value), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isDeleting.value = false;
            toast.success('Estudante deletado com sucesso.');
        },
        onError: () => {
            isDeleting.value = false;
            toast.error('Erro ao deletar estudante.');
        },
    });
};

const submitSearch = (e: Event) => {
    e.preventDefault();
    router.get(route('students'), { search: search.value });
};

const getInitials = (name: string): string => {
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
};

const columns = [
    { key: 'name', label: 'NAME' },
    { key: 'email', label: 'EMAIL' },
    { key: 'cod', label: 'COD' },
    { key: 'actions', label: 'ACTIONS' }
];
</script>

<template>
    <Head title="Alunos" />
    <Toaster />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-fit items-start justify-between flex-row rounded-xl flex-wrap p-4 gap-4">
            <form @submit.prevent="submitSearch"
                class="flex flex-row gap-2 items-center justify-center max-sm:flex-wrap max-sm:justify-start max-sm:w-full">
                <label for="search" class="font-bold text-white">Pesquisar: </label>
                <Input v-model="search" placeholder="Nome, email ou código..." class="w-80 max-sm:w-full"></Input>
                <Button type="submit" class="cursor-pointer max-sm:w-full bg-[var(--primary)]">
                    <Search class="h-5 w-5"/>
                    Pesquisar
                </Button>
            </form>

            <Button class="cursor-pointer max-sm:w-full bg-[var(--primary)]" @click="openDialog">
                <Plus class="h-5 w-5" />
                Adicionar novo
            </Button>
        </div>

        <!-- Data Table Card -->
        <Card class="bg-[var(--sidebar-background)] border-none m-4">
            <CardHeader>
                <CardTitle class="text-white text-xl font-semibold">Lista de Estudantes</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow class="border-b border-gray-700 hover:bg-transparent">
                                <TableHead v-for="column in columns" :key="column.key"
                                    class="text-white font-semibold py-4 px-6">
                                    {{ column.label }}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="student in students" :key="student.id"
                                class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors">
                                <TableCell class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-[var(--primary)] rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ getInitials(student.name) }}
                                        </div>
                                        <span class="text-white font-medium">{{ student.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="py-4 px-6 text-white">{{ student.email }}</TableCell>
                                <TableCell class="py-4 px-6">
                                    <span
                                        class="bg-[var(--secondary)] text-white text-xs px-3 py-1 rounded-full font-medium">
                                        {{ student.cod }}
                                    </span>
                                </TableCell>
                                <TableCell class="py-4 px-6">
                                    <Button @click="openDialogEdit(student.id)"
                                        class="bg-[var(--primary)] hover:bg-[var(--primary)]/80 text-white text-sm px-4 py-2 cursor-pointer flex items-center">
                                        <Edit class="h-4 w-4 mr-2" />
                                        Editar Perfil
                                    </Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="students.length === 0">
                                <TableCell colspan="4" class="text-center py-12 text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <Search class="h-12 w-12 text-gray-500" />
                                        <p class="text-lg font-medium">Nenhum estudante encontrado</p>
                                        <p class="text-sm">Tente ajustar sua pesquisa ou adicione um novo estudante</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>

        <!-- Add Student Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="saveStudent" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Plus class="h-5 w-5" />
                            Novo Estudante
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Nome Completo</label>
                            <Input v-model="studentForm.name" placeholder="Digite o nome completo" class="w-full" />
                            <div v-if="studentForm.errors.name" class="text-red-400 text-sm mt-1">
                                {{ studentForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Email</label>
                            <Input v-model="studentForm.email" type="email" placeholder="exemplo@email.com"
                                class="w-full" />
                            <div v-if="studentForm.errors.email" class="text-red-400 text-sm mt-1">
                                {{ studentForm.errors.email }}
                            </div>
                        </div>

                       
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="submit"
                            class="w-full bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors"
                            :disabled="studentForm.processing">
                            {{ studentForm.processing ? 'SALVANDO...' : 'ADICIONAR ESTUDANTE' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit Student Dialog -->
        <Dialog v-model:open="isDialogOpenEdit">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="editStudent" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Edit class="h-5 w-5" />
                            Editar Estudante
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Nome Completo</label>
                            <Input v-model="editForm.name" placeholder="Digite o nome completo" class="w-full" />
                            <div v-if="editForm.errors.name" class="text-red-400 text-sm mt-1">
                                {{ editForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Email</label>
                            <Input v-model="editForm.email" type="email" placeholder="exemplo@email.com"
                                class="w-full" />
                            <div v-if="editForm.errors.email" class="text-red-400 text-sm mt-1">
                                {{ editForm.errors.email }}
                            </div>
                        </div>

                       
                    </div>

                    <DialogFooter class="flex gap-3 pt-4">
                        <Button @click.prevent="deleteStudent"
                            class="bg-[var(--destructive)] text-white font-semibold py-3 px-6 rounded-lg cursor-pointer hover:bg-[var(--destructive)]/90 transition-colors flex items-center gap-2"
                            :disabled="isDeleting">
                            <Trash2 class="h-4 w-4" />
                            {{ isDeleting ? 'DELETANDO...' : 'DELETAR' }}
                        </Button>
                        <Button type="submit"
                            class="flex-1 bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors flex items-center justify-center gap-2"
                            :disabled="isUpdating">
                            <Edit class="h-4 w-4" />
                            {{ isUpdating ? 'ATUALIZANDO...' : 'ATUALIZAR ESTUDANTE' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

