<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Blocks, LayoutGrid, School, UserRound } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

const icons: Record<string, typeof LayoutGrid> = {
    'Visao geral': LayoutGrid,
    Escolas: Blocks,
    Organizacoes: Blocks,
    'Pontos de Ensino': School,
    Usuarios: UserRound,
    'Minha escola': School,
    Diretoria: LayoutGrid,
    Professor: LayoutGrid,
    Aluno: LayoutGrid,
    Dashboard: LayoutGrid,
};

const mainNavItems = computed<NavItem[]>(() =>
    (page.props.auth.navigation ?? []).map((item) => ({
        ...item,
        icon: icons[item.title] ?? LayoutGrid,
    })),
);

const homeHref = computed(() => mainNavItems.value[0]?.href ?? '/dashboard');

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeHref">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>

            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
