<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="Esqueci a senha" description="Digite seu email para receber um link de redefinição de senha">
        <Head title="Esqueci a senha" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <Form method="post" :action="route('password.email')" v-slot="{ errors, processing }">
                <div class="grid gap-2">
                    <Label for="email">Endereço de email</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" autofocus placeholder="email@exemplo.com" />
                    <InputError :message="errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="processing">
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        Enviar link de redefinição de senha
                    </Button>
                </div>
            </Form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Ou, retorne para</span>
                <TextLink :href="route('login')">fazer login</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
