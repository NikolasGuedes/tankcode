<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { update as updatePassword } from '@/actions/App/Http/Controllers/Settings/PasswordController';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configurações de Senha',
        href: '/settings/password',
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Configurações de Senha" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Atualizar Senha" description="Garanta que sua conta esteja usando uma senha longa e aleatória para permanecer segura" />

                <Form
                    method="put"
                    :action="updatePassword.url()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="['password', 'password_confirmation', 'current_password']"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password">Senha Atual</Label>
                        <Input
                            id="current_password"
                            ref="currentPasswordInput"
                            name="current_password"
                            type="password"
                            class="block w-full mt-1"
                            autocomplete="current-password"
                            placeholder="Senha atual"
                        />
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Nova Senha</Label>
                        <Input
                            id="password"
                            ref="passwordInput"
                            name="password"
                            type="password"
                            class="block w-full mt-1"
                            autocomplete="new-password"
                            placeholder="Nova senha"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirmar Senha</Label>
                        <Input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="block w-full mt-1"
                            autocomplete="new-password"
                            placeholder="Confirmar senha"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">Salvar Senha</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-neutral-600">Salvo.</p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
