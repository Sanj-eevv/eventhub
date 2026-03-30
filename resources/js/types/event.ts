import type { StatusLabel } from "@/types";
import type { OrganizationResource } from "@/types/organization";
import type { App } from "@/wayfinder/types";

export type Event = App.Models.Event;
export type EventStatus = App.Enums.EventStatus;
export type EventStatusData = StatusLabel<EventStatus>;

export type EventResource = Pick<
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
    | "created_at"
    | "updated_at"
> & {
    status: EventStatusData;
    organization: OrganizationResource;
    ticket_types: TicketTypeResource[];
    media?: MediaResource[];
    cover_image?: MediaResource;
};

export type TicketTypeResource = Pick<
    App.Models.TicketType,
    | "uuid"
    | "name"
    | "slug"
    | "description"
    | "price"
    | "capacity"
    | "sort_order"
    | "sale_starts_at"
    | "sale_ends_at"
> & { max_per_user?: number; available_capacity: number };

export type MediaResource = Pick<
    App.Models.Media,
    "uuid" | "filename" | "size" | "is_cover" | "sort_order"
> & { url: string };

export type ActiveOrderResource = {
    uuid: string;
    expires_at: string;
};

export type EventFilterProps = {
    search: string;
    status: EventStatusData | null;
};
