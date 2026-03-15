<script setup lang="ts">
import { Trash2 } from "lucide-vue-next";
import { computed } from "vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import type { EventTicket } from "@/types/event";

const props = defineProps<{
    modelValue: EventTicket[];
    errors?: Record<string, string>;
}>();

const emit = defineEmits<{
    "update:modelValue": [tickets: EventTicket[]];
}>();

const tickets = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const addTicket = () => {
    emit("update:modelValue", [
        ...props.modelValue,
        {
            _key: crypto.randomUUID(),
            label: "",
            price: 0,
            quantity: null,
            sale_starts_at: null,
            sale_ends_at: null,
        },
    ]);
};

const removeTicket = (index: number) => {
    const updated = [...props.modelValue];
    updated.splice(index, 1);
    emit("update:modelValue", updated);
};

const updateTicket = (index: number, field: keyof EventTicket, value: unknown) => {
    const updated = props.modelValue.map((t, i) =>
        i === index ? { ...t, [field]: value } : t,
    );
    emit("update:modelValue", updated);
};

const isUnlimited = (ticket: EventTicket) => ticket.quantity === null;

const toggleUnlimited = (index: number, checked: boolean | string) => {
    updateTicket(index, "quantity", checked ? null : 1);
};

const fieldError = (index: number, field: string) =>
    props.errors?.[`tickets.${index}.${field}`];
</script>

<template>
    <div class="space-y-4">
        <div v-if="tickets.length === 0" class="rounded-lg border border-dashed p-8 text-center">
            <p class="text-muted-foreground text-sm">No ticket types added yet.</p>
            <p class="text-muted-foreground mt-1 text-sm">
                Add ticket types to let attendees register.
            </p>
        </div>

        <div
            v-for="(ticket, index) in tickets"
            :key="ticket._key"
            class="rounded-lg border p-4"
        >
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm font-medium">Ticket {{ index + 1 }}</span>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    @click="removeTicket(index)"
                >
                    <Trash2 class="size-4" />
                </Button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label :for="`ticket-label-${ticket._key}`">Label</Label>
                    <Input
                        :id="`ticket-label-${ticket._key}`"
                        :value="ticket.label"
                        type="text"
                        placeholder="e.g. General Admission"
                        @input="
                            (e: Event) =>
                                updateTicket(
                                    index,
                                    'label',
                                    (e.target as HTMLInputElement).value,
                                )
                        "
                    />
                    <InputError :message="fieldError(index, 'label')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`ticket-price-${ticket._key}`">Price</Label>
                    <Input
                        :id="`ticket-price-${ticket._key}`"
                        :value="ticket.price"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        @input="
                            (e: Event) =>
                                updateTicket(
                                    index,
                                    'price',
                                    (e.target as HTMLInputElement).value,
                                )
                        "
                    />
                    <InputError :message="fieldError(index, 'price')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`ticket-quantity-${ticket._key}`">Quantity</Label>
                    <Input
                        :id="`ticket-quantity-${ticket._key}`"
                        :value="isUnlimited(ticket) ? '' : ticket.quantity"
                        type="number"
                        min="1"
                        :disabled="isUnlimited(ticket)"
                        placeholder="Unlimited"
                        @input="
                            (e: Event) =>
                                updateTicket(
                                    index,
                                    'quantity',
                                    (e.target as HTMLInputElement).value
                                        ? Number((e.target as HTMLInputElement).value)
                                        : null,
                                )
                        "
                    />
                    <div class="flex items-center gap-2">
                        <Checkbox
                            :id="`ticket-unlimited-${ticket._key}`"
                            :checked="isUnlimited(ticket)"
                            @update:checked="toggleUnlimited(index, $event)"
                        />
                        <Label
                            :for="`ticket-unlimited-${ticket._key}`"
                            class="text-muted-foreground cursor-pointer text-xs font-normal"
                        >
                            Unlimited
                        </Label>
                    </div>
                    <InputError :message="fieldError(index, 'quantity')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`ticket-sale-starts-${ticket._key}`">Sale Starts</Label>
                    <Input
                        :id="`ticket-sale-starts-${ticket._key}`"
                        :value="ticket.sale_starts_at ?? ''"
                        type="datetime-local"
                        @input="
                            (e: Event) =>
                                updateTicket(
                                    index,
                                    'sale_starts_at',
                                    (e.target as HTMLInputElement).value || null,
                                )
                        "
                    />
                    <InputError :message="fieldError(index, 'sale_starts_at')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`ticket-sale-ends-${ticket._key}`">Sale Ends</Label>
                    <Input
                        :id="`ticket-sale-ends-${ticket._key}`"
                        :value="ticket.sale_ends_at ?? ''"
                        type="datetime-local"
                        @input="
                            (e: Event) =>
                                updateTicket(
                                    index,
                                    'sale_ends_at',
                                    (e.target as HTMLInputElement).value || null,
                                )
                        "
                    />
                    <InputError :message="fieldError(index, 'sale_ends_at')" />
                </div>
            </div>
        </div>

        <Button type="button" variant="outline" class="w-full" @click="addTicket">
            + Add Ticket Type
        </Button>
    </div>
</template>
