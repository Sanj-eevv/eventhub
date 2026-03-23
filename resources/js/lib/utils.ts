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
        timeZoneName: "short",
    });
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
