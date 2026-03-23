<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

const props = defineProps<{
    meta: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
    links: PaginationLink[];
}>();

const visibleLinks = computed(() =>
    props.links.map((link) => ({
        ...link,
        text: link.label
            .replace(/&laquo;/g, '«')
            .replace(/&raquo;/g, '»')
            .replace(/&amp;/g, '&')
            .replace(/<[^>]+>/g, '')
            .trim(),
    })),
);

const visit = (url: string | null) => {
    if (!url) {
        return;
    }

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <div class="mt-6 flex flex-col gap-4 border-t border-white/5 pt-5 md:flex-row md:items-center md:justify-between">
        <p class="text-sm text-white/55">
            <template v-if="meta.total">
                Mostrando {{ meta.from ?? 0 }}-{{ meta.to ?? 0 }} de {{ meta.total }} registro(s)
            </template>
            <template v-else>
                Nenhum registro encontrado
            </template>
        </p>

        <div v-if="meta.last_page > 1" class="flex flex-wrap items-center justify-end gap-2">
            <button
                v-for="link in visibleLinks"
                :key="`${link.label}-${link.url}`"
                type="button"
                class="inline-flex min-w-10 items-center justify-center rounded-xl border px-3 py-2 text-sm font-medium transition"
                :class="
                    link.active
                        ? 'border-primary bg-primary text-white'
                        : link.url
                          ? 'border-white/10 bg-white/5 text-white/75 hover:border-white/20 hover:bg-white/10'
                          : 'cursor-not-allowed border-white/5 bg-white/5 text-white/30'
                "
                :disabled="!link.url"
                @click="visit(link.url)"
            >
                {{ link.text }}
            </button>
        </div>
    </div>
</template>
