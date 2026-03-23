<script setup lang="ts">
import HomeFooter from "@/components/Home/HomeFooter.vue";
import HomeHeader from "@/components/Home/HomeHeader.vue";
</script>

<template>
    <div
        class="min-h-screen bg-sf-bg text-sf-text font-body transition-colors duration-200"
    >
        <div
            class="fixed inset-0 flex items-center justify-center pointer-events-none select-none rotate-15"
            aria-hidden="true"
        >
            <svg
                viewBox="0 0 120 200"
                class="h-[140vh] w-auto opacity-[0.04]"
                fill="var(--color-sf-gold)"
                xmlns="http://www.w3.org/2000/svg"
            >
                <ellipse
                    cx="34"
                    cy="170"
                    rx="32"
                    ry="20"
                    transform="rotate(-22 34 170)"
                />
                <rect x="60" y="18" width="8" height="155" />
                <path d="M68,18 C130,36 134,90 84,118 C102,96 108,50 68,34 Z" />
            </svg>
        </div>

        <HomeHeader />
        <main class="pt-16">
            <slot />
        </main>
        <HomeFooter />
    </div>
</template>
