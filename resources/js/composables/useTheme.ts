import { onMounted, onUnmounted, ref } from "vue";

export type ThemePreference = "light" | "dark" | "system";

export function useTheme() {
    const preference = ref<ThemePreference>(
        (typeof window !== "undefined"
            ? (localStorage.getItem("theme") as ThemePreference)
            : null) ?? "system",
    );

    const isDark = ref(false);

    const systemQuery =
        typeof window !== "undefined"
            ? window.matchMedia("(prefers-color-scheme: dark)")
            : null;

    const resolveIsDark = (pref: ThemePreference): boolean =>
        pref === "dark" ||
        (pref === "system" && (systemQuery?.matches ?? false));

    const applyTheme = (pref: ThemePreference): void => {
        isDark.value = resolveIsDark(pref);
        document.documentElement.classList.toggle("dark", isDark.value);
    };

    const setPreference = (value: ThemePreference): void => {
        preference.value = value;
        if (value === "system") {
            localStorage.removeItem("theme");
        } else {
            localStorage.setItem("theme", value);
        }
        applyTheme(value);
    };

    const toggle = (): void => {
        setPreference(isDark.value ? "light" : "dark");
    };

    const onSystemChange = (): void => {
        if (preference.value === "system") {
            applyTheme("system");
        }
    };

    onMounted(() => {
        applyTheme(preference.value);
        systemQuery?.addEventListener("change", onSystemChange);
    });

    onUnmounted(() => {
        systemQuery?.removeEventListener("change", onSystemChange);
    });

    return { preference, isDark, setPreference, toggle };
}
