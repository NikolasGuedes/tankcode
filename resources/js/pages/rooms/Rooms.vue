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
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';

// ----------------------------------------------------------------
// Breadcrumbs e título
// ----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Salas', href: '/rooms' },
];

// ----------------------------------------------------------------
// Propriedades recebidas via Inertia
// ----------------------------------------------------------------
const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    rooms: {
        type: Object as () => {
            data: Array<{
                id: number;
                name_room: string;
                code: string;
            }>;
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        },
        required: true,
    }
});

// ----------------------------------------------------------------
// Estado e reatividade
// ----------------------------------------------------------------
const searchQuery = ref(props.search);
const isDialogOpen = ref(false);
const isDialogOpenEdit = ref(false);
const selectRoomId = ref<number | null>(null);
const isDeleting = ref(false);
const isUpdating = ref(false);

watch(() => props.search, (newVal) => {
    searchQuery.value = newVal;
}, { immediate: true });

// ----------------------------------------------------------------
// Formulários
// ----------------------------------------------------------------
const roomForm = useForm({
    name_room: '',
});

const editForm = useForm({
    name_room: '',
});

// ----------------------------------------------------------------
// Funções de ação
// ----------------------------------------------------------------
const openDialog = () => {
    roomForm.reset();
    isDialogOpen.value = true;
};

const openDialogEdit = (id: number) => {
    selectRoomId.value = id;
    const selectedRoom = props.rooms.data.find((r: { id: number }) => r.id === id);
    if (selectedRoom) {
        editForm.name_room = selectedRoom.name_room;
    }
    isDialogOpenEdit.value = true;
};

