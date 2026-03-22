<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { usePermission } from "@/composables/usePermission";
import {
    login as loginCreate,
    register as registerCreate,
} from "@/wayfinder/routes/auth";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { index as ordersIndex } from "@/wayfinder/routes/orders";

const page = usePage();
const appName = page.props.name as string;
const user = computed(
    () => page.props.auth.user as { name: string; email: string } | null,
);

const canDashboard = usePermission("dashboard");
const canAccessDashboard = computed(() => canDashboard("access"));
</script>

<template>
    <footer
        class="mt-28 border-t border-sf-border-subtle transition-colors duration-200"
    >
        <div
            class="mx-auto max-w-7xl px-5 sm:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-5"
        >
            <div class="flex items-center gap-2.5">
                <span class="inline-block w-1 h-1 rounded-full bg-sf-gold" />
                <span
                    class="font-display text-sm font-medium tracking-[0.25em] uppercase text-sf-muted"
                >
                    {{ appName }}
                </span>
            </div>
            <p class="text-xs text-sf-muted font-light">
                © {{ new Date().getFullYear() }} {{ appName }}. All rights
                reserved.
            </p>
            <div class="flex gap-6">
                <template v-if="user">
                    <Link
                        :href="ordersIndex()"
                        class="text-xs text-sf-muted hover:text-sf-text transition-colors"
                        >My Orders</Link
                    >
                    <Link
                        v-if="canAccessDashboard"
                        :href="dashboardIndex()"
                        class="text-xs text-sf-muted hover:text-sf-text transition-colors"
                        >Dashboard</Link
                    >
                </template>
                <template v-else>
                    <Link
                        :href="loginCreate()"
                        class="text-xs text-sf-muted hover:text-sf-text transition-colors"
                        >Sign In</Link
                    >
                    <Link
                        :href="registerCreate()"
                        class="text-xs text-sf-muted hover:text-sf-text transition-colors"
                        >Register</Link
                    >
                </template>
            </div>
        </div>
    </footer>
</template>
