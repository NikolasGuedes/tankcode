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
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

// ----------------------------------------------------------------
// 1. ADAPTAÇÃO: Breadcrumbs e Título
// ----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quartos',
        href: '/rooms',
    },
];

const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    // ----------------------------------------------------------------
    // 2. ADAPTAÇÃO: Propriedade 'rooms' em vez de 'students'
    // Adicionei campos típicos de um Quarto: capacity e price.
    // ----------------------------------------------------------------
    rooms: {
        type: Array as () => Array<{
            id: number;
            name: string;
            capacity: number; // Capacidade (em vez de email)
            price: number;    // Preço (em vez de cod)
        }>,
        required: true,
    }
});

const search = ref(props.search);
const isDialogOpen = ref(false);
const isDialogOpenEdit = ref(false);
const selectRoomId = ref<number | null>(null); // Renomeado para 'RoomId'
const isDeleting = ref(false);
const isUpdating = ref(false);

watch(() => props.search, (newSearch) => {
    search.value = newSearch;
}, { immediate: true });

const openDialog = () => {
    isDialogOpen.value = true;
    roomForm.reset(); // Renomeado para 'roomForm'
};

const openDialogEdit = (id: number) => {
    selectRoomId.value = id;
    const selectedRoom = props.rooms.find(r => r.id === id); // Usa props.rooms
    if (selectedRoom) {
        editForm.name = selectedRoom.name;
        editForm.capacity = selectedRoom.capacity; // Novo campo
        editForm.price = selectedRoom.price;       // Novo campo
    }
    isDialogOpenEdit.value = true;
};

// ----------------------------------------------------------------
// 3. ADAPTAÇÃO: Forms com campos de Quarto
// ----------------------------------------------------------------
const roomForm = useForm({
    name: '',
    capacity: 0,
    price: 0,
});

const editForm = useForm({
    name: '',
    capacity: 0,
    price: 0,
});

