<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import { index } from "@/wayfinder/routes/dashboard";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: index().url,
    },
];
</script>
<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <Head title="Dashboard" />
        <div class="p-4">
            <h1 class="text-2xl font-bold">Dashboard</h1>
        </div>
    </DashboardLayout>
</template>
