<script setup lang="ts">
import { Head, InfiniteScroll, router } from "@inertiajs/vue3";
import { formatDistanceToNow } from "date-fns";
import { Bell } from "lucide-vue-next";
import PageContainer from "@/components/PageContainer.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PaginatedResponse } from "@/types";
import type { NotificationResource } from "@/types/notification";
import { read, readAll } from "@/wayfinder/routes/notifications";

const props = defineProps<{
    notifications: PaginatedResponse<NotificationResource>;
}>();

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
    <HomeLayout>
        <Head title="Notifications" />

        <PageContainer>
            <div class="mb-12 flex items-end justify-between">
                <div>
                    <p
                        class="font-body text-xs tracking-[0.3em] uppercase text-sf-gold mb-3"
                    >
                        Account
                    </p>
                    <h1
                        class="font-display font-semibold text-[clamp(2rem,5vw,3.5rem)] text-sf-text leading-none"
                    >
                        Notifications
                    </h1>
                </div>
                <button
                    v-if="hasUnread"
                    type="button"
                    class="font-body text-sm text-sf-muted transition-colors hover:text-sf-text"
                    @click="markAllAsRead"
                >
                    Mark all as read
                </button>
            </div>

            <div
                v-if="notifications.data.length === 0"
                class="py-24 text-center"
            >
                <div
                    class="inline-flex h-14 w-14 items-center justify-center rounded-full border border-sf-border mb-5"
                >
                    <Bell class="h-6 w-6 text-sf-tertiary" />
                </div>
                <p class="font-display text-lg font-medium text-sf-tertiary">
                    No notifications yet
                </p>
                <p class="font-body text-sm text-sf-tertiary mt-1">
                    We'll let you know when something happens.
                </p>
            </div>

            <InfiniteScroll v-else data="notifications" class="space-y-2">
                <button
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    type="button"
                    class="group flex w-full items-start gap-4 rounded-xl border px-5 py-4 text-left transition-all duration-200"
                    :class="
                        notification.read_at
                            ? 'border-sf-border-subtle bg-sf-surface opacity-60 hover:opacity-80'
                            : 'border-sf-border bg-sf-surface hover:border-sf-gold/30 hover:bg-sf-surface-raised'
                    "
                    @click="markAsRead(notification)"
                >
                    <span
                        class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                        :class="notification.read_at ? 'bg-transparent' : 'bg-sf-gold'"
                    />
                    <div class="min-w-0 flex-1">
                        <p
                            class="font-display text-base font-medium text-sf-text"
                        >
                            {{ notification.data.title }}
                        </p>
                        <p class="font-body text-sm text-sf-muted mt-0.5">
                            {{ notification.data.body }}
                        </p>
                        <p class="font-body text-xs text-sf-tertiary mt-1.5">
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
                            class="h-20 rounded-xl bg-sf-surface-raised animate-pulse"
                        />
                    </div>
                </template>
            </InfiniteScroll>
        </PageContainer>
    </HomeLayout>
</template>
