<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Check, ChevronsUpDown } from "lucide-vue-next";
import { computed, ref } from "vue";
import { toast } from "vue-sonner";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Spinner } from "@/components/ui/spinner";
import { cn } from "@/lib/utils";
import type { Organization } from "@/types/organization";
import type { Role } from "@/types/role";
import type { User } from "@/types/user";
import { store, update } from "@/wayfinder/routes/dashboard/users";

const props = defineProps<{
    open: boolean;
    user?: Omit<User, "role"> & {
        role: Role;
    };
    roles: Role[];
    organizations: Omit<Organization, "id">[];
}>();

const emit = defineEmits<{
    "update:open": [value: boolean];
}>();

const isEditing = computed(() => !!props.user);

const formConfig = computed(() => {
    const editing = isEditing.value;
    return {
        title: editing ? "Edit User" : "Create User",
        descritption: editing
            ? "Update the user details below."
            : "Fill in the details to create a new user.",
        submitButtonText: editing ? "Update" : "Create",
    };
});

const form = useForm({
    name: props.user?.name || undefined,
    email: props.user?.email || undefined,
    password: undefined,
    password_confirmation: undefined,
    role: props.user?.role.slug || undefined,
    organization: props.user?.organization?.uuid || undefined,
});

const orgPopoverOpen = ref(false);

const selectedRole = computed(() =>
    props.roles.find((r) => r.slug === form.role),
);

const requiresOrganization = computed(
    () => selectedRole.value?.slug === "organization-admin",
);

const selectedOrganization = computed(() =>
    props.organizations.find((o) => o.uuid === form.organization),
);

const updateUser = () => {
    const user = props.user as User;
    form.put(update({ user: user.uuid }).url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error(
                "An error occurred while updating the user. Please try again.",
            );
        },
    });
};

const createUser = () => {
    form.post(store().url, {
        onSuccess: () => {
            emit("update:open", false);
        },
        onError: () => {
            toast.error(
                "An error occurred while creating the user. Please try again.",
            );
        },
    });
};
</script>

<template>
    <Dialog
        :open="open"
        @update:open="(val: boolean) => emit('update:open', val)"
    >
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ formConfig.title }}</DialogTitle>
                <DialogDescription>
                    {{ formConfig.descritption }}
                </DialogDescription>
            </DialogHeader>

            <form
                @submit.prevent="(isEditing ? updateUser : createUser)()"
                class="space-y-4"
            >
                <div class="grid gap-2">
                    <Label for="user-name">Name</Label>
                    <Input id="user-name" v-model="form.name" type="text" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="user-email">Email</Label>
                    <Input id="user-email" v-model="form.email" type="email" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2" v-if="!isEditing">
                    <Label for="user-password">
                        Password
                        <span v-if="isEditing" class="text-muted-foreground">
                            (leave blank to keep current)
                        </span>
                    </Label>
                    <Input
                        id="user-password"
                        v-model="form.password"
                        type="password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2" v-if="!isEditing">
                    <Label for="user-password-confirmation">
                        Confirm Password
                    </Label>
                    <Input
                        id="user-password-confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="user-role">Role</Label>
                    <Select v-model="form.role">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.slug"
                                :value="role.slug"
                            >
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.role" />
                </div>

                <div v-if="requiresOrganization" class="grid gap-2">
                    <Label>Organization</Label>
                    <Popover v-model:open="orgPopoverOpen">
                        <PopoverTrigger as-child>
                            <Button
                                variant="outline"
                                role="combobox"
                                :aria-expanded="orgPopoverOpen"
                                class="w-full justify-between font-normal"
                            >
                                {{
                                    selectedOrganization?.title ??
                                    "Select organization..."
                                }}
                                <ChevronsUpDown
                                    class="ml-2 h-4 w-4 shrink-0 opacity-50"
                                />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent
                            class="w-[--reka-popover-trigger-width] p-0"
                        >
                            <Command>
                                <CommandInput
                                    placeholder="Search organization..."
                                />
                                <CommandList>
                                    <CommandEmpty>
                                        No organization found.
                                    </CommandEmpty>
                                    <CommandGroup>
                                        <CommandItem
                                            v-for="org in organizations"
                                            :key="org.uuid"
                                            :value="org.uuid"
                                            @select="
                                                ((form.organization = org.uuid),
                                                (orgPopoverOpen = false))
                                            "
                                        >
                                            <Check
                                                :class="
                                                    cn(
                                                        'mr-2 h-4 w-4',
                                                        form.organization ===
                                                            org.uuid
                                                            ? 'opacity-100'
                                                            : 'opacity-0',
                                                    )
                                                "
                                            />
                                            {{ org.title }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                    <InputError :message="form.errors.organization" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        {{ isEditing ? "Update" : "Create" }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
