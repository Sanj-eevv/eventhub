<script setup lang="ts">
import { ArrowLeftIcon } from "lucide-vue-next";
import OrganizationStatusBadge from "@/components/Dashboard/Organizations/OrganizationStatusBadge.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { Organization } from "@/types/organization";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import {
    index as orgsIndex,
    show as orgsShow,
} from "@/wayfinder/routes/dashboard/organizations";

const props = defineProps<{
    organization: Organization;
}>();

const goBack = () => window.history.back();

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Organizations", href: orgsIndex().url },
    {
        title: props.organization.title,
        href: orgsShow({ organization: props.organization.uuid }).url,
    },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="sm" @click="goBack">
                    <ArrowLeftIcon class="size-4" />
                    Back
                </Button>
                <h1 class="text-2xl font-bold">{{ organization.title }}</h1>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Description
                            </p>
                            <p class="font-medium">
                                {{ organization.description }}
                            </p>
                        </div>
                        <div v-if="organization.contact_address">
                            <p class="text-sm text-muted-foreground">Address</p>
                            <p class="font-medium">
                                {{ organization.contact_address }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Contact Email
                            </p>
                            <p class="font-medium">
                                {{ organization.contact_email }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Status & Dates</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <OrganizationStatusBadge
                                :status="organization.status"
                            />
                        </div>
                        <div v-if="organization.verified_at">
                            <p class="text-sm text-muted-foreground">
                                Verified
                            </p>
                            <p class="font-medium">
                                {{ organization.verified_at }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Created</p>
                            <p class="font-medium">
                                {{ organization.created_at }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
