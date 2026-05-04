<script setup lang="ts">
import AppReveal from '@/components/AppReveal.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import StudentLayout from '@/layouts/StudentLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, CircleAlert, ClipboardCheck, Clock3 } from 'lucide-vue-next';

type ActivityQuestion = {
    id: number;
    order: number;
    type: 'multiple_choice' | 'drag_drop' | 'matching';
    statement: string;
    options: Record<string, string> | null;
    keywords: string[] | null;
    blank_keys: string[] | null;
    left_column: string[] | null;
    right_column: string[] | null;
    submitted_answer: {
        selected_option?: string;
        blanks?: Record<string, string>;
        pairs?: Record<string, string>;
    } | null;
    is_correct: boolean | null;
    earned_points: string | number | null;
    solution: {
        correct_option?: string;
        blanks?: Record<string, string>;
        pairs?: Record<string, { left: string; right: string }>;
    } | null;
};

type SubmissionAnswer = {
    question_id: number;
    selected_option?: string;
    blanks?: Record<string, string>;
    pairs?: Record<string, string>;
};

const props = defineProps<{
    activity: {
        id: number;
        title: string;
        description: string | null;
        total_points: string | number;
        points_per_question: string | number;
        questions_count: number;
        due_date: string | null;
        due_date_label: string;
        classroom: {
            name: string;
            code: string;
        };
        teacher: string;
        questions: ActivityQuestion[];
    };
    submission: {
        id: number;
        status: string;
        submitted_at: string | null;
        submitted_at_label: string | null;
        score: string | number;
        total_points: string | number;
        correct_answers_count: number;
        answers_count: number;
    } | null;
    can_submit: boolean;
    submit_url: string;
    back_url: string;
}>();

