import type { Illuminate } from "@/wayfinder/types";

export type NotificationData = {
    title: string;
    body: string;
    url: string | null;
};

export type NotificationResource = Pick<
    Illuminate.Notifications.DatabaseNotification,
    "id" | "read_at"
> & {
    data: NotificationData;
    created_at: string;
};
