<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { formatCurrency, formatDate } from "@/lib/utils";

interface CancelledOrder {
    uuid: string;
    customer_name: string;
    customer_email: string;
    event_title: string;
    total: number;
    currency: string;
    cancelled_at: string | null;
}

defineProps<{
    orders: CancelledOrder[];
}>();
</script>

<template>
    <div>
        <p
            class="font-display mb-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground"
        >
            Recent Cancelled Orders
        </p>
        <div
            class="overflow-hidden rounded-xl border border-border bg-card"
        >
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Customer</TableHead>
                        <TableHead>Event</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead>Cancelled At</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="order in orders" :key="order.uuid">
                        <TableCell>
                            <p class="font-medium">
                                {{ order.customer_name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ order.customer_email }}
                            </p>
                        </TableCell>
                        <TableCell class="text-muted-foreground">{{
                            order.event_title
                        }}</TableCell>
                        <TableCell>
                            <span class="font-display font-semibold">{{
                                formatCurrency(order.total, order.currency)
                            }}</span>
                        </TableCell>
                        <TableCell class="text-muted-foreground">{{
                            order.cancelled_at
                                ? formatDate(order.cancelled_at)
                                : "—"
                        }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </div>
</template>
