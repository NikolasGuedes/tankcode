<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { getInitials } from '@/composables/useInitials';
import type { AppPageProps, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, Sparkles } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

type AchievementUnlock = {
    code: string;
    title: string;
    description: string;
    image_url: string | null;
    awarded_at: string | null;
};

const page = usePage<AppPageProps<{
    flash?: {
        achievement_unlocks?: AchievementUnlock[] | null;
    };
}>>();
const navigation = computed<NavItem[]>(() => page.props.auth.navigation ?? []);
const currentUser = computed(() => page.props.auth.user);
const currentPath = computed(() => page.url.split('?')[0]);
const achievementUnlockDialogOpen = ref(false);
const achievementUnlocks = ref<AchievementUnlock[]>([]);
const activeAchievementUnlockIndex = ref(0);

const normalizedCurrentPath = computed(() => (currentPath.value === '/student' ? '/student/minha-sala' : currentPath.value));
const activeAchievementUnlock = computed(() => achievementUnlocks.value[activeAchievementUnlockIndex.value] ?? null);
const hasMoreAchievementUnlocks = computed(() => activeAchievementUnlockIndex.value < achievementUnlocks.value.length - 1);

const isActive = (href: string) => {
    if (href === '/student/minha-sala' && normalizedCurrentPath.value.startsWith('/student/atividades/')) {
        return true;
    }

    return href === normalizedCurrentPath.value || href.startsWith(`${normalizedCurrentPath.value}#`);
};

const formatAchievementDate = (value: string | null) => {
    if (!value) return null;

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'full',
        timeStyle: 'short',
        timeZone: 'America/Sao_Paulo',
    }).format(date);
};

const closeAchievementUnlockDialog = () => {
    achievementUnlockDialogOpen.value = false;
    achievementUnlocks.value = [];
    activeAchievementUnlockIndex.value = 0;
};

const advanceAchievementUnlockDialog = () => {
    if (hasMoreAchievementUnlocks.value) {
        activeAchievementUnlockIndex.value += 1;

        return;
    }

    closeAchievementUnlockDialog();
};

watch(
    () => page.props.flash?.achievement_unlocks,
    (unlockPayload) => {
        if (!unlockPayload?.length) {
            return;
        }

        achievementUnlocks.value = unlockPayload;
        activeAchievementUnlockIndex.value = 0;
        achievementUnlockDialogOpen.value = true;
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <AppShell variant="header">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,#24145f_0%,#090611_38%,#030208_100%)] text-white">
            <header class="sticky top-0 z-30 bg-black">
                <div class="mx-auto grid max-w-7xl grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-4 px-4 py-3 md:px-6 lg:grid-cols-[auto_minmax(0,1fr)_auto]">
                    <Link href="/student" class="flex min-w-0 items-center">
                        <img src="/imgs/code_icon_02.png" alt="Tank Code" class="h-10 w-auto object-contain md:h-12" />
                    </Link>

                    <div v-if="currentUser" class="col-start-2 row-start-1 flex justify-end lg:col-start-3">
                        <DropdownMenu>
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    class="h-10 min-w-[68px] rounded-full border border-white/15 bg-white px-1 py-1 text-black hover:bg-white/90"
                                >
                                    <Avatar class="h-8 w-8 shrink-0 overflow-hidden rounded-full border-2 border-[#8471ff]/30">
                                        <AvatarImage v-if="currentUser.avatar" :src="currentUser.avatar" :alt="currentUser.name" />
                                        <AvatarFallback class="bg-[#120d31] text-sm text-white">
                                            {{ getInitials(currentUser.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <ChevronDown class="ml-1 size-3.5 shrink-0 text-black/65" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <UserMenuContent :user="currentUser" />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <nav class="col-span-2 row-start-2 flex justify-center lg:col-span-1 lg:col-start-2 lg:row-start-1">
                        <div class="flex flex-wrap items-center justify-center gap-2 px-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.title"
                                :href="item.href"
                                class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium text-white/72 transition hover:text-white"
                                :class="isActive(item.href) ? 'bg-[#8f7bff] text-white' : 'bg-transparent'"
                            >
                                {{ item.title }}
                            </Link>
                        </div>
                    </nav>
                </div>
            </header>

            <AppContent variant="header" class="w-full max-w-7xl px-4 py-8 md:px-6 md:py-10">
                <slot />
            </AppContent>

            <Dialog :open="achievementUnlockDialogOpen" @update:open="(value) => !value ? closeAchievementUnlockDialog() : (achievementUnlockDialogOpen = value)">
                <DialogContent class="border-white/10 bg-[#120d31] text-white sm:max-w-xl">
                    <DialogHeader>
                        <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#8f7bff]/18 text-[#d9d2ff]">
                            <Sparkles class="size-6" />
                        </div>
                        <DialogTitle class="text-2xl">Parabéns pela nova conquista!</DialogTitle>
                        <DialogDescription class="text-white/65">
                            {{ hasMoreAchievementUnlocks ? `Você desbloqueou mais de um emblema. Confira o ${activeAchievementUnlockIndex + 1}º agora.` : 'Seu novo emblema acabou de entrar para o seu perfil.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <div v-if="activeAchievementUnlock" class="space-y-4">
                        <div class="rounded-[1.75rem] border border-[#8f7bff]/35 bg-[radial-gradient(circle_at_top,#322074_0%,#171038_100%)] p-6 text-center shadow-[0_24px_60px_rgba(143,123,255,0.2)]">
                            <div class="mx-auto flex h-36 w-36 items-center justify-center rounded-[1.75rem] border border-white/10 bg-white/6 p-4 shadow-[0_18px_45px_rgba(0,0,0,0.25)]">
                                <img
                                    v-if="activeAchievementUnlock.image_url"
                                    :src="activeAchievementUnlock.image_url"
                                    :alt="activeAchievementUnlock.title"
                                    class="h-full w-full object-contain"
                                />
                                <div v-else class="text-lg font-semibold text-white/75">
                                    Emblema
                                </div>
                            </div>

                            <p class="mt-5 text-3xl font-semibold text-white">{{ activeAchievementUnlock.title }}</p>
                            <p class="mt-3 text-sm leading-6 text-white/72">{{ activeAchievementUnlock.description }}</p>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/72">
                            <p class="font-semibold text-white">Conquista validada</p>
                            <p class="mt-2 leading-6">
                                {{ activeAchievementUnlock.awarded_at ? `Registrada em ${formatAchievementDate(activeAchievementUnlock.awarded_at)}.` : 'Seu progresso foi validado com sucesso.' }}
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button class="w-full bg-[#8f7bff] text-white hover:bg-[#a593ff]" @click="advanceAchievementUnlockDialog">
                            {{ hasMoreAchievementUnlocks ? 'Ver próxima conquista' : 'Continuar' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppShell>
</template>
