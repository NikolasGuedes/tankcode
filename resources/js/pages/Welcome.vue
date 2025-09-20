<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user" :href="route('students')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                students
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                    Log in
                    </Link>
                    <Link :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full items-center justify-center overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div class="text-center">
                    <img src="/imgs/logo-tankcode.png" alt="Tankcode Logo" class="mx-auto mb-8">
                    
                    <!-- Botões de acesso -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                        <Link :href="route('student.login')"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-3 text-white font-semibold hover:bg-blue-700 transition-colors">
                            Área do Estudante
                        </Link>
                        
                        <Link v-if="!$page.props.auth.user" :href="route('login')"
                            class="inline-flex items-center justify-center rounded-lg border border-[#19140035] px-8 py-3 text-[#1b1b18] font-semibold hover:bg-gray-50 dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:bg-gray-800 transition-colors">
                            Área Administrativa
                        </Link>
                        
                        <Link v-else :href="route('students')"
                            class="inline-flex items-center justify-center rounded-lg border border-[#19140035] px-8 py-3 text-[#1b1b18] font-semibold hover:bg-gray-50 dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:bg-gray-800 transition-colors">
                            Área Administrativa
                        </Link>
                    </div>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-6 max-w-md mx-auto">
                        Acesse a área do estudante para ver seus cursos e materiais, ou a área administrativa para gerenciar o sistema.
                    </p>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
