<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppReveal from '@/components/AppReveal.vue';
import { ArrowLeft, Github, Linkedin, Pencil, Sparkles, UserRound } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface SocialLinks {
    github: string | null;
    linkedin: string | null;
}

interface StudentProfile {
    name: string;
    email: string;
    avatar: string | null;
    points: number;
    bio: string;
    bio_raw?: string | null;
    links: SocialLinks;
    status: {
        completed_activities: number;
        ranking_position: number;
    };
    classroom: {
        name: string;
        code: string;
        point_of_school: string;
        teacher: string;
    };
}

const props = withDefaults(
    defineProps<{
        profile: StudentProfile;
        viewerMode?: boolean;
        viewerLabel?: string | null;
        editable?: boolean;
        updateUrl?: string | null;
    }>(),
    {
        viewerMode: false,
        viewerLabel: null,
        editable: false,
        updateUrl: null,
    },
);

const socialEntries = computed(() => [
    { key: 'github', href: props.profile.links.github, label: 'GitHub', icon: Github },
    { key: 'linkedin', href: props.profile.links.linkedin, label: 'LinkedIn', icon: Linkedin },
]);

const photoDialogOpen = ref(false);
const bioDialogOpen = ref(false);
const linksDialogOpen = ref(false);

const photoForm = useForm({
    section: 'photo',
    photo: null as File | null,
});

const bioForm = useForm({
    section: 'bio',
    bio: props.profile.bio_raw ?? '',
});

const linksForm = useForm({
    section: 'links',
    github_url: props.profile.links.github ?? '',
    linkedin_url: props.profile.links.linkedin ?? '',
});

const avatarPreview = computed(() => {
    if (photoForm.photo) {
        return URL.createObjectURL(photoForm.photo);
    }

    return props.profile.avatar ?? null;
});

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    photoForm.photo = target.files?.[0] ?? null;
};

const submitPhoto = () => {
    if (!props.updateUrl) return;

    photoForm
        .transform((data) => ({
            ...data,
            _method: 'patch',
        }))
        .post(props.updateUrl, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                photoDialogOpen.value = false;
                photoForm.reset();
            },
        });
};

const submitBio = () => {
    if (!props.updateUrl) return;

    bioForm.transform((data) => ({ ...data, _method: 'patch' })).post(props.updateUrl, {
        preserveScroll: true,
        onSuccess: () => {
            bioDialogOpen.value = false;
        },
    });
};

const submitLinks = () => {
    if (!props.updateUrl) return;

    linksForm.transform((data) => ({ ...data, _method: 'patch' })).post(props.updateUrl, {
        preserveScroll: true,
        onSuccess: () => {
            linksDialogOpen.value = false;
        },
    });
};
</script>

