export type ActivityItem = {
    uuid: string;
    event: { value: string; label: string };
    causer_name: string | null;
    subject_label: string | null;
    properties: Record<string, unknown> | null;
    created_at: string;
};
