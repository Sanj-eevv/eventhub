<script setup lang="ts">
defineProps<{ description: string }>();
</script>

<template>
    <div>
        <div class="flex items-center gap-3 mb-6">
            <span class="h-px w-6 bg-sf-gold" />
            <h2 class="font-display text-2xl font-medium text-sf-text">
                About this Event
            </h2>
        </div>
        <p
            class="font-body font-light text-sf-muted leading-relaxed whitespace-pre-line text-base"
        >
            {{ description }}
        </p>
    </div>
</template>
