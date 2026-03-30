import type { StatusLabel } from "@/types";
import type { TicketTypeResource } from "@/types/event";
import type { App } from "@/wayfinder/types";

export type OrderTicket = Pick<
    App.Models.Ticket,
    "uuid" | "booking_reference" | "qr_code_path" | "attendee_name"
> & {
    status: App.Enums.TicketStatus;
    ticket_type: TicketTypeResource;
};

export type Order = Pick<
    App.Models.Order,
    "uuid" | "currency" | "total" | "reserved_at" | "expires_at" | "paid_at"
> & {
    status: StatusLabel<App.Enums.OrderStatus>;
    event: Pick<App.Models.Event, "title" | "slug">;
    tickets: OrderTicket[];
};
