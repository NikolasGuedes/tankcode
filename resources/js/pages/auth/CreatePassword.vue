<script setup lang="ts">
import { store as activateAccount } from '@/actions/App/Http/Controllers/Auth/UserActivationController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    token: string;
    user: {
        id: number;
        name: string;
        email: string;
        role?: string | null;
        school?: string | null;
    };
}>();
</script>

<template>
    <AuthBase title="Ative sua conta" description="Defina sua senha de primeiro acesso para entrar na plataforma.">
        <Head title="Ativar conta" />

        <div class="rounded-2xl border border-border bg-card/70 p-4 text-sm text-white/80">
            <p class="font-semibold text-white">{{ user.name }}</p>
            <p>{{ user.email }}</p>
            <p v-if="user.role || user.school" class="mt-2 text-white/60">
                {{ user.role ?? 'Usuario' }}<span v-if="user.school"> • {{ user.school }}</span>
            </p>
        </div>

        <Form method="post" :action="activateAccount.url()" v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <input type="hidden" name="token" :value="token" />
            <input type="hidden" name="user_id" :value="user.id" />

            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="password">Nova senha</Label>
                    <Input id="password" name="password" type="password" placeholder="Minimo de 8 caracteres" required />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar senha</Label>
                    <Input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repita sua senha" required />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Criar senha e acessar
                </Button>
            </div>
        </Form>
    </AuthBase>
</template>
