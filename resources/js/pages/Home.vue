<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { create } from "@/actions/App/Http/Controllers/Auth/LoginController";
import { register } from "@/actions/App/Http/Controllers/Auth/RegisterController";
import TextLink from "@/components/TextLink.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { logout } from "@/routes/auth";
const handleLogout = () => {
    router.flushAll();
};
</script>
<template>
    <HomeLayout>
        <TextLink
            :href="register()"
            class="underline underline-offset-4"
            :tabindex="6"
            >Register</TextLink
        >
        <Link
            class="block w-full cursor-pointer"
            :href="logout()"
            @click="handleLogout"
            at="button"
            data-test="logout-button"
        >
            Log out
        </Link>
        <TextLink
            :href="create()"
            class="underline underline-offset-4"
            :tabindex="6"
            >Log in</TextLink
        >
    </HomeLayout>
</template>
