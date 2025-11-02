<script setup lang="ts">
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
    form.post(route('student.login.submit'), {
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
                <h1 class="text-2xl font-bold text-foreground mb-2">
                    Log in to your account
                </h1>
                <p class="text-muted-foreground text-sm">
                    Enter your email and password below to log in
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
                        Email address
                    </label>
                    <Input 
                        v-model="form.email" 
                        type="email" 
                        placeholder="email@example.com"
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
                            Password
                        </label>
                    </div>
                    <Input 
                        v-model="form.password" 
                        type="password" 
                        placeholder="Password"
                        class="w-full bg-background border-border text-foreground placeholder:text-muted-foreground"
                        required
                    />
                </div>

                <Button 
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-medium"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Logging in...' : 'Log in' }}
                </Button>
            </form>

            <div class="mt-6 text-center">
                <Link :href="route('home')" class="text-sm text-primary hover:text-primary/80 transition-colors">
                    ← Back to home
                </Link>
            </div>
        </div>
    </div>
</template>
