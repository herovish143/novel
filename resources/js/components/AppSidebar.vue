<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, CheckSquare, Activity, DollarSign, LayoutGrid } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import CommandPalette from '@/components/CommandPalette.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as novelIndex } from '@/routes/novels';
import { index as reviewIndex } from '@/routes/reviews';
import { index as pipelineIndex } from '@/routes/pipeline';
import { index as costIndex } from '@/routes/costs';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Web Novels',
        href: novelIndex.url(),
        icon: BookOpen,
    },
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Review Queue',
        href: reviewIndex.url(),
        icon: CheckSquare,
    },
    {
        title: 'Pipeline Monitor',
        href: pipelineIndex.url(),
        icon: Activity,
    },
    {
        title: 'Cost Analytics',
        href: costIndex.url(),
        icon: DollarSign,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <div class="px-2 pt-2">
                <CommandPalette />
            </div>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
