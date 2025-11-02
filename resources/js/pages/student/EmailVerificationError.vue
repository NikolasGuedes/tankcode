<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { XCircle } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

defineProps<{
    message: string;
}>();
</script>

<template>
    <Head title="Erro na Verificação" />
    
    <div class="min-h-screen flex items-center justify-center bg-background p-4">
        <div class="max-w-md w-full bg-card border border-border rounded-lg shadow-2xl p-8 text-center">
            <!-- Logo -->
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
            
            <div class="w-16 h-16 bg-destructive/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <XCircle class="w-8 h-8 text-destructive" />
            </div>
            <h1 class="text-2xl font-bold text-foreground mb-2">
                Erro na Verificação
            </h1>
            <p class="text-muted-foreground mb-6">{{ message }}</p>
            
            <Button 
                @click="$inertia.visit(route('home'))"
                class="w-full bg-primary hover:bg-primary/90 text-primary-foreground"
            >
                Voltar para Início
            </Button>
        </div>
    </div>
</template>
