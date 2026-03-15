<script setup lang="ts">
import { router, usePage } from "@inertiajs/vue3";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import type { BreadcrumbItem } from "@/types";
import type { GroupedPermissions, Role } from "@/types/role";
import { index as dashboardIndex } from "@/wayfinder/routes/dashboard";
import { edit, index, show } from "@/wayfinder/routes/dashboard/roles";

type RoleShowPageProps = {
    role: Role;
    groupedPermissions: GroupedPermissions;
};

const props = defineProps<RoleShowPageProps>();
const pageProps = usePage().props;

const breadcrumbs: BreadcrumbItem[] = [
    { title: "Dashboard", href: dashboardIndex().url },
    { title: "Roles", href: index().url },
    { title: props.role.name, href: show(props.role).url },
];
</script>

<template>
    <DashboardLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold">{{ role.name }}</h1>
                    <Badge :variant="role.preserved ? 'secondary' : 'outline'">
                        {{ role.preserved ? "System" : "Custom" }}
                    </Badge>
                </div>
                <Button
                    v-if="pageProps.can.role.update"
                    @click="router.visit(edit(role).url)"
                >
                    Edit Role
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Role Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Name</p>
                            <p class="font-medium">{{ role.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Slug</p>
                            <p class="font-mono text-sm">{{ role.slug }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Description
                            </p>
                            <p class="font-medium">
                                {{ role.description }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Usage</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Type</p>
                            <Badge
                                :variant="
                                    role.preserved ? 'secondary' : 'outline'
                                "
                            >
                                {{
                                    role.preserved
                                        ? "System Role"
                                        : "Custom Role"
                                }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-4">
                <div>
                    <h2 class="text-base font-semibold">Permissions</h2>
                </div>
                <p
                    v-if="!groupedPermissions"
                    class="text-sm text-muted-foreground"
                >
                    No permissions assigned.
                </p>

                <Card
                    v-for="(actions, group) in groupedPermissions"
                    :key="group"
                >
                    <CardHeader class="pb-3">
                        <CardTitle
                            class="text-xs font-semibold uppercase tracking-widest text-muted-foreground"
                        >
                            {{ group }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-2.5 md:grid-cols-4">
                            <div
                                v-for="action in actions"
                                :key="action.id"
                                class="flex items-center capitalize gap-2.5 rounded-md border border-primary bg-primary/5 px-3 py-2.5 text-sm font-medium"
                            >
                                {{ action.name }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
