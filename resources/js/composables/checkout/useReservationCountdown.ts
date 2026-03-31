import { useNow } from "@vueuse/core";
import { computed } from "vue";

const EXPIRING_SOON_THRESHOLD_SECONDS = 120;

export function useReservationCountdown(
    expiresAt: string,
    reservedAt?: string,
) {
    const expiresAtMs = new Date(expiresAt).getTime();
    const totalSeconds = reservedAt
        ? (expiresAtMs - new Date(reservedAt).getTime()) / 1000
        : null;

    const now = useNow({ interval: 1000 });

    const secondsRemaining = computed(() =>
        Math.max(0, Math.floor((expiresAtMs - now.value.getTime()) / 1000)),
    );

    const formattedCountdown = computed(() => {
        const minutes = Math.floor(secondsRemaining.value / 60);
        const seconds = secondsRemaining.value % 60;
        return `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
    });

    const isActive = computed(() => secondsRemaining.value > 0);
    const isExpiringSoon = computed(
        () =>
            secondsRemaining.value > 0 &&
            secondsRemaining.value < EXPIRING_SOON_THRESHOLD_SECONDS,
    );
    const isExpired = computed(() => secondsRemaining.value === 0);
    const timerProgress = computed(() =>
        totalSeconds !== null && totalSeconds > 0
            ? secondsRemaining.value / totalSeconds
            : 0,
    );

    return {
        formattedCountdown,
        isActive,
        isExpiringSoon,
        isExpired,
        timerProgress,
    };
}
