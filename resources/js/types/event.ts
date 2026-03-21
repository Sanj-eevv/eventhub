import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type EventStatus = App.Enums.EventStatus;
export type EventStatusData = { value: EventStatus; label: string };

export type MediaItem = {
    uuid: string;
    url: string;
    original_url: string;
    filename: string;
    size: number;
    is_cover: boolean;
    sort_order: number;
    processing: boolean;
    processing_failed: boolean;
};

export type EventLocation = {
    venue_name?: string | null;
    address_line_1?: string | null;
    address_line_2?: string | null;
    zip?: string | null;
    map_url?: string | null;
};

export type OrderStatus = 'pending' | 'reserved' | 'paid' | 'expired' | 'cancelled';
export type TicketStatus = 'pending' | 'active' | 'cancelled' | 'used';

export type OrderTicket = {
    uuid: string;
    booking_reference: string;
    status: TicketStatus;
    qr_code_path: string | null;
    attendee_name: string;
    ticket_type: string;
};

export type Order = {
    uuid: string;
    status: { value: OrderStatus; label: string };
    currency: string;
    total: number;
    total_formatted: string;
    reserved_at: string | null;
    expires_at: string | null;
    paid_at: string | null;
    event: { title: string; slug: string };
    tickets: OrderTicket[];
};

export type PublicTicketType = {
    uuid: string;
    name: string;
    description: string | null;
    price_cents: number;
    price_formatted: string;
    capacity: number;
    max_per_user: number;
    is_active: boolean;
    sale_starts_at: string | null;
    sale_ends_at: string | null;
};

export type PublicEvent = {
    uuid: string;
    slug: string;
    title: string;
    description: string;
    starts_at: string;
    ends_at: string | null;
    timezone: string;
    location: EventLocation | null;
    cover_image: MediaItem | null;
    media: MediaItem[];
};

export type TicketType = App.Models.TicketType;

export type Event = Omit<App.Models.Event, "status" | "location" | "organization_id" | "user_id"> & {
    status: EventStatusData;
    location: EventLocation | null;
    organization_uuid: string;
    ticket_types?: TicketType[];
    media?: MediaItem[];
    cover_image?: MediaItem | null;
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
