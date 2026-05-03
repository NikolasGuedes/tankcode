<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItemType {
    title: string;
    href?: string;
}

defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList class="min-w-0 flex-nowrap gap-1.5 text-sm text-white/70">
            <template v-for="(item, index) in breadcrumbs" :key="index">
                <BreadcrumbItem class="min-w-0">
                    <template v-if="index === breadcrumbs.length - 1">
                        <BreadcrumbPage class="truncate font-semibold text-white">{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child class="text-white/70 transition hover:text-white">
                            <Link :href="item.href ?? '#'" class="truncate">{{ item.title }}</Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" class="text-white/35" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
