<script setup lang="ts">
import { useEventListener } from "@vueuse/core";
import { computed, ref, watch } from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from "@/components/ui/dialog";
import type { MediaResource } from "@/types/event";

const props = defineProps<{
    images: MediaResource[];
    initialIndex?: number;
}>();

const open = defineModel<boolean>("open", { required: true });

const currentIndex = ref(props.initialIndex ?? 0);
const direction = ref<"next" | "prev">("next");

watch(
    () => props.initialIndex,
    (index) => {
        if (index !== undefined) {
            currentIndex.value = index;
        }
    },
);

const currentImage = computed(() => props.images[currentIndex.value]);

function prev(): void {
    if (currentIndex.value > 0) {
        direction.value = "prev";
        currentIndex.value--;
    }
}

function next(): void {
    if (currentIndex.value < props.images.length - 1) {
        direction.value = "next";
        currentIndex.value++;
    }
}

useEventListener("keydown", (event: KeyboardEvent) => {
    if (!open.value) {
        return;
    }
    if (event.key === "ArrowLeft") {
        prev();
    } else if (event.key === "ArrowRight") {
        next();
    }
});
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            :show-close-button="false"
            class="w-[90vh] sm:max-w-none p-0 overflow-hidden"
        >
            <DialogTitle class="sr-only">
                {{ currentImage?.filename }}
            </DialogTitle>
            <DialogDescription class="sr-only">
                Image {{ currentIndex + 1 }} of {{ images.length }}
            </DialogDescription>

            <div class="relative h-[90vh] overflow-hidden">
                <Transition
                    :name="direction === 'next' ? 'slide-left' : 'slide-right'"
                >
                    <img
                        v-if="currentImage"
                        :key="currentImage.uuid"
                        :src="currentImage.url"
                        :alt="currentImage.filename"
                        class="absolute inset-0 h-full w-full object-contain bg-background"
                    />
                </Transition>

                <button
                    v-if="images.length > 1"
                    type="button"
                    class="absolute left-3 top-1/2 -translate-y-1/2 h-10 w-10 flex items-center justify-center rounded-full bg-secondary border border-input text-muted-foreground hover:text-foreground hover:bg-secondary/80 transition-all disabled:opacity-30 disabled:pointer-events-none z-10"
                    :disabled="currentIndex === 0"
                    @click="prev"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 19.5L8.25 12l7.5-7.5"
                        />
                    </svg>
                    <span class="sr-only">Previous image</span>
                </button>

                <button
                    v-if="images.length > 1"
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 h-10 w-10 flex items-center justify-center rounded-full bg-secondary border border-input text-muted-foreground hover:text-foreground hover:bg-secondary/80 transition-all disabled:opacity-30 disabled:pointer-events-none z-10"
                    :disabled="currentIndex === images.length - 1"
                    @click="next"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 4.5l7.5 7.5-7.5 7.5"
                        />
                    </svg>
                    <span class="sr-only">Next image</span>
                </button>

                <button
                    type="button"
                    class="absolute top-3 right-3 h-8 w-8 flex items-center justify-center rounded-full bg-secondary border border-input text-muted-foreground hover:text-foreground hover:bg-secondary/80 transition-all z-10"
                    @click="open = false"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                    <span class="sr-only">Close</span>
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.slide-left-enter-active,
.slide-left-leave-active,
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.3s ease;
}

.slide-left-enter-from {
    transform: translateX(100%);
}
.slide-left-leave-to {
    transform: translateX(-100%);
}

.slide-right-enter-from {
    transform: translateX(-100%);
}
.slide-right-leave-to {
    transform: translateX(100%);
}
</style>
