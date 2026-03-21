<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Check, ChevronsUpDown } from "lucide-vue-next";
import { computed, ref } from "vue";
import EventFormSection from "@/components/Dashboard/Events/EventFormSection.vue";
import type { TicketFormItem } from "@/components/Dashboard/Events/ticket-form-types";
import TicketRepeater from "@/components/Dashboard/Events/TicketRepeater.vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Spinner } from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea";
import { useEventSections } from "@/composables/events/useEventSections";
import { useFormScrollToError } from "@/composables/useFormScrollToError";
import type { OrganizationPicker } from "@/types/organization";

type EventFormInitial = {
    organization_uuid?: string;
    title?: string;
    description?: string;
    starts_at?: string;
    ends_at?: string;
    timezone?: string;
    location?: {
        venue_name?: string;
        address_line_1?: string;
        address_line_2?: string;
        zip?: string;
        map_url?: string;
    };
    ticket_types?: TicketFormItem[];
};

const props = defineProps<{
    initialValues?: EventFormInitial;
    submitUrl: string;
    submitMethod: "post" | "put";
    organizations: OrganizationPicker[];
    timezones: string[];
    isEditing?: boolean;
}>();

const emit = defineEmits<{
    cancel: [];
}>();

const form = useForm({
    organization_uuid: props.initialValues?.organization_uuid ?? "",
    title: props.initialValues?.title ?? "",
    description: props.initialValues?.description ?? "",
    starts_at: props.initialValues?.starts_at ?? "",
    ends_at: props.initialValues?.ends_at ?? "",
    timezone: props.initialValues?.timezone ?? "",
    location: {
        venue_name: props.initialValues?.location?.venue_name ?? "",
        address_line_1: props.initialValues?.location?.address_line_1 ?? "",
        address_line_2: props.initialValues?.location?.address_line_2 ?? "",
        zip: props.initialValues?.location?.zip ?? "",
        map_url: props.initialValues?.location?.map_url ?? "",
    },
    ticket_types: (props.initialValues?.ticket_types ?? []) as TicketFormItem[],
});

const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        // _key is a Vue-only tracking key — strip before sending to server
        ticket_types: data.ticket_types.map(({ _key, ...rest }) => rest),
    }))[props.submitMethod](props.submitUrl, { preserveScroll: true });
};

const {
    activeSection,
    sections,
    scrollContainerRef,
    sectionRefs,
    scrollToSection,
} = useEventSections();

const timezoneOpen = ref(false);
const timezoneSearch = ref("");

const filteredTimezones = computed(() =>
    props.timezones.filter((timezone) =>
        timezone.toLowerCase().includes(timezoneSearch.value.toLowerCase()),
    ),
);

useFormScrollToError(form, scrollContainerRef);
</script>

