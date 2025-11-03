<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthLayout title="Confirme sua senha" description="Esta é uma área segura da aplicação. Por favor, confirme sua senha antes de continuar.">
        <Head title="Confirmar senha" />

        <Form method="post" :action="route('password.confirm')" reset-on-success v-slot="{ errors, processing }">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Senha</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        class="block w-full mt-1"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button class="w-full" :disabled="processing">
                        <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin" />
                        Confirmar Senha
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
