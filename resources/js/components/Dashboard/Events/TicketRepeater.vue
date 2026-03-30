<script setup lang="ts">
import { Trash2 } from "lucide-vue-next";
import DateTimePicker from "@/components/DateTimePicker.vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import type { TicketTypeResource } from "@/types/event";

const localItems = defineModel<TicketTypeResource[]>({ required: true });
const props = defineProps<{
    errors?: Record<string, string>;
    timezone?: string;
}>();

const addItem = () => {
    localItems.value.push({
        uuid: crypto.randomUUID(),
        name: "",
        description: "",
        slug: "",
        price: 0,
        capacity: 0,
        sort_order: 0,
        sale_starts_at: null,
        sale_ends_at: null,
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
            :key="item.uuid"
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

            <div class="grid gap-4 sm:grid-cols-2 sm:items-start">
                <div class="grid gap-2 sm:col-span-2">
                    <Label :for="`tt-name-${item.uuid}`" class="required"
                        >Name</Label
                    >
                    <Input
                        :id="`tt-name-${item.uuid}`"
                        v-model="localItems[index].name"
                        type="text"
                    />
                    <InputError :message="fieldError(index, 'name')" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label :for="`tt-description-${item.uuid}`" class="required"
                        >Description</Label
                    >
                    <Textarea
                        :id="`tt-description-${item.uuid}`"
                        v-model="localItems[index].description"
                        rows="4"
                    />
                    <InputError :message="fieldError(index, 'description')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-price-${item.uuid}`" class="required"
                        >Price ($)</Label
                    >
                    <Input
                        :id="`tt-price-${item.uuid}`"
                        v-model="localItems[index].price"
                        type="number"
                        min="1"
                        step="0.01"
                    />
                    <InputError :message="fieldError(index, 'price')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-capacity-${item.uuid}`" class="required"
                        >Capacity</Label
                    >
                    <Input
                        :id="`tt-capacity-${item.uuid}`"
                        v-model="localItems[index].capacity"
                        type="number"
                        min="1"
                    />
                    <InputError :message="fieldError(index, 'capacity')" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label :for="`tt-max-${item.uuid}`">
                        Max per Person
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <Input
                        :id="`tt-max-${item.uuid}`"
                        v-model="localItems[index].max_per_user"
                        type="number"
                        min="1"
                        max="100"
                    />
                    <InputError :message="fieldError(index, 'max_per_user')" />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-sale-starts-${item.uuid}`">
                        Sale Starts
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <DateTimePicker
                        :id="`tt-sale-starts-${item.uuid}`"
                        v-model="localItems[index].sale_starts_at"
                    />
                    <p v-if="timezone" class="text-muted-foreground text-xs">
                        {{ timezone }}
                    </p>
                    <InputError
                        :message="fieldError(index, 'sale_starts_at')"
                    />
                </div>

                <div class="grid gap-2">
                    <Label :for="`tt-sale-ends-${item.uuid}`">
                        Sale Ends
                        <span class="text-muted-foreground text-xs"
                            >(optional)</span
                        >
                    </Label>
                    <DateTimePicker
                        :id="`tt-sale-ends-${item.uuid}`"
                        v-model="localItems[index].sale_ends_at"
                    />
                    <p v-if="timezone" class="text-muted-foreground text-xs">
                        {{ timezone }}
                    </p>
                    <InputError :message="fieldError(index, 'sale_ends_at')" />
                </div>
            </div>
        </div>

        <Button type="button" variant="outline" class="w-full" @click="addItem">
            + Add Ticket Type
        </Button>
    </div>
</template>
