<script setup lang="ts">
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useTheme } from "@/composables/useTheme";
import { index as eventsIndex } from "@/wayfinder/routes/events";
import { index as ordersIndex } from "@/wayfinder/routes/orders";
import { login as loginCreate, logout, register as registerCreate } from "@/wayfinder/routes/auth";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";

const page = usePage();
const appName = page.props.name as string;
const user = computed(() => page.props.auth?.user as { name: string; email: string } | null);
const can = computed(() => page.props.can as { event?: { viewAny?: boolean }; organization?: { viewAny?: boolean } } | null);
const canAccessDashboard = computed(() => !!(can.value?.event?.viewAny || can.value?.organization?.viewAny));

const mobileOpen = ref(false);

const { isDark, toggle } = useTheme();

const handleLogout = (): void => {
    router.flushAll();
};
</script>

<template>
    <div class="min-h-screen bg-sf-bg text-sf-text font-body transition-colors duration-200">

        <!-- Nav -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-sf-bg/90 backdrop-blur-md border-b border-sf-border-subtle transition-colors duration-200">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Logo -->
                    <Link
                        :href="eventsIndex()"
                        class="flex items-center gap-2.5 group"
                    >
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-sf-gold group-hover:scale-150 transition-transform duration-300" />
                        <span class="font-display text-base font-semibold tracking-[0.25em] uppercase text-sf-text">
                            {{ appName }}
                        </span>
                    </Link>

                    <!-- Desktop nav links -->
                    <nav class="hidden md:flex items-center gap-8">
                        <Link
                            :href="eventsIndex()"
                            class="text-sm tracking-wide text-sf-muted hover:text-sf-text transition-colors duration-200"
                        >
                            Browse Events
                        </Link>
                        <template v-if="user">
                            <Link
                                :href="ordersIndex()"
                                class="text-sm tracking-wide text-sf-muted hover:text-sf-text transition-colors duration-200"
                            >
                                My Orders
                            </Link>
                            <Link
                                v-if="canAccessDashboard"
                                :href="dashboardIndex()"
                                class="text-sm tracking-wide text-sf-muted hover:text-sf-text transition-colors duration-200"
                            >
                                Dashboard
                            </Link>
                        </template>
                    </nav>

                    <!-- Auth + theme toggle -->
                    <div class="hidden md:flex items-center gap-5">
                        <!-- Theme toggle -->
                        <button
                            type="button"
                            class="h-8 w-8 flex items-center justify-center rounded border border-sf-border text-sf-muted hover:text-sf-text hover:border-sf-border transition-all duration-200"
                            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                            @click="toggle"
                        >
                            <!-- Sun icon (show in dark mode to switch to light) -->
                            <svg v-if="isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <!-- Moon icon (show in light mode to switch to dark) -->
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </button>

                        <template v-if="user">
                            <span class="text-sm text-sf-muted font-light">{{ user.name }}</span>
                            <Link
                                :href="logout()"
                                method="post"
                                as="button"
                                class="text-sm text-sf-muted hover:text-sf-text transition-colors duration-200"
                                @click="handleLogout"
                            >
                                Sign out
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="loginCreate()"
                                class="text-sm text-sf-muted hover:text-sf-text transition-colors duration-200"
                            >
                                Sign in
                            </Link>
                            <Link
                                :href="registerCreate()"
                                class="text-sm px-5 py-2 bg-sf-ember text-white rounded hover:bg-sf-ember-hover active:scale-95 transition-all duration-200 tracking-wide"
                            >
                                Get Tickets
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile: theme + hamburger -->
                    <div class="md:hidden flex items-center gap-2">
                        <button
                            type="button"
                            class="p-2 text-sf-muted hover:text-sf-text transition-colors"
                            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                            @click="toggle"
                        >
                            <svg v-if="isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </button>
                        <button
                            class="p-2 text-sf-muted hover:text-sf-text transition-colors"
                            @click="mobileOpen = !mobileOpen"
                        >
                            <svg v-if="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div
                v-if="mobileOpen"
                class="md:hidden border-t border-sf-border-subtle bg-sf-surface px-5 py-5 space-y-1 transition-colors duration-200"
            >
                <Link :href="eventsIndex()" class="block py-3 text-sm text-sf-muted hover:text-sf-text border-b border-sf-border-subtle transition-colors">Browse Events</Link>
                <template v-if="user">
                    <Link :href="ordersIndex()" class="block py-3 text-sm text-sf-muted hover:text-sf-text border-b border-sf-border-subtle transition-colors">My Orders</Link>
                    <Link v-if="canAccessDashboard" :href="dashboardIndex()" class="block py-3 text-sm text-sf-muted hover:text-sf-text border-b border-sf-border-subtle transition-colors">Dashboard</Link>
                    <Link :href="logout()" method="post" as="button" class="block w-full text-left py-3 text-sm text-sf-muted hover:text-sf-text transition-colors" @click="handleLogout">Sign out</Link>
                </template>
                <template v-else>
                    <Link :href="loginCreate()" class="block py-3 text-sm text-sf-muted hover:text-sf-text border-b border-sf-border-subtle transition-colors">Sign in</Link>
                    <Link :href="registerCreate()" class="block py-3 text-sm text-sf-ember hover:text-sf-ember-hover transition-colors">Get Tickets</Link>
                </template>
            </div>
        </header>

        <!-- Main -->
        <main class="pt-16">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-28 border-t border-sf-border-subtle transition-colors duration-200">
            <div class="mx-auto max-w-7xl px-5 sm:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block w-1 h-1 rounded-full bg-sf-gold" />
                    <span class="font-display text-sm font-medium tracking-[0.25em] uppercase text-sf-muted">
                        {{ appName }}
                    </span>
                </div>
                <p class="text-xs text-sf-muted font-light">
                    © {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.
                </p>
                <div class="flex gap-6">
                    <Link :href="eventsIndex()" class="text-xs text-sf-muted hover:text-sf-text transition-colors">Events</Link>
                    <template v-if="user">
                        <Link :href="ordersIndex()" class="text-xs text-sf-muted hover:text-sf-text transition-colors">My Orders</Link>
                        <Link v-if="canAccessDashboard" :href="dashboardIndex()" class="text-xs text-sf-muted hover:text-sf-text transition-colors">Dashboard</Link>
                    </template>
                    <template v-else>
                        <Link :href="loginCreate()" class="text-xs text-sf-muted hover:text-sf-text transition-colors">Sign In</Link>
                        <Link :href="registerCreate()" class="text-xs text-sf-muted hover:text-sf-text transition-colors">Register</Link>
                    </template>
                </div>
            </div>
        </footer>

    </div>
</template>
