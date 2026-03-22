import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type StatusLabel<T extends string> = { value: T; label: string };

export type TicketType = Omit<
    App.Models.TicketType,
    | "id"
    | "event_id"
    | "uuid"
    | "created_at"
    | "updated_at"
    | "event"
    | "tickets"
> & {
    _key: string;
    uuid: string | null;
};

export type EventStatus = App.Enums.EventStatus;
export type EventStatusData = StatusLabel<EventStatus>;

export type MediaItem = Pick<
    App.Models.Media,
    "uuid" | "filename" | "size" | "is_cover" | "sort_order"
> & {
    url: string;
};

export type EventLocation = {
    venue_name?: string | null;
    address_line_1?: string | null;
    address_line_2?: string | null;
    zip?: string | null;
    map_url?: string | null;
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
> & {
    location: EventLocation | null;
    cover_image: MediaItem | null;
    media: MediaItem[];
};

export type Event = Omit<
    App.Models.Event,
    "id" | "status" | "location" | "organization_id" | "user_id"
> & {
    organization_uuid: string;
    status: EventStatusData;
    location: EventLocation | null;
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
    location?: { [K in keyof EventLocation]?: string };
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
