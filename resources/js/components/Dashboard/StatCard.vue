<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ArrowRight } from "lucide-vue-next";
import type { Component } from "vue";

defineProps<{
    label: string;
    formatted: string;
    href: string;
    linkText: string;
    icon: Component;
    iconClass: string;
    pillClass: string;
    accentClass: string;
}>();
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl border border-border bg-card p-5 transition-shadow hover:shadow-md"
    >
        <div
            class="absolute inset-x-0 top-0 h-px bg-gradient-to-r opacity-60"
            :class="accentClass"
        />

        <div class="flex items-start justify-between">
            <p
                class="text-[11px] font-medium uppercase tracking-widest text-muted-foreground"
            >
                {{ label }}
            </p>
            <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                :class="pillClass"
            >
                <component :is="icon" class="h-4 w-4" :class="iconClass" />
            </div>
        </div>

        <p class="font-display mt-4 text-4xl font-semibold tracking-tight">
            {{ formatted }}
        </p>

        <Link
            :href="href"
            class="mt-4 inline-flex items-center gap-1 text-xs text-muted-foreground transition-colors hover:text-foreground"
        >
            {{ linkText }}
            <ArrowRight class="h-3 w-3" />
        </Link>
    </div>
</template>
