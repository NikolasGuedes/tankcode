<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { getInitials } from '@/composables/useInitials';
import type { AppPageProps, PointContextItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { Building2, Check, ChevronDown } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage<AppPageProps>();

const pointContext = computed(() => page.props.pointContext);
const currentPoint = computed(() => pointContext.value.current);
const availablePoints = computed(() => pointContext.value.available ?? []);
const isEnabled = computed(() => pointContext.value.enabled && !!currentPoint.value);

const switchPoint = (selection: number | 'all') => {
    router.post(
        `/point-of-school-context/${selection}`,
        {},
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const subtitle = (point: PointContextItem | null) => {
    if (!point) {
        return 'Ponto de Ensino';
    }

    return point.id === 'all' ? 'Pontos de Ensino' : 'Ponto de Ensino';
};
</script>

<template>
    <DropdownMenu v-if="isEnabled">
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                class="h-auto min-w-0 rounded-2xl border border-white/10 bg-card px-2 py-1.5 text-left text-white hover:bg-[var(--surface-elevated)]"
            >
                <Avatar class="h-9 w-9 rounded-xl">
                    <AvatarFallback class="rounded-xl bg-secondary text-xs font-semibold text-white">
                        {{ getInitials(currentPoint?.name) || 'PE' }}
                    </AvatarFallback>
                </Avatar>
                <div class="hidden min-w-0 flex-1 flex-col text-left sm:flex">
                    <span class="truncate text-sm font-semibold">{{ currentPoint?.name ?? 'Ponto de Ensino' }}</span>
                    <span class="truncate text-xs text-white/60">{{ subtitle(currentPoint) }}</span>
                </div>
                <ChevronDown class="size-4 text-white/60" />
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-72 border-0 bg-[var(--surface-elevated)] p-0 text-white">
            <DropdownMenuLabel class="px-4 py-3 font-normal">
                <div class="flex items-center gap-3">
                    <Avatar class="h-10 w-10 rounded-xl">
                        <AvatarFallback class="rounded-xl bg-secondary text-sm font-bold text-white">
                            {{ getInitials(currentPoint?.name) || 'PE' }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ currentPoint?.name ?? 'Ponto de Ensino' }}</p>
                        <p class="truncate text-xs text-white/60">{{ currentPoint?.cnpj ?? subtitle(currentPoint) }}</p>
                    </div>
                </div>
            </DropdownMenuLabel>

            <DropdownMenuSeparator class="bg-white/10" />

            <DropdownMenuGroup>
                <DropdownMenuLabel class="px-4 py-2 text-xs text-white/55">Pontos de Ensino ({{ availablePoints.length }})</DropdownMenuLabel>

                <DropdownMenuItem class="cursor-pointer gap-2 px-4 py-2.5 text-white focus:bg-card focus:text-white" @click="switchPoint('all')">
                    <Building2 class="size-4 text-white/60" />
                    <span class="flex-1 text-sm">Todos os Pontos</span>
                    <Check v-if="currentPoint?.id === 'all'" class="size-4 text-secondary" />
                </DropdownMenuItem>

                <DropdownMenuItem
                    v-for="point in availablePoints"
                    :key="point.id"
                    class="cursor-pointer gap-2 px-4 py-2.5 text-white focus:bg-card focus:text-white"
                    @click="switchPoint(point.id)"
                >
                    <Building2 class="size-4 text-white/60" />
                    <span class="flex-1 truncate text-sm">{{ point.name }}</span>
                    <Check v-if="point.id === currentPoint?.id" class="size-4 text-secondary" />
                </DropdownMenuItem>
            </DropdownMenuGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
