import { computed, onMounted, onUnmounted, shallowRef } from "vue";

export function useReservationCountdown(
    expiresAt: string,
    reservedAt?: string,
) {
    const secondsRemaining = shallowRef(0);

    const totalSeconds = reservedAt
        ? (new Date(expiresAt).getTime() - new Date(reservedAt).getTime()) /
          1000
        : null;

    const formattedCountdown = computed(() => {
        const minutes = Math.floor(secondsRemaining.value / 60);
        const seconds = secondsRemaining.value % 60;
        return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
    });

    const isActive = computed(() => secondsRemaining.value > 0);

    const isExpiringSoon = computed(
        () => secondsRemaining.value > 0 && secondsRemaining.value < 120,
    );

    const isExpired = computed(() => secondsRemaining.value === 0);

    const timerProgress = computed(() =>
        totalSeconds !== null && totalSeconds > 0
            ? secondsRemaining.value / totalSeconds
            : null,
    );

    onMounted(() => {
        const expiresAtMs = new Date(expiresAt).getTime();

        const interval = setInterval(() => {
            const remaining = Math.max(
                0,
                Math.floor((expiresAtMs - Date.now()) / 1000),
            );
            secondsRemaining.value = remaining;
            if (remaining === 0) clearInterval(interval);
        }, 1000);

        onUnmounted(() => clearInterval(interval));
    });

    return { formattedCountdown, isActive, isExpiringSoon, isExpired, timerProgress };
}
