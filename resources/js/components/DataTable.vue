<script setup lang="ts" generic="TData, TValue">
import type { PaginationState, SortingState } from "@tanstack/vue-table";
import type { ColumnDef, ColumnFiltersState } from "@tanstack/vue-table";
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";
import {
    ArrowDownIcon,
    ArrowUpIcon,
    ChevronDown,
    ChevronsUpDownIcon,
} from "lucide-vue-next";
import { ref } from "vue";
import DataTablePagination from "@/components/DataTablePagination.vue";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Skeleton } from "@/components/ui/skeleton";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import type { PaginatedResponse } from "@/types";

const props = defineProps<{
    data: PaginatedResponse<TData>;
    columns: ColumnDef<TData, TValue>[];
    pagination: PaginationState;
    sorting: SortingState;
    isLoading: boolean;
}>();

const columnFilters = ref<ColumnFiltersState>([]);

const table = useVueTable({
    get data() {
        return props.data.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    get pageCount() {
        return props.data.meta.last_page;
    },
    manualPagination: true,
    manualSorting: true,
    manualFiltering: true,
    enableMultiSort: true,
    onSortingChange: (updater) => {
        const newValue =
            typeof updater === "function" ? updater(props.sorting) : updater;
        emit("update:sorting", newValue);
    },
    onPaginationChange: (updater) => {
        const newValue =
            typeof updater === "function" ? updater(props.pagination) : updater;
        emit("update:pagination", newValue);
    },
    onColumnFiltersChange: (updater) =>
        typeof updater === "function" ? updater(columnFilters.value) : updater,
    state: {
        get sorting() {
            return props.sorting;
        },
        get pagination() {
            return props.pagination;
        },
        get columnFilters() {
            return columnFilters.value;
        },
    },
});

const emit = defineEmits<{
    (e: "update:pagination", value: PaginationState): void;
    (e: "update:sorting", value: SortingState): void;
}>();
</script>

<template>
    <div class="full">
        <div class="flex items-center py-4">
            <slot name="filterSlot"></slot>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columns <ChevronDown class="ml-2 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem
                        v-for="column in table
                            .getAllColumns()
                            .filter((column) => column.getCanHide())"
                        :key="column.id"
                        class="capitalize"
                        :model-value="column.getIsVisible()"
                        @update:model-value="
                            (value) => {
                                column.toggleVisibility(!!value);
                            }
                        "
                    >
                        {{ column.columnDef.header }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="
                                header.column.getCanSort()
                                    ? 'cursor-pointer select-none'
                                    : ''
                            "
                            @click="
                                header.column.getCanSort()
                                    ? header.column.toggleSorting(
                                          undefined,
                                          $event.shiftKey,
                                      )
                                    : undefined
                            "
                        >
                            <div class="flex items-center gap-1 truncate">
                                <FlexRender
                                    v-if="!header.isPlaceholder"
                                    :render="header.column.columnDef.header"
                                    :props="header.getContext()"
                                />
                                <template v-if="header.column.getCanSort()">
                                    <ArrowUpIcon
                                        v-if="
                                            header.column.getIsSorted() ===
                                            'asc'
                                        "
                                        class="size-4 shrink-0"
                                    />
                                    <ArrowDownIcon
                                        v-else-if="
                                            header.column.getIsSorted() ===
                                            'desc'
                                        "
                                        class="size-4 shrink-0"
                                    />
                                    <ChevronsUpDownIcon
                                        v-else
                                        class="size-4 shrink-0 text-muted-foreground"
                                    />
                                    <span
                                        v-if="
                                            header.column.getIsSorted() &&
                                            sorting.length > 1
                                        "
                                        class="text-muted-foreground text-xs font-medium"
                                    >
                                        {{
                                            sorting.findIndex(
                                                (s) =>
                                                    s.id === header.column.id,
                                            ) + 1
                                        }}
                                    </span>
                                </template>
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="isLoading">
                        <TableRow
                            v-for="i in pagination.pageSize"
                            :key="`skeleton-${i}`"
                        >
                            <TableCell
                                v-for="(_, j) in columns"
                                :key="`skeleton-cell-${j}`"
                            >
                                <Skeleton class="h-4 w-full" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                No results.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
            <DataTablePagination :table="table" :total="data.meta.total" />
        </div>
    </div>
</template>
