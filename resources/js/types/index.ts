import type { InertiaLinkProps } from "@inertiajs/vue3";
import type { SortingState } from "@tanstack/vue-table";
import type { LucideIcon } from "lucide-vue-next";

export type BreadcrumbItem = {
    title: string;
    href?: string;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps["href"]>;
    icon?: LucideIcon;
    isActive?: boolean;
    matchPrefix?: boolean;
    show: boolean;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
    page: number | null;
};

export type PaginatedResponseMeta = {
    sort: SortingState;
    current_page: number;
    from: number;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    total: number;
};
export type PaginatedResponse<T> = {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        next: string | null;
        prev: string | null;
    };
    meta: PaginatedResponseMeta;
    filters?: Record<string, any>;
};
