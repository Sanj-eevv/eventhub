<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import HomeLayout from "@/layouts/HomeLayout.vue";
import { index as eventsIndex } from "@/wayfinder/routes/events";
import { register as registerCreate } from "@/wayfinder/routes/auth";
</script>

<template>
    <HomeLayout>
        <Head title="Live Events — Discover & Book" />

        <!-- Hero -->
        <section class="relative min-h-[92vh] flex flex-col justify-center overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,color-mix(in_srgb,var(--sf-gold)_8%,transparent),transparent)]" />

            <div class="relative mx-auto max-w-7xl px-5 sm:px-8 pt-16 pb-24">
                <div class="grid lg:grid-cols-2 gap-16 items-center">

                    <!-- Left: Typography -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-3">
                            <span class="h-px w-10 bg-sf-gold" />
                            <span class="text-xs font-body tracking-[0.3em] uppercase text-sf-gold">
                                Live Events Platform
                            </span>
                        </div>

                        <h1 class="font-display font-semibold leading-[0.92] tracking-tight">
                            <span class="block text-[clamp(3.5rem,8vw,7rem)] text-sf-text">Live</span>
                            <span class="block text-[clamp(3.5rem,8vw,7rem)] text-sf-text italic font-normal pl-8 lg:pl-14">Events,</span>
                            <span class="block text-[clamp(3.5rem,8vw,7rem)] text-sf-gold">Reimagined.</span>
                        </h1>

                        <p class="font-body font-light text-sf-muted text-lg leading-relaxed max-w-md">
                            Discover concerts, shows, and experiences worth attending. Reserve your seat in seconds.
                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <Link
                                :href="eventsIndex()"
                                class="inline-flex items-center gap-3 px-7 py-3.5 bg-sf-ember text-white text-sm tracking-wide rounded hover:bg-sf-ember-hover active:scale-95 transition-all duration-200"
                            >
                                Browse Events
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                            <Link
                                :href="registerCreate()"
                                class="inline-flex items-center gap-3 px-7 py-3.5 border border-sf-border text-sf-muted text-sm tracking-wide rounded hover:border-sf-muted hover:text-sf-text transition-all duration-200"
                            >
                                Create account
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Decorative poster stack (always dark — intentional poster aesthetic) -->
                    <div class="hidden lg:flex items-center justify-center relative h-[480px]">
                        <div class="absolute w-56 h-80 rounded-xl bg-gradient-to-br from-[#1a0f2e] via-[#2d1b4e] to-[#0f0a1c] border border-white/5 rotate-[10deg] translate-x-16 -translate-y-6 shadow-2xl opacity-50" />
                        <div class="absolute w-56 h-80 rounded-xl bg-gradient-to-br from-[#0d1f1a] via-[#1a3d30] to-[#0a1812] border border-white/5 rotate-[4deg] translate-x-6 translate-y-4 shadow-2xl opacity-75">
                            <div class="absolute bottom-0 left-0 right-0 p-5">
                                <div class="h-2 w-20 bg-white/15 rounded mb-2" />
                                <div class="h-2 w-12 bg-[#c9a55a]/30 rounded" />
                            </div>
                        </div>
                        <div class="relative w-56 h-80 rounded-xl bg-gradient-to-br from-[#1f0d0d] via-[#3d1a1a] to-[#150a0a] border border-white/10 shadow-2xl -rotate-[2deg]">
                            <div class="absolute top-5 right-5">
                                <span class="font-code text-[10px] text-[#c9a55a] tracking-widest">SAT 14 DEC</span>
                            </div>
                            <div class="absolute top-1/3 left-0 right-0 px-5">
                                <div class="h-px w-full bg-[#c9a55a]/20 mb-4" />
                                <div class="h-3 w-24 bg-white/10 rounded mb-2" />
                                <div class="h-2 w-16 bg-white/5 rounded" />
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/60 to-transparent rounded-b-xl">
                                <p class="font-display text-sm font-medium text-white/90 leading-tight">The Grand<br><em>Gala Night</em></p>
                                <p class="font-code text-[10px] text-[#c9a55a] mt-1 tracking-wider">From $45</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-sf-bg to-transparent" />
        </section>

        <!-- Feature strip -->
        <section class="border-t border-sf-border-subtle bg-sf-surface transition-colors duration-200">
            <div class="mx-auto max-w-7xl px-5 sm:px-8 py-16 grid sm:grid-cols-3 gap-10">
                <div class="space-y-3">
                    <div class="w-8 h-px bg-sf-gold" />
                    <h3 class="font-display text-xl font-semibold text-sf-text">Instant Reservations</h3>
                    <p class="font-body font-light text-sm text-sf-muted leading-relaxed">Browse, select, and secure your spot in under a minute. No back-and-forth.</p>
                </div>
                <div class="space-y-3">
                    <div class="w-8 h-px bg-sf-gold" />
                    <h3 class="font-display text-xl font-semibold text-sf-text">Secure Checkout</h3>
                    <p class="font-body font-light text-sm text-sf-muted leading-relaxed">Stripe-powered payments. Your card details never touch our servers.</p>
                </div>
                <div class="space-y-3">
                    <div class="w-8 h-px bg-sf-gold" />
                    <h3 class="font-display text-xl font-semibold text-sf-text">Digital Tickets</h3>
                    <p class="font-body font-light text-sm text-sf-muted leading-relaxed">QR-coded tickets delivered instantly. Present from your phone at the door.</p>
                </div>
            </div>
        </section>

    </HomeLayout>
</template>
