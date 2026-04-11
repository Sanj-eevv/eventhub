import { createInertiaApp, usePage } from "@inertiajs/vue3";
import { createApp, h, watch } from "vue";
import "../css/app.css";
import "vue-sonner/style.css";
import { toast } from "vue-sonner";
import { Toaster } from "@/components/ui/sonner";

type Flash = {
    toast_error?: string | null;
    toast_info?: string | null;
    toast_success?: string | null;
    toast_warning?: string | null;
};

const appName = import.meta.env.VITE_APP_NAME || "Laravel";
createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob("./pages/**/*.vue");
        return pages[`./pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        createApp({
            setup() {
                const page = usePage();
                watch(
                    () => page.props?.flash as Flash,
                    (flash: Flash) => {
                        if (flash?.toast_success)
                            toast.success(flash.toast_success);
                        if (flash?.toast_error) toast.error(flash.toast_error);
                        if (flash?.toast_info) toast.info(flash.toast_info);
                        if (flash?.toast_warning)
                            toast.warning(flash.toast_warning);
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