<template>
    <form class="flex min-h-0 flex-1" @submit.prevent="handleSubmit()">
        <aside class="hidden w-52 shrink-0 flex-col border-r px-4 py-6 lg:flex">
            <p
                class="text-muted-foreground mb-3 px-3 text-xs font-semibold uppercase tracking-widest"
            >
                Sections
            </p>
            <nav class="space-y-0.5">
                <button
                    v-for="section in sections"
                    :key="section.id"
                    type="button"
                    :class="[
                        'flex w-full items-center gap-2.5 rounded-md px-3 py-2 text-left text-sm transition-colors',
                        activeSection === section.id
                            ? 'bg-accent text-accent-foreground font-medium'
                            : 'text-muted-foreground hover:bg-accent/50 hover:text-foreground',
                    ]"
                    @click="scrollToSection(section.id)"
                >
                    <span
                        :class="[
                            'size-1.5 shrink-0 rounded-full transition-colors duration-200',
                            activeSection === section.id
                                ? 'bg-primary'
                                : 'bg-border',
                        ]"
                    />
                    {{ section.label }}
                </button>
            </nav>
        </aside>

        <div class="flex min-h-0 flex-1 flex-col">
            <div
                ref="scrollContainerRef"
                class="flex-1 overflow-y-auto px-6 py-6 lg:px-8"
            >
                <div class="space-y-6">
                    <div :ref="sectionRefs.details">
                        <EventFormSection
                            title="Details"
                            description="Basic information about your event."
                        >
                            <div class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="event-org" class="required"
                                        >Organization</Label
                                    >
                                    <Select v-model="form.organization_uuid">
                                        <SelectTrigger id="event-org">
                                            <SelectValue
                                                placeholder="Select organization"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="org in organizations"
                                                :key="org.uuid"
                                                :value="org.uuid"
                                            >
                                                {{ org.title }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError
                                        :message="form.errors.organization_uuid"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="event-title" class="required"
                                        >Title</Label
                                    >
                                    <Input
                                        id="event-title"
                                        v-model="form.title"
                                        type="text"
                                    />
                                    <InputError :message="form.errors.title" />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        for="event-description"
                                        class="required"
                                        >Description</Label
                                    >
                                    <Textarea
                                        id="event-description"
                                        v-model="form.description"
                                        rows="4"
                                    />
                                    <InputError
                                        :message="form.errors.description"
                                    />
                                </div>

                                <div
                                    class="grid gap-4 sm:grid-cols-2 sm:items-start"
                                >
                                    <div class="grid gap-2">
                                        <Label
                                            for="event-starts-at"
                                            class="required"
                                            >Starts At</Label
                                        >
                                        <Input
                                            id="event-starts-at"
                                            v-model="form.starts_at"
                                            type="datetime-local"
                                        />
                                        <InputError
                                            :message="form.errors.starts_at"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="event-ends-at">
                                            Ends At
                                        </Label>
                                        <Input
                                            id="event-ends-at"
                                            v-model="form.ends_at"
                                            type="datetime-local"
                                        />
                                        <InputError
                                            :message="form.errors.ends_at"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <Label class="required">Timezone</Label>
                                    <Popover v-model:open="timezoneOpen">
                                        <PopoverTrigger as-child>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                role="combobox"
                                                class="justify-between font-normal"
                                            >
                                                <span class="truncate">
                                                    {{
                                                        form.timezone ||
                                                        "Select timezone"
                                                    }}
                                                </span>
                                                <ChevronsUpDown
                                                    class="ml-2 size-4 shrink-0 opacity-50"
                                                />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-80 p-0">
                                            <Command>
                                                <CommandInput
                                                    v-model="timezoneSearch"
                                                    placeholder="Search timezone..."
                                                />
                                                <CommandList>
                                                    <CommandEmpty>
                                                        No timezone found.
                                                    </CommandEmpty>
                                                    <CommandGroup>
                                                        <CommandItem
                                                            v-for="tz in filteredTimezones"
                                                            :key="tz"
                                                            :value="tz"
                                                            @select="
                                                                () => {
                                                                    form.timezone =
                                                                        tz;
                                                                    timezoneOpen = false;
                                                                    timezoneSearch =
                                                                        '';
                                                                }
                                                            "
                                                        >
                                                            <Check
                                                                :class="[
                                                                    'mr-2 size-4',
                                                                    form.timezone ===
                                                                    tz
                                                                        ? 'opacity-100'
                                                                        : 'opacity-0',
                                                                ]"
                                                            />
                                                            {{ tz }}
                                                        </CommandItem>
                                                    </CommandGroup>
                                                </CommandList>
                                            </Command>
                                        </PopoverContent>
                                    </Popover>
                                    <InputError
                                        :message="form.errors.timezone"
                                    />
                                </div>
                            </div>
                        </EventFormSection>
                    </div>

                    <div :ref="sectionRefs.location">
                        <EventFormSection
                            title="Location"
                            description="Where will your event take place?"
                        >
                            <div class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="location-venue"
                                        >Venue Name</Label
                                    >
                                    <Input
                                        id="location-venue"
                                        v-model="form.location.venue_name"
                                        type="text"
                                        placeholder="e.g. City Hall Auditorium"
                                    />
                                    <InputError
                                        :message="
                                            form.errors['location.venue_name']
                                        "
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="location-addr1"
                                        >Address Line 1</Label
                                    >
                                    <Input
                                        id="location-addr1"
                                        v-model="form.location.address_line_1"
                                        type="text"
                                        placeholder="Street address"
                                    />
                                    <InputError
                                        :message="
                                            form.errors[
                                                'location.address_line_1'
                                            ]
                                        "
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="location-addr2"
                                        >Address Line 2</Label
                                    >
                                    <Input
                                        id="location-addr2"
                                        v-model="form.location.address_line_2"
                                        type="text"
                                        placeholder="Suite, floor, etc."
                                    />
                                    <InputError
                                        :message="
                                            form.errors[
                                                'location.address_line_2'
                                            ]
                                        "
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="location-zip"
                                        >ZIP / Postal Code</Label
                                    >
                                    <Input
                                        id="location-zip"
                                        v-model="form.location.zip"
                                        type="text"
                                    />
                                    <InputError
                                        :message="form.errors['location.zip']"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="location-map">Map URL</Label>
                                    <Input
                                        id="location-map"
                                        v-model="form.location.map_url"
                                        type="url"
                                        placeholder="https://maps.google.com/..."
                                    />
                                    <InputError
                                        :message="
                                            form.errors['location.map_url']
                                        "
                                    />
                                </div>
                            </div>
                        </EventFormSection>
                    </div>

                    <div :ref="sectionRefs.tickets">
                        <EventFormSection
                            title="Tickets"
                            description="Define ticket types and pricing for your event."
                        >
                            <TicketRepeater
                                v-model="form.ticket_types"
                                :errors="form.errors"
                            />
                            <InputError :message="form.errors.ticket_types" />
                        </EventFormSection>
                    </div>
                </div>
            </div>

            <div class="bg-background shrink-0 border-t px-6 py-4 lg:px-8">
                <div class="flex max-w-3xl items-center justify-end gap-3">
                    <Button
                        type="button"
                        variant="ghost"
                        @click="emit('cancel')"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        {{ isEditing ? "Save Changes" : "Create Event" }}
                    </Button>
                </div>
            </div>
        </div>
    </form>
</template>
