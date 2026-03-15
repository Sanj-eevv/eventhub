<script setup lang="ts" generic="TData">
import type { Table } from "@tanstack/vue-table";
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronsLeftIcon,
    ChevronsRightIcon,
} from "lucide-vue-next";
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from "@/components/ui/pagination";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

interface Props {
    table: Table<TData>;
    total: number;
}

defineProps<Props>();
</script>

<template>
    <div
        class="flex flex-col items-center gap-4 px-4 py-3 sm:flex-row sm:justify-between"
    >
        <div class="flex items-center gap-2">
            <Select
                :model-value="`${table.getState().pagination.pageSize}`"
                @update:model-value="(v) => table.setPageSize(Number(v))"
            >
                <SelectTrigger class="h-8 w-[70px]">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent side="top">
                    <SelectItem
                        v-for="size in [5, 10, 15, 20, 30, 40, 50]"
                        :key="size"
                        :value="`${size}`"
                    >
                        {{ size }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p class="text-sm text-muted-foreground whitespace-nowrap">
                Page {{ table.getState().pagination.pageIndex + 1 }} of
                {{ table.getPageCount() }}
            </p>
        </div>

        <Pagination
            :total="total"
            :items-per-page="table.getState().pagination.pageSize"
            :page="table.getState().pagination.pageIndex + 1"
            :sibling-count="1"
            show-edges
            class="justify-end"
            @update:page="(page) => table.setPageIndex(page - 1)"
        >
            <PaginationContent v-slot="{ items }">
                <PaginationFirst>
                    <ChevronsLeftIcon />
                </PaginationFirst>
                <PaginationPrevious>
                    <ChevronLeftIcon />
                </PaginationPrevious>
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="
                            table.getState().pagination.pageIndex + 1 ===
                            item.value
                        "
                    >
                        {{ item.value }}
                    </PaginationItem>
                    <PaginationEllipsis v-else :index="index" />
                </template>
                <PaginationNext>
                    <ChevronRightIcon />
                </PaginationNext>
                <PaginationLast>
                    <ChevronsRightIcon />
                </PaginationLast>
            </PaginationContent>
        </Pagination>
    </div>
</template>
