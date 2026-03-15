import { usePage } from "@inertiajs/vue3";

export function usePermission(model: string) {
    const can = (action: string) =>
        usePage().props.can[model]?.[action] ?? false;
    return can;
}
