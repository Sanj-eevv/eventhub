<script setup lang="ts">
import { computed, ref } from "vue";
import ImageLightbox from "@/components/ImageLightbox.vue";
import type { MediaResource } from "@/types/event";

const props = defineProps<{ media?: MediaResource[] }>();

const images = computed(
    () => props.media?.filter((mediaItem) => !mediaItem.is_cover) ?? [],
);

const isOpen = ref(false);
const activeIndex = ref(0);

function openLightbox(index: number): void {
    activeIndex.value = index;
    isOpen.value = true;
}
</script>

<template>
    <div
        v-if="images.length > 0"
        class="border-t border-sf-border-subtle pt-10"
    >
        <div class="flex items-center gap-3 mb-5">
            <span class="h-px w-6 bg-sf-gold" />
            <h2 class="font-display text-2xl font-medium text-sf-text">
                Gallery
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            <button
                v-for="(image, index) in images"
                :key="image.uuid"
                type="button"
                class="aspect-square overflow-hidden rounded-lg border border-sf-border-subtle bg-sf-surface cursor-zoom-in group"
                @click="openLightbox(index)"
            >
                <img
                    :src="image.url"
                    :alt="image.filename"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
            </button>
        </div>
    </div>

    <ImageLightbox
        v-model:open="isOpen"
        :images="images"
        :initial-index="activeIndex"
    />
</template>
