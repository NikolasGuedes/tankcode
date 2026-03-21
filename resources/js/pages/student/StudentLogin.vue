<script setup lang="ts">
import { login as studentLogin } from '@/actions/App/Http/Controllers/StudentAuthController';
import { showRequestForm as studentForgotPassword } from '@/actions/App/Http/Controllers/StudentPasswordResetController';
import { home } from '@/routes';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Flash {
    error?: string;
    success?: string;
    info?: string;
}

const page = usePage();
const flashError = computed(() => (page.props.flash as Flash)?.error);
const flashSuccess = computed(() => (page.props.flash as Flash)?.success);
const flashInfo = computed(() => (page.props.flash as Flash)?.info);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(studentLogin.url(), {
        onFinish: () => {
            form.password = '';
        },
    });
};
</script>

<template>
    <Head title="Login do Estudante" />
    
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="max-w-md w-full bg-card border border-border rounded-lg shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link :href="home.url()" class="inline-block mb-6 hover:opacity-80 transition-opacity">
                    <img src="/imgs/logo-tankcode.png" alt="Logo TANKCODE" class="h-12 mx-auto" />
                </Link>
                <h1 class="text-2xl font-bold text-foreground mb-2">
                    Faça login na sua conta
                </h1>
                <p class="text-muted-foreground text-sm">
                    Digite seu email e senha abaixo para fazer login
                </p>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashError" class="mb-4 p-3 bg-destructive/10 border border-destructive/20 rounded-lg">
                <p class="text-destructive text-sm">{{ flashError }}</p>
            </div>

            <div v-if="flashSuccess" class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                <p class="text-green-500 text-sm">{{ flashSuccess }}</p>
            </div>

            <div v-if="flashInfo" class="mb-4 p-3 bg-primary/10 border border-primary/20 rounded-lg">
                <p class="text-primary text-sm">{{ flashInfo }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Endereço de email
                    </label>
                    <Input 
                        v-model="form.email" 
                        type="email" 
                        placeholder="email@exemplo.com"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                    <div v-if="form.errors.email" class="text-destructive text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-foreground">
                            Senha
                        </label>
                        <Link 
                            :href="studentForgotPassword.url()" 
                            class="text-sm text-primary hover:underline"
                        >
                            Esqueceu a senha?
                        </Link>
                    </div>
                    <Input 
                        v-model="form.password" 
                        type="password" 
                        placeholder="Senha"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                </div>

                <Button 
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-medium"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Entrando...' : 'Entrar' }}
                </Button>
            </form>

            <div class="mt-6 text-center">
                <Link :href="home.url()" class="text-sm text-primary hover:text-primary/80 transition-colors">
                    ← Voltar para a página inicial
                </Link>
            </div>
        </div>
    </div>
</template>
