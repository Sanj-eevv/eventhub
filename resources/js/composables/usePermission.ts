import { usePage } from "@inertiajs/vue3";

const page = usePage();

export function usePermission(model: string) {
    const permissions = page.props.can as Record<string, Record<string, boolean>> | null;
    const can = (action: string) => permissions?.[model]?.[action] ?? false;
    return can;
}
