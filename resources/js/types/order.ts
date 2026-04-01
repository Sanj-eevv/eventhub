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
    can_download_pdf: boolean;
    can_cancel: boolean;
    event?: EventResource;
    tickets?: TicketResource[];
};
