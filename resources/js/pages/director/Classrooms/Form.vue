<script setup lang="ts">
import {
    store as storeClassroom,
    update as updateClassroom,
} from '@/actions/App/Http/Controllers/Director/ClassroomController';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type PointOption = { id: number; name: string };
type TeacherOption = { id: number; name: string; point_of_school_ids: number[] };
type StudentOption = {
    id: number;
    name: string;
    point_of_school_ids: number[];
    assigned_classroom_id: number | null;
    assigned_classroom_name: string | null;
    is_assigned_to_current_classroom: boolean;
};

type ClassroomFormData = {
    id: number;
    point_of_school_id: number;
    teacher_id: number | null;
    name: string;
    code: string;
    status: string;
    student_ids: number[];
} | null;

const props = defineProps<{
    classroom: ClassroomFormData;
    points: PointOption[];
    teachers: TeacherOption[];
    students: StudentOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/director' },
    { title: 'Turmas', href: '/director/classrooms' },
    { title: props.classroom ? 'Editar Turma' : 'Nova Turma', href: props.classroom ? `/director/classrooms/${props.classroom.id}/edit` : '/director/classrooms/create' },
];

const studentSearch = ref('');

const form = useForm({
    point_of_school_id: props.classroom?.point_of_school_id ? String(props.classroom.point_of_school_id) : (props.points[0]?.id ? String(props.points[0].id) : ''),
    teacher_id: props.classroom?.teacher_id ? String(props.classroom.teacher_id) : '',
    name: props.classroom?.name ?? '',
    code: props.classroom?.code ?? '',
    student_ids: props.classroom?.student_ids.map(String) ?? [],
    status: props.classroom?.status ?? 'active',
});

const availableTeachers = computed(() =>
    props.teachers.filter((teacher) => !form.point_of_school_id || teacher.point_of_school_ids.includes(Number(form.point_of_school_id))),
);

const filteredStudents = computed(() =>
    props.students.filter((student) => {
        const matchesPoint = !form.point_of_school_id || student.point_of_school_ids.includes(Number(form.point_of_school_id));
        const matchesSearch = student.name.toLowerCase().includes(studentSearch.value.toLowerCase());

        return matchesPoint && matchesSearch;
    }),
);

const selectedStudentsCount = computed(() => form.student_ids.length);

const canSelectStudent = (student: StudentOption) =>
    !student.assigned_classroom_id || student.is_assigned_to_current_classroom;

const studentAssignmentLabel = (student: StudentOption) => {
    if (student.is_assigned_to_current_classroom) {
        return 'Nesta turma';
    }

    if (student.assigned_classroom_name) {
        return `Vinculado a ${student.assigned_classroom_name}`;
    }

    return 'Sem sala';
};

const studentAssignmentClass = (student: StudentOption) => {
    if (student.is_assigned_to_current_classroom) {
        return 'bg-primary/15 text-primary ring-1 ring-primary/30';
    }

    if (student.assigned_classroom_name) {
        return 'bg-amber-500/15 text-amber-200 ring-1 ring-amber-500/30';
    }

    return 'bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-500/30';
};

const toggleStudent = (studentId: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.student_ids.includes(studentId)) {
            form.student_ids = [...form.student_ids, studentId];
        }

        return;
    }

    form.student_ids = form.student_ids.filter((id) => id !== studentId);
};

const submit = () => {
    const options = { preserveScroll: true };

    if (props.classroom) {
        form.put(updateClassroom(props.classroom.id).url, options);
        return;
    }

    form.post(storeClassroom().url, options);
};

watch(() => form.point_of_school_id, () => {
    const allowedStudents = new Set(
        props.students
            .filter((student) => !form.point_of_school_id || student.point_of_school_ids.includes(Number(form.point_of_school_id)))
            .filter((student) => canSelectStudent(student))
            .map((student) => String(student.id)),
    );

    form.student_ids = form.student_ids.filter((studentId) => allowedStudents.has(studentId));

    if (form.teacher_id && !availableTeachers.value.some((teacher) => String(teacher.id) === form.teacher_id)) {
        form.teacher_id = '';
    }
});
</script>

