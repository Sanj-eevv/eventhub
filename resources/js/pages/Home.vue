<script setup lang="ts">
import { Link, router, usePage } from "@inertiajs/vue3";
import TextLink from "@/components/TextLink.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { create } from "@/wayfinder/App/Http/Controllers/Auth/LoginController";
import { register } from "@/wayfinder/App/Http/Controllers/Auth/RegisterController";
import DashboardController from "@/wayfinder/App/Http/Controllers/DashboardController";
import { logout } from "@/wayfinder/routes/auth";

const page = usePage();
const user = page.props.auth.user;
const handleLogout = () => {
    router.flushAll();
};
</script>
<template>
    <HomeLayout>
        <template v-if="user">
            <TextLink
                :href="DashboardController()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Dashboard</TextLink
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
        </template>
        <template v-else>
            <TextLink
                :href="register()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Register</TextLink
            >
            <TextLink
                :href="create()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Log in</TextLink
            >
        </template>
    </HomeLayout>
</template>
