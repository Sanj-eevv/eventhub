import { ref } from "vue";

export function useDialogState() {
    const modalOpen = ref(false);

    const isOpen = () => {
        return modalOpen.value;
    };

    const open = (): void => {
        modalOpen.value = true;
    };

    const close = (): void => {
        modalOpen.value = false;
    };

    return { isOpen, close, open };
}
