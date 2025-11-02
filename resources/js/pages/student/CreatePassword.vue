<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Lock, User, Mail, Hash } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

const props = defineProps<{
    student: {
        id: number;
        name: string;
        email: string;
        cod: string;
    };
    message: string;
}>();

const form = useForm({
    student_id: props.student.id,
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    form.post(route('student.create-password'), {
        onFinish: () => {
            form.password = '';
            form.password_confirmation = '';
        },
    });
};
</script>

<template>
    <Head title="Criar Senha" />
    
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="max-w-md w-full bg-card border border-border rounded-lg shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link :href="route('home')" class="inline-block mb-6 hover:opacity-80 transition-opacity">
                    <div class="flex items-center justify-center gap-2">
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-foreground">TANK<span class="text-primary">CODE</span></span>
                    </div>
                </Link>
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Lock class="w-8 h-8 text-primary" />
                </div>
                <h1 class="text-2xl font-bold text-foreground mb-2">
                    Criar Senha
                </h1>
                <p class="text-muted-foreground text-sm">{{ message }}</p>
            </div>

            <!-- Informações do Estudante -->
            <div class="bg-primary/5 border border-primary/20 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <User class="w-4 h-4 text-primary" />
                    <span class="text-sm font-medium text-foreground">{{ student.name }}</span>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <Mail class="w-4 h-4 text-primary" />
                    <span class="text-sm text-muted-foreground">{{ student.email }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <Hash class="w-4 h-4 text-primary" />
                    <span class="text-sm text-muted-foreground">Código: {{ student.cod }}</span>
                </div>
            </div>

            <form @submit.prevent="submitPassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Nova Senha
                    </label>
                    <Input 
                        v-model="form.password" 
                        type="password" 
                        placeholder="Digite sua senha (mínimo 8 caracteres)"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                    <div v-if="form.errors.password" class="text-destructive text-sm mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Confirmar Senha
                    </label>
                    <Input 
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
                    {{ form.processing ? 'Criando...' : 'Criar Senha e Continuar' }}
                </Button>

                <div class="mt-4 p-3 bg-accent/10 border border-accent/20 rounded-lg">
                    <p class="text-foreground text-xs">
                        <strong>Dica:</strong> Use uma senha forte com letras, números e caracteres especiais.
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
