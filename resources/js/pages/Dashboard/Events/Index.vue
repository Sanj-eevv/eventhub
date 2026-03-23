<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { XIcon } from "lucide-vue-next";
import DeleteEventDialog from "@/components/Dashboard/Events/DeleteEventDialog.vue";
import DataTable from "@/components/DataTable.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { useEventTable } from "@/composables/events/useEvents";
import { eventStatusLabels } from "@/lib/statusLabels";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem, FilteredResponse } from "@/types";
import type { Event, EventFilterProps } from "@/types/event";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    create as eventsCreate,
    index as eventsIndex,
} from "@/wayfinder/routes/dashboard/events";

type PageProps = {
    events: FilteredResponse<Event, EventFilterProps>;
};

const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Events", href: eventsIndex().url },
];

const {
    sorting,
    pagination,
    search,
    statusFilter,
    isLoading,
    columns,
    activeEvent,
    deleteDialog,
} = useEventTable(props.events.meta, props.events.filters);
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Events" />
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Events</h1>
                <Button @click="router.visit(eventsCreate().url)">
                    Add Event
                </Button>
            </div>

            <DataTable
                :isLoading="isLoading"
                :columns="columns"
                :data="events"
                :sorting="sorting"
                :pagination="pagination"
                @update:sorting="(newSorting) => (sorting = newSorting)"
                @update:pagination="
                    (newPagination) => (pagination = newPagination)
                "
            >
                <template #filterSlot>
                    <div class="flex items-center gap-2">
                        <div class="relative max-w-sm">
                            <Input
                                v-model="search"
                                placeholder="Search..."
                                :class="search ? 'pr-8' : ''"
                            />
                            <button
                                v-if="search"
                                type="button"
                                class="absolute inset-y-0 right-2 flex items-center text-muted-foreground hover:text-foreground"
                                @click="search = ''"
                            >
                                <XIcon class="size-4" />
                            </button>
                        </div>
                        <div class="relative">
                            <Select v-model="statusFilter">
                                <SelectTrigger class="w-40">
                                    <SelectValue placeholder="All statuses" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(
                                            label, value
                                        ) in eventStatusLabels"
                                        :value="value"
                                        :key="value"
                                    >
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <button
                                v-if="statusFilter"
                                type="button"
                                class="absolute -right-5 inset-y-0 flex items-center text-muted-foreground hover:text-foreground"
                                @click="statusFilter = ''"
                            >
                                <XIcon class="size-4" />
                            </button>
                        </div>
                    </div>
                </template>
            </DataTable>
        </div>

        <DeleteEventDialog
            v-if="deleteDialog.isOpen.value"
            :open="deleteDialog.isOpen.value"
            :event="activeEvent as Event"
            @update:open="deleteDialog.close()"
        />
    </DashboardLayout>
</template>
