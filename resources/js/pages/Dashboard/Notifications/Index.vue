<script setup lang="ts">
import { Head, InfiniteScroll, router } from "@inertiajs/vue3";
import { formatDistanceToNow } from "date-fns";
import { Bell } from "lucide-vue-next";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem, PaginatedResponse } from "@/types";
import type { NotificationResource } from "@/types/notification";
import { index, read, readAll } from "@/wayfinder/routes/notifications";

const props = defineProps<{
    notifications: PaginatedResponse<NotificationResource>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Notifications", href: index().url },
];

const hasUnread = props.notifications.data.some((notification) => !notification.read_at);

function markAsRead(notification: NotificationResource): void {
    if (!notification.read_at) {
        router.patch(read(notification.id).url, {}, { preserveScroll: true });
    }

    if (notification.data.url) {
        router.visit(notification.data.url);
    }
}

function markAllAsRead(): void {
    router.delete(readAll().url, { preserveScroll: true });
}
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Notifications" />

        <div class="flex flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Notifications</h1>
                <button
                    v-if="hasUnread"
                    type="button"
                    class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                    @click="markAllAsRead"
                >
                    Mark all as read
                </button>
            </div>

            <div
                v-if="notifications.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 rounded-lg border border-dashed py-24 text-center"
            >
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-muted"
                >
                    <Bell class="h-5 w-5 text-muted-foreground" />
                </div>
                <p class="text-sm font-medium text-muted-foreground">
                    No notifications yet
                </p>
                <p class="text-xs text-muted-foreground/60">
                    We'll let you know when something happens.
                </p>
            </div>

            <InfiniteScroll v-else data="notifications" class="space-y-2">
                <button
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    type="button"
                    class="group flex w-full items-start gap-4 rounded-lg border px-4 py-3.5 text-left text-foreground transition-all duration-200"
                    :class="
                        notification.read_at
                            ? 'border-border/50 bg-transparent opacity-60 hover:opacity-80'
                            : 'border-border bg-card hover:bg-accent'
                    "
                    @click="markAsRead(notification)"
                >
                    <span
                        class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                        :class="notification.read_at ? 'bg-transparent' : 'bg-primary'"
                    />
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium leading-snug">
                            {{ notification.data.title }}
                        </p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ notification.data.body }}
                        </p>
                        <p class="mt-1.5 text-xs text-muted-foreground/60">
                            {{
                                formatDistanceToNow(
                                    new Date(notification.created_at),
                                    { addSuffix: true },
                                )
                            }}
                        </p>
                    </div>
                </button>

                <template #loading>
                    <div class="mt-2 space-y-2">
                        <div
                            v-for="i in 3"
                            :key="i"
                            class="h-16 animate-pulse rounded-lg bg-muted"
                        />
                    </div>
                </template>
            </InfiniteScroll>
        </div>
    </DashboardLayout>
</template>
