<script setup lang="ts">
import { parseDate } from "@internationalized/date";
import { CalendarIcon } from "lucide-vue-next";
import type { DateValue } from "reka-ui";
import { computed, ref } from "vue";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { cn } from "@/lib/utils";

const modelValue = defineModel<string | null>({ default: null });
const props = defineProps<{ id: string }>();
const open = ref(false);

const datePart = computed((): string | null => {
    return modelValue.value?.split("T")[0] ?? null;
});

const timePart = computed((): string => {
    return modelValue.value?.split("T")[1] ?? "00:00";
});

const calendarValue = computed((): DateValue | undefined => {
    if (!datePart.value) return undefined;
    try {
        return parseDate(datePart.value);
    } catch {
        return undefined;
    }
});

function formatDateStr(date: DateValue): string {
    const m = String(date.month).padStart(2, "0");
    const d = String(date.day).padStart(2, "0");
    return `${date.year}-${m}-${d}`;
}

function onDateSelect(date: DateValue | undefined): void {
    if (!date) {
        modelValue.value = null;
        return;
    }
    modelValue.value = `${formatDateStr(date)}T${timePart.value}`;
}

function onTimeChange(event: Event): void {
    const time = (event.target as HTMLInputElement).value;
    if (!datePart.value) return;
    modelValue.value = `${datePart.value}T${time}`;
}

const displayLabel = computed((): string => {
    if (!modelValue.value) return "Pick date & time";
    const [date, time] = modelValue.value.split("T");
    try {
        const formatted = new Date(`${date}T00:00`).toLocaleDateString(
            "en-US",
            { month: "short", day: "numeric", year: "numeric" },
        );
        return `${formatted} ${time}`;
    } catch {
        return modelValue.value;
    }
});
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                :id="props.id"
                type="button"
                variant="outline"
                :class="
                    cn(
                        'w-full justify-start text-left font-normal',
                        !modelValue && 'text-muted-foreground',
                    )
                "
            >
                <CalendarIcon class="mr-2 size-4 shrink-0" />
                {{ displayLabel }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
            <Calendar
                :model-value="calendarValue"
                @update:model-value="onDateSelect"
            />
            <div class="border-t px-3 pb-3 pt-2">
                <Label class="text-muted-foreground mb-1.5 block text-xs">
                    Time
                </Label>
                <Input
                    type="time"
                    :value="timePart"
                    :disabled="!datePart"
                    class="w-full"
                    @change="onTimeChange"
                />
            </div>
        </PopoverContent>
    </Popover>
</template>
