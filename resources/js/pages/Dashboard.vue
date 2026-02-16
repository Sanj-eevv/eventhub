<script setup lang="ts">
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import { index } from "@/routes/dashboard";
import type { BreadcrumbItem } from "@/types";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: index().url,
    },
];
</script>
<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome to your dashboard!</p>
        </div>
    </DashboardLayout>
</template>
