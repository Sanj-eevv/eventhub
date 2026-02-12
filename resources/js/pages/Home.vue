<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {register} from "@/actions/App/Http/Controllers/Auth/RegisterController";
import HomeLayout from "@/layouts/HomeLayout.vue";
</script>
<template>
    <HomeLayout>
        <Link :href="register()">Register</Link>
    </HomeLayout>
</template>
