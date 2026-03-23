<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import AppLogoIcon from "@/components/AppLogoIcon.vue";
import { useTheme } from "@/composables/useTheme";
import { home } from "@/wayfinder/routes";

defineProps<{
    title?: string;
    description?: string;
}>();

const { isDark, toggle } = useTheme();
const appName = usePage().props.name as string;
</script>

<template>
    <div
        class="min-h-svh bg-sf-bg flex flex-col items-center justify-center px-5 py-16 transition-colors duration-200"
    >
        <button
            type="button"
            class="fixed top-4 right-4 h-9 w-9 flex items-center justify-center rounded-lg border border-sf-border bg-sf-surface text-sf-muted hover:text-sf-text hover:border-sf-gold transition-all duration-200"
            :aria-label="
                isDark ? 'Switch to light mode' : 'Switch to dark mode'
            "
            @click="toggle"
        >
            <svg
                v-if="isDark"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                />
            </svg>
            <svg
                v-else
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                />
            </svg>
        </button>

        <div class="w-full max-w-sm">
            <div class="flex flex-col items-center mb-10">
                <Link :href="home()" class="flex items-center gap-3 group">
                    <AppLogoIcon
                        class="h-8 w-8 text-sf-gold transition-colors group-hover:text-sf-text"
                    />
                    <span
                        class="font-display font-bold text-xl tracking-tight text-sf-text group-hover:text-sf-muted transition-colors"
                    >
                        {{ appName }}
                    </span>
                </Link>
            </div>
            <div class="mb-6 text-center">
                <h1
                    class="font-display font-semibold text-2xl text-sf-text leading-tight"
                >
                    {{ title }}
                </h1>
                <p
                    v-if="description"
                    class="font-body text-sm text-sf-muted mt-2 leading-relaxed"
                >
                    {{ description }}
                </p>
            </div>
            <div
                class="bg-sf-surface border border-sf-border-subtle rounded-2xl px-7 py-8 transition-colors duration-200"
            >
                <slot />
            </div>
        </div>
    </div>
</template>
