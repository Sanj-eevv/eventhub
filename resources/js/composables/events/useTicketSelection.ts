import { computed, reactive } from "vue";
import type { TicketTypeResource } from "@/types/event";

export function useTicketSelection(ticketTypes: TicketTypeResource[]) {
    const quantities = reactive<Record<string, number>>(
        Object.fromEntries(ticketTypes.map((ticketType) => [ticketType.uuid, 0])),
    );

    const hasSelection = computed(() =>
        ticketTypes.some((ticketType) => quantities[ticketType.uuid] > 0),
    );

    const orderTotal = computed(() =>
        ticketTypes.reduce(
            (sum, ticketType) => sum + ticketType.price * quantities[ticketType.uuid],
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

    function increment(ticketType: TicketTypeResource): void {
        const max = ticketType.max_per_user;
        quantities[ticketType.uuid] =
            max != null
                ? Math.min(max, quantities[ticketType.uuid] + 1)
                : quantities[ticketType.uuid] + 1;
    }

    function decrement(ticketType: TicketTypeResource): void {
        quantities[ticketType.uuid] = Math.max(0, quantities[ticketType.uuid] - 1);
    }

    function isAtMax(ticketType: TicketTypeResource): boolean {
        return (
            ticketType.max_per_user != null &&
            quantities[ticketType.uuid] >= ticketType.max_per_user
        );
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
