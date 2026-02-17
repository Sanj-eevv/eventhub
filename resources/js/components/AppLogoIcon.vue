<script setup lang="ts">
import type { HTMLAttributes } from "vue";

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes["class"];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 32 32"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Guitar -->
        <ellipse cx="10" cy="23" rx="5" ry="6" />
        <line x1="10" y1="17" x2="10" y2="4" />
        <line x1="7" y1="4" x2="13" y2="4" />
        <line x1="10" y1="20" x2="10" y2="26" />

        <!-- Music notes -->
        <circle cx="24" cy="11" r="2" fill="currentColor" />
        <line x1="26" y1="11" x2="26" y2="3" />
        <path d="M26 3c2 0 3 1.5 3 3" />

        <circle cx="22" cy="19" r="2" fill="currentColor" />
        <line x1="24" y1="19" x2="24" y2="11" />
    </svg>
</template>
