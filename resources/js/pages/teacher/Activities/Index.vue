<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  CircleDashed,
  ClipboardList,
  FileQuestion,
  ListChecks,
  Pencil,
  Search,
  Plus,
  Trash2,
} from 'lucide-vue-next'

type Difficulty = 'Fácil' | 'Médio' | 'Difícil'
type Status = 'Publicado' | 'Rascunho'
type OptionKey = 'A' | 'B' | 'C' | 'D'
type QuestionType = 'multiple_choice' | 'drag_drop' | 'matching'

interface ActivityQuestion {
  id: number
  type: QuestionType
  statement: string
  // Para múltipla escolha
  correct?: OptionKey
  options?: Record<OptionKey, string>
  // Para drag and drop (fill in the blanks)
  keywords?: string[] // Palavras disponíveis para arrastar
  blankAnswers?: Record<string, number> // blank_1 -> índice em keywords
  // Para matching
  leftColumn?: string[]
  rightColumn?: string[]
  pairs?: Record<string, string> // left -> right
}

interface ActivityItem {
  id: number
  title: string
  room: string
  questions: number
  difficulty: Difficulty
  status: Status
  language?: string
  description?: string
  questionData?: ActivityQuestion[]
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Atividades',
    href: '/teacher/activities',
  },
]

const search = ref('')
const isDialogOpen = ref(false)
const step = ref(1)
const mode = ref<'create' | 'edit'>('create')
const previewQuestion = ref(0)
const activeQuestionIndex = ref(0)

const activities = ref<ActivityItem[]>([])

const publishedActivities = computed(() =>
  activities.value.filter((activity) => activity.status === 'Publicado').length,
)

const draftActivities = computed(() =>
  activities.value.filter((activity) => activity.status === 'Rascunho').length,
)

const totalQuestions = computed(() =>
  activities.value.reduce((total, activity) => total + activity.questions, 0),
)

const activityForm = ref({
  id: 0,
  title: '',
  room: '',
  language: '',
  description: '',
  difficulty: 'Médio' as Difficulty,
  status: 'Rascunho' as Status,
  questions: [
    {
      id: 1,
      type: 'multiple_choice' as QuestionType,
      statement: '',
      correct: 'A' as OptionKey,
      options: {
        A: '',
        B: '',
        C: '',
        D: '',
      },
    },
  ] as ActivityQuestion[],
})

const filteredActivities = computed(() => {
  if (!search.value.trim()) return activities.value

  const term = search.value.toLowerCase()
  return activities.value.filter((activity) =>
    [activity.title, activity.room, activity.language ?? '', activity.difficulty]
      .join(' ')
      .toLowerCase()
      .includes(term),
  )
})

const currentQuestion = computed(() => {
  return activityForm.value.questions[activeQuestionIndex.value]
})

const resetForm = () => {
  activityForm.value = {
    id: 0,
    title: '',
    room: '',
    language: '',
    description: '',
    difficulty: 'Médio',
    status: 'Rascunho',
    questions: [
      {
        id: 1,
        type: 'multiple_choice',
        statement: '',
        correct: 'A',
        options: {
          A: '',
          B: '',
          C: '',
          D: '',
        },
      },
    ],
  }

  step.value = 1
  previewQuestion.value = 0
  activeQuestionIndex.value = 0
}

const openCreateDialog = () => {
  mode.value = 'create'
  resetForm()
  isDialogOpen.value = true
}

const openEditDialog = (activity: ActivityItem) => {
  mode.value = 'edit'

  activityForm.value = {
    id: activity.id,
    title: activity.title,
    room: activity.room,
    language: activity.language || '',
    description: activity.description || '',
    difficulty: activity.difficulty,
    status: activity.status,
    questions: activity.questionData && activity.questionData.length > 0
      ? activity.questionData.map(q => ({
          ...q,
          type: q.type || 'multiple_choice', // Compatibilidade com dados antigos
        }))
      : [
          {
            id: 1,
            type: 'multiple_choice',
            statement: '',
            correct: 'A',
            options: {
              A: '',
              B: '',
              C: '',
              D: '',
            },
          },
        ],
  }

  step.value = 1
  previewQuestion.value = 0
  activeQuestionIndex.value = 0
  isDialogOpen.value = true
}

const nextStep = () => {
  if (step.value < 3) step.value++
}

const prevStep = () => {
  if (step.value > 1) step.value--
}

const addQuestion = () => {
  activityForm.value.questions.push({
    id: Date.now(),
    type: 'multiple_choice',
    statement: '',
    correct: 'A',
    options: {
      A: '',
      B: '',
      C: '',
      D: '',
    },
  })

  activeQuestionIndex.value = activityForm.value.questions.length - 1
}

const selectQuestion = (index: number) => {
  activeQuestionIndex.value = index
}

const setCorrectOption = (key: OptionKey) => {
  activityForm.value.questions[activeQuestionIndex.value].correct = key
}

