import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type EventStatus = App.Enums.EventStatus;
export type EventStatusData = { value: EventStatus; label: string };

export type EventLocation = {
    venue_name?: string | null;
    address_line_1?: string | null;
    address_line_2?: string | null;
    city?: string | null;
    state?: string | null;
    zip?: string | null;
    country?: string | null;
    map_url?: string | null;
};

export type EventTicket = {
    _key: string;
    label: string;
    price: number | string;
    quantity: number | null;
    sale_starts_at: string | null;
    sale_ends_at: string | null;
};

export type Event = Omit<App.Models.Event, "status" | "location" | "tickets"> & {
    status: EventStatusData;
    location: EventLocation | null;
    tickets: Omit<EventTicket, "_key">[] | null;
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
