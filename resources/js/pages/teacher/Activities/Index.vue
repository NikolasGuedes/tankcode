<script setup lang="ts">
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
  Search,
  Plus,
  Trash2,
} from 'lucide-vue-next'

type Difficulty = 'Fácil' | 'Médio' | 'Difícil'
type Status = 'Publicado' | 'Rascunho'
type OptionKey = 'A' | 'B' | 'C' | 'D'

interface ActivityQuestion {
  id: number
  statement: string
  correct: OptionKey
  options: Record<OptionKey, string>
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
    href: '/challenger',
  },
]

const search = ref('')
const isDialogOpen = ref(false)
const step = ref(1)
const mode = ref<'create' | 'edit'>('create')
const previewQuestion = ref(0)
const activeQuestionIndex = ref(0)

const activities = ref<ActivityItem[]>([])

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
      ? [...activity.questionData]
      : [
          {
            id: 1,
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
    const allOptionsHaveText = (['A', 'B', 'C', 'D'] as OptionKey[]).every(
      (option) => question.options[option]?.trim() !== ''
    )
    return hasStatement && allOptionsHaveText
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
</script>

<template>
  <Head title="Atividades" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 md:p-6">
      <div class="rounded-3xl border border-white/10 bg-[var(--sidebar-background)] overflow-hidden">
        <div class="border-b border-white/5 px-6 py-5 flex items-center justify-between gap-4 flex-wrap">
          <h1 class="text-xl font-semibold text-white">Atividades</h1>

          <Button
            type="button"
            class="cursor-pointer bg-[var(--primary)] hover:bg-[var(--primary)]/90 text-white"
            @click="openCreateDialog"
          >
            <Plus class="h-4 w-4" />
            Criar atividade
          </Button>
        </div>

        <div class="p-6 space-y-5">
          <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
              <span class="text-white font-semibold">Pesquisar atividade:</span>

              <Input
                v-model="search"
                placeholder="Digite o nome da atividade..."
                class="w-full sm:w-[360px]"
              />

              <Button
                type="button"
                class="cursor-pointer bg-[var(--primary)] hover:bg-[var(--primary)]/90"
              >
                <Search class="h-4 w-4" />
                Pesquisar
              </Button>
            </div>
          </div>

          <div class="rounded-3xl bg-[#0d0830] border border-white/5 overflow-hidden">
            <div class="px-6 py-5 border-b border-white/5 flex items-center justify-between">
              <h2 class="text-2xl font-semibold text-white">Lista de Atividades</h2>
              <span class="text-sm text-white/45">{{ filteredActivities.length }} atividades</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full min-w-[980px]">
                <thead>
                  <tr class="text-left text-xs text-white/45 uppercase tracking-[0.16em]">
                    <th class="px-6 py-4 font-medium">Nome</th>
                    <th class="px-6 py-4 font-medium">Sala</th>
                    <th class="px-6 py-4 font-medium">Questões</th>
                    <th class="px-6 py-4 font-medium">Dificuldade</th>
                    <th class="px-6 py-4 font-medium">Linguagem</th>
                    <th class="px-6 py-4 font-medium text-center">Ações</th>
                  </tr>
                </thead>

                <tbody>
                  <tr
                    v-for="activity in filteredActivities"
                    :key="activity.id"
                    class="border-t border-white/5 hover:bg-white/[0.03] transition"
                  >
                    <td class="px-6 py-5">
                      <div class="flex items-center gap-3">
                        <div
                          class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#6f5bff] to-[#383df5] flex items-center justify-center text-sm font-bold text-white shadow-lg shadow-[#4b4dff]/25"
                        >
                          {{ getInitials(activity.title) }}
                        </div>

                        <div>
                          <div class="font-semibold text-white">{{ activity.title }}</div>
                          <div class="text-sm text-white/50 mt-1">Atividade de múltipla escolha</div>
                        </div>
                      </div>
                    </td>

                    <td class="px-6 py-5 text-white/85 font-medium">
                      {{ activity.room }}
                    </td>

                    <td class="px-6 py-5 text-white/85">
                      {{ activity.questions }}
                    </td>

                    <td class="px-6 py-5">
                      <span
                        class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                        :class="getDifficultyClass(activity.difficulty)"
                      >
                        {{ activity.difficulty }}
                      </span>
                    </td>

                    <td class="px-6 py-5 text-white/85 font-medium">
                      {{ activity.language || 'Não definida' }}
                    </td>

                    <td class="px-6 py-5">
                      <div class="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          class="rounded-xl bg-[#2d2ff8] px-4 py-2 text-sm font-medium text-white hover:opacity-95"
                          @click="openEditDialog(activity)"
                        >
                          Editar
                        </button>

                        <button
                          type="button"
                          class="rounded-xl border border-red-500/20 bg-red-500/5 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/10"
                          @click="deleteActivity(activity.id)"
                        >
                          <Trash2 class="h-4 w-4" />
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="filteredActivities.length === 0">
                    <td colspan="6" class="px-6 py-14 text-center text-white/45">
                      Nenhuma atividade encontrada.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <Dialog v-model:open="isDialogOpen">
        <DialogContent class="bg-[#090612] border border-[#2a2169] text-white min-w-[95vw] lg:min-w-[1100px] p-0 flex flex-col max-h-[90vh]">
          <DialogHeader class="px-6 py-5 border-b border-white/5 flex-shrink-0">
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

          <div class="px-6 pt-5 flex-shrink-0">
            <div class="grid grid-cols-3 gap-3">
              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 1
                  ? 'border-[#5b4dff] bg-[#5b4dff]/15 text-white'
                  : step > 1
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                1. Dados da atividade
              </div>

              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 2
                  ? 'border-[#5b4dff] bg-[#5b4dff]/15 text-white'
                  : step > 2
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                2. Adicionar questões
              </div>

              <div
                class="rounded-2xl border px-4 py-3 text-sm font-medium"
                :class="step === 3
                   ? 'border-[#5b4dff] bg-[#5b4dff]/15 text-white'
                  : step > 2
                    ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300'
                    : 'border-white/10 bg-white/[0.03] text-white/45'"
              >
                3. Revisão final
              </div>
            </div>
          </div>

          <div class="flex-1 overflow-auto p-6">
            <div v-if="step === 1" class="space-y-5">
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="text-sm text-white/70 block mb-2">Nome da atividade</label>
                  <input
                    v-model="activityForm.title"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
                    placeholder="Ex: Avaliação de SQL"
                  />
                </div>

                <div>
                  <label class="text-sm text-white/70 block mb-2">Sala</label>
                  <input
                    v-model="activityForm.room"
                    class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
                    placeholder="Ex: Sala 1A"
                  />
                </div>
              </div>

              <div>
                <label class="text-sm text-white/70 block mb-2">Linguagem</label>
                <input
                  v-model="activityForm.language"
                  class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
                  placeholder="Ex: SQL, Python, C#"
                />
              </div>

              <div>
                <label class="text-sm text-white/70 block mb-2">Dificuldade da atividade</label>

                <div class="grid grid-cols-3 gap-3">
                  <button
                    v-for="level in (['Fácil', 'Médio', 'Difícil'] as Difficulty[])"
                    :key="level"
                    type="button"
                    class="rounded-xl border px-4 py-3 text-sm font-medium transition"
                    :class="activityForm.difficulty === level
                      ? 'border-[#5b4dff] bg-[#5b4dff]/20 text-white'
                      : getDifficultyClass(level)"
                    @click="activityForm.difficulty = level"
                  >
                    {{ level }}
                  </button>
                </div>
              </div>

              <div>
                <label class="text-sm text-white/70 block mb-2">Descrição</label>
                <textarea
                  v-model="activityForm.description"
                  class="w-full min-h-[140px] rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
                  placeholder="Descreva rapidamente a proposta da atividade."
                />
              </div>
            </div>

            <div v-else-if="step === 2" class="grid lg:grid-cols-[280px_1fr] gap-5">
              <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 h-fit">
                <div class="flex items-center justify-between gap-2 mb-4">
                  <h4 class="text-lg font-semibold">Questões</h4>

                  <button
                    type="button"
                    class="rounded-xl bg-[var(--primary)] px-3 py-2 text-xs font-semibold text-white hover:bg-[var(--primary)]/90"
                    @click="addQuestion"
                  >
                    + Nova
                  </button>
                </div>

                <div class="space-y-2">
                  <div
                    v-for="(question, index) in activityForm.questions"
                    :key="question.id"
                    :class="activeQuestionIndex === index
                      ? 'rounded-xl border border-[#5b4dff] bg-[#5b4dff]/15 text-white'
                      : 'rounded-xl border border-white/10 bg-black/20 text-white/70 hover:bg-white/[0.04]'"
                    class="cursor-pointer"
                    @click="selectQuestion(index)"
                  >
                    <div class="px-4 py-3 flex items-start justify-between gap-3">
                      <div class="min-w-0">
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

              <div v-if="currentQuestion" class="space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-3">
                  <div>
                    <h4 class="text-xl font-semibold">Questão {{ activeQuestionIndex + 1 }}</h4>
                    <p class="text-sm text-white/45 mt-1">
                      Questão de assinalar com alternativas A, B, C e D.
                    </p>
                  </div>
                </div>

                <div>
                  <label class="text-sm text-white/70 block mb-2">Enunciado</label>
                  <textarea
                    v-model="currentQuestion.statement"
                    class="w-full min-h-[120px] rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
                    placeholder="Digite o enunciado da questão."
                  />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                  <div
                    v-for="option in (['A', 'B', 'C', 'D'] as OptionKey[])"
                    :key="option"
                    class="rounded-2xl border p-4"
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

                      <div class="flex-1">
                        <div class="text-sm font-semibold text-white/85 mb-2">
                          Alternativa {{ option }}
                        </div>

                        <input
                          v-model="currentQuestion.options[option]"
                          class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm outline-none focus:border-[#5b4dff]"
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
                    <span class="text-white">Múltipla escolha</span>
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
                        ? 'border-[#5b4dff] bg-[#5b4dff] text-white'
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

                  <div class="mt-4 space-y-3">
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
                        activityForm.questions[previewQuestion]?.options[option] ||
                        `Alternativa ${option}`
                      }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <DialogFooter class="px-6 py-5 border-t border-white/5 flex items-center justify-between gap-3 flex-shrink-0 bg-[#090612]">
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
                class="rounded-xl bg-gradient-to-r from-[#7c5cff] to-[#4b4dff] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#4b4dff]/25 hover:opacity-95 disabled:opacity-50 disabled:cursor-not-allowed"
                @click="nextStep"
              >
                Próximo
              </button>

              <button
                v-else
                type="button"
                class="rounded-xl bg-gradient-to-r from-[#7c5cff] to-[#4b4dff] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#4b4dff]/25 hover:opacity-95"
                @click="finishActivity"
              >
                {{ mode === 'edit' ? 'Editar atividade' : 'Finalizar atividade' }}
              </button>
            </div>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>