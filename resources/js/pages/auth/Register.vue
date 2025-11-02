<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase title="Criar uma conta" description="Digite seus dados abaixo para criar sua conta">
        <Head title="Registrar" />

        <Form
            method="post"
            :action="route('register')"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nome</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" name="name" placeholder="Nome completo" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Endereço de email</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" name="email" placeholder="email@exemplo.com" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Senha</Label>
                    <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" name="password" placeholder="Senha" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar senha</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirmar senha"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="w-full mt-2" tabindex="5" :disabled="processing">
                    <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin" />
                    Criar conta
                </Button>
            </div>

            <div class="text-sm text-center text-muted-foreground">
                Já tem uma conta?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Fazer login</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
