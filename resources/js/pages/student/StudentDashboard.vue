<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Student {
    id: number;
    name: string;
    email: string;
    cod: string;
}

const props = defineProps<{
    student: Student;
}>();

const getInitials = (name: string): string => {
    return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
};

const logout = () => {
    window.location.href = '/student/logout';
};
</script>

<template>
    <Head title="Dashboard do Estudante" />
    
    <div class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] p-6">
        <!-- Header -->
        <header class="mb-8">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="/imgs/logo-tankcode.png" alt="Tankcode Logo" class="h-10">
                    <div>
                        <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            Bem-vindo, {{ student.name }}!
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Área do Estudante</p>
                    </div>
                </div>
                <button @click="logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                    Sair
                </button>
            </div>
        </header>

        <div class="max-w-6xl mx-auto">
            <!-- Profile Card -->
            <div class="mb-6 bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A]">
                <h2 class="text-[#1b1b18] dark:text-[#EDEDEC] text-lg font-semibold mb-4">Meu Perfil</h2>
                
                <div class="flex items-start gap-6">
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ getInitials(student.name) }}
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nome</p>
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ student.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ student.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Código do Estudante</p>
                            <p class="font-medium text-blue-600">{{ student.cod }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A] hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">📚</div>
                        <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Meus Cursos</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Acesse seus cursos e materiais</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A] hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">🏆</div>
                        <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Certificados</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Veja seus certificados conquistados</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-lg p-6 border border-[#19140035] dark:border-[#3E3E3A] hover:shadow-xl transition-shadow cursor-pointer">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">📅</div>
                        <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Cronograma</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Organize seus estudos</p>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-4">Bem-vindo à sua área de estudos!</h2>
                <p class="text-blue-100 mb-6">
                    Aqui você pode acessar todos os seus cursos, materiais de estudo e acompanhar seu progresso.
                    Comece explorando os recursos disponíveis acima.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <button class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-2 rounded-md font-semibold transition-colors">
                        Explorar Cursos
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-400 text-white px-6 py-2 rounded-md font-semibold transition-colors">
                        Ver Progresso
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
