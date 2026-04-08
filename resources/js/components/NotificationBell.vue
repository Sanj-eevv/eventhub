<script setup lang="ts">
import { router, usePage } from "@inertiajs/vue3";
import { formatDistanceToNow } from "date-fns";
import { Bell } from "lucide-vue-next";
import { computed } from "vue";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { read, readAll } from "@/wayfinder/routes/notifications";

const props = withDefaults(defineProps<{ variant?: "dashboard" | "home" }>(), {
    variant: "dashboard",
});

const theme = computed(() =>
    props.variant === "home"
        ? {
              trigger:
                  "relative flex h-8 w-8 items-center justify-center rounded border border-sf-border text-sf-muted transition-all duration-200 hover:border-sf-border hover:text-sf-text",
              content: "bg-sf-bg border-sf-border-subtle text-sf-text",
              headerBorder: "border-sf-border-subtle",
              markAll:
                  "text-xs text-sf-muted transition-colors hover:text-sf-text",
              item: "flex w-full flex-col gap-1 px-4 py-3 text-left bg-sf-bg transition-colors hover:bg-sf-surface",
              body: "text-xs text-sf-muted leading-snug",
              timestamp: "text-xs text-sf-muted/60",
              emptyIcon: "size-8 text-sf-muted/40",
              emptyText: "text-sm text-sf-muted",
          }
        : {
              trigger:
                  "relative flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground",
              content: "",
              headerBorder: "border-border",
              markAll:
                  "text-xs text-muted-foreground transition-colors hover:text-foreground",
              item: "flex w-full flex-col gap-1 px-4 py-3 text-left transition-colors hover:bg-accent",
              body: "text-xs text-muted-foreground leading-snug",
              timestamp: "text-xs text-muted-foreground/60",
              emptyIcon: "size-8 text-muted-foreground/40",
              emptyText: "text-sm text-muted-foreground",
          },
);

interface Notification {
    id: string;
    data: {
        title: string;
        body: string;
        url: string | null;
    };
    created_at: string;
}

const page = usePage<{
    notifications: Notification[];
    unread_notifications_count: number;
}>();

const notifications = computed(() => page.props.notifications);
const unreadCount = computed(() => page.props.unread_notifications_count);

function markAsRead(notification: Notification): void {
    router.patch(read(notification.id).url, {}, { preserveScroll: true });

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
                class="flex items-center justify-between border-b px-4 py-3"
                :class="theme.headerBorder"
            >
                <span class="text-sm font-semibold">Notifications</span>
                <button
                    v-if="unreadCount > 0"
                    type="button"
                    :class="theme.markAll"
                    @click="markAllAsRead"
                >
                    Mark all as read
                </button>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div
                    v-if="notifications.length === 0"
                    class="flex flex-col items-center justify-center gap-2 py-10 text-center"
                >
                    <Bell :class="theme.emptyIcon" />
                    <p :class="theme.emptyText">No new notifications</p>
                </div>

                <button
                    v-for="notification in notifications"
                    :key="notification.id"
                    type="button"
                    :class="theme.item"
                    @click="markAsRead(notification)"
                >
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
                </button>
            </div>
        </PopoverContent>
    </Popover>
</template>
