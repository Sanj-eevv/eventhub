import { createInertiaApp, usePage } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import type { DefineComponent } from "vue";
import { createApp, h, watch } from "vue";
import "../css/app.css";
import "vue-sonner/style.css";
import { toast } from "vue-sonner";
import { Toaster } from "@/components/ui/sonner";

type Flash = {
    toastError?: string | null;
    toastInfo?: string | null;
    toastSuccess?: string | null;
    toastWarning?: string | null;
};

const appName = import.meta.env.VITE_APP_NAME || "Laravel";
createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>("./pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        createApp({
            setup() {
                const page = usePage();
                watch(
                    () => page.props?.flash as Flash,
                    (flash: Flash) => {
                        if (flash?.toastSuccess)
                            toast.success(flash.toastSuccess);
                        if (flash?.toastError) toast.error(flash.toastError);
                        if (flash?.toastInfo) toast.info(flash.toastInfo);
                        if (flash?.toastWarning)
                            toast.warning(flash.toastWarning);
                    },
                    { immediate: true },
                );
                return () => [h(Toaster), h(App, props)];
            },
        })
            .use(plugin)
            .mount(el);
    },
});
