<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Flash {
    error?: string;
    success?: string;
}

const page = usePage();
const flashError = computed(() => (page.props.flash as Flash)?.error);

const props = defineProps<{
    token: string;
    email: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('student.password.update'), {
        onFinish: () => {
            form.password = '';
            form.password_confirmation = '';
        },
    });
};
</script>

<template>
    <Head title="Redefinir Senha" />
    
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="max-w-md w-full bg-card border border-border rounded-lg shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link :href="route('home')" class="inline-block mb-6 hover:opacity-80 transition-opacity">
                    <img src="/imgs/logo-tankcode.png" alt="Logo TANKCODE" class="h-12 mx-auto" />
                </Link>
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Lock class="w-8 h-8 text-primary" />
                </div>
                <h1 class="text-2xl font-bold text-foreground mb-2">
                    Redefinir Senha
                </h1>
                <p class="text-muted-foreground text-sm">
                    Digite sua nova senha abaixo
                </p>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashError" class="mb-4 p-3 bg-destructive/10 border border-destructive/20 rounded-lg">
                <p class="text-destructive text-sm">{{ flashError }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Endereço de email
                    </label>
                    <Input 
                        id="email"
                        v-model="form.email" 
                        type="email" 
                        class="w-full bg-background border-border text-foreground"
                        readonly
                    />
                    <div v-if="form.errors.email" class="text-destructive text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-2">
                        Nova Senha
                    </label>
                    <Input 
                        id="password"
                        v-model="form.password" 
                        type="password" 
                        placeholder="Digite sua nova senha (mínimo 8 caracteres)"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                    <div v-if="form.errors.password" class="text-destructive text-sm mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-2">
                        Confirmar Nova Senha
                    </label>
                    <Input 
                        id="password_confirmation"
                        v-model="form.password_confirmation" 
                        type="password" 
                        placeholder="Digite sua senha novamente"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                </div>

                <Button 
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-medium"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Redefinindo...' : 'Redefinir Senha' }}
                </Button>
            </form>

            <div class="mt-6 p-3 bg-accent/10 border border-accent/20 rounded-lg">
                <p class="text-foreground text-xs">
                    <strong>Dica:</strong> Use uma senha forte com letras, números e caracteres especiais.
                </p>
            </div>
        </div>
    </div>
</template>
