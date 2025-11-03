<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CheckCircle2, XCircle } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

const props = defineProps<{
    success: boolean;
    message: string;
    loginUrl?: string;
}>();
</script>

<template>
    <Head title="Verificação de Email" />
    
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800 p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8">
            <div class="text-center">
                <CheckCircle2 v-if="props.success" class="w-20 h-20 text-green-500 mx-auto mb-4" />
                <XCircle v-else class="w-20 h-20 text-red-500 mx-auto mb-4" />
                
                <h1 class="text-2xl font-bold mb-4" :class="success ? 'text-green-700' : 'text-red-700'">
                    {{ success ? 'Email Verificado!' : 'Erro na Verificação' }}
                </h1>
                
                <p class="text-gray-600 mb-6">{{ message }}</p>
                
                <Button 
                    v-if="success && loginUrl" 
                    @click="$inertia.visit(loginUrl)"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white"
                >
                    Ir para Login
                </Button>
                
                <Button 
                    v-else
                    @click="$inertia.visit('/')"
                    class="w-full bg-gray-600 hover:bg-gray-700 text-white"
                >
                    Voltar para Início
                </Button>
            </div>
        </div>
    </div>
</template>
