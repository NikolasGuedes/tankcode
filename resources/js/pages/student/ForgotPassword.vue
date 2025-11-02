<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Mail, ArrowLeft } from 'lucide-vue-next';
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

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('student.password.email'));
};
</script>

<template>
    <Head title="Esqueci minha Senha" />
    
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="max-w-md w-full bg-card border border-border rounded-lg shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link :href="route('home')" class="inline-block mb-6 hover:opacity-80 transition-opacity">
                    <img src="/imgs/logo-tankcode.png" alt="Logo TANKCODE" class="h-12 mx-auto" />
                </Link>
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Mail class="w-8 h-8 text-primary" />
                </div>
                <h1 class="text-2xl font-bold text-foreground mb-2">
                    Esqueci minha Senha
                </h1>
                <p class="text-muted-foreground text-sm">
                    Digite seu email para receber instruções de redefinição de senha
                </p>
            </div>

            <!-- Flash Messages -->
            <div v-if="flashError" class="mb-4 p-3 bg-destructive/10 border border-destructive/20 rounded-lg">
                <p class="text-destructive text-sm">{{ flashError }}</p>
            </div>

            <div v-if="flashSuccess" class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                <p class="text-green-500 text-sm">{{ flashSuccess }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Email
                    </label>
                    <Input 
                        id="email"
                        v-model="form.email" 
                        type="email" 
                        placeholder="seu.email@exemplo.com"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                        autofocus
                    />
                    <div v-if="form.errors.email" class="text-destructive text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <Button 
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-medium"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Enviando...' : 'Enviar Link de Recuperação' }}
                </Button>

                <Link 
                    :href="route('student.login')" 
                    class="flex items-center justify-center gap-2 text-sm text-primary hover:text-primary/80 transition-colors mt-4"
                >
                    <ArrowLeft class="w-4 h-4" />
                    Voltar para o login
                </Link>
            </form>

            <div class="mt-6 p-3 bg-accent/10 border border-accent/20 rounded-lg">
                <p class="text-foreground text-xs">
                    <strong>Nota:</strong> O link de recuperação será enviado apenas se o email estiver cadastrado e verificado no sistema.
                </p>
            </div>
        </div>
    </div>
</template>