<template>
    <Head :title="props.classroom ? 'Editar Turma' : 'Nova Turma'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-semibold tracking-tight text-white">{{ props.classroom ? 'Editar Turma' : 'Nova Turma' }}</h1>
                        <p class="mt-2 max-w-3xl text-lg text-white/70">Preencha os dados da turma com mais espaco para revisar os alunos, identificando quem ja esta sem sala, nesta turma ou vinculado a outra.</p>
                    </div>
                    <Button as-child variant="outline" class="rounded-2xl border-white/10 bg-white/5 text-white hover:bg-white/10">
                        <a href="/director/classrooms"><ArrowLeft class="size-4" />Voltar para Turmas</a>
                    </Button>
                </div>
            </AppReveal>

            <form class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]" @submit.prevent="submit">
                <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.06">
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-2xl font-semibold text-white">Dados da Turma</h2>
                            <p class="mt-1 text-sm text-white/60">Defina o ponto de ensino, professor responsavel, identificacao e status.</p>
                        </div>

                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="classroom-point">Ponto de Ensino</Label>
                                <Select v-model="form.point_of_school_id">
                                    <SelectTrigger id="classroom-point" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione o ponto de ensino" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem v-for="point in props.points" :key="point.id" :value="String(point.id)">
                                            {{ point.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid gap-2">
                                <Label for="classroom-teacher">Professor responsavel</Label>
                                <Select v-model="form.teacher_id">
                                    <SelectTrigger id="classroom-teacher" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione um professor" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem v-for="teacher in availableTeachers" :key="teacher.id" :value="String(teacher.id)">
                                            {{ teacher.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid gap-2">
                                <Label for="classroom-name">Nome</Label>
                                <Input id="classroom-name" v-model="form.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: 3A - Manha" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="classroom-code">Codigo</Label>
                                <Input id="classroom-code" v-model="form.code" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: 3A-MNH" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="classroom-status">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger id="classroom-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione o status" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem value="active">Ativa</SelectItem>
                                        <SelectItem value="inactive">Inativa</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/20 p-4 text-sm text-white/60">
                            <p class="font-medium text-white">Regra importante</p>
                            <p class="mt-1">Cada aluno pode estar vinculado a apenas uma sala. Alunos ja alocados aparecem identificados e ficam bloqueados para selecao.</p>
                        </div>
                    </div>
                </AppReveal>

                <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.1">
                    <div class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-white">Lista de Alunos</h2>
                                <p class="mt-1 text-sm text-white/60">Veja mais contexto antes de vincular: status da sala, disponibilidade e ponto de ensino.</p>
                            </div>
                            <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">
                                {{ selectedStudentsCount }} selecionado(s)
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                            <div class="relative">
                                <Search class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-white/40" />
                                <Input v-model="studentSearch" class="h-11 border-white/10 bg-[var(--surface-elevated)] pr-4 pl-11 text-white" placeholder="Buscar aluno por nome" />
                            </div>
                            <div class="flex items-center rounded-2xl border border-white/10 bg-black/20 px-4 text-sm text-white/60">
                                {{ filteredStudents.length }} aluno(s) visiveis
                            </div>
                        </div>

                        <div class="max-h-[720px] space-y-3 overflow-y-auto rounded-[1.5rem] border border-white/10 bg-[var(--surface-elevated)] p-4">
                            <div
                                v-for="student in filteredStudents"
                                :key="student.id"
                                class="flex flex-wrap items-center gap-4 rounded-[1.25rem] border px-4 py-4"
                                :class="canSelectStudent(student) ? 'border-white/10 bg-black/20' : 'border-amber-500/20 bg-amber-500/5 opacity-80'"
                            >
                                <Checkbox
                                    :model-value="form.student_ids.includes(String(student.id))"
                                    :disabled="!canSelectStudent(student)"
                                    class="border-white/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary"
                                    @update:model-value="(checked) => toggleStudent(String(student.id), checked)"
                                />

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <p class="truncate text-base font-semibold text-white">{{ student.name }}</p>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="studentAssignmentClass(student)">
                                            {{ studentAssignmentLabel(student) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-white/50">
                                        {{
                                            student.point_of_school_ids.includes(Number(form.point_of_school_id))
                                                ? 'Disponivel no ponto de ensino selecionado.'
                                                : 'Fora do ponto de ensino selecionado.'
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="!filteredStudents.length" class="rounded-[1.25rem] border border-dashed border-white/10 px-4 py-10 text-center text-sm text-white/50">
                                Nenhum aluno encontrado para os filtros atuais.
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-3">
                            <Button as-child type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]">
                                <a href="/director/classrooms">Cancelar</a>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ props.classroom ? 'Salvar alteracoes' : 'Criar turma' }}
                            </Button>
                        </div>
                    </div>
                </AppReveal>
            </form>
        </section>
    </AppLayout>
</template>
