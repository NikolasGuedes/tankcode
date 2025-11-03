<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="Verificar email" description="Por favor, verifique seu endereço de email clicando no link que acabamos de enviar para você.">
        <Head title="Verificação de email" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Um novo link de verificação foi enviado para o endereço de email que você forneceu durante o registro.
        </div>

        <Form method="post" :action="route('verification.send')" class="space-y-6 text-center" v-slot="{ processing }">
            <Button :disabled="processing" variant="secondary">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Reenviar email de verificação
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> Sair </TextLink>
        </Form>
    </AuthLayout>
</template>
