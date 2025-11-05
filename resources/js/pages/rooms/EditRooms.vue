// resources/js/Pages/rooms/EditRooms.vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, Plus, Trash2, ArrowLeft } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
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
  filter: {
    type: String,
    default: 'linked'
  },
  students: {
    type: Object as () => {
      data: Array<{
        id: number;
        name: string;
        email: string;
        cod: string;
        room_id: number | null;
        is_linked: boolean;
      }>;
      links: Array<{
        url: string | null;
        label: string;
        active: boolean;
      }>;
      current_page: number;
      last_page: number;
    },
    required: true,
  },
  totalLinked: {
    type: Number,
    required: true
  },
  totalUnlinked: {
    type: Number,
    required: true
  },
  room: {
    type: Object as () => {
      id: number;
      name_room: string;
      code: string;
    },
    required: true,
  }
});

// ----------------------------------------------------------------
// Estado e reatividade
// ----------------------------------------------------------------
const searchQuery = ref(props.search);
const activeTab = ref(props.filter);

const form = useForm({
  student_id: null as number | null,
});

watch(() => props.search, (newVal) => {
  searchQuery.value = newVal;
}, { immediate: true });

watch(() => props.filter, (newVal) => {
  activeTab.value = newVal;
}, { immediate: true });

// ----------------------------------------------------------------
// Funções de Ação
// ----------------------------------------------------------------
const addStudent = (studentId: number) => {
  form.student_id = studentId;

  const currentPage = new URLSearchParams(window.location.search).get('page') || '1';
  form.post(route('rooms.addStudent', props.room.id) + `?page=${currentPage}`, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      toast.success('Aluno adicionado!', {
        description: 'O aluno foi vinculado à sala.',
      });
      form.reset('student_id');
    },
    onError: (errors) => {
      const errorMessage = errors.error || 'Erro ao adicionar aluno.';
      toast.error('Erro!', {
        description: errorMessage,
      });
    },
  });
};

const removeStudent = (studentId: number) => {
  form.student_id = studentId;

  form.delete(route('rooms.removeStudent', { room: props.room.id, student: studentId }), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      toast.success('Aluno removido com sucesso!', {
        description: 'O aluno foi desvinculado da sala.',
      });
      form.reset('student_id');
    },
    onError: (errors) => {
      const errorMessage = errors.error || 'Erro ao remover aluno.';
      toast.error('Erro!', {
        description: errorMessage,
      });
    },
  });
};

