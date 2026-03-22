<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { AppPageProps } from '@/types';
import { toast } from 'vue-sonner';
import { watch } from 'vue';

const page = usePage<AppPageProps<{
    flash?: {
        success?: string | null;
        error?: string | null;
        info?: string | null;
    };
    errors?: Record<string, string | string[] | null | undefined>;
}>>();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.success(flash.success);
        }

        if (flash?.error) {
            toast.error(flash.error);
        }

        if (flash?.info) {
            toast.info(flash.info);
        }
    },
    { deep: true, immediate: true },
);

watch(
    () => page.props.errors,
    (errors) => {
        if (!errors) {
            return;
        }

        const firstError = Object.values(errors)
            .flatMap((value) => Array.isArray(value) ? value : value ? [value] : [])
            .find(Boolean);

        if (firstError) {
            toast.error(firstError);
        }
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <span class="hidden" />
</template>
