<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { home } from '@/routes';
import { Head } from '@inertiajs/vue3';

defineProps<{
    message: string;
}>();
</script>

<template>
    <AuthBase title="Convite inválido" description="Não foi possível concluir o primeiro acesso.">
        <Head title="Convite inválido" />

        <div class="rounded-2xl border border-destructive/40 bg-destructive/10 p-4 text-sm text-white/80">
            {{ message }}
        </div>

        <TextLink :href="home.url()" class="text-center">
            Voltar para a página inicial
        </TextLink>
    </AuthBase>
</template>
