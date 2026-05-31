<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { router, usePage } from '@inertiajs/vue3';
import { LogOut, XCircle } from 'lucide-vue-next';

interface Props {
    impersonating: boolean;
    original_user: {
        id: number;
        name: string;
        email: string;
    } | null;
}

defineProps<Props>();

const page = usePage();
const currentUser = page.props.auth.user;

const stopImpersonation = () => {
    router.post('/impersonate/stop', {});
};
</script>

<template>
    <div v-if="impersonating && original_user && currentUser" class="pointer-events-none fixed right-4 bottom-4 z-50 sm:right-6 sm:bottom-6">
        <div
            class="pointer-events-auto w-[min(22rem,calc(100vw-2rem))] rounded-2xl border border-white/15 bg-(--primary) p-4 shadow-[0_20px_45px_rgba(0,0,0,0.35)] backdrop-blur"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 rounded-full bg-white/12 p-1.5">
                        <XCircle class="size-4 text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Você está sendo acessado como</p>
                        <p class="mt-1 text-sm leading-5" style="color: rgba(255, 255, 255, 0.8)">
                            {{ currentUser.name }}
                        </p>
                        <p class="text-xs" style="color: rgba(255, 255, 255, 0.65)">{{ currentUser.email }}</p>
                    </div>
                </div>
                <Button size="sm" class="w-full gap-2" style="background-color: white; color: var(--primary)" @click="stopImpersonation">
                    <LogOut class="size-4" />
                    Voltar ao Admin
                </Button>
            </div>
        </div>
    </div>
</template>
