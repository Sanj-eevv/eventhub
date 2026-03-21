export type TicketFormItem = {
    _key: string;
    uuid: string | null;
    name: string;
    price: string;
    capacity: string;
    max_per_user: string;
    sale_starts_at: string | undefined;
    sale_ends_at: string | undefined;
};
