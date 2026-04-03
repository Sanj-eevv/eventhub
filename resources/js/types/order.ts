import type { StatusLabel } from "@/types";
import type { EventResource, TicketResource } from "@/types/event";
import type { App } from "@/wayfinder/types";

export type OrderResource = Pick<
    App.Models.Order,
    | "uuid"
    | "currency"
    | "total"
    | "reserved_at"
    | "expires_at"
    | "paid_at"
    | "cancelled_at"
    | "refunded_at"
> & {
    status: StatusLabel<App.Enums.OrderStatus>;
    refund_status: App.Enums.RefundStatus | null;
    event?: EventResource;
    tickets?: TicketResource[];
};

export type OrderIndexItem = {
    uuid: string;
    status: StatusLabel<App.Enums.OrderStatus>;
    total: number;
    currency: string;
    paid_at: string | null;
    created_at: string;
    event: { title: string } | null;
    user: { name: string; email: string } | null;
};

export type OrderFilterProps = {
    search?: string;
};
