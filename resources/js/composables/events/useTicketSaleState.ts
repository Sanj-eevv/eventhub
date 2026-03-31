import { useNow } from "@vueuse/core";
import { computed } from "vue";
import type { TicketTypeResource } from "@/types/event";

export type TicketSaleState = "upcoming" | "on_sale" | "ended" | "sold_out";

function formatCountdown(diff: number): string {
    const days = Math.floor(diff / 86_400_000);
    const hours = Math.floor((diff % 86_400_000) / 3_600_000);
    const minutes = Math.floor((diff % 3_600_000) / 60_000);
    const seconds = Math.floor((diff % 60_000) / 1_000);

    if (days > 0) return `${days}d ${hours}h`;
    if (hours > 0) return `${hours}h ${minutes}m`;
    return `${minutes}m ${seconds}s`;
}

function resolveState(
    ticketType: TicketTypeResource,
    current: number,
): TicketSaleState {
    if (ticketType.available_capacity <= 0) return "sold_out";
    if (
        ticketType.sale_starts_at &&
        new Date(ticketType.sale_starts_at).getTime() > current
    )
        return "upcoming";
    if (
        ticketType.sale_ends_at &&
        new Date(ticketType.sale_ends_at).getTime() <= current
    )
        return "ended";
    return "on_sale";
}

export function useTicketSaleState(getTicketTypes: () => TicketTypeResource[]) {
    const now = useNow({ interval: 1000 });

    const resolved = computed(() => {
        const current = now.value.getTime();

        return getTicketTypes().map((ticketType) => {
            const state = resolveState(ticketType, current);

            const target =
                state === "upcoming"
                    ? ticketType.sale_starts_at
                    : ticketType.sale_ends_at;
            const diff = target ? new Date(target).getTime() - current : 0;
            return {
                uuid: ticketType.uuid,
                state,
                countdown: diff > 0 ? formatCountdown(diff) : null,
            };
        });
    });

    const states = computed(() =>
        Object.fromEntries(
            resolved.value.map((ticket) => [ticket.uuid, ticket.state]),
        ),
    );

    const countdowns = computed(() =>
        Object.fromEntries(
            resolved.value.map((ticket) => [ticket.uuid, ticket.countdown]),
        ),
    );

    return { states, countdowns };
}