const formatPoints = (value: string | number | null | undefined) =>
    Number(value ?? 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

const multipleChoiceOptions = (question: ActivityQuestion) => Object.entries(question.options ?? {});
const matchingPairs = (question: ActivityQuestion) => (question.left_column ?? []).map((left, index) => ({ left, index }));
const blankLabel = (_blankKey: string, index: number) => `Lacuna ${index + 1}`;
const tokenPattern = /(\[blank_\d+\])/gi;

const initialAnswer = (question: ActivityQuestion): SubmissionAnswer => {
    if (question.type === 'multiple_choice') {
        return {
            question_id: question.id,
            selected_option: '',
        };
    }

    if (question.type === 'drag_drop') {
        return {
            question_id: question.id,
            blanks: Object.fromEntries((question.blank_keys ?? []).map((blankKey) => [blankKey, ''])),
        };
    }

    return {
        question_id: question.id,
        pairs: Object.fromEntries((question.left_column ?? []).map((_, index) => [String(index), ''])),
    };
};

const form = useForm({
    answers: props.activity.questions.map((question) => initialAnswer(question)),
});

const errorFor = (path: string) => (form.errors as Record<string, string | undefined>)[path];
const answerAt = (index: number) => form.answers[index] as SubmissionAnswer;

const selectOption = (index: number, option: string) => {
    if (!props.can_submit) {
        return;
    }

    answerAt(index).selected_option = option;
};

const submit = () => {
    form.post(props.submit_url, {
        preserveScroll: true,
    });
};

const submittedBlankLabel = (question: ActivityQuestion, blankKey: string) => {
    const submittedValue = String(question.submitted_answer?.blanks?.[blankKey] ?? '');

    return (question.keywords ?? []).find((keyword) => keyword.toLowerCase() === submittedValue) ?? submittedValue ?? '-';
};

const submittedMatchingLabel = (question: ActivityQuestion, leftIndex: number) => {
    const rightIndex = question.submitted_answer?.pairs?.[String(leftIndex)];

    if (rightIndex === undefined || rightIndex === null || rightIndex === '') {
        return '-';
    }

    return question.right_column?.[Number(rightIndex)] ?? '-';
};

const statementFragments = (question: ActivityQuestion) => {
    if (question.type !== 'drag_drop') {
        return [{ type: 'text', value: question.statement }];
    }

    return question.statement
        .split(tokenPattern)
        .filter(Boolean)
        .map((fragment) => {
            const matchedToken = fragment.match(/^\[(blank_\d+)\]$/i);

            if (matchedToken) {
                return {
                    type: 'blank',
                    value: matchedToken[1].toLowerCase(),
                };
            }

            return {
                type: 'text',
                value: fragment,
            };
        });
};

const inlineBlankValue = (question: ActivityQuestion, index: number, blankKey: string) => {
    if (props.can_submit) {
        const value = answerAt(index).blanks?.[blankKey];

        if (value === undefined || value === null || value === '') {
            return '';
        }

        return question.keywords?.[Number(value)] ?? '';
    }

    return submittedBlankLabel(question, blankKey);
};
</script>

<template>
    <Head :title="activity.title" />

    <StudentLayout>
        <section>
            <AppReveal class-name="relative overflow-hidden rounded-[2.2rem] border border-white/10 bg-[#271D67] p-6 shadow-[0_24px_80px_rgba(9,6,17,0.32)] md:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="relative z-10 max-w-3xl">
                        <div class="mb-6">
                            <Link
                                :href="back_url"
                                class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-black/10 px-4 py-2.5 text-sm text-white/72 transition hover:border-white/20 hover:bg-white/8 hover:text-white"
                            >
                                <ArrowLeft class="size-4" />
                                Voltar para Minha sala
                            </Link>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1.5 text-sm font-medium text-white">
                                Atividade da turma
                            </p>
                            <span class="inline-flex rounded-full border border-white/10 bg-white/6 px-3 py-1.5 text-xs uppercase tracking-[0.18em] text-white/55">
                                {{ activity.questions_count }} questões
                            </span>
                        </div>

                        <h1 class="mt-5 max-w-2xl text-3xl font-semibold tracking-[-0.03em] text-white md:text-[2.7rem]">{{ activity.title }}</h1>
                        <p class="mt-4 max-w-2xl text-[15px] leading-7 text-white/65">{{ activity.description || 'Atividade publicada para sua turma.' }}</p>

                        <div class="mt-8 grid gap-3 md:grid-cols-3">
                            <div class="rounded-[1.45rem] border border-white/10 bg-white/5 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.16em] text-white/45">Turma</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ activity.classroom.name }}</p>
                                <p class="mt-1 text-sm text-white/55">{{ activity.classroom.code }}</p>
                            </div>

                            <div class="rounded-[1.45rem] border border-white/10 bg-white/5 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.16em] text-white/45">Professor</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ activity.teacher }}</p>
                                <p class="mt-1 text-sm text-white/55">{{ activity.due_date_label }}</p>
                            </div>

                            <div class="rounded-[1.45rem] border border-white/10 bg-white/5 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.16em] text-white/45">Pontuação</p>
                                <p class="mt-2 text-lg font-semibold text-white">{{ formatPoints(activity.total_points) }} pts</p>
                                <p class="mt-1 text-sm text-white/55">{{ activity.questions_count }} questões</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="submission" class="relative z-10 w-full max-w-md rounded-[1.8rem] border border-emerald-300/20 bg-white/5 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.16em] text-emerald-100/70">Resultado final</p>
                                <p class="mt-2 text-3xl font-semibold text-white">{{ formatPoints(submission.score) }} / {{ formatPoints(submission.total_points) }}</p>
                                <p class="mt-1 text-sm text-emerald-100/75">{{ submission.correct_answers_count }} de {{ submission.answers_count }} questões corretas</p>
                            </div>
                            <CheckCircle2 class="size-8 text-emerald-200" />
                        </div>

                        <div class="mt-5 flex items-center gap-2 text-sm text-emerald-100/75">
                            <Clock3 class="size-4" />
                            Enviada em {{ submission.submitted_at_label }}
                        </div>
                    </div>
                </div>
            </AppReveal>
        </section>

        <section class="mt-8">
            <form class="space-y-5" @submit.prevent="submit">
                <AppReveal
                    v-for="(question, index) in activity.questions"
                    :key="question.id"
                    class-name="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[#271D67] p-6 shadow-[0_22px_70px_rgba(9,6,17,0.22)]"
                    :delay="0.04 * (index + 1)"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="relative z-10">
                            <p class="inline-flex rounded-full border border-white/8 bg-white/8 px-3 py-1 text-xs uppercase tracking-[0.16em] text-white/70">
                                Questão {{ question.order }}
                            </p>
                            <div class="mt-4 text-xl font-semibold leading-9 text-white">
                                <template v-if="question.type === 'drag_drop'">
                                    <template v-for="(fragment, fragmentIndex) in statementFragments(question)" :key="`${question.id}-${fragmentIndex}`">
                                        <span v-if="fragment.type === 'text'">{{ fragment.value }}</span>
                                        <span
                                            v-else
                                            class="mx-1.5 inline-flex min-h-10 min-w-[120px] items-end justify-center border-b-2 border-dashed border-[#b7abff]/65 px-2 pb-1 text-center align-middle text-base font-semibold text-[#ddd7ff]"
                                        >
                                            {{ inlineBlankValue(question, index, fragment.value) || ' ' }}
                                        </span>
                                    </template>
                                </template>
                                <template v-else>
                                    {{ question.statement }}
                                </template>
                            </div>
                        </div>

                        <div
                            v-if="question.is_correct !== null"
                            class="relative z-10 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                            :class="question.is_correct ? 'bg-emerald-500/20 text-emerald-100' : 'bg-rose-500/20 text-rose-100'"
                        >
                            <CheckCircle2 v-if="question.is_correct" class="size-4" />
                            <CircleAlert v-else class="size-4" />
                            {{ question.is_correct ? 'Correta' : 'Incorreta' }}
                        </div>
                    </div>

                    <div v-if="question.type === 'multiple_choice'" class="relative z-10 mt-6 grid gap-3">
                        <button
                            v-for="[option, label] in multipleChoiceOptions(question)"
                            :key="option"
                            type="button"
                            class="rounded-[1.35rem] border px-4 py-4 text-left transition duration-200"
                            :class="
                                can_submit
                                    ? answerAt(index).selected_option === option
                                        ? 'border-[#9c8fff] bg-[#8f7bff]/18 text-white'
                                        : 'border-white/10 bg-white/5 text-white/80 hover:-translate-y-0.5 hover:border-white/20 hover:bg-white/8'
                                    : question.submitted_answer?.selected_option === option
                                      ? 'border-[#9c8fff] bg-[#8f7bff]/18 text-white'
                                      : 'border-white/10 bg-white/5 text-white/70'
                            "
                            :disabled="!can_submit"
                            @click="selectOption(index, option)"
                        >
                            <span class="font-semibold">{{ option }}</span>
                            <span class="ml-3">{{ label }}</span>
                        </button>

                        <InputError :message="errorFor(`answers.${index}.selected_option`)" />

                        <div v-if="!can_submit" class="rounded-[1.25rem] border border-white/10 bg-black/10 px-4 py-3 text-sm text-white/65">
                            Alternativa correta: <span class="font-semibold text-white">{{ question.solution?.correct_option }}</span>
                        </div>
                    </div>

                    <div v-else-if="question.type === 'drag_drop'" class="relative z-10 mt-6 space-y-4">
                        <div class="rounded-[1.25rem] border border-white/10 bg-black/10 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.16em] text-white/45">Banco de palavras</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    v-for="keyword in question.keywords ?? []"
                                    :key="`${question.id}-${keyword}`"
                                    class="inline-flex rounded-full border border-[#9c8fff]/18 bg-[#8f7bff]/12 px-3 py-1.5 text-sm font-medium text-[#ddd7ff]"
                                >
                                    {{ keyword }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-for="(blankKey, blankIndex) in question.blank_keys ?? []"
                            :key="blankKey"
                            class="rounded-[1.35rem] border border-white/10 bg-white/5 p-4"
                        >
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-white">{{ blankLabel(blankKey, blankIndex) }}</label>
                                <Select v-if="can_submit" v-model="answerAt(index).blanks![blankKey]">
                                    <SelectTrigger class="h-12 border-white/10 bg-white/6 text-white">
                                        <SelectValue placeholder="Selecione a palavra correta" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="(keyword, keywordIndex) in question.keywords ?? []"
                                            :key="`${blankKey}-${keyword}`"
                                            :value="String(keywordIndex)"
                                        >
                                            {{ keyword }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>

                                <div v-else class="rounded-xl border border-white/10 bg-black/10 px-4 py-3 text-sm text-white/70">
                                    Sua resposta: <span class="font-semibold text-white">{{ submittedBlankLabel(question, blankKey) }}</span>
                                </div>
                            </div>
                        </div>

                        <InputError :message="errorFor(`answers.${index}.blanks`)" />

                        <div v-if="!can_submit" class="rounded-[1.25rem] border border-white/10 bg-black/10 px-4 py-3 text-sm text-white/65">
                            <p class="font-medium text-white">Gabarito</p>
                            <div class="mt-2 grid gap-2">
                                <p v-for="(blankKey, blankIndex) in question.blank_keys ?? []" :key="`solution-${blankKey}`">
                                    {{ blankLabel(blankKey, blankIndex) }}:
                                    <span class="font-semibold text-white">{{ question.solution?.blanks?.[blankKey] }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="relative z-10 mt-6 space-y-4">
                        <div
                            v-for="pair in matchingPairs(question)"
                            :key="pair.index"
                            class="grid gap-3 rounded-[1.35rem] border border-white/10 bg-white/5 p-4 md:grid-cols-[minmax(0,1fr)_240px]"
                        >
                            <div class="flex items-center rounded-xl bg-black/10 px-4 py-3 text-white/85">
                                {{ pair.left }}
                            </div>

                            <Select v-if="can_submit" v-model="answerAt(index).pairs![String(pair.index)]">
                                <SelectTrigger class="h-12 border-white/10 bg-white/6 text-white">
                                    <SelectValue placeholder="Selecione o item correspondente" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(rightItem, rightIndex) in question.right_column ?? []"
                                        :key="`${pair.index}-${rightItem}`"
                                        :value="String(rightIndex)"
                                    >
                                        {{ rightItem }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <div v-else class="rounded-xl border border-white/10 bg-black/10 px-4 py-3 text-sm text-white/70">
                                Sua resposta: <span class="font-semibold text-white">{{ submittedMatchingLabel(question, pair.index) }}</span>
                            </div>
                        </div>

                        <InputError :message="errorFor(`answers.${index}.pairs`)" />

                        <div v-if="!can_submit" class="rounded-[1.25rem] border border-white/10 bg-black/10 px-4 py-3 text-sm text-white/65">
                            <p class="font-medium text-white">Gabarito</p>
                            <div class="mt-2 grid gap-2">
                                <p v-for="(pairData, leftIndex) in question.solution?.pairs ?? {}" :key="`pair-solution-${leftIndex}`">
                                    <span class="font-semibold text-white">{{ pairData.left }}</span> -> {{ pairData.right }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="question.earned_points !== null" class="relative z-10 mt-4 inline-flex rounded-full border border-white/10 bg-white/6 px-3 py-1 text-sm text-white/70">
                        Pontos obtidos: {{ formatPoints(question.earned_points) }}
                    </div>
                </AppReveal>

                <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6 shadow-[0_22px_70px_rgba(9,6,17,0.22)]" :delay="0.12">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start gap-3">
                            <ClipboardCheck class="mt-0.5 size-5 text-[#8f7bff]" />
                            <div>
                                <p class="text-base font-semibold text-white">Envio da atividade</p>
                                <p class="mt-1 text-sm text-white/60">
                                    {{ can_submit ? 'Esta atividade permite apenas uma tentativa e a correção acontece imediatamente após o envio.' : 'Esta atividade já foi enviada e agora está disponível apenas para revisão.' }}
                                </p>
                            </div>
                        </div>

                        <Button v-if="can_submit" type="submit" class="h-12 rounded-full bg-[var(--secondary)] px-6 text-white" :disabled="form.processing">
                            {{ form.processing ? 'Enviando...' : 'Enviar atividade' }}
                        </Button>
                    </div>

                    <InputError :message="form.errors.answers" class="mt-4" />
                </AppReveal>
            </form>
        </section>
    </StudentLayout>
</template>
