<script setup lang="ts">
import { Link, router, usePage } from "@inertiajs/vue3";
import { formatDistanceToNow } from "date-fns";
import { Bell } from "lucide-vue-next";
import { computed, ref } from "vue";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import type { NotificationResource } from "@/types/notification";
import { index as dashboardNotificationsIndex } from "@/wayfinder/routes/dashboard/notifications";
import { index as notificationsIndex, read, readAll } from "@/wayfinder/routes/notifications";

const props = withDefaults(defineProps<{ variant?: "dashboard" | "home" }>(), {
    variant: "dashboard",
});

const activeTab = ref<"all" | "unread">("all");

const theme = computed(() =>
    props.variant === "home"
        ? {
              trigger:
                  "relative flex h-8 w-8 items-center justify-center rounded border border-sf-border text-sf-muted transition-all duration-200 hover:border-sf-border hover:text-sf-text",
              content: "bg-sf-bg border-sf-border-subtle text-sf-text",
              headerBorder: "border-sf-border-subtle",
              tabActive: "border-b-2 border-sf-gold text-sf-text",
              tabInactive: "text-sf-muted hover:text-sf-text",
              markAll:
                  "text-xs text-sf-muted transition-colors hover:text-sf-text",
              item: "flex w-full flex-col gap-1 px-4 py-3 text-left bg-sf-bg transition-colors hover:bg-sf-surface",
              itemRead: "opacity-60",
              body: "text-xs text-sf-muted leading-snug",
              timestamp: "text-xs text-sf-muted/60",
              unreadDot: "h-1.5 w-1.5 rounded-full bg-sf-gold shrink-0 mt-1.5",
              emptyIcon: "size-8 text-sf-muted/40",
              emptyText: "text-sm text-sf-muted",
              viewAll: "text-xs text-sf-muted transition-colors hover:text-sf-text",
          }
        : {
              trigger:
                  "relative flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground",
              content: "",
              headerBorder: "border-border",
              tabActive: "border-b-2 border-primary text-foreground",
              tabInactive: "text-muted-foreground hover:text-foreground",
              markAll:
                  "text-xs text-muted-foreground transition-colors hover:text-foreground",
              item: "flex w-full flex-col gap-1 px-4 py-3 text-left transition-colors hover:bg-accent",
              itemRead: "opacity-60",
              body: "text-xs text-muted-foreground leading-snug",
              timestamp: "text-xs text-muted-foreground/60",
              unreadDot: "h-1.5 w-1.5 rounded-full bg-primary shrink-0 mt-1.5",
              emptyIcon: "size-8 text-muted-foreground/40",
              emptyText: "text-sm text-muted-foreground",
              viewAll: "text-xs text-muted-foreground transition-colors hover:text-foreground",
          },
);

const page = usePage<{
    unread_notifications: NotificationResource[];
    all_notifications: NotificationResource[];
    unread_notifications_count: number;
}>();

const unreadNotifications = computed(() => page.props.unread_notifications);
const allNotifications = computed(() => page.props.all_notifications);
const unreadCount = computed(() => page.props.unread_notifications_count);

const activeNotifications = computed(() =>
    activeTab.value === "unread" ? unreadNotifications.value : allNotifications.value,
);

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
    <Popover>
        <PopoverTrigger as-child>
            <button
                type="button"
                :class="theme.trigger"
                aria-label="Notifications"
            >
                <Bell class="size-4" />
                <span
                    v-if="unreadCount > 0"
                    class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-white"
                >
                    {{ unreadCount > 9 ? "9+" : unreadCount }}
                </span>
            </button>
        </PopoverTrigger>

        <PopoverContent class="w-80 p-0" :class="theme.content" align="end">
            <div
                class="flex items-center justify-between border-b px-4 pt-3"
                :class="theme.headerBorder"
            >
                <div class="flex gap-4">
                    <button
                        type="button"
                        class="pb-2.5 text-sm font-medium transition-colors"
                        :class="activeTab === 'all' ? theme.tabActive : theme.tabInactive"
                        @click="activeTab = 'all'"
                    >
                        All
                    </button>
                    <button
                        type="button"
                        class="pb-2.5 text-sm font-medium transition-colors"
                        :class="activeTab === 'unread' ? theme.tabActive : theme.tabInactive"
                        @click="activeTab = 'unread'"
                    >
                        Unread
                        <span
                            v-if="unreadCount > 0"
                            class="ml-1.5 rounded-full bg-destructive px-1.5 py-0.5 text-[10px] font-medium text-white"
                        >
                            {{ unreadCount > 9 ? "9+" : unreadCount }}
                        </span>
                    </button>
                </div>
                <button
                    v-if="unreadCount > 0"
                    type="button"
                    :class="theme.markAll"
                    class="mb-2.5"
                    @click="markAllAsRead"
                >
                    Mark all as read
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div
                    v-if="activeNotifications.length === 0"
                    class="flex flex-col items-center justify-center gap-2 py-10 text-center"
                >
                    <Bell :class="theme.emptyIcon" />
                    <p :class="theme.emptyText">No notifications</p>
                </div>

                <button
                    v-for="notification in activeNotifications"
                    :key="notification.id"
                    type="button"
                    :class="[theme.item, notification.read_at ? theme.itemRead : '']"
                    @click="markAsRead(notification)"
                >
                    <div class="flex items-start gap-2">
                        <span
                            v-if="!notification.read_at"
                            :class="theme.unreadDot"
                        />
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <span class="text-sm font-medium leading-tight">
                                {{ notification.data.title }}
                            </span>
                            <span :class="theme.body">
                                {{ notification.data.body }}
                            </span>
                            <span :class="theme.timestamp">
                                {{
                                    formatDistanceToNow(
                                        new Date(notification.created_at),
                                        { addSuffix: true },
                                    )
                                }}
                            </span>
                        </div>
                    </div>
                </button>
            </div>

            <div
                class="border-t px-4 py-2.5"
                :class="theme.headerBorder"
            >
                <Link
                    :href="variant === 'dashboard' ? dashboardNotificationsIndex().url : notificationsIndex().url"
                    :class="theme.viewAll"
                >
                    View all notifications →
                </Link>
            </div>
        </PopoverContent>
    </Popover>
</template>
