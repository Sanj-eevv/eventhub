<script setup lang="ts">
import { Form } from "@inertiajs/vue3";
import { ref } from "vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea";
import type { GroupedPermissions, Role } from "@/types/role";
import { store, update } from "@/wayfinder/routes/dashboard/roles";

const props = defineProps<{
    groupedPermissions: GroupedPermissions;
    role?: Role;
}>();

const isEditing = !!props.role;

const currentAllowedPermissionIds = ref<number[]>(
    props.role?.permissions?.map((p) => p.id) ?? [],
);

const isGroupAllSelected = (ids: number[]): boolean => {
    if (!ids.length) return false;
    return ids.every((id) => currentAllowedPermissionIds.value.includes(id));
};

const isGroupIndeterminate = (ids: number[]): boolean => {
    if (!ids.length) return false;
    const selectedCount = ids.filter((id) =>
        currentAllowedPermissionIds.value.includes(id),
    ).length;
    return selectedCount > 0 && selectedCount < ids.length;
};

const getGroupCheckboxState = (ids: number[]): boolean | "indeterminate" => {
    if (isGroupIndeterminate(ids)) return "indeterminate";
    return isGroupAllSelected(ids);
};

const togglePermission = (id: number, checked: boolean) => {
    const newIds = checked
        ? [...currentAllowedPermissionIds.value, id]
        : currentAllowedPermissionIds.value.filter((i) => i !== id);
    currentAllowedPermissionIds.value = newIds;
};

const toggleGroupAll = (ids: number[], checked: boolean) => {
    const newIds =
        checked === true
            ? [...new Set([...currentAllowedPermissionIds.value, ...ids])]
            : currentAllowedPermissionIds.value.filter(
                  (id) => !ids.includes(id),
              );
    currentAllowedPermissionIds.value = newIds;
};

const goBack = () => window.history.back();
</script>

<template>
    <Form
        v-bind="
            isEditing
                ? update.form({ role: role?.slug as string })
                : store.form()
        "
        #default="{ errors, processing }"
        class="flex min-h-0 flex-1 flex-col"
    >
        <div class="flex-1 overflow-y-auto px-6 py-6 lg:px-8">
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                        <CardDescription>
                            Basic information about the role.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                autocomplete="off"
                                id="name"
                                :default-value="role?.name"
                                type="text"
                                name="name"
                                required
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div v-if="isEditing" class="grid gap-2">
                            <Label for="slug">Slug</Label>
                            <Input
                                id="slug"
                                :default-value="role?.slug"
                                type="text"
                                disabled
                                class="font-mono text-muted-foreground"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role-description"> Description </Label>
                            <Textarea
                                id="description"
                                name="description"
                                :default-value="role?.description"
                                rows="3"
                            />
                            <InputError :message="errors.description" />
                        </div>
                    </CardContent>
                </Card>

                <div class="space-y-4">
                    <div>
                        <h2 class="text-base font-semibold">Permissions</h2>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            Choose which actions this role is allowed to
                            perform.
                        </p>
                    </div>

                    <Card
                        v-for="(actions, group) in groupedPermissions"
                        :key="group"
                    >
                        <CardHeader class="pb-3">
                            <div class="flex items-center justify-between">
                                <CardTitle
                                    class="text-xs font-semibold uppercase tracking-widest text-muted-foreground"
                                >
                                    {{ group }}
                                </CardTitle>
                                <label
                                    class="flex cursor-pointer items-center gap-2 text-sm font-medium"
                                >
                                    <Checkbox
                                        :model-value="
                                            getGroupCheckboxState(
                                                actions.map(
                                                    (action) => action.id,
                                                ),
                                            )
                                        "
                                        @update:model-value="
                                            toggleGroupAll(
                                                actions.map(
                                                    (action) => action.id,
                                                ),
                                                $event as boolean,
                                            )
                                        "
                                    />
                                    Select all
                                </label>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                class="grid grid-cols-2 gap-2.5 md:grid-cols-4"
                            >
                                <label
                                    v-for="action in actions"
                                    :key="action.id"
                                    class="flex capitalize cursor-pointer items-center gap-2.5 rounded-md border px-3 py-2.5 text-sm font-medium transition-colors hover:bg-accent has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                                >
                                    <Checkbox
                                        :model-value="
                                            currentAllowedPermissionIds.includes(
                                                action.id,
                                            )
                                        "
                                        :value="action.id"
                                        name="permissions[]"
                                        @update:model-value="
                                            togglePermission(
                                                action.id,
                                                $event as boolean,
                                            )
                                        "
                                    />
                                    {{ action.name }}
                                </label>
                            </div>
                        </CardContent>
                    </Card>
                    <InputError :message="errors.permissions" />
                </div>
            </div>
        </div>

        <div class="shrink-0 border-t bg-background px-6 py-4 lg:px-8">
            <div class="flex items-center justify-end gap-3">
                <Button type="button" variant="ghost" @click="goBack()">
                    Cancel
                </Button>
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" />
                    {{ isEditing ? "Save Changes" : "Create Role" }}
                </Button>
            </div>
        </div>
    </Form>
</template>
