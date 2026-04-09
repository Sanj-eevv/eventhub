export type CancelledOrder = {
    uuid: string;
    customer_name: string;
    customer_email: string;
    event_title: string;
    total: number;
    currency: string;
    cancelled_at: string | null;
};

export type EventCheckInRate = {
    uuid: string;
    title: string;
    starts_at: string;
    total_tickets: number;
    checked_in_count: number;
};

export type DraftEvent = {
    uuid: string;
    title: string;
    starts_at: string;
};
