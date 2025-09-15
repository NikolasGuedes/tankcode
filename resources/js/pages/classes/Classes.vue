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
        ClassForm.reset();
    };

    const openDialogEdit = (id: number) => {
        selectStudentId.value = id;
        const selectedStudent = props.students.find(s => s.id === id);
        if (selectedStudent) {
            editForm.name = selectedStudent.name;
            //editForm.email = selectedStudent.email;

            editForm.cod = selectedStudent.cod;
        }
        isDialogOpenEdit.value = true;
    };

    const ClassForm = useForm({
        name: '',
       // email: '',
        cod: '',
    });

    const editForm = useForm({
        name: '',
       // email: '',
        cod: '',
    });

    const saveStudent = () => {
        ClassForm.post(route('students.store'), {
            preserveState: true,
            onSuccess: () => {
                isDialogOpen.value = false;
                ClassForm.reset();
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
        router.get(route('students.index', { search: search.value }));
    };

    const getInitials = (name: string): string => {
        return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
    };

    const columns = [
        { key: 'name', label: 'NAME DA SALA' },
        { key: 'cod', label: 'CÓDIGO DA SALA' },
    ];
</script>

<template>
    <Head title="Classes"/>
    <Toaster />

<AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-fit items-start justify-between flex-row rounded-xl flex-wrap p-4 gap-4">
            <form @submit.prevent="submitSearch"
                class="flex flex-row gap-2 items-center justify-center max-sm:flex-wrap max-sm:justify-start max-sm:w-full">
                <label for="search" class="font-bold text-white">Pesquisar: </label>
                <Input v-model="search" placeholder="Nome da sala, código da sala..." class="w-80 max-sm:w-full"></Input>
                <Button type="submit" class="cursor-pointer max-sm:w-full bg-[var(--primary)]">
                    <Search class="h-5 w-5"/>
                    Pesquisar 
                </Button>
            </form>

            <Button class="cursor-pointer max-sm:w-full bg-[var(--primary)]" @click="openDialog">
                <Plus class="h-5 w-5" />
                Criar Sala
            </Button>
        </div>

        <Card class="bg-[var(--sidebar-background)] border-none m-4">
            <CardHeader>
                <CardTitle class="text-white text-xl font-semibold">Lista de Salas</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableHead v-for="column in columns" :key="column.key"
                                    class="text-white font-semibold py-4 px-6">
                                    {{ column.label }}
                            </TableHead>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="student in students" :key="student.id" class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors">
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

                            </TableRow>    
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>

        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="saveStudent" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Plus class="h-5 w-5" />
                            Nova Sala
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Nome da Sala</label>
                            <Input v-model="ClassForm.name" placeholder="Digite o nome da sala" class="w-full" />
                            <div v-if="ClassForm.errors.name" class="text-red-400 text-sm mt-1">
                                {{ ClassForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Código da Sala</label>
                            <Input v-model="ClassForm.cod" placeholder="STD-123" class="w-full" />
                            <div v-if="ClassForm.errors.cod" class="text-red-400 text-sm mt-1">
                                {{ ClassForm.errors.cod }}
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="submit"
                            class="w-full bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors"
                            :disabled="ClassForm.processing">
                            {{ ClassForm.processing ? 'CRIANDO...' : 'CRIAR SALA' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
       
</AppLayout>

</template>

