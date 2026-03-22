<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import type { PublicEvent } from "@/types/event";
import {
    index as eventsIndex,
    show as eventShow,
} from "@/wayfinder/routes/events";

defineProps<{ events: PublicEvent[] }>();

const appName = usePage().props.name as string;
</script>

<template>
    <section
        class="relative h-[calc(100vh-4rem)] overflow-hidden border-b border-sf-border-subtle"
    >
        <div class="relative mx-auto max-w-7xl px-5 sm:px-8 h-full flex">
            <div
                class="flex-1 flex flex-col gap-6 p-10 pl-0 min-w-0 justify-center"
            >
                <div class="flex items-center gap-4">
                    <span class="anim-line h-px w-8 bg-sf-gold origin-left" />
                    <span
                        class="anim-eyebrow font-code text-[10px] tracking-[0.4em] uppercase text-sf-gold"
                        >Live Events Platform</span
                    >
                </div>
                <div>
                    <h1
                        class="font-display font-black uppercase leading-[0.86] tracking-tight mb-8 flex flex-col gap-2"
                    >
                        <div class="overflow-hidden">
                            <span
                                class="anim-word-1 block text-[clamp(3.2rem,6.5vw,8rem)] text-sf-text"
                                >Every</span
                            >
                        </div>
                        <div class="overflow-hidden">
                            <span
                                class="anim-word-2 block text-[clamp(3.2rem,6.5vw,8rem)] font-light text-sf-muted"
                                >Stage,</span
                            >
                        </div>
                        <div class="overflow-hidden">
                            <span
                                class="anim-word-3 block text-[clamp(3.2rem,6.5vw,8rem)] text-sf-gold"
                                >Front Row.</span
                            >
                        </div>
                    </h1>

                    <p
                        class="anim-desc font-body font-light text-sf-muted text-base leading-relaxed max-w-sm mb-10"
                    >
                        From intimate jazz evenings to stadium sell-outs — every
                        ticket, one platform. Reserve in seconds.
                    </p>

                    <div class="anim-cta flex flex-wrap items-center gap-5">
                        <Link
                            :href="eventsIndex()"
                            class="group inline-flex items-center gap-3 px-7 py-4 bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.98] transition-all duration-200"
                        >
                            Browse Events
                            <svg
                                class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
            <div
                class="hidden lg:flex flex-col w-[36%] max-w-105 shrink-0 border-x border-sf-border-subtle overflow-hidden"
            >
                <div class="relative flex-1 overflow-hidden">
                    <div
                        class="absolute top-0 inset-x-0 h-10 bg-linear-to-b from-sf-bg to-transparent z-10 pointer-events-none"
                    />
                    <div
                        v-if="events.length === 0"
                        class="flex flex-col items-center justify-center h-full px-8 text-center"
                    >
                        <p
                            class="font-display text-sm font-semibold text-sf-tertiary uppercase tracking-widest mb-2"
                        >
                            Coming Soon
                        </p>
                        <p class="font-body text-xs text-sf-tertiary/60">
                            Events will appear here once published.
                        </p>
                    </div>
                    <ul v-else class="sf-ticker-track px-8 xl:px-10">
                        <template v-for="pass in 2" :key="pass">
                            <li
                                v-for="(event, i) in events"
                                :key="`${pass}-${i}`"
                                class="border-b border-sf-border-subtle last:border-0"
                            >
                                <Link
                                    :href="eventShow({ event: event.slug })"
                                    class="group flex flex-col py-4 gap-2"
                                >
                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <p
                                            class="font-display font-semibold text-xs text-sf-muted tracking-[0.06em] uppercase leading-snug group-hover:text-sf-text transition-colors duration-200 flex-1 min-w-0"
                                        >
                                            {{ event.title }}
                                        </p>
                                        <svg
                                            class="h-3 w-3 text-sf-border group-hover:text-sf-gold shrink-0 mt-0.5 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-[10px] font-code tracking-wider text-sf-muted uppercase"
                                    >
                                        <span>{{ event.starts_at }}</span>
                                        <template
                                            v-if="event.location?.venue_name"
                                        >
                                            <span
                                                class="h-0.75 w-0.75 rounded-full bg-sf-border shrink-0"
                                            />
                                            <span class="truncate">{{
                                                event.location.venue_name
                                            }}</span>
                                        </template>
                                    </div>
                                    <div
                                        class="h-px w-full bg-sf-border-subtle overflow-hidden"
                                    >
                                        <div
                                            class="h-full bg-sf-gold/25 group-hover:bg-sf-gold/60 transition-colors duration-500"
                                            :style="{
                                                width: `${38 + ((i * 23) % 48)}%`,
                                            }"
                                        />
                                    </div>
                                </Link>
                            </li>
                        </template>
                    </ul>

                    <div
                        class="absolute bottom-0 inset-x-0 h-16 bg-linear-to-t from-sf-bg to-transparent z-10 pointer-events-none"
                    />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes sfFadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes sfCurtain {
    from { transform: translateY(110%); }
    to   { transform: translateY(0); }
}

@keyframes sfLineGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}

.anim-line   { animation: sfLineGrow 0.7s cubic-bezier(0.16, 1, 0.3, 1) both 0s; }
.anim-eyebrow { animation: sfFadeUp  0.5s ease both 0.2s; }
.anim-word-1 { animation: sfCurtain 0.9s cubic-bezier(0.16, 1, 0.3, 1) both 0.05s; }
.anim-word-2 { animation: sfCurtain 0.9s cubic-bezier(0.16, 1, 0.3, 1) both 0.18s; }
.anim-word-3 { animation: sfCurtain 0.9s cubic-bezier(0.16, 1, 0.3, 1) both 0.31s; }
.anim-desc   { animation: sfFadeUp  0.6s ease both 0.55s; }
.anim-cta    { animation: sfFadeUp  0.6s ease both 0.7s; }
</style>

<style scoped>
.sf-ticker-track {
    animation: sfTickerUp 30s linear infinite;
}

.sf-ticker-track:hover {
    animation-play-state: paused;
}

@keyframes sfTickerUp {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-50%);
    }
}
</style>
