<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { LayoutGrid } from "lucide-vue-next";
import { LogOut } from "lucide-vue-next";
import { ChevronsUpDown } from "lucide-vue-next";
import {
    DropdownMenu,
    DropdownMenuLabel,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuItem,
    DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import {
    useSidebar,
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroupContent,
    SidebarGroup,
    SidebarGroupLabel,
} from "@/components/ui/sidebar";
import { useCurrentUrl } from "@/composables/useCurrentUrl";
import { logout } from "@/routes/auth";
import { index } from "@/routes/dashboard";
import type { NavItem } from "@/types";
import AppLogo from "./AppLogo.vue";
import { Avatar, AvatarFallback } from "./ui/avatar";
import { useInitials } from "@/composables/useInitials";

const mainNavItems: NavItem[] = [
    {
        title: "Dashboard",
        href: index(),
        icon: LayoutGrid,
    },
];

const pageProps = usePage<{
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}>().props;

const { isCurrentUrl } = useCurrentUrl();
const user = pageProps.auth.user;
const { isMobile, state } = useSidebar();
const { getInitials } = useInitials();

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="index()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Platform</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in mainNavItems"
                        :key="item.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(item.href)"
                            :tooltip="item.title"
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <SidebarGroup class="group-data-[collapsible=icon]:p-0">
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <SidebarMenuButton
                                        size="lg"
                                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                                        data-test="sidebar-menu-button"
                                    >
                                        <Avatar
                                            class="h-8 w-8 overflow-hidden rounded-lg"
                                        >
                                            <AvatarFallback
                                                class="rounded-lg text-black dark:text-white"
                                            >
                                                {{ getInitials(user.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <span
                                            class="truncate text-sm leading-tight font-medium"
                                            >{{ user.name }}</span
                                        >
                                        <ChevronsUpDown
                                            class="ml-auto size-4"
                                        />
                                    </SidebarMenuButton>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent
                                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                                    :side="
                                        isMobile
                                            ? 'bottom'
                                            : state === 'collapsed'
                                              ? 'left'
                                              : 'bottom'
                                    "
                                    align="end"
                                    :side-offset="4"
                                >
                                    <DropdownMenuLabel class="p-0 font-normal">
                                        <div
                                            class="flex items-center gap-2 px-1 py-1.5 text-left text-sm"
                                        >
                                            <Avatar
                                                class="h-8 w-8 overflow-hidden rounded-lg"
                                            >
                                                <AvatarFallback
                                                    class="rounded-lg text-black dark:text-white"
                                                >
                                                    {{ getInitials(user.name) }}
                                                </AvatarFallback>
                                            </Avatar>

                                            <div
                                                class="grid flex-1 text-left text-sm leading-tight"
                                            >
                                                <span
                                                    class="truncate font-medium"
                                                    >{{ user.name }}</span
                                                >
                                                <span
                                                    class="truncate text-xs text-muted-foreground"
                                                    >{{ user.email }}</span
                                                >
                                            </div>
                                        </div>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem :as-child="true">
                                        <Link
                                            class="block w-full cursor-pointer"
                                            :href="logout()"
                                            @click="handleLogout"
                                            as="button"
                                            data-test="logout-button"
                                        >
                                            <LogOut class="mr-2 h-4 w-4" />
                                            Log out
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
