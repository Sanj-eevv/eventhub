<script setup lang="ts">
defineProps<{
    totalSteps: number;
    activeStep: number;
}>();
</script>

<template>
    <div class="flex items-center gap-2">
        <template v-for="step in totalSteps" :key="step">
            <span
                v-if="step > 1"
                class="h-px flex-1 transition-colors duration-300"
                :class="activeStep >= step ? 'bg-sf-gold' : 'bg-sf-border'"
            />
            <span
                class="flex h-6 w-6 items-center justify-center rounded-full font-code text-xs font-medium transition-all duration-300"
                :class="step === activeStep
                    ? 'bg-sf-gold text-sf-bg'
                    : step < activeStep
                        ? 'bg-sf-gold/30 text-sf-gold'
                        : 'bg-sf-surface border border-sf-border text-sf-tertiary'
                "
            >
                {{ step }}
            </span>
        </template>
    </div>
</template>
