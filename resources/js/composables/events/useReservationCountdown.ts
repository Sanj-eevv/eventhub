import { computed, onMounted, onUnmounted, shallowRef } from "vue";

export function useReservationCountdown(expiresAt: string) {
    const secondsRemaining = shallowRef(0);

    function update(): void {
        const diff = Math.floor(
            (new Date(expiresAt).getTime() - Date.now()) / 1000,
        );
        secondsRemaining.value = Math.max(0, diff);
    }

    const formattedCountdown = computed<string>(() => {
        const minutes = Math.floor(secondsRemaining.value / 60);
        const seconds = secondsRemaining.value % 60;
        return `${minutes}:${seconds.toString().padStart(2, "0")}`;
    });

    const isActive = computed<boolean>(() => secondsRemaining.value > 0);

    onMounted(() => {
        update();
        const interval = setInterval(update, 1000);
        onUnmounted(() => clearInterval(interval));
    });

    return { formattedCountdown, isActive };
}
