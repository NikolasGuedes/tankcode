<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('student.login.submit'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login do Estudante" />
    
    <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="/imgs/logo-tankcode.png" alt="Tankcode Logo" class="mx-auto mb-4 h-16">
                <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Área do Estudante</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Entre com suas credenciais de estudante</p>
            </div>

            <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A]">
                <h2 class="text-center text-[#1b1b18] dark:text-[#EDEDEC] text-lg font-semibold mb-6">Login</h2>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 block">
                            Email
                        </label>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            placeholder="seu.email@exemplo.com"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required 
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 block">
                            Senha
                        </label>
                        <input 
                            v-model="form.password" 
                            type="password" 
                            placeholder="Sua senha"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required 
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition-colors disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Entrando...' : 'Entrar' }}
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Senha padrão: <code class="bg-gray-200 dark:bg-gray-700 px-1 rounded">password123</code>
                    </p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a 
                    href="/" 
                    class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]"
                >
                    ← Voltar ao início
                </a>
            </div>
        </div>
    </div>
</template>
