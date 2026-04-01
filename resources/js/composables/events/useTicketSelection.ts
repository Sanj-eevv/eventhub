import { computed, reactive } from "vue";
import type { TicketTypeResource } from "@/types/event";

export function useTicketSelection(ticketTypes: TicketTypeResource[]) {
    const quantities = reactive<Record<string, number>>(
        Object.fromEntries(
            ticketTypes.map((ticketType) => [ticketType.uuid, 0]),
        ),
    );

    const hasSelection = computed(() =>
        ticketTypes.some((ticketType) => quantities[ticketType.uuid] > 0),
    );

    const orderTotal = computed(() =>
        ticketTypes.reduce(
            (sum, ticketType) =>
                sum + ticketType.price * quantities[ticketType.uuid],
            0,
        ),
    );

    const selectedItems = computed(() =>
        ticketTypes
            .filter((ticketType) => quantities[ticketType.uuid] > 0)
            .map((ticketType) => ({
                ticket_type_uuid: ticketType.uuid,
                quantity: quantities[ticketType.uuid],
            })),
    );

    function effectiveMax(ticketType: TicketTypeResource): number | undefined {
        return ticketType.effective_max_per_user ?? ticketType.max_per_user;
    }

    function increment(ticketType: TicketTypeResource): void {
        const max = effectiveMax(ticketType);
        quantities[ticketType.uuid] =
            max != null
                ? Math.min(max, quantities[ticketType.uuid] + 1)
                : quantities[ticketType.uuid] + 1;
    }

    function decrement(ticketType: TicketTypeResource): void {
        quantities[ticketType.uuid] = Math.max(
            0,
            quantities[ticketType.uuid] - 1,
        );
    }

    function isAtMax(ticketType: TicketTypeResource): boolean {
        const max = effectiveMax(ticketType);
        return max != null && quantities[ticketType.uuid] >= max;
    }

    return {
        quantities,
        hasSelection,
        orderTotal,
        selectedItems,
        increment,
        decrement,
        isAtMax,
    };
}
