<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import AppSidebar from "@/components/AppSidebar.vue";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import NotificationBell from "@/components/NotificationBell.vue";
import { SidebarProvider, SidebarInset, SidebarTrigger } from "@/components/ui/sidebar";
import { useTheme } from "@/composables/useTheme";
import type { BreadcrumbItem } from "@/types";

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
const page = usePage<{
    sidebarOpen: boolean;
}>();
const sidebarOpen = page.props.sidebarOpen;
const { isDark, toggle } = useTheme();
</script>

<template>
    <SidebarProvider :defaultOpen="sidebarOpen">
        <AppSidebar />
        <SidebarInset class="overflow-hidden">
             <header
                    class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
                >
                    <div class="flex items-center gap-2">
                        <SidebarTrigger class="-ml-1" />
                        <template v-if="breadcrumbs && breadcrumbs.length > 0">
                            <Breadcrumbs :breadcrumbs="breadcrumbs" />
                        </template>
                    </div>
                    <div class="ml-auto flex items-center gap-1">
                        <NotificationBell />
                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                            @click="toggle"
                        >
                            <svg v-if="isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                        </button>
                    </div>
                </header>
            <slot />
        </sidebarInset>
    </SidebarProvider>
</template>
