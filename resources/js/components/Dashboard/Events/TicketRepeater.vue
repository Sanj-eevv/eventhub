<script setup lang="ts">
import { Trash2 } from "lucide-vue-next";
import type { TicketFormItem } from "@/components/Dashboard/Events/ticket-form-types";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

const localItems = defineModel<TicketFormItem[]>({ required: true });

const props = defineProps<{
    errors?: Record<string, string>;
}>();

const addItem = () => {
    localItems.value.push({
        _key: crypto.randomUUID(),
        uuid: null,
        name: "",
        price: "",
        capacity: "",
        max_per_user: "",
        sale_starts_at: undefined,
        sale_ends_at: undefined,
    });
};

const removeItem = (index: number) => {
    localItems.value.splice(index, 1);
};

const fieldError = (index: number, field: string) =>
    props.errors?.[`ticket_types.${index}.${field}`];
</script>

<template>
    <div class="space-y-4">
        <div
            v-if="localItems.length === 0"
            class="rounded-lg border border-dashed p-8 text-center"
        >
            <p class="text-muted-foreground text-sm">
                No ticket types added yet.
            </p>
            <p class="text-muted-foreground mt-1 text-sm">
                Add at least one ticket type so attendees can register.
            </p>
        </div>

        <div
            v-for="(item, index) in localItems"
            :key="item._key"
            class="rounded-lg border p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <span class="text-sm font-medium"
                    >Ticket Type {{ index + 1 }}</span
                >
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    @click="removeItem(index)"
                >
                    <Trash2 class="size-4" />
                </Button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2 sm:col-span-2">
                    <Label :for="`tt-name-${item._key}`" class="required"
                        >Name</Label
                    >
                    <Input
                        :id="`tt-name-${item._key}`"
                        v-model="localItems[index].name"
                        type="text"
                        placeholder="e.g. General Admission"
                    />
                    <InputError :message="fieldError(index, 'name')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-price-${item._key}`" class="required"
                        >Price ($)</Label
                    >
                    <Input
                        :id="`tt-price-${item._key}`"
                        v-model="localItems[index].price"
                        type="number"
                        min="0.01"
                        step="0.01"
                        placeholder="0.00"
                    />
                    <InputError :message="fieldError(index, 'price')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-capacity-${item._key}`" class="required"
                        >Capacity</Label
                    >
                    <Input
                        :id="`tt-capacity-${item._key}`"
                        v-model="localItems[index].capacity"
                        type="number"
                        min="1"
                        placeholder="e.g. 100"
                    />
                    <InputError :message="fieldError(index, 'capacity')" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label :for="`tt-max-${item._key}`">
                        Max per Person
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <Input
                        :id="`tt-max-${item._key}`"
                        v-model="localItems[index].max_per_user"
                        type="number"
                        min="1"
                        max="100"
                    />
                    <InputError :message="fieldError(index, 'max_per_user')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-sale-starts-${item._key}`">
                        Sale Starts
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <Input
                        :id="`tt-sale-starts-${item._key}`"
                        v-model="localItems[index].sale_starts_at"
                        type="datetime-local"
                    />
                    <InputError
                        :message="fieldError(index, 'sale_starts_at')"
                    />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-sale-ends-${item._key}`">
                        Sale Ends
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <Input
                        :id="`tt-sale-ends-${item._key}`"
                        v-model="localItems[index].sale_ends_at"
                        type="datetime-local"
                    />
                    <InputError :message="fieldError(index, 'sale_ends_at')" />
                </div>
            </div>
        </div>

        <Button type="button" variant="outline" class="w-full" @click="addItem">
            + Add Ticket Type
        </Button>
    </div>
</template>
