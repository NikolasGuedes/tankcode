<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { XCircle, LogOut } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

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
    <div v-if="impersonating && original_user && currentUser" class="fixed top-0 left-0 right-0 z-50 px-6 py-4 shadow-lg bg-(--primary)">
        <div class="mx-auto max-w-7xl">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <XCircle class="size-5 text-white" />
                    <div>
                        <p class="font-semibold text-white">Você está sendo acessado como</p>
                        <p class="text-sm" style="color: rgba(255, 255, 255, 0.8)">{{ currentUser.name }} ({{ currentUser.email }})</p>
                    </div>
                </div>
                <Button
                    size="sm"
                    class="gap-2"
                    style="background-color: white; color: var(--primary)"
                    @click="stopImpersonation"
                >
                    <LogOut class="size-4" />
                    Voltar ao Admin
                </Button>
            </div>
        </div>
        <!-- Add padding to the page when banner is visible -->
        <div class="fixed top-0 left-0 right-0 h-[80px] pointer-events-none" />
    </div>
</template>
