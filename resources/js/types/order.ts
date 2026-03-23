import type { StatusLabel } from "@/types";
import type { App } from "@/wayfinder/types";

export type OrderTicket = Pick<
    App.Models.Ticket,
    "uuid" | "booking_reference" | "qr_code_path" | "attendee_name"
> & {
    status: App.Enums.TicketStatus;
    ticket_type: string;
};

export type Order = Pick<
    App.Models.Order,
    "uuid" | "currency" | "total" | "reserved_at" | "expires_at" | "paid_at"
> & {
    status: StatusLabel<App.Enums.OrderStatus>;
    total_formatted: string;
    event: Pick<App.Models.Event, "title" | "slug">;
    tickets: OrderTicket[];
};
