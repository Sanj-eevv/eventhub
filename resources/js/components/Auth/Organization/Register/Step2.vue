<script lang="ts" setup>
import type { FormComponentRef } from "@inertiajs/core";
import { useFormContext } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";

const form = useFormContext() as FormComponentRef;
const emit = defineEmits<{
    back: [];
}>();
</script>
<template>
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="name">Your name</Label>
            <Input
                id="name"
                name="name"
                type="text"
                required
                :tabindex="1"
                autocomplete="name"
                placeholder="Full name"
            />
            <InputError :message="form.errors.name" />
        </div>
        <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input
                id="email"
                name="email"
                type="email"
                required
                :tabindex="2"
                autocomplete="email"
                placeholder="Email"
            />
            <InputError :message="form.errors.email" />
        </div>
        <div class="grid gap-2">
            <Label for="password">Password</Label>
            <Input
                id="password"
                name="password"
                type="password"
                required
                :tabindex="3"
                autocomplete="new-password"
                placeholder="Password"
            />
            <InputError :message="form.errors.password" />
        </div>
        <div class="grid gap-2">
            <Label for="password_confirmation">Confirm password</Label>
            <Input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                :tabindex="4"
                autocomplete="new-password"
                placeholder="Confirm password"
            />
        </div>
        <div class="flex gap-3">
            <Button
                type="button"
                variant="outline"
                :tabindex="6"
                :disabled="form.processing"
                class="flex-1"
                @click="emit('back')"
            >
                Back
            </Button>
            <Button type="submit" :tabindex="5" :disabled="form.processing">
                Create account
                <Spinner v-if="form.processing" />
            </Button>
        </div>
    </div>
</template>
