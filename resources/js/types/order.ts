import type { StatusLabel } from "@/types";
import type { EventResource, TicketResource } from "@/types/event";
import type { App } from "@/wayfinder/types";

export type OrderResource = Pick<
    App.Models.Order,
    "uuid" | "currency" | "total" | "reserved_at" | "expires_at" | "paid_at"
> & {
    status: StatusLabel<App.Enums.OrderStatus>;
    event?: EventResource;
    tickets?: TicketResource[];
};
