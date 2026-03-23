import { usePage } from "@inertiajs/vue3";
import type { Inertia } from "@/wayfinder/types";

type Permissions = NonNullable<Inertia.SharedData["can"]>;

export function usePermission<M extends keyof Permissions>(model: M) {
    const page = usePage();

    return (action: keyof Permissions[M]): boolean => {
        const permissions = page.props.can as Permissions | null;
        return (permissions?.[model]?.[action] as boolean | undefined) ?? false;
    };
}
