import type { VariantProps } from "class-variance-authority";
import type { badgeVariants } from "@/components/ui/badge";
import type { PaginatedResponse } from "@/types";

export type EventStatus = "draft" | "published" | "cancelled";

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

export type Event = {
    id: number;
    uuid: string;
    user_id: number;
    organization_id: number;
    title: string;
    slug: string;
    description: string;
    starts_at: string;
    ends_at: string | null;
    timezone: string;
    location: EventLocation | null;
    tickets: Omit<EventTicket, "_key">[] | null;
    status: EventStatus;
    created_at: string | null;
    updated_at: string | null;
    organization?: { id: number; uuid: string; title: string } | null;
    user?: { id: number; uuid: string; name: string } | null;
};

type BadgeVariants = VariantProps<typeof badgeVariants>;

export type EventStatusConfig = Record<
    EventStatus,
    { variant: BadgeVariants["variant"]; class: string; label: string }
>;

export type EventFilterProps = {
    search: string;
    status: EventStatus | null;
};

export type EventPageProps = {
    events: PaginatedResponse<Event> & {
        filters: EventFilterProps;
    };
};
