<script setup lang="ts">
import { Deferred, Head } from "@inertiajs/vue3";
import { Building2, Ticket, TrendingUp, XCircle } from "lucide-vue-next";
import { computed } from "vue";
import ActivityFeed from "@/components/Dashboard/ActivityFeed.vue";
import CancelledOrdersTable from "@/components/Dashboard/CancelledOrdersTable.vue";
import CheckInRatesTable from "@/components/Dashboard/CheckInRatesTable.vue";
import DraftEventsAlert from "@/components/Dashboard/DraftEventsAlert.vue";
import EventsOverviewPanel from "@/components/Dashboard/EventsOverviewPanel.vue";
import StatCard from "@/components/Dashboard/StatCard.vue";
import { usePermission } from "@/composables/usePermission";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import { formatCurrency } from "@/lib/utils";
import type { BreadcrumbItem } from "@/types";
import type { ActivityItem } from "@/types/activity";
import type {
    CancelledOrder,
    DraftEvent,
    EventCheckInRate,
} from "@/types/dashboard";
import { index } from "@/wayfinder/routes/dashboard";
import { index as eventsIndex } from "@/wayfinder/routes/dashboard/events";
import { index as ordersIndex } from "@/wayfinder/routes/dashboard/orders";
import { index as organizationsIndex } from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<{
    pendingOrganizationsCount: number;
    cancelledOrdersCount: number;
    recentCancelledOrders?: CancelledOrder[] | null;
    eventsCheckInRates?: EventCheckInRate[] | null;
    revenueThisMonth: { amount: number; currency: string };
    ticketsSoldThisMonth: number;
    draftEventsNearStartDate: DraftEvent[];
    eventsByStatus: { draft: number; published: number; cancelled: number };
    recentActivity?: ActivityItem[] | null;
}>();

const canOrg = usePermission("organization");

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: index().url },
];

const primaryStats = computed(() => {
    const stats = [];

    if (canOrg("viewAny")) {
        stats.push({
            label: "Pending Approvals",
            formatted: String(props.pendingOrganizationsCount),
            href: organizationsIndex().url + "?status=pending",
            linkText: "Review organizations",
            icon: Building2,
            iconClass: "text-amber-600 dark:text-amber-400",
            pillClass: "bg-amber-100 dark:bg-amber-500/15",
            accentClass: "from-amber-400 to-amber-600",
        });
    }

    stats.push(
        {
            label: "Cancelled Orders",
            formatted: String(props.cancelledOrdersCount),
            href: ordersIndex().url,
            linkText: "View all orders",
            icon: XCircle,
            iconClass: "text-destructive",
            pillClass: "bg-destructive/10",
            accentClass: "from-red-400 to-red-600",
        },
        {
            label: "Revenue This Month",
            formatted: formatCurrency(
                props.revenueThisMonth.amount,
                props.revenueThisMonth.currency,
            ),
            href: ordersIndex().url,
            linkText: "View paid orders",
            icon: TrendingUp,
            iconClass: "text-emerald-600 dark:text-emerald-400",
            pillClass: "bg-emerald-100 dark:bg-emerald-500/15",
            accentClass: "from-emerald-400 to-emerald-600",
        },
        {
            label: "Tickets Sold This Month",
            formatted: String(props.ticketsSoldThisMonth),
            href: eventsIndex().url,
            linkText: "View events",
            icon: Ticket,
            iconClass: "text-blue-600 dark:text-blue-400",
            pillClass: "bg-blue-100 dark:bg-blue-500/15",
            accentClass: "from-blue-400 to-blue-600",
        },
    );

    return stats;
});
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Dashboard" />

        <div class="flex flex-col gap-8 p-6">
            <div
                class="grid gap-4"
                :class="
                    canOrg('viewAny')
                        ? 'sm:grid-cols-2 lg:grid-cols-4'
                        : 'sm:grid-cols-3'
                "
            >
                <StatCard
                    v-for="stat in primaryStats"
                    :key="stat.label"
                    v-bind="stat"
                />
            </div>

            <EventsOverviewPanel :events-by-status="props.eventsByStatus" />

            <DraftEventsAlert
                v-if="props.draftEventsNearStartDate.length > 0"
                :events="props.draftEventsNearStartDate"
            />

            <Deferred data="eventsCheckInRates">
                <template #fallback>
                    <div class="h-40 animate-pulse rounded-lg bg-muted" />
                </template>
                <CheckInRatesTable
                    v-if="
                        props.eventsCheckInRates &&
                        props.eventsCheckInRates.length > 0
                    "
                    :events="props.eventsCheckInRates"
                />
            </Deferred>

            <Deferred data="recentActivity">
                <template #fallback>
                    <div class="h-40 animate-pulse rounded-lg bg-muted" />
                </template>
                <ActivityFeed :items="props.recentActivity ?? []" />
            </Deferred>

            <Deferred data="recentCancelledOrders">
                <template #fallback>
                    <div class="h-40 animate-pulse rounded-lg bg-muted" />
                </template>
                <CancelledOrdersTable
                    v-if="
                        props.recentCancelledOrders &&
                        props.recentCancelledOrders.length > 0
                    "
                    :orders="props.recentCancelledOrders"
                />
            </Deferred>
        </div>
    </DashboardLayout>
</template>
