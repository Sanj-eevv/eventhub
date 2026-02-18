<script setup lang="ts">
defineProps<{
    totalSteps: number;
    activeStep: number;
}>();
</script>
<template>
    <div class="flex items-center gap-2 text-sm text-muted-foreground">
        <template v-for="step in totalSteps" :key="step">
            <span
                v-if="step > 1"
                class="h-px flex-1"
                :class="activeStep === step ? 'bg-primary' : 'bg-border'"
            />
            <span
                class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-medium"
                :class="
                    step === activeStep
                        ? 'bg-primary text-primary-foreground'
                        : 'bg-muted text-muted-foreground'
                "
            >
                {{ step }}
            </span>
        </template>
    </div>
</template>
