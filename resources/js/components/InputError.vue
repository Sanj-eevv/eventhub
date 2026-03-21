<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <p v-if="message" data-error class="font-body text-xs text-sf-ember mt-0.5">
        {{ message }}
    </p>
</template>