// ----------------------------------------------------------------
// 4. ADAPTAÇÃO: Rotas para 'rooms.store'
// ----------------------------------------------------------------
const saveRoom = () => {
    roomForm.post(route('rooms.store'), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpen.value = false;
            roomForm.reset();
            toast.success('Quarto adicionado com sucesso', { // Texto adaptado
                description: 'O quarto foi criado e adicionado à lista.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            toast.error('Erro ao adicionar quarto', { // Texto adaptado
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

// ----------------------------------------------------------------
// 5. ADAPTAÇÃO: Rotas para 'rooms.update'
// ----------------------------------------------------------------
const editRoom = () => {
    isUpdating.value = true;
    editForm.put(route('rooms.update', selectRoomId.value), { // Usa 'rooms.update' e 'selectRoomId'
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isUpdating.value = false;
            toast.success('Quarto editado com sucesso', { // Texto adaptado
                description: 'As informações foram atualizadas.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isUpdating.value = false;
            toast.error('Erro ao editar quarto', { // Texto adaptado
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

// ----------------------------------------------------------------
// 6. ADAPTAÇÃO: Rotas para 'rooms.destroy'
// ----------------------------------------------------------------
const deleteRoom = () => {
    isDeleting.value = true;
    editForm.delete(route('rooms.destroy', selectRoomId.value), { // Usa 'rooms.destroy' e 'selectRoomId'
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isDeleting.value = false;
            toast.success('Quarto deletado com sucesso', { // Texto adaptado
                description: 'O quarto foi removido permanentemente.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isDeleting.value = false;
            toast.error('Erro ao deletar quarto', { // Texto adaptado
                description: 'Não foi possível deletar o quarto.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

// ----------------------------------------------------------------
// 7. ADAPTAÇÃO: Rotas para 'rooms' na busca
// ----------------------------------------------------------------
const submitSearch = (e: Event) => {
    e.preventDefault();
    router.get(route('rooms.index'), { search: search.value }); // Usa 'rooms.index'
};

const getInitials = (name: string): string => {
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
};

// ----------------------------------------------------------------
// 8. ADAPTAÇÃO: Colunas da Tabela
// ----------------------------------------------------------------
const columns = [
    { key: 'name', label: 'NOME' },
    { key: 'capacity', label: 'CAPACIDADE' },
    { key: 'price', label: 'PREÇO' },
    { key: 'actions', label: 'AÇÕES' }
];
</script>

<template>
    <Head title="Quartos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-fit items-start justify-between flex-row rounded-xl flex-wrap p-4 gap-4">
            <form @submit.prevent="submitSearch"
                class="flex flex-row gap-2 items-center justify-center max-sm:flex-wrap max-sm:justify-start max-sm:w-full">
                <label for="search" class="font-bold text-white">Pesquisar: </label>
                <Input v-model="search" placeholder="Nome, capacidade ou preço..." class="w-80 max-sm:w-full"></Input>
                <Button type="submit" class="cursor-pointer max-sm:w-full bg-[var(--primary)]">
                    <Search class="h-5 w-5" />
                    Pesquisar
                </Button>
            </form>

            <Button class="cursor-pointer max-sm:w-full bg-[var(--primary)]" @click="openDialog">
                <Plus class="h-5 w-5" />
                Adicionar novo
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
                            <TableRow class="border-b border-gray-700 hover:bg-transparent">
                                <TableHead v-for="column in columns" :key="column.key"
                                    class="text-white font-semibold py-4 px-6">
                                    {{ column.label }}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="room in rooms" :key="room.id"
                                class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors">
                                <TableCell class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-[var(--primary)] rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ getInitials(room.name) }}
                                        </div>
                                        <span class="text-white font-medium">{{ room.name }}</span>
                                    </div>
                                </TableCell>
                                <TableCell class="py-4 px-6 text-white">{{ room.capacity }}</TableCell>
                                <TableCell class="py-4 px-6">
                                    <span
                                        class="bg-[var(--secondary)] text-white text-xs px-3 py-1 rounded-full font-medium">
                                        {{ room.price }}
                                    </span>
                                </TableCell>
                                <TableCell class="py-4 px-6">
                                    <Button @click="openDialogEdit(room.id)"
                                        class="bg-[var(--primary)] hover:bg-[var(--primary)]/80 text-white text-sm px-4 py-2 cursor-pointer flex items-center">
                                        <Edit class="h-4 w-4 mr-2" />
                                        Editar Quarto
                                    </Button>
                                </TableCell>
                            </TableRow>
                            <TableRow>
                                <TableCell colspan="4" class="text-center py-12 text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <Search class="h-12 w-12 text-gray-500" />
                                        <p class="text-lg font-medium">Nenhum quarto encontrado</p>
                                        <p class="text-sm">Tente ajustar sua pesquisa ou adicione um novo quarto</p>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>

        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="saveRoom" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Plus class="h-5 w-5" />
                            Novo Quarto
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Nome/Número do Quarto</label>
                            <Input v-model="roomForm.name" placeholder="Ex: Quarto 101" class="w-full" />
                            <div v-if="roomForm.errors.name" class="text-red-400 text-sm mt-1">
                                {{ roomForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Capacidade (Pessoas)</label>
                            <Input v-model.number="roomForm.capacity" type="number" placeholder="Ex: 2"
                                class="w-full" />
                            <div v-if="roomForm.errors.capacity" class="text-red-400 text-sm mt-1">
                                {{ roomForm.errors.capacity }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Preço Diária</label>
                            <Input v-model.number="roomForm.price" type="number" placeholder="Ex: 150.00"
                                class="w-full" />
                            <div v-if="roomForm.errors.price" class="text-red-400 text-sm mt-1">
                                {{ roomForm.errors.price }}
                            </div>
                        </div>


                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="submit"
                            class="w-full bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors"
                            :disabled="roomForm.processing">
                            {{ roomForm.processing ? 'SALVANDO...' : 'ADICIONAR QUARTO' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isDialogOpenEdit">
            <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl max-sm:min-w-fit mx-auto">
                <form @submit.prevent="editRoom" class="space-y-6">
                    <DialogHeader class="pb-4">
                        <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
                            <Edit class="h-5 w-5" />
                            Editar Quarto
                        </DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Nome/Número do Quarto</label>
                            <Input v-model="editForm.name" placeholder="Ex: Quarto 101" class="w-full" />
                            <div v-if="editForm.errors.name" class="text-red-400 text-sm mt-1">
                                {{ editForm.errors.name }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Capacidade (Pessoas)</label>
                            <Input v-model.number="editForm.capacity" type="number" placeholder="Ex: 2"
                                class="w-full" />
                            <div v-if="editForm.errors.capacity" class="text-red-400 text-sm mt-1">
                                {{ editForm.errors.capacity }}
                            </div>
                        </div>

                        <div>
                            <label class="text-white text-sm font-medium mb-2 block">Preço Diária</label>
                            <Input v-model.number="editForm.price" type="number" placeholder="Ex: 150.00"
                                class="w-full" />
                            <div v-if="editForm.errors.price" class="text-red-400 text-sm mt-1">
                                {{ editForm.errors.price }}
                            </div>
                        </div>

                    </div>

                    <DialogFooter class="flex gap-3 pt-4">
                        <Button @click.prevent="deleteRoom"
                            class="bg-[var(--destructive)] text-white font-semibold py-3 px-6 rounded-lg cursor-pointer hover:bg-[var(--destructive)]/90 transition-colors flex items-center gap-2"
                            :disabled="isDeleting">
                            <Trash2 class="h-4 w-4" />
                            {{ isDeleting ? 'DELETANDO...' : 'DELETAR' }}
                        </Button>
                        <Button type="submit"
                            class="flex-1 bg-[var(--primary)] text-white font-semibold py-3 rounded-lg cursor-pointer hover:bg-[var(--primary)]/90 transition-colors flex items-center justify-center gap-2"
                            :disabled="isUpdating">
                            <Edit class="h-4 w-4" />
                            {{ isUpdating ? 'ATUALIZANDO...' : 'ATUALIZAR QUARTO' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>