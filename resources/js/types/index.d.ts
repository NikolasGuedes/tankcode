import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User | null;
    navigation: NavItem[];
}

export interface PointContextItem {
    id: number | 'all';
    name: string;
    cnpj: string | null;
}

export interface PointContext {
    enabled: boolean;
    current: PointContextItem | null;
    available: PointContextItem[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    pointContext: PointContext;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    status: string;
    last_login_at: string | null;
    role: {
        name: string;
        label: string;
    } | null;
    school: {
        id: number;
        name: string;
    } | null;
    email_verified_at: string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
