import { shallowRef } from "vue";

export function useTableLoading() {
    const isLoading = shallowRef(false);
    let timeout: ReturnType<typeof setTimeout> | null = null;

    const onStart = (): void => {
        timeout = setTimeout(() => {
            isLoading.value = true;
        }, 250);
    };

    const onFinish = (): void => {
        if (timeout) {
            clearTimeout(timeout);
            timeout = null;
        }
        isLoading.value = false;
    };

    return { isLoading, onStart, onFinish };
}
