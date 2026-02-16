<script setup lang="ts">
import { create } from "@/actions/App/Http/Controllers/Auth/LoginController";
import { register } from "@/actions/App/Http/Controllers/Auth/RegisterController";
import TextLink from "@/components/TextLink.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
</script>
<template>
    <HomeLayout>
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
    </HomeLayout>
</template>
