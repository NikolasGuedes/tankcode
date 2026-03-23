<script setup lang="ts">
import {
    store as storeDirector,
    update as updateDirector,
} from '@/actions/App/Http/Controllers/Owner/DirectorController';
import AppReveal from '@/components/AppReveal.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { watch } from 'vue';

type PointOption = { id: number; name: string };
type DirectorFormData = {
    id: number;
    name: string;
    email: string;
    status: string;
    point_of_school_ids: number[];
} | null;

const props = defineProps<{
    director: DirectorFormData;
    points: PointOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visao geral', href: '/owner' },
    { title: 'Diretores', href: '/owner/directors' },
    { title: props.director ? 'Editar Diretor' : 'Novo Diretor', href: props.director ? `/owner/directors/${props.director.id}/edit` : '/owner/directors/create' },
];

const form = useForm({
    name: props.director?.name ?? '',
    email: props.director?.email ?? '',
    point_of_school_ids: props.director?.point_of_school_ids.map(String) ?? [],
    status: props.director?.status ?? 'active',
});

const togglePoint = (pointId: string, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!form.point_of_school_ids.includes(pointId)) {
            form.point_of_school_ids = [...form.point_of_school_ids, pointId];
        }

        return;
    }

    form.point_of_school_ids = form.point_of_school_ids.filter((id) => id !== pointId);
};

const submit = () => {
    const options = { preserveScroll: true };

    if (props.director) {
        form.put(updateDirector(props.director.id).url, options);
        return;
    }

    form.post(storeDirector().url, options);
};

watch(
    () => props.points,
    (points) => {
        const allowedPointIds = new Set(points.map((point) => String(point.id)));
        form.point_of_school_ids = form.point_of_school_ids.filter((pointId) => allowedPointIds.has(pointId));
    },
    { immediate: true },
);
</script>

<template>
    <Head :title="props.director ? 'Editar Diretor' : 'Novo Diretor'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <section class="space-y-6 p-6">
            <AppReveal class-name="rounded-[2rem] border border-border bg-card p-8 shadow-[0_20px_60px_rgba(0,0,0,0.35)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-semibold tracking-tight text-white">{{ props.director ? 'Editar Diretor' : 'Novo Diretor' }}</h1>
                        <p class="mt-2 max-w-3xl text-lg text-white/70">Gerencie os dados do diretor em uma pagina dedicada, com mais conforto para revisar os pontos de ensino vinculados.</p>
                    </div>
                    <Button as-child variant="outline" class="rounded-2xl border-white/10 bg-white/5 text-white hover:bg-white/10">
                        <a href="/owner/directors"><ArrowLeft class="size-4" />Voltar para Diretores</a>
                    </Button>
                </div>
            </AppReveal>

            <form class="grid gap-6 xl:grid-cols-[460px_minmax(0,1fr)]" @submit.prevent="submit">
                <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.06">
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-2xl font-semibold text-white">Dados do Diretor</h2>
                            <p class="mt-1 text-sm text-white/60">Defina os dados principais e o status de acesso da conta.</p>
                        </div>

                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="director-name">Nome</Label>
                                <Input id="director-name" v-model="form.name" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="Ex.: Camila Santos" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="director-email">E-mail</Label>
                                <Input id="director-email" v-model="form.email" type="email" class="border-white/10 bg-[var(--surface-elevated)] text-white" placeholder="diretor@escola.com" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="director-status">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger id="director-status" class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectValue placeholder="Selecione o status" />
                                    </SelectTrigger>
                                    <SelectContent class="border-white/10 bg-[var(--surface-elevated)] text-white">
                                        <SelectItem value="active">Ativo</SelectItem>
                                        <SelectItem value="inactive">Inativo</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>
                </AppReveal>

                <AppReveal class-name="rounded-[2rem] border border-border bg-card p-6 shadow-[0_20px_45px_rgba(0,0,0,0.25)]" :delay="0.1">
                    <div class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-white">Pontos de Ensino</h2>
                                <p class="mt-1 text-sm text-white/60">Diretores podem atuar em um ou mais pontos de ensino vinculados ao owner.</p>
                            </div>
                            <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">
                                {{ form.point_of_school_ids.length }} selecionado(s)
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div
                                v-for="point in props.points"
                                :key="point.id"
                                class="flex items-center gap-3 rounded-[1.25rem] border border-white/10 bg-black/20 px-4 py-4"
                            >
                                <Checkbox
                                    :model-value="form.point_of_school_ids.includes(String(point.id))"
                                    class="border-white/20 data-[state=checked]:border-primary data-[state=checked]:bg-primary"
                                    @update:model-value="(checked) => togglePoint(String(point.id), checked)"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-base font-semibold text-white">{{ point.name }}</p>
                                    <p class="mt-1 text-sm text-white/50">Disponivel para vinculacao do diretor.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-end gap-3">
                            <Button as-child type="button" variant="outline" class="!border-destructive !bg-destructive !text-white hover:!border-[var(--destructive-hover)] hover:!bg-[var(--destructive-hover)]">
                                <a href="/owner/directors">Cancelar</a>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ props.director ? 'Salvar alteracoes' : 'Criar diretor' }}
                            </Button>
                        </div>
                    </div>
                </AppReveal>
            </form>
        </section>
    </AppLayout>
</template>