const submitSearch = (e: Event) => {
  e.preventDefault();
  router.get(route('rooms.editrooms', props.room.id), {
    search: searchQuery.value,
    filter: activeTab.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

watch(activeTab, (newTab) => {
  if (newTab) {
    router.get(route('rooms.editrooms', props.room.id), {
      filter: newTab,
      search: searchQuery.value
    }, {
      preserveScroll: true,
      preserveState: true,
      replace: true,
    });
  }
});

const getInitials = (name: string): string => {
  return name.split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const goBack = () => {
  router.get(route('rooms.index'));
};

const columns = [
  { key: 'name', label: 'NAME' },
  { key: 'email', label: 'EMAIL' },
  { key: 'actions', label: 'CODE/ACTIONS' }
];

const goToPage = (page: number) => {
  if (page < 1 || page > (props.students?.last_page || 1)) return;
  
  router.get(route('rooms.editrooms', props.room.id), {
    search: searchQuery.value,
    filter: activeTab.value,
    page: page
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};
</script>
<template>
  <Head :title="`Gerenciar Alunos - ${room.name_room}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 bg-[var(--sidebar-background)] m-4 rounded-lg">
      <h1 class="text-white text-2xl font-bold">
        Gerenciar Alunos na Sala: {{ room.name_room }} ({{ room.code }})
      </h1>
    </div>

    <div class="flex flex-wrap gap-4 items-center justify-between p-4">
      <form @submit.prevent="submitSearch" class="flex flex-wrap gap-2 items-center">
        <label for="search" class="font-bold text-white">Pesquisar Aluno (aba atual):</label>
        <Input v-model="searchQuery" placeholder="Digite o nome do aluno..." class="w-80" />
        <Button type="submit" class="bg-[var(--primary)]">
          <Search class="h-5 w-5" />
          Pesquisar
        </Button>
      </form>

      <div class="flex items-center">
        <Button @click="goBack" type="button" class="bg-[var(--primary)]">
          <ArrowLeft class="h-4 w-4 mr-2" />
          Voltar
        </Button>
      </div>
    </div>

    <Tabs v-model="activeTab" default-value="linked" class="w-full px-4">
      <TabsList class="grid w-full grid-cols-2 bg-gray-700/50">
        <TabsTrigger value="linked" class="data-[state=active]:bg-[var(--primary)] data-[state=active]:text-white">
          Alunos na Sala
        </TabsTrigger>
        <TabsTrigger value="unlinked" class="data-[state=active]:bg-[var(--primary)] data-[state=active]:text-white">
          Alunos Disponíveis
        </TabsTrigger>
      </TabsList>

      <!-- ABA LINKED -->
      <TabsContent value="linked" class="mt-4">
        <Card class="bg-[var(--sidebar-background)] border-none">
          <CardHeader>
            <CardTitle class="text-white text-xl font-semibold">
              Alunos Atualmente Vinculados ({{ totalLinked }})
            </CardTitle>
          </CardHeader>
          <CardContent class="p-0">
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow class="border-b border-gray-700 hover:bg-transparent">
                    <TableHead class="text-white font-semibold py-4 px-6">NAME</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6">EMAIL</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6">CÓDIGO</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6 text-center">AÇÃO</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow
                    v-for="student in students.data"
                    :key="student.id"
                    class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors"
                  >
                    <TableCell class="py-4 px-6">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[var(--primary)] rounded-full flex items-center justify-center text-white font-bold text-sm">
                          {{ getInitials(student.name) }}
                        </div>
                        <span class="text-white font-medium">{{ student.name }}</span>
                      </div>
                    </TableCell>
                    <TableCell class="py-4 px-6 text-white">{{ student.email }}</TableCell>
                    <TableCell class="py-4 px-6">
                      <span class="bg-[var(--secondary)] text-white text-xs px-3 py-1 rounded-full font-medium">
                        {{ student.cod }}
                      </span>
                    </TableCell>
                    <TableCell class="py-4 px-6 text-center">
                      <template v-if="activeTab === 'linked' && student.is_linked">
                        <Button
                          @click="removeStudent(student.id)"
                          class="bg-[var(--destructive)] hover:bg-[var(--destructive)]/80 text-white text-sm px-4 py-2 flex items-center justify-center gap-2"
                          :disabled="form.processing"
                        >
                          <Trash2 class="h-4 w-4" />
                          Remover
                        </Button>
                      </template>
                      <span v-else class="text-gray-500 text-sm">Vinculado</span>
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="students.data.length === 0">
                    <TableCell :colspan="4" class="text-center py-12 text-gray-400">
                      <div class="flex flex-col items-center gap-2">
                        <Search class="h-12 w-12 text-gray-500" />
                        <p class="text-lg font-medium">Nenhum estudante vinculado</p>
                        <p class="text-sm">Tente adicionar um aluno na aba ao lado.</p>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
            <!-- Paginação para aba Linked -->
            <div v-if="students.last_page > 1" class="flex justify-center py-4">
              <Pagination v-slot="{ page }" :items-per-page="10" :total="totalLinked" :default-page="students.current_page">
                <PaginationContent v-slot="{ items }">
                  <PaginationPrevious 
                    @click.prevent="goToPage(students.current_page - 1)"
                    :class="students.current_page <= 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                  />

                  <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                      v-if="item.type === 'page'"
                      :value="item.value"
                      :is-active="item.value === students.current_page"
                      @click.prevent="goToPage(item.value)"
                    >
                      {{ item.value }}
                    </PaginationItem>
                    <PaginationEllipsis v-else-if="item.type === 'ellipsis'" :index="index" class="text-white" />
                  </template>

                  <PaginationNext 
                    @click.prevent="goToPage(students.current_page + 1)"
                    :class="students.current_page >= students.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                  />
                </PaginationContent>
              </Pagination>
            </div>
          </CardContent>
        </Card>
      </TabsContent>

      <!-- ABA UNLINKED -->
      <TabsContent value="unlinked" class="mt-4">
        <Card class="bg-[var(--sidebar-background)] border-none">
          <CardHeader>
            <CardTitle class="text-white text-xl font-semibold">
              Alunos Não Vinculados ({{ totalUnlinked }})
            </CardTitle>
          </CardHeader>
          <CardContent class="p-0">
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow class="border-b border-gray-700 hover:bg-transparent">
                    <TableHead class="text-white font-semibold py-4 px-6">NAME</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6">EMAIL</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6">CÓDIGO</TableHead>
                    <TableHead class="text-white font-semibold py-4 px-6 text-center">AÇÃO</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow
                    v-for="student in students.data"
                    :key="student.id"
                    class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors"
                  >
                    <TableCell class="py-4 px-6">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[var(--primary)] rounded-full flex items-center justify-center text-white font-bold text-sm">
                          {{ getInitials(student.name) }}
                        </div>
                        <span class="text-white font-medium">{{ student.name }}</span>
                      </div>
                    </TableCell>
                    <TableCell class="py-4 px-6 text-white">{{ student.email }}</TableCell>
                    <TableCell class="py-4 px-6">
                      <span class="bg-[var(--secondary)] text-white text-xs px-3 py-1 rounded-full font-medium">
                        {{ student.cod }}
                      </span>
                    </TableCell>
                    <TableCell class="py-4 px-6 text-center">
                      <template v-if="activeTab === 'unlinked' && !student.is_linked">
                        <Button
                          @click="addStudent(student.id)"
                          class="bg-[var(--green_site)] hover:bg-[var(--green_site)]/80 text-white text-sm px-4 py-2 flex items-center justify-center gap-2"
                          :disabled="form.processing"
                        >
                          <Plus class="h-4 w-4" />
                          Adicionar
                        </Button>
                      </template>
                      <span v-else class="text-gray-500 text-sm">Disponível</span>
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="students.data.length === 0">
                    <TableCell :colspan="4" class="text-center py-12 text-gray-400">
                      <div class="flex flex-col items-center gap-2">
                        <Search class="h-12 w-12 text-gray-500" />
                        <p class="text-lg font-medium">Nenhum estudante disponível</p>
                        <p class="text-sm">Todos os alunos estão vinculados ou tente ajustar sua pesquisa.</p>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
            <!-- Paginação -->
            <div v-if="students.last_page > 1" class="flex justify-center py-4">
              <Pagination v-slot="{ page }" :items-per-page="10" :total="totalUnlinked" :default-page="students.current_page">
                <PaginationContent v-slot="{ items }">
                  <PaginationPrevious 
                    @click.prevent="goToPage(students.current_page - 1)"
                    :class="students.current_page <= 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                  />

                  <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                      v-if="item.type === 'page'"
                      :value="item.value"
                      :is-active="item.value === students.current_page"
                      @click.prevent="goToPage(item.value)"
                    >
                      {{ item.value }}
                    </PaginationItem>
                    <PaginationEllipsis v-else-if="item.type === 'ellipsis'" :index="index" class="text-white" />
                  </template>

                  <PaginationNext 
                    @click.prevent="goToPage(students.current_page + 1)"
                    :class="students.current_page >= students.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'"
                  />
                </PaginationContent>
              </Pagination>
            </div>
          </CardContent>
        </Card>
      </TabsContent>
    </Tabs>
  </AppLayout>
</template>