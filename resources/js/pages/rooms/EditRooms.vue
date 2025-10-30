// resources/js/Pages/rooms/EditRooms.vue
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, Plus, Trash2 } from 'lucide-vue-next'; // Importe Plus e Trash2
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

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
  students: {
    type: Array as () => Array<{
      id: number;
      name: string;
      email: string;
      cod: string;
      room_id: number | null;
      is_linked: boolean; // Flag adicionada no controller
    }>,
    required: true,
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
// Formulário Inertia para o POST/DELETE. Adicionamos 'student_id' como uma chave de dado que será populada dinamicamente.
const form = useForm({
    student_id: null as number | null,
});

watch(() => props.search, (newVal) => {
  searchQuery.value = newVal;
}, { immediate: true });

// ----------------------------------------------------------------
// Funções de Ação
// ----------------------------------------------------------------

const addStudent = (studentId: number) => {
    form.student_id = studentId;

    form.post(route('rooms.addStudent', props.room.id), {
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
            toast.success('Aluno removido!', {
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
  // Atenção: Aqui usamos 'rooms.editrooms' (minúsculo) conforme a correção de rota.
  router.get(route('rooms.editrooms', props.room.id), { search: searchQuery.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
};

const getInitials = (name: string): string => {
  return name.split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

// 'columns' não está sendo usado no template, mas pode ser útil para futuras refatorações
const columns = [
  { key: 'name', label: 'NAME' },
  { key: 'email', label: 'EMAIL' },
  { key: 'actions', label: 'CODE/ACTIONS' }
];
</script>

<template>
  <Head :title="`Gerenciar Alunos - ${room.name_room}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 bg-[var(--sidebar-background)] m-4 rounded-lg">
        <h1 class="text-white text-2xl font-bold">Gerenciar Alunos na Sala: {{ room.name_room }} ({{ room.code }})</h1>
    </div>

    <div class="flex flex-wrap gap-4 items-center justify-between p-4">
      <form @submit.prevent="submitSearch" class="flex flex-wrap gap-2 items-center">
        <label for="search" class="font-bold text-white">Pesquisar Aluno (disponível ou vinculado):</label>
        <Input v-model="searchQuery" placeholder="Digite o nome do aluno..." class="w-80" />
        <Button type="submit" class="bg-[var(--primary)]">
          <Search class="h-5 w-5" />
          Pesquisar
        </Button>
      </form>
    </div>

    <Card class="bg-[var(--sidebar-background)] border-none m-4">
      <CardHeader>
        <CardTitle class="text-white text-xl font-semibold">Lista de Estudantes (Disponíveis ou Vinculados)</CardTitle>
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
              <TableRow v-for="student in students" :key="student.id"
                class="border-b border-gray-700 hover:bg-[var(--secondary)]/30 transition-colors">
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
                  <span
                    class="bg-[var(--secondary)] text-white text-xs px-3 py-1 rounded-full font-medium">
                    {{ student.cod }}
                  </span>
                </TableCell>
                <TableCell class="py-4 px-6 text-center">
                  <template v-if="student.is_linked">
                      <Button 
                          @click="removeStudent(student.id)"
                          class="bg-[var(--destructive)] hover:bg-[var(--destructive)]/80 text-white text-sm px-4 py-2 flex items-center justify-center gap-2"
                          :disabled="form.processing"
                      >
                          <Trash2 class="h-4 w-4" />
                          Remover
                      </Button>
                  </template>
                  <template v-else>
                      <Button
                          @click="addStudent(student.id)"
                          class="bg-[var(--green_site)] hover:bg-[var(--green_site)]/80 text-black text-sm px-4 py-2 flex items-center justify-center gap-2"
                          :disabled="form.processing"
                      >
                          <Plus class="h-4 w-4" />
                          Adicionar
                      </Button>
                  </template>
                </TableCell>
              </TableRow>

              <TableRow v-if="students.length === 0">
                <TableCell :colspan="4" class="text-center py-12 text-gray-400">
                  <div class="flex flex-col items-center gap-2">
                    <Search class="h-12 w-12 text-gray-500" />
                    <p class="text-lg font-medium">Nenhum estudante encontrado</p>
                    <p class="text-sm">Tente ajustar sua pesquisa.</p>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </CardContent>
    </Card>

  </AppLayout>
</template>