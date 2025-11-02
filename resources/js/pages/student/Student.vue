<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, Plus, Edit, Trash2, Upload, Download, Mail, CheckCircle2, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';


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
        // Ajuste de tipagem para incluir os campos usados no template
        type: Object as () => {
            data: Array<{
                id: number;
                name: string;
                email: string;
                cod: string;
                email_verified_at: string | null;
                platform_access: boolean;
            }>;
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        },
        required: true,
    }
});

const search = ref(props.search);
const isDialogOpen = ref(false);
const isDialogOpenEdit = ref(false);
const isDialogOpenImport = ref(false);
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
    const selectedStudent = props.students.data.find(s => s.id === id);
    if (selectedStudent) {
        editForm.name = selectedStudent.name;
        editForm.email = selectedStudent.email;
    }
    isDialogOpenEdit.value = true;
};

const openDialogImport = () => {
    isDialogOpenImport.value = true;
    importForm.reset();
};

const studentForm = useForm({
    name: '',
    email: '',
});

const editForm = useForm({
    name: '',
    email: '',
});

const importForm = useForm({
    file: null as File | null,
});

const saveStudent = () => {
    studentForm.post(route('students.store'), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpen.value = false;
            studentForm.reset();
            toast.success('Estudante adicionado com sucesso', {
                description: 'O estudante foi criado e adicionado à lista.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            toast.error('Erro ao adicionar estudante', {
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
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
            toast.success('Estudante editado com sucesso', {
                description: 'As informações foram atualizadas.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isUpdating.value = false;
            toast.error('Erro ao editar estudante', {
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

// AJUSTE: deleteStudent aceita id opcional (usa selectStudentId como fallback)
const deleteStudent = (id?: number) => {
    const targetId = id ?? selectStudentId.value;
    if (!targetId) return;

    isDeleting.value = true;
    editForm.delete(route('students.destroy', targetId), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isDeleting.value = false;
            toast.success('Estudante deletado com sucesso', {
                description: 'O estudante foi removido permanentemente.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isDeleting.value = false;
            toast.error('Erro ao deletar estudante', {
                description: 'Não foi possível deletar o estudante.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

const importStudents = () => {
    if (!importForm.file) {
        toast.error('Selecione um arquivo', {
            description: 'Por favor, selecione um arquivo Excel para importar.',
            style: { background: 'var(--destructive)', color: 'black' }
        });
        return;
    }

    importForm.post(route('students.import'), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenImport.value = false;
            importForm.reset();
            toast.success('Estudantes importados com sucesso', {
                description: 'Os dados do Excel foram processados e importados.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: (errors) => {
            toast.error('Erro ao importar estudantes', {
                description: errors.msg || 'Verifique o arquivo e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        importForm.file = target.files[0];
    }
};

const submitSearch = (e: Event) => {
    e.preventDefault();
    router.get(route('students'), { search: search.value, page: 1 });
};

const goToPage = (page: number) => {
    if (page < 1 || page > props.students.last_page) return;
    router.get(route('students'), { search: search.value, page });
};

const getInitials = (name: string): string => {
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
};

const columns = [
    { key: 'name', label: 'NAME' },
    { key: 'email', label: 'EMAIL' },
    { key: 'cod', label: 'COD' },
    { key: 'email_verified', label: 'EMAIL VERIFICADO' },
    { key: 'platform_access', label: 'ACESSO PLATAFORMA' },
    { key: 'actions', label: 'ACTIONS' }
];

const downloadTemplate = () => {
    window.location.href = route('students.download-template');
};

// REMOVA as funções togglePlatformAccess e resendVerificationEmail antigas
// ADICIONE:
const togglePlatformAccess = (studentId: number) => {
    router.post(route('students.toggle-access', studentId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Acesso à plataforma atualizado!', {
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            toast.error('Erro ao alterar acesso à plataforma');
        }
    });
};

const resendVerificationEmail = (studentId: number, studentName: string) => {
    if (!confirm(`Reenviar email de verificação para ${studentName}?`)) {
        return;
    }

    router.post(route('students.resend-verification', studentId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Email de verificação reenviado com sucesso!', {
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            toast.error('Erro ao reenviar email de verificação');
        }
    });
};
</script>

<template>

    <Head title="Alunos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-fit items-start justify-between flex-row rounded-xl flex-wrap p-4 gap-4">
            <form @submit.prevent="submitSearch"
                class="flex flex-row gap-2 items-center justify-center max-sm:flex-wrap max-sm:justify-start max-sm:w-full">
                <label for="search" class="font-bold text-white">Pesquisar: </label>
                <Input v-model="search" placeholder="Nome, email ou código..." class="w-80 max-sm:w-full"></Input>
                <Button type="submit" class="cursor-pointer max-sm:w-full bg-[var(--primary)]">
                    <Search class="h-5 w-5" />
                    Pesquisar
                </Button>

                <!-- Adicionar novo - visível apenas em telas grandes -->
                <Button type="button" class="cursor-pointer bg-[var(--primary)] ml-2 max-sm:hidden" @click="openDialog">
                    <Plus class="h-5 w-5" />
                    Adicionar novo
                </Button>
            </form>

            <div class="flex gap-2 max-sm:w-full max-sm:flex-col">
                <!-- Adicionar novo - visível apenas em telas pequenas -->
                <Button type="button" class="cursor-pointer bg-[var(--primary)] hidden max-sm:block" @click="openDialog">
                    <Plus class="h-5 w-5" />
                    Adicionar novo
                </Button>
                
                <Button class="cursor-pointer max-sm:flex-1 bg-[var(--secondary)] hover:bg-[var(--secondary)]/80 text-white" @click="downloadTemplate">
                    <Download class="h-5 w-5" />
                    Baixar Template
                </Button>
                
                <Button class="cursor-pointer max-sm:flex-1 bg-green-600 hover:bg-green-700 text-white" @click="openDialogImport">
                    <Upload class="h-5 w-5" />
                    Importar Excel
                </Button>
            </div>
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
                            <TableRow v-for="student in students.data" :key="student.id"
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
                                <TableCell class="py-4 px-6 text-center">
                                    <div class="flex justify-center">
                                        <CheckCircle2 
                                            v-if="student.email_verified_at" 
                                            class="w-5 h-5 text-green-500" 
                                            :title="`Verificado em ${new Date(student.email_verified_at).toLocaleString('pt-BR')}`"
                                        />
                                        <XCircle 
                                            v-else 
                                            class="w-5 h-5 text-destructive" 
                                            title="Email não verificado"
                                        />
                                    </div>
                                </TableCell>
                                <TableCell class="py-4 px-6 text-center">
                                    <Select
                                        :model-value="student.platform_access ? '1' : '0'"
                                        @update:model-value="() => togglePlatformAccess(student.id)"
                                        :disabled="!student.email_verified_at"
                                    >
                                        <SelectTrigger class="w-32 mx-auto">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="1">
                                                <span class="text-green-500 font-medium">Habilitado</span>
                                            </SelectItem>
                                            <SelectItem value="0">
                                                <span class="text-destructive font-medium">Desabilitado</span>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </TableCell>
                                <TableCell class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <Button
                                            v-if="!student.email_verified_at"
                                            variant="ghost"
                                            size="sm"
                                            @click="resendVerificationEmail(student.id, student.name)"
                                            title="Reenviar email de verificação"
                                        >
                                            <Mail class="w-4 h-4" />
                                        </Button>
                                        
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="openDialogEdit(student.id)"
                                        >
                                            <Edit class="h-4 w-4" />
                                            Editar Perfil
                                        </Button>
                                        
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="deleteStudent(student.id)"
                                        >
                                            <Trash2 class="w-4 h-4 text-destructive" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="students.data.length === 0">
                                <TableCell colspan="6" class="text-center py-12 text-gray-400">
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

        <!-- Pagination -->
        <div v-if="students.last_page > 1" class="flex justify-center py-4 m-4">
            <Pagination v-slot="{ page }" :items-per-page="students.per_page" :total="students.total" :default-page="students.current_page">
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious 
                        @click.prevent="goToPage(page - 1)"
                        :class="page <= 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                    />

                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem
                            v-if="item.type === 'page'"
                            :value="item.value"
                            :is-active="item.value === page"
                            @click.prevent="goToPage(item.value)"
                            :class="[
                                'cursor-pointer',
                                item.value === page 
                                    ? 'bg-[var(--primary)] text-white hover:bg-[var(--primary)]' 
                                    : 'text-white hover:bg-[var(--primary)]/20'
                            ]"
                        >
                            {{ item.value }}
                        </PaginationItem>
                        <PaginationEllipsis v-else-if="item.type === 'ellipsis'" :index="index" class="text-white" />
                    </template>

                    <PaginationNext 
                        @click.prevent="goToPage(page + 1)"
                        :class="page >= students.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                    />
                </PaginationContent>
            </Pagination>
        </div>

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

        <!-- Import Students Dialog -->
        <Dialog v-model:open="isDialogOpenImport">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="importStudents" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Upload class="h-5 w-5" />
                            Importar Estudantes via Excel
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Arquivo Excel</label>
                            <input 
                                type="file" 
                                @change="handleFileChange"
                                accept=".xlsx,.xls"
                                class="w-full text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[var(--primary)] file:text-white hover:file:bg-[var(--primary)]/80 file:cursor-pointer cursor-pointer"
                            />
                            <p class="text-gray-400 text-xs mt-2">
                                Formato aceito: .xlsx, .xls
                            </p>
                            <p class="text-gray-400 text-xs mt-1">
                                Colunas esperadas: Nome (A), Email (B)
                            </p>
                            <div v-if="importForm.errors.file" class="text-red-400 text-sm mt-1">
                                {{ importForm.errors.file }}
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="submit"
                            class="w-full bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors flex items-center justify-center gap-2"
                            :disabled="importForm.processing">
                            <Upload class="h-4 w-4" />
                            {{ importForm.processing ? 'IMPORTANDO...' : 'IMPORTAR ESTUDANTES' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