const saveRoom = () => {
    roomForm.post(route('rooms.store'), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpen.value = false;
            roomForm.reset();
            toast.success('Sala adicionada com sucesso', {
                description: 'A sala foi criada com sucesso.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            toast.error('Erro ao adicionar sala', {
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

const editRoom = () => {
    isUpdating.value = true;
    editForm.put(route('rooms.update', selectRoomId.value), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isUpdating.value = false;
            toast.success('Sala editada com sucesso', {
                description: 'As informações foram atualizadas.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isUpdating.value = false;
            toast.error('Erro ao editar sala', {
                description: 'Verifique os dados e tente novamente.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

const deleteRoom = () => {
    isDeleting.value = true;
    editForm.delete(route('rooms.destroy', selectRoomId.value), {
        preserveState: true,
        onSuccess: () => {
            isDialogOpenEdit.value = false;
            editForm.reset();
            isDeleting.value = false;
            toast.success('Sala deletada com sucesso', {
                description: 'A sala foi removida.',
                style: { background: 'var(--green_site)', color: 'black' }
            });
        },
        onError: () => {
            isDeleting.value = false;
            toast.error('Erro ao deletar sala', {
                description: 'Não foi possível deletar a sala.',
                style: { background: 'var(--destructive)', color: 'black' }
            });
        },
    });
};

const submitSearch = (e: Event) => {
    e.preventDefault();
    router.get(route('rooms.index'), { search: searchQuery.value, page: 1 });
};

const goToPage = (page: number) => {
    if (page < 1 || page > props.rooms.last_page) return;
    router.get(route('rooms.index'), { search: searchQuery.value, page });
};


const getInitials = (name: string): string => {
    return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

// ----------------------------------------------------------------
// Colunas da tabela (para referência, se precisar de manipulação futura)
// ----------------------------------------------------------------
const columns = [
    { key: 'name_room', label: 'NOME DA SALA' },
    { key: 'code', label: 'CÓDIGO DA SALA' }
];
</script>
<template>

  <Head title="Salas" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Cabeçalho: Pesquisa e Botão Adicionar -->
    <div class="flex flex-wrap gap-4 items-center justify-between p-4">
      <form @submit.prevent="submitSearch" class="flex flex-wrap gap-2 items-center">
        <label for="search" class="font-bold text-white">Pesquisar Sala:</label>
        <Input v-model="searchQuery" placeholder="Digite o nome da sala..." class="w-80" />
        <Button type="submit" class="bg-[var(--primary)]">
          <Search class="h-5 w-5" />
          Pesquisar
        </Button>
      </form>
      <Button class="bg-[var(--primary)]" @click="openDialog">
        <Plus class="h-5 w-5" />
        Adicionar novo
      </Button>
    </div>

    <!-- Card com a Tabela -->
    <Card class="bg-[var(--sidebar-background)] m-4 border-none">
      <CardHeader>
        <CardTitle class="text-white text-xl font-semibold">Lista de Salas</CardTitle>
      </CardHeader>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow class="border-b border-gray-700">
                <TableHead class="text-white font-semibold py-4 px-6">NAME</TableHead>
                <TableHead class="text-white font-semibold py-4 px-6">COD</TableHead>
                <TableHead class="text-white font-semibold py-4 px-6">ACTIONS</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="room in rooms.data" :key="room.id" class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors">
                <TableCell class="py-4 px-6">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[var(--primary)] rounded-full flex items-center justify-center text-white font-bold text-sm">
                      {{ getInitials(room.name_room) }}
                    </div>
                    <span class="text-white font-medium">{{ room.name_room }}</span>
                  </div>
                </TableCell>
                <TableCell class="py-4 px-6">
                  <span class="text-white font-medium">{{ room.code }}</span>
                </TableCell>
                <TableCell class="py-4 px-6">
                <div class="flex flex-row gap-2">
                  <Button @click="openDialogEdit(room.id)" class="bg-[var(--primary)] hover:bg-[var(--primary)]/80 text-white text-sm px-4 py-2 flex items-center gap-2">
                    <Edit class="h-4 w-4" />
                    Editar Sala
                  </Button>
                 <Button
                  @click="router.visit(route('rooms.EditRooms', room.id) + '?tab=students')"
                  class="bg-[var(--primary)] hover:bg-[var(--primary)]/80 text-white text-sm px-4 py-2 flex items-center gap-2">
                  <Edit class="h-4 w-4" />
                  Gerenciar Alunos
                </Button>
                </div>
                </TableCell>
                
              </TableRow>
              <TableRow v-if="rooms.data.length === 0">
                <TableCell colspan="3" class="text-center py-12 text-gray-400">
                  <div class="flex flex-col items-center gap-2">
                    <Search class="h-12 w-12 text-gray-500" />
                    <p class="text-lg font-medium">Nenhuma sala encontrada</p>
                    <p class="text-sm">Tente ajustar sua pesquisa ou adicione uma nova sala</p>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>

    <!-- Paginação -->
    <div v-if="rooms.last_page > 1" class="flex justify-center py-4 m-4">
      <Pagination v-slot="{ page }" :items-per-page="rooms.per_page" :total="rooms.total" :default-page="rooms.current_page">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious 
            @click.prevent="goToPage(rooms.current_page - 1)"
            :class="rooms.current_page <= 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
          />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem
              v-if="item.type === 'page'"
              :value="item.value"
              :is-active="item.value === rooms.current_page"
              @click.prevent="goToPage(item.value)"
            >
              {{ item.value }}
            </PaginationItem>
            <PaginationEllipsis v-else-if="item.type === 'ellipsis'" :index="index" class="text-white" />
          </template>

          <PaginationNext 
            @click.prevent="goToPage(rooms.current_page + 1)"
            :class="rooms.current_page >= rooms.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
          />
        </PaginationContent>
      </Pagination>
    </div>

    <!-- Dialog: Adicionar nova Sala -->
    <Dialog v-model:open="isDialogOpen">
      <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl mx-auto">
        <form @submit.prevent="saveRoom" class="space-y-6 p-4">
          <DialogHeader>
            <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
              <Plus class="h-5 w-5" />
              Nova Sala
            </DialogTitle>
          </DialogHeader>
          <div>
            <label class="text-white text-sm font-medium mb-2 block">Nome da Sala</label>
            <Input v-model="roomForm.name_room" placeholder="Digite o nome da sala..." class="w-full" />
            <div v-if="roomForm.errors.name_room" class="text-red-400 text-sm mt-1">
              {{ roomForm.errors.name_room }}
            </div>
          </div>
          <DialogFooter>
            <Button type="submit" class="w-full bg-[var(--primary)] text-white py-3 rounded-lg hover:bg-[var(--primary)]/90 transition-colors" :disabled="roomForm.processing">
              {{ roomForm.processing ? 'SALVANDO...' : 'ADICIONAR SALA' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Dialog: Editar Sala -->
    <Dialog v-model:open="isDialogOpenEdit">
      <DialogContent class="bg-[var(--sidebar-background)] border-none min-w-xl mx-auto">
        <form @submit.prevent="editRoom" class="space-y-6 p-4">
          <DialogHeader>
            <DialogTitle class="text-white text-xl font-semibold flex items-center gap-2">
              <Edit class="h-5 w-5" />
              Editar Sala
            </DialogTitle>
          </DialogHeader>
          <div>
            <label class="text-white text-sm font-medium mb-2 block">Nome da Sala</label>
            <Input v-model="editForm.name_room" placeholder="Digite o nome da sala..." class="w-full" />
            <div v-if="editForm.errors.name_room" class="text-red-400 text-sm mt-1">
              {{ editForm.errors.name_room }}
            </div>
          </div>
          <DialogFooter class="flex gap-3">
            <Button @click.prevent="deleteRoom" class="bg-[var(--destructive)] text-white py-3 px-6 rounded-lg hover:bg-[var(--destructive)]/90 transition-colors flex items-center gap-2" :disabled="isDeleting">
              <Trash2 class="h-4 w-4" />
              {{ isDeleting ? 'DELETANDO...' : 'DELETAR' }}
            </Button>
            <Button type="submit" class="flex-1 bg-[var(--primary)] text-white py-3 rounded-lg hover:bg-[var(--primary)]/90 transition-colors flex items-center justify-center gap-2" :disabled="isUpdating">
              <Edit class="h-4 w-4" />
              {{ isUpdating ? 'ATUALIZANDO...' : 'ATUALIZAR SALA' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>