<template>
    <section class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <AppReveal class-name="relative rounded-[2rem] border border-white/10 bg-[#271D67] p-6">
            <Dialog v-if="editable" v-model:open="photoDialogOpen">
                <DialogTrigger as-child>
                    <button
                        type="button"
                        class="absolute top-5 right-5 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/12 bg-[#8f7bff] text-white transition hover:bg-[#a593ff]"
                    >
                        <Pencil class="size-4" />
                    </button>
                </DialogTrigger>
                <DialogContent class="border-white/10 bg-[#120d31] text-white">
                    <DialogHeader>
                        <DialogTitle>Editar foto de perfil</DialogTitle>
                        <DialogDescription class="text-white/60">Envie uma imagem JPG, PNG ou WEBP com ate 2 MB.</DialogDescription>
                    </DialogHeader>

                    <form class="space-y-4" @submit.prevent="submitPhoto">
                        <div class="flex justify-center">
                            <div class="h-28 w-28 overflow-hidden rounded-full border-2 border-[#8f7bff]/50 bg-white/10">
                                <img v-if="avatarPreview" :src="avatarPreview" :alt="profile.name" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center text-white/70">
                                    <UserRound class="size-12" />
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="student-photo">Foto</Label>
                            <Input
                                id="student-photo"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="border-white/10 bg-white/5 text-white file:text-white"
                                @change="handlePhotoChange"
                            />
                            <InputError :message="photoForm.errors.photo" />
                        </div>

                        <DialogFooter>
                            <Button
                                type="submit"
                                class="w-full bg-[#8f7bff] text-white hover:bg-[#a593ff]"
                                :disabled="photoForm.processing"
                            >
                                Salvar foto
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <div class="flex flex-col items-center text-center">
                <div class="h-44 w-44 overflow-hidden rounded-full border-4 border-[#8f7bff] bg-white/10 shadow-[0_18px_50px_rgba(132,113,255,0.35)]">
                    <img v-if="profile.avatar" :src="profile.avatar" :alt="profile.name" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center bg-[radial-gradient(circle,#30206d_0%,#120b2d_100%)] text-6xl text-white/85">
                        <UserRound class="size-16" />
                    </div>
                </div>

                <h1 class="mt-5 text-3xl font-light tracking-tight text-white">{{ profile.name }}</h1>
                <p class="mt-2 text-sm text-white/55">{{ profile.email }}</p>

                <div class="mt-5 inline-flex items-center overflow-hidden rounded-full bg-white text-sm font-medium text-[#120d31]">
                    <span class="px-4 py-2">Pontuacao:</span>
                    <span class="bg-[#2f1ef4] px-4 py-2 font-semibold text-white">{{ profile.points }} pts</span>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <component
                        :is="link.href ? 'a' : 'span'"
                        v-for="link in socialEntries"
                        :key="link.key"
                        :href="link.href ?? undefined"
                        :target="link.href ? '_blank' : undefined"
                        :rel="link.href ? 'noreferrer noopener' : undefined"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-[#271D67] transition"
                        :class="link.href ? 'text-white hover:border-[#8f7bff] hover:bg-[#8f7bff]/18' : 'cursor-not-allowed text-white/30'"
                        :title="link.href ? link.label : `${link.label} indisponivel`"
                    >
                        <component :is="link.icon" class="size-5" />
                    </component>

                    <Dialog v-if="editable" v-model:open="linksDialogOpen">
                        <DialogTrigger as-child>
                            <button
                                type="button"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-[#8f7bff] text-white transition hover:bg-[#a593ff]"
                            >
                                <Pencil class="size-4" />
                            </button>
                        </DialogTrigger>
                        <DialogContent class="border-white/10 bg-[#120d31] text-white">
                            <DialogHeader>
                                <DialogTitle>Editar links</DialogTitle>
                                <DialogDescription class="text-white/60">Atualize seus links do GitHub e LinkedIn.</DialogDescription>
                            </DialogHeader>

                            <form class="space-y-4" @submit.prevent="submitLinks">
                                <div class="grid gap-2">
                                    <Label for="github-url">GitHub</Label>
                                    <Input id="github-url" v-model="linksForm.github_url" class="border-white/10 bg-white/5 text-white" placeholder="https://github.com/seu-usuario" />
                                    <InputError :message="linksForm.errors.github_url" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="linkedin-url">LinkedIn</Label>
                                    <Input id="linkedin-url" v-model="linksForm.linkedin_url" class="border-white/10 bg-white/5 text-white" placeholder="https://linkedin.com/in/seu-usuario" />
                                    <InputError :message="linksForm.errors.linkedin_url" />
                                </div>

                                <DialogFooter>
                                    <Button
                                        type="submit"
                                        class="w-full bg-[#8f7bff] text-white hover:bg-[#a593ff]"
                                        :disabled="linksForm.processing"
                                    >
                                        Salvar links
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </AppReveal>

        <div class="grid gap-6">
            <AppReveal
                v-if="viewerMode"
                class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-5"
                :delay="0.04"
            >
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">
                            {{ viewerLabel ?? 'Visualizacao' }}
                        </p>
                        <p class="mt-3 text-white/65">Este perfil esta em modo somente leitura.</p>
                    </div>
                    <Link
                        href="/student/minha-sala"
                        class="inline-flex items-center gap-2 rounded-full border border-white/12 bg-[#271D67] px-4 py-2 text-sm font-medium text-white transition hover:border-[#8f7bff] hover:bg-[#8f7bff]/18"
                    >
                        <ArrowLeft class="size-4" />
                        Voltar para minha sala
                    </Link>
                </div>
            </AppReveal>

            <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.06">
                <div class="flex items-start justify-between gap-4">
                    <span class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Bio</span>

                    <Dialog v-if="editable" v-model:open="bioDialogOpen">
                        <DialogTrigger as-child>
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/12 bg-[#8f7bff] text-white transition hover:bg-[#a593ff]"
                            >
                                <Pencil class="size-4" />
                            </button>
                        </DialogTrigger>
                        <DialogContent class="border-white/10 bg-[#120d31] text-white">
                            <DialogHeader>
                                <DialogTitle>Editar bio</DialogTitle>
                                <DialogDescription class="text-white/60">Escreva uma breve apresentacao para o seu perfil.</DialogDescription>
                            </DialogHeader>

                            <form class="space-y-4" @submit.prevent="submitBio">
                                <div class="grid gap-2">
                                    <Label for="student-bio">Bio</Label>
                                    <textarea
                                        id="student-bio"
                                        v-model="bioForm.bio"
                                        rows="6"
                                        class="min-h-32 rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white outline-none transition focus:border-[#8f7bff]"
                                        placeholder="Conte um pouco sobre voce"
                                    />
                                    <InputError :message="bioForm.errors.bio" />
                                </div>

                                <DialogFooter>
                                    <Button
                                        type="submit"
                                        class="w-full bg-[#8f7bff] text-white hover:bg-[#a593ff]"
                                        :disabled="bioForm.processing"
                                    >
                                        Salvar bio
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
                <p class="mt-5 text-lg leading-8 text-white/85">
                    {{ profile.bio }}
                </p>
            </AppReveal>

            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.1">
                    <span class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Status</span>
                    <div class="mt-6 space-y-4 text-white/85">
                        <div>
                            <p class="text-sm uppercase tracking-[0.18em] text-white/45">Atividades concluidas</p>
                            <p class="mt-1 text-3xl font-semibold text-white">{{ profile.status.completed_activities }}</p>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.18em] text-white/45">Posicao no ranking</p>
                            <p class="mt-1 text-3xl font-semibold text-white">#{{ profile.status.ranking_position }}</p>
                        </div>
                    </div>
                </AppReveal>

                <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.14">
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Conquistas</span>
                        <Sparkles class="size-5 text-[#c3b9ff]" />
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-4 sm:grid-cols-5">
                        <div
                            v-for="slot in 5"
                            :key="slot"
                            class="aspect-square rounded-full border border-dashed border-white/20 bg-white/5"
                        />
                    </div>
                    <div class="mt-8 rounded-[1.5rem] border border-dashed border-white/15 bg-[#271D67] px-5 py-8 text-center text-sm leading-6 text-white/55">
                        Suas badges vao aparecer aqui quando a integracao de conquistas estiver pronta.
                    </div>
                </AppReveal>
            </div>

            <AppReveal class-name="rounded-[2rem] border border-white/10 bg-[#271D67] p-6" :delay="0.18">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="inline-flex rounded-full bg-[#8f7bff] px-4 py-1 text-sm text-white">Minha sala</p>
                        <h2 class="mt-4 text-2xl font-semibold text-white">{{ profile.classroom.name }}</h2>
                        <p class="mt-2 text-white/60">Contexto da sala vinculada ao aluno.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] border border-white/10 bg-[#271D67] p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-white/45">Turma</p>
                        <p class="mt-2 text-xl font-semibold text-white">{{ profile.classroom.name }}</p>
                        <p class="mt-1 text-sm text-white/55">Codigo {{ profile.classroom.code }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-[#271D67] p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-white/45">Professor</p>
                        <p class="mt-2 text-xl font-semibold text-white">{{ profile.classroom.teacher }}</p>
                        <p class="mt-1 text-sm text-white/55">{{ profile.classroom.point_of_school }}</p>
                    </div>
                </div>
            </AppReveal>
        </div>
    </section>
</template>
