import type { InertiaLinkProps } from "@inertiajs/vue3";
import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]): string {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps["href"]>): string {
    return typeof href === "string" ? href : href?.url;
}

export function formatDate(iso: string | null, timezone?: string): string {
    if (!iso) return "";
    return new Date(iso).toLocaleDateString("en-US", {
        timeZone: timezone,
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}

export function formatTime(iso: string | null, timezone?: string): string {
    if (!iso) return "";
    return new Date(iso).toLocaleTimeString("en-US", {
        timeZone: timezone,
        hour: "numeric",
        minute: "2-digit",
    });
}

export function formatDateTime(iso: string | null, timezone?: string): string {
    if (!iso) return "";
    return new Date(iso).toLocaleString("en-US", {
        timeZone: timezone,
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
    });
}


export function formatCurrency(cents: number, currency = "USD"): string {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency,
    }).format(cents / 100);
}

export function toDatetimeLocal(
    iso: string | null | undefined,
    timezone?: string,
): string {
    if (!iso) return "";
    return new Date(iso)
        .toLocaleString("sv-SE", { timeZone: timezone })
        .slice(0, 16)
        .replace(" ", "T");
}

export function withTimezone(
    formatter: (iso: string | null, timezone?: string) => string,
): (iso: string | null, timezone?: string) => string {
    return (iso: string | null, timezone?: string) => {
        if (!iso) return "";
        const base = formatter(iso, timezone);
        const tzPart =
            new Intl.DateTimeFormat("en-US", {
                timeZone: timezone,
                timeZoneName: "shortGeneric",
            })
                .formatToParts(new Date(iso))
                .find((part) => part.type === "timeZoneName")?.value ?? "";
        return tzPart ? `${base} ${tzPart}` : base;
    };
}
