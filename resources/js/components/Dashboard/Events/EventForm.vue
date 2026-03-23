<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Check, ChevronsUpDown } from "lucide-vue-next";
import { computed, ref } from "vue";
import EventFormSection from "@/components/Dashboard/Events/EventFormSection.vue";
import TicketRepeater from "@/components/Dashboard/Events/TicketRepeater.vue";
import DateTimePicker from "@/components/DateTimePicker.vue";
import InputError from "@/components/InputError.vue";
import MediaUploader from "@/components/MediaUploader.vue";
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
import { toDatetimeLocal } from "@/lib/utils";
import type { EventResource } from "@/types/event";
import type { OrganizationPicker } from "@/types/organization";
import {
    cover as coverMediaRoute,
    destroy as destroyMediaRoute,
    store as storeMediaRoute,
} from "@/wayfinder/routes/dashboard/events/media";

const props = defineProps<{
    initialValues?: EventResource;
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
    organization_uuid: props.initialValues?.organization.uuid ?? "",
    title: props.initialValues?.title ?? "",
    description: props.initialValues?.description ?? "",
    starts_at: toDatetimeLocal(
        props.initialValues?.starts_at,
        props.initialValues?.timezone,
    ),
    ends_at: toDatetimeLocal(
        props.initialValues?.ends_at,
        props.initialValues?.timezone,
    ),
    timezone: props.initialValues?.timezone ?? "",
    venue_name: props.initialValues?.venue_name ?? "",
    address: props.initialValues?.address ?? "",
    zip: props.initialValues?.zip ?? "",
    map_url: props.initialValues?.map_url ?? "",
    ticket_types:
        props.initialValues?.ticket_types.map((ticketType) => ({
            ...ticketType,
            sale_starts_at: toDatetimeLocal(
                ticketType.sale_starts_at,
                props.initialValues?.timezone,
            ),
            sale_ends_at: toDatetimeLocal(
                ticketType.sale_ends_at,
                props.initialValues?.timezone,
            ),
        })) ?? [],
});

const handleSubmit = () => {
    form[props.submitMethod](props.submitUrl, { preserveScroll: true });
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

defineExpose({ scrollToSection });
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
                                        >
                                            Starts At
                                        </Label>
                                        <DateTimePicker
                                            id="event-starts-at"
                                            v-model="form.starts_at"
                                        />
                                        <p
                                            v-if="form.timezone"
                                            class="text-muted-foreground text-xs"
                                        >
                                            {{ form.timezone }}
                                        </p>
                                        <InputError
                                            :message="form.errors.starts_at"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label
                                            for="event-ends-at"
                                            class="required"
                                        >
                                            Ends At
                                        </Label>
                                        <DateTimePicker
                                            id="event-ends-at"
                                            v-model="form.ends_at"
                                        />
                                        <p
                                            v-if="form.timezone"
                                            class="text-muted-foreground text-xs"
                                        >
                                            {{ form.timezone }}
                                        </p>
                                        <InputError
                                            :message="form.errors.ends_at"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="event-timezone" class="required"
                                        >Timezone</Label
                                    >
                                    <Popover v-model:open="timezoneOpen">
                                        <PopoverTrigger as-child>
                                            <Button
                                                id="event-timezone"
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
                                    <Label for="venue-name" class="required"
                                        >Venue Name</Label
                                    >
                                    <Input
                                        id="venue-name"
                                        v-model="form.venue_name"
                                        type="text"
                                    />
                                    <InputError
                                        :message="form.errors.venue_name"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="event-address" class="required"
                                        >Address</Label
                                    >
                                    <Input
                                        id="event-address"
                                        v-model="form.address"
                                        type="text"
                                    />
                                    <InputError
                                        :message="form.errors.address"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="zip" class="required"
                                        >ZIP / Postal Code</Label
                                    >
                                    <Input
                                        id="zip"
                                        v-model="form.zip"
                                        type="text"
                                    />
                                    <InputError :message="form.errors.zip" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="map-url">Map URL</Label>
                                    <Input
                                        id="map-url"
                                        v-model="form.map_url"
                                        type="url"
                                        placeholder="https://maps.google.com/..."
                                    />
                                    <InputError
                                        :message="form.errors.map_url"
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
                                :timezone="form.timezone"
                            />
                            <InputError :message="form.errors.ticket_types" />
                        </EventFormSection>
                    </div>

                    <div :ref="sectionRefs.media">
                        <EventFormSection
                            title="Media"
                            description="Upload images for your event. The first image will become the cover."
                        >
                            <div
                                v-if="!isEditing"
                                class="text-muted-foreground flex flex-col items-center justify-center rounded-lg border border-dashed px-6 py-10 text-center text-sm"
                            >
                                <p class="font-medium">No media yet</p>
                                <p class="mt-1">
                                    Save your event first to start uploading
                                    images.
                                </p>
                            </div>
                            <MediaUploader
                                v-else
                                :items="initialValues?.media"
                                :upload-url="
                                    storeMediaRoute({
                                        event: initialValues?.uuid as string,
                                    }).url
                                "
                                :delete-url="
                                    (mediaUuid) =>
                                        destroyMediaRoute({
                                            event: initialValues?.uuid as string,
                                            media: mediaUuid,
                                        }).url
                                "
                                :cover-url="
                                    (mediaUuid) =>
                                        coverMediaRoute({
                                            event: initialValues?.uuid as string,
                                            media: mediaUuid,
                                        }).url
                                "
                                partial-reload-key="event"
                            />
                        </EventFormSection>
                    </div>
                </div>
            </div>

            <div class="bg-background shrink-0 border-t px-6 py-4 lg:px-8">
                <div class="flex items-center justify-end gap-3">
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
