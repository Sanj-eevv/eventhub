import { readonly, shallowRef } from "vue";

export function useDialogState() {
    const modalOpen = shallowRef(false);

    const open = (): void => {
        modalOpen.value = true;
    };

    const close = (): void => {
        modalOpen.value = false;
    };

    return { isOpen: readonly(modalOpen), open, close };
}
