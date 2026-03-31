<script setup lang="ts">
defineProps<{
    formattedCountdown: string;
    isExpiringSoon: boolean;
    timerProgress: number;
}>();
</script>

<template>
    <div>
        <div
            :class="[
                'rounded-xl border px-5 py-4 mb-3 flex items-center justify-between transition-all duration-500',
                isExpiringSoon
                    ? 'border-sf-ember/40 bg-sf-ember/10'
                    : 'border-sf-border bg-sf-surface',
            ]"
        >
            <div class="flex items-center gap-3">
                <div
                    :class="[
                        'h-2 w-2 rounded-full transition-colors',
                        isExpiringSoon
                            ? 'bg-sf-ember animate-pulse'
                            : 'bg-sf-gold',
                    ]"
                />
                <span class="font-body text-sm text-sf-muted">
                    Reservation expires in
                </span>
            </div>
            <span
                :class="[
                    'font-code text-xl tabular-nums transition-colors',
                    isExpiringSoon ? 'text-sf-ember' : 'text-sf-text',
                ]"
            >
                {{ formattedCountdown }}
            </span>
        </div>

        <div
            class="h-px bg-sf-border-subtle mb-10 overflow-hidden rounded-full"
        >
            <div
                class="h-full transition-all duration-1000 rounded-full"
                :class="isExpiringSoon ? 'bg-sf-ember' : 'bg-sf-gold'"
                :style="{ width: `${timerProgress * 100}%` }"
            />
        </div>
    </div>
</template>