const changeQuestionType = (newType: QuestionType) => {
  // Atualiza o tipo da questão atual e reseta apenas os campos específicos
  // para o novo tipo, preservando dados compatíveis.
  const question = activityForm.value.questions[activeQuestionIndex.value]
  question.type = newType
  question.statement = question.statement || '' // Manter enunciado

  // Resetar campos específicos baseado no tipo
  if (newType === 'multiple_choice') {
    question.correct = question.correct || 'A'
    question.options = question.options || { A: '', B: '', C: '', D: '' }
    delete question.items
    delete question.zones
    delete question.mapping
    delete question.leftColumn
    delete question.rightColumn
    delete question.pairs
  } else if (newType === 'drag_drop') {
    question.keywords = question.keywords || ['', '']
    question.blankAnswers = question.blankAnswers || { blank_1: 0 }
    delete question.correct
    delete question.options
    delete question.leftColumn
    delete question.rightColumn
    delete question.pairs
  } else if (newType === 'matching') {
    question.leftColumn = question.leftColumn || ['', '']
    question.rightColumn = question.rightColumn || ['', '']
    question.pairs = question.pairs || {}
    delete question.correct
    delete question.options
    delete question.items
    delete question.zones
    delete question.mapping
  }
}

const deleteQuestion = (index: number) => {
  if (activityForm.value.questions.length > 1) {
    activityForm.value.questions.splice(index, 1)
    if (activeQuestionIndex.value >= activityForm.value.questions.length) {
      activeQuestionIndex.value = activityForm.value.questions.length - 1
    }
  }
}

const isStep1Valid = () => {
  return (
    activityForm.value.title.trim() !== '' &&
    activityForm.value.room.trim() !== ''
  )
}

const isStep2Valid = () => {
  if (!activityForm.value.questions.length) return false

  return activityForm.value.questions.every((question) => {
    const hasStatement = question.statement.trim() !== ''

    if (question.type === 'multiple_choice') {
      const allOptionsHaveText = question.options && (['A', 'B', 'C', 'D'] as OptionKey[]).every(
        (option) => question.options![option]?.trim() !== ''
      )
      return hasStatement && allOptionsHaveText
    } else if (question.type === 'drag_drop') {
      const hasKeywords = question.keywords && question.keywords.length > 0 && question.keywords.every(kw => kw.trim() !== '')
      const hasBlankAnswers = question.blankAnswers && Object.keys(question.blankAnswers).length > 0
      return hasStatement && hasKeywords && hasBlankAnswers
    } else if (question.type === 'matching') {
      const hasLeft = question.leftColumn && question.leftColumn.length > 0 && question.leftColumn.every(item => item.trim() !== '')
      const hasRight = question.rightColumn && question.rightColumn.length > 0 && question.rightColumn.every(item => item.trim() !== '')
      const hasPairs = question.pairs && Object.keys(question.pairs).length > 0
      return hasStatement && hasLeft && hasRight && hasPairs
    }

    return false
  })
}

const finishActivity = () => {
  if (mode.value === 'create') {
    activities.value.unshift({
      id: Date.now(),
      title: activityForm.value.title || 'Nova atividade',
      room: activityForm.value.room || 'Sem sala',
      questions: activityForm.value.questions.length,
      difficulty: activityForm.value.difficulty,
      status: activityForm.value.status,
      language: activityForm.value.language || '',
      description: activityForm.value.description || '',
      questionData: [...activityForm.value.questions],
    })
  } else if (mode.value === 'edit') {
    const index = activities.value.findIndex(activity => activity.id === activityForm.value.id)
    if (index !== -1) {
      activities.value[index] = {
        ...activities.value[index],
        title: activityForm.value.title || 'Nova atividade',
        room: activityForm.value.room || 'Sem sala',
        questions: activityForm.value.questions.length,
        difficulty: activityForm.value.difficulty,
        status: activityForm.value.status,
        language: activityForm.value.language || '',
        description: activityForm.value.description || '',
        questionData: [...activityForm.value.questions],
      }
    }
  }

  isDialogOpen.value = false
}

const deleteActivity = (activityId: number) => {
  const index = activities.value.findIndex(activity => activity.id === activityId)
  if (index !== -1) {
    activities.value.splice(index, 1)
  }
}

