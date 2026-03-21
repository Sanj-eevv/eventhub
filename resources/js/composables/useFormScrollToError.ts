import { type Ref, watch } from "vue";

export function useFormScrollToError(
    form: { errors: Record<string, string> },
    scrollContainerRef: Ref<HTMLElement | null>,
): void {
    watch(
        () => form.errors,
        (errors) => {
            if (!Object.keys(errors).length) return;
            scrollContainerRef.value
                ?.querySelector("[data-error]")
                ?.scrollIntoView({ behavior: "smooth", block: "center" });
        },
        { flush: "post" },
    );
}
