<script setup lang="ts">
import ImpersonationBanner from '@/components/ImpersonationBanner.vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
const impersonating = usePage().props.auth.impersonating;
const originalUser = usePage().props.auth.original_user;
</script>

<template>
    <ImpersonationBanner :impersonating="impersonating" :original_user="originalUser" />
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
    </SidebarProvider>
</template>
