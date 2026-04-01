<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import {
    Building2,
    CalendarDays,
    ChevronsUpDown,
    LayoutGrid,
    LogOut,
    Receipt,
    Settings,
    ShieldCheck,
    Users,
} from "lucide-vue-next";
import { computed } from "vue";
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
import { getInitials } from "@/composables/useInitials";
import { usePermission } from "@/composables/usePermission";
import { toUrl } from "@/lib/utils";
import type { NavItem } from "@/types";
import { logout } from "@/wayfinder/routes/auth";
import { index } from "@/wayfinder/routes/dashboard";
import { home } from "@/wayfinder/routes";
import { index as eventsIndex } from "@/wayfinder/routes/dashboard/events";
import { index as ordersIndex } from "@/wayfinder/routes/dashboard/orders";
import { index as orgsIndex } from "@/wayfinder/routes/dashboard/organizations";
import { index as rolesIndex } from "@/wayfinder/routes/dashboard/roles";
import { index as usersIndex } from "@/wayfinder/routes/dashboard/users";
import { edit as settingsEdit } from "@/wayfinder/routes/dashboard/settings";
import AppLogo from "./AppLogo.vue";
import UserRoleBadge from "./UserRoleBadge.vue";
import { Avatar, AvatarFallback } from "./ui/avatar";

const pageProps = usePage<{
    auth: { user: { name: string; email: string } };
}>().props;

const mainNavItems: NavItem[] = [
    {
        title: "Dashboard",
        href: index(),
        icon: LayoutGrid,
        show: true,
    },
    {
        title: "Organizations",
        href: orgsIndex(),
        icon: Building2,
        matchPrefix: true,
        show: usePermission("organization")("viewAny"),
    },
    {
        title: "Users",
        href: usersIndex(),
        icon: Users,
        matchPrefix: true,
        show: usePermission("user")("viewAny"),
    },
    {
        title: "Events",
        href: eventsIndex(),
        icon: CalendarDays,
        matchPrefix: true,
        show: usePermission("event")("viewAny"),
    },
    {
        title: "Orders",
        href: ordersIndex(),
        icon: Receipt,
        matchPrefix: true,
        show: usePermission("order")("viewAny"),
    },
    {
        title: "Roles",
        href: rolesIndex(),
        icon: ShieldCheck,
        matchPrefix: true,
        show: usePermission("role")("viewAny"),
    },
    {
        title: "Settings",
        href: settingsEdit(),
        icon: Settings,
        matchPrefix: true,
        show: usePermission("setting")("update"),
    },
];
const visibleNavItems = computed(() =>
    mainNavItems.filter((item) => item.show),
);

const { isCurrentUrl, currentUrl } = useCurrentUrl();

function isNavItemActive(item: NavItem): boolean {
    if (item.matchPrefix) {
        const urlString = toUrl(item.href);
        try {
            const pathname = urlString.startsWith("http")
                ? new URL(urlString).pathname
                : urlString;
            return currentUrl.value.startsWith(pathname);
        } catch {
            return false;
        }
    }
    return isCurrentUrl(item.href);
}

const user = pageProps.auth.user;
const { isMobile, state } = useSidebar();

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
                        <Link :href="home()">
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
                        v-for="item in visibleNavItems"
                        :key="item.title"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="isNavItemActive(item)"
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
                                        <div class="flex flex-1 items-center gap-2 text-left">
                                            <span
                                                class="truncate text-sm font-medium"
                                                >{{ user.name }}</span
                                            >
                                            <UserRoleBadge />
                                        </div>
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
