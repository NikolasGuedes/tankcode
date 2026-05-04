<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import ImpersonationBanner from '@/components/ImpersonationBanner.vue';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
const impersonating = usePage().props.auth.impersonating;
const originalUser = usePage().props.auth.original_user;

const paddingClass = computed(() => (impersonating && originalUser ? 'pt-20' : ''));
</script>

<template>
    <ImpersonationBanner :impersonating="impersonating" :original_user="originalUser" />
    <div v-if="variant === 'header'" :class="['flex min-h-screen w-full flex-col', paddingClass]">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen" :class="paddingClass">
        <slot />
    </SidebarProvider>
</template>
