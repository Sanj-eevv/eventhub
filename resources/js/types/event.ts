import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type StatusLabel<T extends string> = { value: T; label: string };

export type TicketType = Omit<
    App.Models.TicketType,
    | "id"
    | "event_id"
    | "uuid"
    | "max_per_user"
    | "created_at"
    | "updated_at"
    | "event"
    | "tickets"
> & {
    _key: string;
    uuid: string | null;
    max_per_user: number | null;
};

export type EventStatus = App.Enums.EventStatus;
export type EventStatusData = StatusLabel<EventStatus>;

export type MediaItem = Pick<
    App.Models.Media,
    "uuid" | "filename" | "size" | "is_cover" | "sort_order"
> & {
    url: string;
};

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

export type PublicTicketType = Pick<
    App.Models.TicketType,
    | "uuid"
    | "name"
    | "description"
    | "capacity"
    | "max_per_user"
    | "is_active"
    | "sale_starts_at"
    | "sale_ends_at"
> & {
    price: number;
};

export type PublicEvent = Pick<
    App.Models.Event,
    | "uuid"
    | "slug"
    | "title"
    | "description"
    | "starts_at"
    | "ends_at"
    | "timezone"
    | "venue_name"
    | "address"
    | "zip"
    | "map_url"
> & {
    cover_image: MediaItem | null;
    media: MediaItem[];
};

export type Event = Omit<
    App.Models.Event,
    "id" | "status" | "organization_id" | "user_id"
> & {
    organization_uuid: string;
    status: EventStatusData;
    ticket_types?: TicketType[];
    media?: MediaItem[];
    cover_image?: MediaItem | null;
};

export type EventFormInitial = {
    uuid: string;
    organization_uuid?: string;
    title?: string;
    description?: string;
    starts_at?: string;
    ends_at?: string;
    timezone?: string;
    venue_name?: string;
    address?: string;
    zip?: string;
    map_url?: string;
    ticket_types?: TicketType[];
};

export type EventFilterProps = {
    search: string;
    status: EventStatus | null;
};

export type EventPageProps = {
    events: PaginatedResponse<Event> & {
        filters: EventFilterProps;
    };
};