const getInitials = (text: string) => {
  return text
    .split(' ')
    .map((item) => item.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const getQuestionTypeLabel = (type: QuestionType) => {
  switch (type) {
    case 'multiple_choice':
      return 'Múltipla escolha'
    case 'drag_drop':
      return 'Arrastar e Soltar'
    case 'matching':
      return 'Relacionar Colunas'
    default:
      return 'Tipo desconhecido'
  }
}

// Helper para extrair todos os blanks do enunciado (ex: [blank_1], [blank_2])
const extractBlanksFromStatement = (statement: string): string[] => {
  const regex = /\[blank_(\d+)\]/g
  const blanks: string[] = []
  let match
  while ((match = regex.exec(statement)) !== null) {
    blanks.push(`blank_${match[1]}`)
  }
  return [...new Set(blanks)] // Remove duplicatas
}

// Helper para inicializar blankAnswers quando o statement muda
const initializeBlanks = (question: ActivityQuestion) => {
  const blanks = extractBlanksFromStatement(question.statement || '')
  const newBlankAnswers: Record<string, number> = {}
  blanks.forEach(blank => {
    // Preserva resposta anterior se existir, senão define como -1 (não respondido)
    newBlankAnswers[blank] = question.blankAnswers?.[blank] ?? -1
  })
  question.blankAnswers = newBlankAnswers
}

const getDifficultyClass = (difficulty: Difficulty) => {
  switch (difficulty) {
    case 'Fácil':
      return 'bg-emerald-500/15 text-emerald-300 border-emerald-400/20'
    case 'Médio':
      return 'bg-amber-500/15 text-amber-300 border-amber-400/20'
    case 'Difícil':
      return 'bg-rose-500/15 text-rose-300 border-rose-400/20'
    default:
      return 'bg-zinc-500/15 text-zinc-300 border-zinc-400/20'
  }
}

const getDifficultyButtonClass = (difficulty: Difficulty) => {
  return activityForm.value.difficulty === difficulty
    ? 'border-primary bg-primary text-white shadow-lg shadow-primary/25'
    : 'border-white/10 bg-white/[0.04] text-white/50 hover:border-secondary/50 hover:bg-secondary/10 hover:text-white'
}

const getStatusClass = (status: Status) => {
  return status === 'Publicado'
    ? 'bg-emerald-500/15 text-emerald-300 border-emerald-400/20'
    : 'bg-secondary/15 text-secondary border-secondary/30'
}
</script>

<template>
  <Head title="Atividades" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <section class="space-y-6 p-6">
      <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
          <div class="flex gap-4">
            <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-primary/15 text-secondary">
              <ClipboardList class="size-7" />
            </div>

            <div>
              <h1 class="text-4xl font-semibold tracking-tight text-white">Atividades</h1>
              <p class="mt-2 max-w-3xl text-lg text-white/70">
                Monte atividades para suas turmas, organize questões por dificuldade e acompanhe o que ainda está em rascunho.
              </p>
            </div>
          </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <div class="rounded-3xl border border-border bg-black/40 p-5">
            <div class="flex items-center justify-between gap-3">
              <p class="text-sm text-white/60">Total</p>
              <ClipboardList class="size-5 text-secondary" />
            </div>
            <p class="mt-2 text-4xl font-semibold text-white">{{ activities.length }}</p>
          </div>
          <div class="rounded-3xl border border-border bg-black/40 p-5">
            <div class="flex items-center justify-between gap-3">
              <p class="text-sm text-white/60">Publicadas</p>
              <ListChecks class="size-5 text-secondary" />
            </div>
            <p class="mt-2 text-4xl font-semibold text-white">{{ publishedActivities }}</p>
          </div>
          <div class="rounded-3xl border border-border bg-black/40 p-5">
            <div class="flex items-center justify-between gap-3">
              <p class="text-sm text-white/60">Rascunhos</p>
              <CircleDashed class="size-5 text-secondary" />
            </div>
            <p class="mt-2 text-4xl font-semibold text-white">{{ draftActivities }}</p>
          </div>
          <div class="rounded-3xl border border-border bg-black/40 p-5">
            <div class="flex items-center justify-between gap-3">
              <p class="text-sm text-white/60">Questões</p>
              <FileQuestion class="size-5 text-secondary" />
            </div>
            <p class="mt-2 text-4xl font-semibold text-white">{{ totalQuestions }}</p>
          </div>
        </div>
      </AppReveal>

      <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.08">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-white">Lista de atividades</h2>
            <p class="text-sm text-white/60">Crie, revise e edite atividades antes de publicar para as turmas.</p>
          </div>

          <Button
            type="button"
            class="rounded-2xl !bg-primary !text-white hover:!bg-[var(--primary-hover)]"
            @click="openCreateDialog"
          >
            <Plus class="size-4" />
            Criar atividade
          </Button>
        </div>

        <form class="mb-6 grid gap-3 rounded-3xl border border-white/10 bg-black/20 p-4 md:grid-cols-[minmax(0,1fr)_auto]" @submit.prevent>
          <div class="relative">
            <Search class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-white/40" />
            <Input
              v-model="search"
              placeholder="Buscar por nome, sala, linguagem ou dificuldade"
              class="border-white/10 bg-[var(--surface-elevated)] pl-10 text-white placeholder:text-white/35"
            />
          </div>

          <Button
            type="button"
            variant="outline"
            class="rounded-2xl border-white/10 bg-[var(--surface-elevated)] text-white hover:border-secondary hover:bg-[var(--accent-hover)]"
            @click="search = ''"
          >
            Limpar
          </Button>
        </form>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-border">
            <thead>
              <tr class="text-left text-sm text-secondary">
                <th class="pb-4 font-medium">Atividade</th>
                <th class="pb-4 font-medium">Sala</th>
                <th class="pb-4 font-medium">Questões</th>
                <th class="pb-4 font-medium">Dificuldade</th>
                <th class="pb-4 font-medium">Status</th>
                <th class="pb-4 font-medium">Linguagem</th>
                <th class="pb-4 text-right font-medium">Ações</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-white/5 text-sm text-white/75">
              <tr
                v-for="activity in filteredActivities"
                :key="activity.id"
                class="transition hover:bg-white/[0.03]"
              >
                <td class="py-4 pr-6">
                  <div class="flex items-center gap-3">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-primary/15 text-sm font-bold text-secondary">
                      {{ getInitials(activity.title) }}
                    </div>

                    <div class="min-w-0">
                      <p class="truncate font-semibold text-white">{{ activity.title }}</p>
                      <p class="mt-1 truncate text-sm text-white/50">
                        {{ getQuestionTypeLabel(activity.questionData?.[0]?.type || 'multiple_choice') }}
                      </p>
                    </div>
                  </div>
                </td>

                <td class="py-4 pr-6 font-medium text-white/85">
                  {{ activity.room }}
                </td>

                <td class="py-4 pr-6 text-white/85">
                  {{ activity.questions }}
                </td>

                <td class="py-4 pr-6">
                  <span
                    class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                    :class="getDifficultyClass(activity.difficulty)"
                  >
                    {{ activity.difficulty }}
                  </span>
                </td>

                <td class="py-4 pr-6">
                  <span
                    class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                    :class="getStatusClass(activity.status)"
                  >
                    {{ activity.status }}
                  </span>
                </td>

                <td class="py-4 pr-6 font-medium text-white/85">
                  {{ activity.language || 'Não definida' }}
                </td>

                <td class="py-4">
                  <div class="flex justify-end gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="icon-sm"
                      class="!border-primary !bg-primary !text-white hover:!border-[var(--primary-hover)] hover:!bg-[var(--primary-hover)]"
                      title="Editar atividade"
                      @click="openEditDialog(activity)"
                    >
                      <Pencil class="size-4" />
                    </Button>

                    <Button
                      type="button"
                      variant="outline"
                      size="icon-sm"
                      class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]"
                      title="Excluir atividade"
                      @click="deleteActivity(activity.id)"
                    >
                      <Trash2 class="size-4" />
                    </Button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredActivities.length === 0">
                <td colspan="7" class="py-14 text-center">
                  <div class="mx-auto flex max-w-sm flex-col items-center">
                    <FileQuestion class="size-10 text-secondary" />
                    <p class="mt-4 font-semibold text-white">Nenhuma atividade encontrada</p>
                    <p class="mt-1 text-sm text-white/55">Crie uma atividade ou ajuste a busca para visualizar resultados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </AppReveal>

      <Dialog v-model:open="isDialogOpen">
        <DialogContent class="flex max-h-[94vh] w-[96vw] max-w-[1400px] flex-col overflow-hidden border border-border bg-card p-0 text-white sm:max-h-[94vh] sm:max-w-[1400px]">
          <DialogHeader class="flex-shrink-0 border-b border-white/5 px-5 py-5 sm:px-6">
            <div class="flex items-center justify-between gap-4">
              <div>
                <DialogTitle class="text-2xl font-semibold text-white">
                  {{ mode === 'edit' ? 'Editar atividade' : 'Criar atividade' }}
                </DialogTitle>
                <p class="text-sm text-white/45 mt-1">
                  Cadastre a atividade em etapas, adicione questões e revise antes de salvar.
                </p>
              </div>
            </div>
          </DialogHeader>

          <div class="flex-shrink-0 px-5 pt-5 sm:px-6">
            <div class="grid gap-3 md:grid-cols-3">
              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 1
                  ? 'border-secondary bg-secondary/15 text-white'
                  : step > 1
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                1. Dados da atividade
              </div>

              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 2
                  ? 'border-secondary bg-secondary/15 text-white'
                  : step > 2
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                2. Adicionar questões
              </div>

              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 3
                   ? 'border-secondary bg-secondary/15 text-white'
                  : step > 2
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                3. Revisão final
              </div>
            </div>
          </div>

          <div class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto p-5 sm:p-6">
            <div v-if="step === 1" class="space-y-5">
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="text-sm text-white/70 block mb-2">Nome da atividade</label>
                  <input
                    v-model="activityForm.title"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                    placeholder="Ex: Avaliação de SQL"
                  />
                </div>

                <div>
                  <label class="text-sm text-white/70 block mb-2">Sala</label>
                  <input
                    v-model="activityForm.room"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                    placeholder="Ex: Sala 1A"
                  />
                </div>
              </div>

              <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_420px]">
                <div class="min-w-0">
                  <label class="text-sm text-white/70 block mb-2">Linguagem</label>
                  <input
                    v-model="activityForm.language"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                    placeholder="Ex: SQL, Python, C#"
                  />
                </div>

                <div class="min-w-0">
                  <label class="text-sm text-white/70 block mb-2">Dificuldade da atividade</label>

                  <div class="grid grid-cols-3 gap-3">
                    <button
                      v-for="level in (['Fácil', 'Médio', 'Difícil'] as Difficulty[])"
                      :key="level"
                      type="button"
                      class="rounded-xl border px-4 py-3 text-sm font-medium transition"
                      :class="getDifficultyButtonClass(level)"
                      @click="activityForm.difficulty = level"
                    >
                      {{ level }}
                    </button>
                  </div>
                </div>
              </div>

              <div>
                <label class="text-sm text-white/70 block mb-2">Descrição</label>
                <textarea
                  v-model="activityForm.description"
                  class="w-full min-h-[140px] rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                  placeholder="Descreva rapidamente a proposta da atividade."
                />
              </div>
            </div>

            <div v-else-if="step === 2" class="grid min-w-0 gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
              <div class="min-w-0 rounded-2xl border border-white/10 bg-black/30 p-4 lg:self-start">
                <div class="flex items-center justify-between gap-2 mb-4">
                  <h4 class="text-lg font-semibold">Questões</h4>

                  <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-[var(--primary-hover)]"
                    @click="addQuestion"
                  >
                    <Plus class="size-3.5" />
                    Nova
                  </button>
                </div>

                <div class="space-y-2">
                  <div
                    v-for="(question, index) in activityForm.questions"
                    :key="question.id"
                    :class="activeQuestionIndex === index
                      ? 'rounded-xl border border-secondary bg-secondary/15 text-white'
                      : 'rounded-xl border border-white/10 bg-black/20 text-white/70 hover:bg-white/[0.04]'"
                    class="cursor-pointer"
                    @click="selectQuestion(index)"
                  >
                    <div class="px-4 py-3 flex items-start justify-between gap-3">
                      <div class="min-w-0 flex-1">
                        <div class="font-medium">Questão {{ index + 1 }}</div>
                        <div class="text-xs text-white/45 truncate mt-1">
                          {{ question.statement || 'Sem enunciado ainda' }}
                        </div>
                      </div>

                      <button
                        v-if="activityForm.questions.length > 1"
                        type="button"
                        class="rounded-lg border border-red-500/20 bg-red-500/5 p-2 text-red-300 hover:bg-red-500/10"
                        @click.stop="deleteQuestion(index)"
                        title="Deletar questão"
                      >
                        <Trash2 class="h-4 w-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="currentQuestion" class="min-w-0 space-y-5 rounded-2xl border border-white/10 bg-black/20 p-4 sm:p-5">
                <div class="flex items-center justify-between flex-wrap gap-3">
                  <div class="min-w-0">
                    <h4 class="text-xl font-semibold">Questão {{ activeQuestionIndex + 1 }}</h4>
                    <p class="text-sm text-white/45 mt-1">
                      {{ currentQuestion.type === 'multiple_choice'
                        ? 'Questão de assinalar com alternativas A, B, C e D.'
                        : currentQuestion.type === 'drag_drop'
                          ? 'Questão Arrastar e Soltar: defina itens, zonas e mapeamentos.'
                          : 'Questão Relacionar Colunas: defina pares entre as colunas esquerda e direita.'
                      }}
                    </p>
                  </div>
                </div>

                <!-- Novo seletor de tipo de questão -->
                <div>
                  <label class="text-sm text-white/70 block mb-2">Tipo de questão</label>
                  <select
                    v-model="currentQuestion.type"
                    @change="changeQuestionType(currentQuestion.type)"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                  >
                    <option value="multiple_choice">Múltipla Escolha</option>
                    <option value="drag_drop">Arrastar e Soltar</option>
                    <option value="matching">Relacionar Colunas</option>
                  </select>
                </div>

                <div>
                  <label class="text-sm text-white/70 block mb-2">Enunciado</label>
                  <textarea
                    v-model="currentQuestion.statement"
                    class="w-full min-h-[120px] rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                    placeholder="Digite o enunciado da questão."
                  />
                </div>

                <!-- Múltipla Escolha -->
                <div v-if="currentQuestion.type === 'multiple_choice'" class="grid md:grid-cols-2 gap-4">
                  <div
                    v-for="option in (['A', 'B', 'C', 'D'] as OptionKey[])"
                    :key="option"
                    class="min-w-0 rounded-2xl border p-4"
                    :class="currentQuestion.correct === option
                      ? 'border-emerald-400/30 bg-emerald-500/10'
                      : 'border-white/10 bg-white/[0.03]'"
                  >
                    <div class="flex items-start gap-3">
                      <button
                        type="button"
                        class="mt-1 flex h-5 w-5 items-center justify-center rounded-full border text-[10px] font-bold cursor-pointer"
                        :class="currentQuestion.correct === option
                          ? 'border-emerald-400 bg-emerald-400 text-black'
                          : 'border-white/20 text-white/40'"
                        @click="setCorrectOption(option)"
                      >
                        <span v-if="currentQuestion.correct === option">●</span>
                      </button>

                      <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-white/85 mb-2">
                          Alternativa {{ option }}
                        </div>

                        <input
                          v-model="currentQuestion.options![option]"
                          class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                          :placeholder="`Digite a alternativa ${option}`"
                        />

                        <button
                          type="button"
                          class="mt-3 rounded-lg px-3 py-2 text-xs font-medium transition"
                          :class="currentQuestion.correct === option
                            ? 'bg-emerald-400 text-black'
                            : 'bg-white/5 text-white/60 hover:bg-white/10'"
                          @click="setCorrectOption(option)"
                        >
                          {{
                            currentQuestion.correct === option
                              ? 'Resposta correta selecionada'
                              : 'Marcar como correta'
                          }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Drag and Drop -->
                <div v-else-if="currentQuestion.type === 'drag_drop'" class="space-y-4">
                  <div class="rounded-2xl border border-secondary/20 bg-secondary/10 p-4 text-sm text-white/70">
                    <p class="font-semibold text-white">Formato com lacunas</p>
                    <p class="mt-2">
                      Use marcadores como <code class="rounded bg-black/30 px-2 py-1 text-white">[blank_1]</code> no enunciado e relacione cada lacuna a uma palavra-chave.
                    </p>
                  </div>

                  <div>
                    <label class="text-sm text-white/70 block mb-2">Enunciado (use [blank_1], [blank_2], etc)</label>
                    <textarea
                      v-model="currentQuestion.statement"
                      @input="initializeBlanks(currentQuestion)"
                      class="w-full min-h-[120px] rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                      placeholder="Ex: A capital do Brasil é [blank_1] e fica no estado de [blank_2]"
                    />
                  </div>

                  <div>
                    <label class="text-sm text-white/70 block mb-2">Palavras-chave (disponíveis para arrastar)</label>
                    <div class="space-y-2">
                      <div v-for="(keyword, index) in currentQuestion.keywords" :key="index" class="flex min-w-0 gap-2">
                        <input
                          v-model="currentQuestion.keywords![index]"
                          class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                          :placeholder="`Palavra-chave ${index + 1}`"
                        />
                        <button
                          v-if="currentQuestion.keywords!.length > 1"
                          type="button"
                          class="rounded-lg border border-red-500/20 bg-red-500/5 p-2 text-red-300 hover:bg-red-500/10"
                          @click="currentQuestion.keywords!.splice(index, 1)"
                        >
                          <Trash2 class="h-4 w-4" />
                        </button>
                      </div>
                      <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-[var(--primary-hover)]"
                        @click="currentQuestion.keywords!.push('')"
                      >
                        <Plus class="size-3.5" />
                        Adicionar palavra-chave
                      </button>
                    </div>
                  </div>

                  <!-- Mapeamento de blanks para palavras-chave -->
                  <div v-if="extractBlanksFromStatement(currentQuestion.statement || '').length > 0">
                    <label class="text-sm text-white/70 block mb-2">Defina qual palavra vai em cada espaço</label>
                    <div class="space-y-2">
                      <div v-for="blank in extractBlanksFromStatement(currentQuestion.statement || '')" :key="blank" class="grid min-w-0 gap-2 rounded-xl border border-white/10 bg-white/5 p-3 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-center">
                        <span class="text-sm text-white/85 font-medium">{{ blank }}:</span>
                        <select
                          v-model.number="currentQuestion.blankAnswers![blank]"
                          class="w-full min-w-0 rounded-xl border border-white/10 bg-black/30 px-4 py-2 text-sm outline-none focus:border-secondary"
                        >
                          <option :value="-1">Selecionar palavra-chave</option>
                          <option v-for="(keyword, idx) in currentQuestion.keywords" :key="idx" :value="idx">
                            {{ keyword || `Palavra ${idx + 1}` }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div v-else class="rounded-2xl border border-dashed border-amber-500/20 bg-amber-500/5 p-4 text-sm text-amber-300">
                    Adicione [blank_1], [blank_2], etc no enunciado para definir os espaços vazios.
                  </div>
                </div>

                <!-- Matching -->
                <div v-else-if="currentQuestion.type === 'matching'" class="space-y-4">
                  <div class="grid md:grid-cols-2 gap-4">
                    <div class="min-w-0">
                      <label class="text-sm text-white/70 block mb-2">Coluna Esquerda</label>
                      <div class="space-y-2">
                        <div v-for="(left, index) in currentQuestion.leftColumn" :key="index" class="flex min-w-0 gap-2">
                          <input
                            v-model="currentQuestion.leftColumn![index]"
                            class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                            :placeholder="`Item ${index + 1}`"
                          />
                          <button
                            v-if="currentQuestion.leftColumn!.length > 1"
                            type="button"
                            class="rounded-lg border border-red-500/20 bg-red-500/5 p-2 text-red-300 hover:bg-red-500/10"
                            @click="currentQuestion.leftColumn!.splice(index, 1)"
                          >
                            <Trash2 class="h-4 w-4" />
                          </button>
                        </div>
                        <button
                          type="button"
                          class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-[var(--primary-hover)]"
                          @click="currentQuestion.leftColumn!.push('')"
                        >
                          <Plus class="size-3.5" />
                          Adicionar item
                        </button>
                      </div>
                    </div>

                    <div class="min-w-0">
                      <label class="text-sm text-white/70 block mb-2">Coluna Direita</label>
                      <div class="space-y-2">
                        <div v-for="(right, index) in currentQuestion.rightColumn" :key="index" class="flex min-w-0 gap-2">
                          <input
                            v-model="currentQuestion.rightColumn![index]"
                            class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-secondary"
                            :placeholder="`Item ${index + 1}`"
                          />
                          <button
                            v-if="currentQuestion.rightColumn!.length > 1"
                            type="button"
                            class="rounded-lg border border-red-500/20 bg-red-500/5 p-2 text-red-300 hover:bg-red-500/10"
                            @click="currentQuestion.rightColumn!.splice(index, 1)"
                          >
                            <Trash2 class="h-4 w-4" />
                          </button>
                        </div>
                        <button
                          type="button"
                          class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-[var(--primary-hover)]"
                          @click="currentQuestion.rightColumn!.push('')"
                        >
                          <Plus class="size-3.5" />
                          Adicionar item
                        </button>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="text-sm text-white/70 block mb-2">Pares Corretos (Esquerda → Direita)</label>
                    <div class="space-y-2">
                      <div v-for="(left, leftIndex) in currentQuestion.leftColumn" :key="leftIndex" class="grid min-w-0 gap-2 rounded-xl border border-white/10 bg-white/5 p-3 sm:grid-cols-[minmax(0,1fr)_auto_minmax(220px,1fr)] sm:items-center">
                        <span class="min-w-0 truncate text-sm text-white/85">{{ left || `Item ${leftIndex + 1}` }}</span>
                        <span class="text-white/45">→</span>
                        <select
                          v-model="currentQuestion.pairs![leftIndex.toString()]"
                          class="w-full min-w-0 rounded-xl border border-white/10 bg-black/30 px-4 py-2 text-sm outline-none focus:border-secondary"
                        >
                          <option value="">Selecionar correspondente</option>
                          <option v-for="(right, rightIndex) in currentQuestion.rightColumn" :key="rightIndex" :value="rightIndex.toString()">
                            {{ right || `Item ${rightIndex + 1}` }}
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="rounded-2xl border border-dashed border-white/10 bg-white/[0.02] p-4 text-sm text-white/50">
                  Aqui o professor pode adicionar várias questões, sempre com A, B, C e D, escolhendo dinamicamente a alternativa correta.
                </div>
              </div>
            </div>

            <div v-else class="grid lg:grid-cols-[360px_1fr] gap-5">
              <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                <div class="text-lg font-semibold">Resumo da atividade</div>

                <div class="mt-4 space-y-3 text-sm text-white/65">
                  <div class="flex justify-between gap-4">
                    <span>Nome</span>
                    <span class="text-white">{{ activityForm.title || 'Sem nome' }}</span>
                  </div>

                  <div class="flex justify-between gap-4">
                    <span>Sala</span>
                    <span class="text-white">{{ activityForm.room || 'Sem sala' }}</span>
                  </div>

                  <div class="flex justify-between gap-4">
                    <span>Dificuldade</span>
                    <span class="text-white">{{ activityForm.difficulty }}</span>
                  </div>

                  <div class="flex justify-between gap-4">
                    <span>Linguagem</span>
                    <span class="text-white">{{ activityForm.language || 'Não definida' }}</span>
                  </div>

                  <div class="flex justify-between gap-4">
                    <span>Total de questões</span>
                    <span class="text-white">{{ activityForm.questions.length }}</span>
                  </div>

                  <div class="flex justify-between gap-4">
                    <span>Formato</span>
                    <span class="text-white">
                      {{ getQuestionTypeLabel(activityForm.questions[previewQuestion]?.type || 'multiple_choice') }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                  <div class="text-lg font-semibold">Prévia das questões</div>

                  <div class="flex items-center gap-2 flex-wrap">
                    <button
                      v-for="(question, index) in activityForm.questions"
                      :key="question.id"
                      type="button"
                      class="h-10 min-w-10 rounded-xl border px-3 text-sm font-semibold transition"
                      :class="previewQuestion === index
                        ? 'border-secondary bg-secondary text-white'
                        : 'border-white/10 bg-black/20 text-white/65 hover:bg-white/5'"
                      @click="previewQuestion = index"
                    >
                      {{ index + 1 }}
                    </button>
                  </div>
                </div>

                <div class="mt-5 rounded-2xl border border-white/10 bg-black/20 p-4">
                  <div class="text-xs uppercase tracking-[0.18em] text-white/40">
                    Questão {{ previewQuestion + 1 }}
                  </div>

                  <div class="mt-3 text-sm text-white/80">
                    {{ activityForm.questions[previewQuestion]?.statement || 'Sem enunciado' }}
                  </div>

                  <!-- Preview para múltipla escolha -->
                  <div v-if="activityForm.questions[previewQuestion]?.type === 'multiple_choice'" class="mt-4 space-y-3">
                    <div
                      v-for="option in (['A', 'B', 'C', 'D'] as OptionKey[])"
                      :key="option"
                      class="rounded-xl border px-4 py-3 text-sm"
                      :class="activityForm.questions[previewQuestion]?.correct === option
                        ? 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300'
                        : 'border-white/10 bg-black/20 text-white/70'"
                    >
                      {{ option }}.
                      {{
                        activityForm.questions[previewQuestion]?.options?.[option] ||
                        `Alternativa ${option}`
                      }}
                    </div>
                  </div>

                  <!-- Preview para drag and drop -->
                  <div v-else-if="activityForm.questions[previewQuestion]?.type === 'drag_drop'" class="mt-4 space-y-3">
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                      <div class="text-xs uppercase tracking-[0.18em] text-white/40 mb-3">Enunciado (com espaços vazios)</div>
                      <div class="text-sm text-white/80 leading-relaxed">
                        <template v-if="activityForm.questions[previewQuestion]?.statement">
                          {{ activityForm.questions[previewQuestion]!.statement.split(/(\[blank_\d+\])/).map((part, idx) => {
                            const match = part.match(/\[blank_(\d+)\]/)
                            if (match) {
                              const blankKey = `blank_${match[1]}`
                              const answerIdx = activityForm.questions[previewQuestion]?.blankAnswers?.[blankKey] ?? -1
                              const answerText = answerIdx >= 0 && activityForm.questions[previewQuestion]?.keywords
                                ? activityForm.questions[previewQuestion]!.keywords[answerIdx]
                                : '______'
                              return answerText
                            }
                            return part
                          }).join('') }}
                        </template>
                        <span v-else class="text-white/45">Sem enunciado</span>
                      </div>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                      <div class="text-xs uppercase tracking-[0.18em] text-white/40 mb-3">Palavras-chave disponíveis</div>
                      <div class="flex flex-wrap gap-2">
                        <span
                          v-for="(keyword, index) in activityForm.questions[previewQuestion]?.keywords"
                          :key="index"
                          class="rounded-lg border border-secondary/40 bg-secondary/20 px-3 py-1 text-sm text-white/85"
                        >
                          {{ keyword || `Palavra ${index + 1}` }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Preview para matching -->
                  <div v-else-if="activityForm.questions[previewQuestion]?.type === 'matching'" class="mt-4 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <div class="text-sm text-white/70 mb-2">Coluna Esquerda</div>
                        <div class="space-y-1">
                          <div
                            v-for="(left, index) in activityForm.questions[previewQuestion]?.leftColumn"
                            :key="index"
                            class="rounded-lg bg-white/10 px-3 py-1 text-sm text-white/85"
                          >
                            {{ left || `Item ${index + 1}` }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <div class="text-sm text-white/70 mb-2">Coluna Direita</div>
                        <div class="space-y-1">
                          <div
                            v-for="(right, index) in activityForm.questions[previewQuestion]?.rightColumn"
                            :key="index"
                            class="rounded-lg bg-white/10 px-3 py-1 text-sm text-white/85"
                          >
                            {{ right || `Item ${index + 1}` }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <DialogFooter class="flex flex-shrink-0 items-center justify-between gap-3 border-t border-white/5 bg-card px-5 py-5 sm:justify-between sm:px-6">
            <button
              type="button"
              class="rounded-xl border px-5 py-3 text-sm font-medium"
              :class="step === 1
                ? 'pointer-events-none border-white/5 bg-white/[0.02] text-white/25'
                : 'border-white/10 bg-white/5 text-white/75 hover:bg-white/10'"
              @click="prevStep"
            >
              Voltar
            </button>

            <div class="flex items-center gap-3">
              <button
                v-if="step < 3"
                type="button"
                :disabled="!isStep1Valid() || (step === 2 && !isStep2Valid())"
                class="rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-[var(--primary-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                @click="nextStep"
              >
                Próximo
              </button>

              <button
                v-else
                type="button"
                class="rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-[var(--primary-hover)]"
                @click="finishActivity"
              >
                {{ mode === 'edit' ? 'Editar atividade' : 'Finalizar atividade' }}
              </button>
            </div>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </section>
  </AppLayout>
</template>
