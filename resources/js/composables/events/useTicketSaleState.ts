import { computed, onUnmounted, shallowRef } from "vue";
import type { TicketTypeResource } from "@/types/event";

export type TicketSaleState =
    | "upcoming"
    | "on_sale"
    | "on_sale_ending"
    | "ended"
    | "sold_out";

export function useTicketSaleState(getTicketTypes: () => TicketTypeResource[]) {
    const now = shallowRef(Date.now());
    const interval = setInterval(() => {
        now.value = Date.now();
    }, 1000);
    onUnmounted(() => clearInterval(interval));

    function resolveState(ticketType: TicketTypeResource): TicketSaleState {
        if (ticketType.available_capacity <= 0) return "sold_out";

        const current = now.value;

        if (
            ticketType.sale_starts_at &&
            new Date(ticketType.sale_starts_at).getTime() > current
        ) {
            return "upcoming";
        }

        if (
            ticketType.sale_ends_at &&
            new Date(ticketType.sale_ends_at).getTime() <= current
        ) {
            return "ended";
        }

        return "on_sale";
    }

    function formatCountdown(isoDate: string): string {
        const diff = new Date(isoDate).getTime() - now.value;
        if (diff <= 0) return "";

        const days = Math.floor(diff / 86_400_000);
        const hours = Math.floor((diff % 86_400_000) / 3_600_000);
        const minutes = Math.floor((diff % 3_600_000) / 60_000);
        const seconds = Math.floor((diff % 60_000) / 1_000);

        if (days > 0) return `${days}d ${hours}h`;
        if (hours > 0) return `${hours}h ${minutes}m`;
        return `${minutes}m ${seconds}s`;
    }

    const states = computed(() =>
        Object.fromEntries(
            getTicketTypes().map((ticketType) => [
                ticketType.uuid,
                resolveState(ticketType),
            ]),
        ),
    );

    const countdowns = computed(() =>
        Object.fromEntries(
            getTicketTypes().map((ticketType) => {
                const state = states.value[ticketType.uuid];
                const target =
                    state === "upcoming"
                        ? ticketType.sale_starts_at
                        : ticketType.sale_ends_at;
                return [
                    ticketType.uuid,
                    target ? formatCountdown(target) : null,
                ];
            }),
        ),
    );

    return { states, countdowns };
}
