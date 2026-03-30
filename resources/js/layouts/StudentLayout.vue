<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { getInitials } from '@/composables/useInitials';
import type { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const navigation = computed<NavItem[]>(() => page.props.auth.navigation ?? []);
const currentUser = computed(() => page.props.auth.user);
const currentPath = computed(() => page.url.split('?')[0]);

const normalizedCurrentPath = computed(() => (currentPath.value === '/student' ? '/student/minha-sala' : currentPath.value));

const isActive = (href: string) => href === normalizedCurrentPath.value || href.startsWith(`${normalizedCurrentPath.value}#`);
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
        </div>
    </AppShell>
</template>
