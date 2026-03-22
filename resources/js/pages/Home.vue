<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import HomeEventsGrid from "@/components/Home/HomeEventsGrid.vue";
import HomeHero from "@/components/Home/HomeHero.vue";
import HomeLayout from "@/layouts/HomeLayout.vue";
import type { PublicEvent } from "@/types/event";

const props = defineProps<{ events: PublicEvent[] }>();
</script>

<template>
    <HomeLayout>
        <Head title="Home" />
        <HomeHero :events="props.events" />
        <HomeEventsGrid :events="props.events" />
    </HomeLayout>
</template>
