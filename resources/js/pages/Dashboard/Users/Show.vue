<script setup lang="ts">
import { ArrowLeftIcon } from "lucide-vue-next";
import { formatDate } from "@/lib/utils";
import OrganizationStatusBadge from "@/components/Dashboard/Organizations/OrganizationStatusBadge.vue";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { usePermission } from "@/composables/usePermission";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { UserResource } from "@/types/user";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { show as orgsShow } from "@/wayfinder/routes/dashboard/organizations";
import {
    index as usersIndex,
    show as usersShow,
} from "@/wayfinder/routes/dashboard/users";

const props = defineProps<{ user: UserResource }>();

const can = usePermission("organization");

const goBack = () => window.history.back();
const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Users", href: usersIndex().url },
    { title: props.user.name, href: usersShow({ user: props.user.uuid }).url },
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
                <h1 class="text-2xl font-bold">{{ user.name }}</h1>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Account Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Email</p>
                            <p class="font-medium">{{ user.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Email Verified
                            </p>
                            <p class="font-medium">
                                {{
                                    user.email_verified
                                        ? "Verified"
                                        : "Not verified"
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Role</p>
                            <Badge variant="secondary">
                                {{ user.role.name }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Created</p>
                            <p class="font-medium">
                                {{ formatDate(user.created_at) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="user.organization">
                    <CardHeader>
                        <CardTitle>Organization</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Title</p>
                            <a
                                :href="
                                    orgsShow({
                                        organization: user.organization.uuid,
                                    }).url
                                "
                                v-if="can('viewAny')"
                                class="font-medium text-blue-600 hover:underline"
                            >
                                {{ user.organization.title }}
                            </a>
                            <span v-else class="font-medium text-gray-500">
                                {{ user.organization.title }}
                            </span>
                        </div>

                        <div>
                            <p class="text-sm text-muted-foreground">Status</p>
                            <OrganizationStatusBadge
                                :status="user.organization.status"
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
