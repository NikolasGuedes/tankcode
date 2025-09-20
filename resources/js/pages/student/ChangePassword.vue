<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

interface Student {
    name: string;
    email: string;
}

const props = defineProps<{
    student: Student;
}>();

const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const submit = () => {
    form.post(route('student.change-password.submit'), {
        onFinish: () => {
            form.reset('current_password', 'new_password', 'new_password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Alterar Senha - Primeiro Acesso" />
    
    <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="/imgs/logo-tankcode.png" alt="Tankcode Logo" class="mx-auto mb-4 h-16">
                <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Primeiro Acesso</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Olá, <strong>{{ props.student.name }}</strong>! Por favor, altere sua senha para continuar.
                </p>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-yellow-600 dark:text-yellow-400 text-xl">⚠️</span>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Alteração Obrigatória</h3>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                            Por segurança, você deve alterar sua senha antes de acessar o sistema.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A]">
                <h2 class="text-center text-[#1b1b18] dark:text-[#EDEDEC] text-lg font-semibold mb-6">Alterar Senha</h2>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 block">
                            Senha Atual
                        </label>
                        <input 
                            v-model="form.current_password" 
                            type="password" 
                            placeholder="Digite sua senha atual (password123)"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required 
                        />
                        <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 block">
                            Nova Senha
                        </label>
                        <input 
                            v-model="form.new_password" 
                            type="password" 
                            placeholder="Digite sua nova senha (mínimo 8 caracteres)"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required 
                        />
                        <div v-if="form.errors.new_password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.new_password }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 block">
                            Confirmar Nova Senha
                        </label>
                        <input 
                            v-model="form.new_password_confirmation" 
                            type="password" 
                            placeholder="Digite novamente sua nova senha"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required 
                        />
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <strong>Dicas para uma senha segura:</strong><br>
                            • Mínimo de 8 caracteres<br>
                            • Use letras maiúsculas e minúsculas<br>
                            • Inclua números e símbolos<br>
                            • Evite informações pessoais
                        </p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md transition-colors disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Alterando Senha...' : 'Alterar Senha e Continuar' }}
                    </button>
                </form>
            </div>

            <div class="text-center mt-6">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Precisa de ajuda? Entre em contato com o suporte.
                </p>
            </div>
        </div>
    </div>
</template>
