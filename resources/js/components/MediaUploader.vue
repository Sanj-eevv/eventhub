<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { onUnmounted, ref } from "vue";
import type { MediaItem } from "@/types/event";

const props = defineProps<{
    items: MediaItem[];
    uploadUrl: string;
    deleteUrl: (mediaUuid: string) => string;
    coverUrl: (mediaUuid: string) => string;
    maxFiles?: number;
    partialReloadKey?: string;
}>();

const maxFiles = props.maxFiles ?? 10;
const partialKey = props.partialReloadKey ?? "event";

const isDragging = ref(false);
const uploadingCount = ref(0);
const uploadErrors = ref<string[]>([]);

const objectUrls = ref<string[]>([]);

onUnmounted(() => {
    objectUrls.value.forEach((url) => URL.revokeObjectURL(url));
});

const remainingSlots = () => maxFiles - props.items.length;

function handleDragOver(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = true;
}

function handleDragLeave(): void {
    isDragging.value = false;
}

function handleDrop(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = false;

    const files = Array.from(event.dataTransfer?.files ?? []).filter((file) =>
        ["image/jpeg", "image/png", "image/webp"].includes(file.type),
    );

    uploadFiles(files);
}

function handleFileInput(event: Event): void {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    uploadFiles(files);
    input.value = "";
}

function uploadFiles(files: File[]): void {
    uploadErrors.value = [];

    const slots = remainingSlots();
    const toUpload = files.slice(0, slots);

    if (files.length > slots) {
        uploadErrors.value.push(
            `Only ${slots} more image${slots === 1 ? "" : "s"} can be uploaded (max ${maxFiles}).`,
        );
    }

    toUpload.forEach((file) => uploadSingle(file));
}

function uploadSingle(file: File): void {
    uploadingCount.value++;

    router.post(
        props.uploadUrl,
        { file },
        {
            forceFormData: true,
            preserveScroll: true,
            only: [partialKey],
            onError(errors) {
                uploadErrors.value.push(errors.file ?? "Upload failed.");
            },
            onFinish() {
                uploadingCount.value--;
            },
        },
    );
}

function deleteMedia(mediaUuid: string): void {
    router.delete(props.deleteUrl(mediaUuid), {
        preserveScroll: true,
        only: [partialKey],
    });
}

function setCover(mediaUuid: string): void {
    router.post(
        props.coverUrl(mediaUuid),
        {},
        {
            preserveScroll: true,
            only: [partialKey],
        },
    );
}
</script>

<template>
    <div class="space-y-4">
        <!-- Drop zone -->
        <div
            :class="[
                'relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-10 text-center transition-all duration-200 cursor-pointer',
                isDragging
                    ? 'border-primary bg-accent/30'
                    : remainingSlots() > 0
                      ? 'border-border hover:border-muted-foreground/50 hover:bg-accent/10'
                      : 'border-border opacity-50 cursor-not-allowed pointer-events-none',
            ]"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
            @click="remainingSlots() > 0 && ($refs.fileInput as HTMLInputElement).click()"
        >
            <input
                ref="fileInput"
                type="file"
                class="sr-only"
                accept="image/jpeg,image/png,image/webp"
                multiple
                @change="handleFileInput"
            />

            <div
                class="mb-3 flex h-10 w-10 items-center justify-center rounded-full border border-border bg-background"
            >
                <svg
                    class="h-5 w-5 text-muted-foreground"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                    />
                </svg>
            </div>

            <p class="text-sm font-medium text-foreground">
                <span class="text-primary">Click to upload</span> or drag &amp;
                drop
            </p>
            <p class="mt-1 text-xs text-muted-foreground">
                JPEG, PNG, or WebP — max 10 MB per file
            </p>
            <p class="mt-2 text-xs text-muted-foreground">
                {{ items.length }} / {{ maxFiles }} images
            </p>
        </div>

        <!-- Upload errors -->
        <div v-if="uploadErrors.length > 0" class="space-y-1">
            <p
                v-for="(error, i) in uploadErrors"
                :key="i"
                class="text-xs text-destructive"
            >
                {{ error }}
            </p>
        </div>

        <!-- Uploading indicator -->
        <div
            v-if="uploadingCount > 0"
            class="flex items-center gap-2 text-xs text-muted-foreground"
        >
            <svg
                class="h-4 w-4 animate-spin"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 3v1m0 16v1m8.66-13H20m-16 0H2.34M17.66 17.66l-.71-.71M6.34 6.34l-.71-.71M17.66 6.34l-.71-.71M6.34 17.66l-.71-.71"
                />
            </svg>
            Uploading {{ uploadingCount }} file{{ uploadingCount > 1 ? "s" : ""
            }}…
        </div>

        <!-- Image grid -->
        <div v-if="items.length > 0" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
            <div
                v-for="item in items"
                :key="item.uuid"
                class="group relative aspect-square overflow-hidden rounded-lg border border-border bg-muted"
            >
                <!-- Image or processing shimmer -->
                <template v-if="item.processing">
                    <div class="absolute inset-0 animate-pulse bg-muted" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5">
                        <svg
                            class="h-5 w-5 animate-spin text-muted-foreground"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>
                        <span class="text-[10px] text-muted-foreground">Processing…</span>
                    </div>
                </template>
                <template v-else-if="item.processing_failed">
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 bg-destructive/10">
                        <svg
                            class="h-5 w-5 text-destructive"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                            />
                        </svg>
                        <span class="text-[10px] text-destructive">Failed</span>
                    </div>
                </template>
                <img
                    v-else
                    :src="item.url"
                    :alt="item.filename"
                    class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"
                />

                <!-- Cover badge -->
                <div
                    v-if="item.is_cover"
                    class="absolute left-2 top-2 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-yellow-400 backdrop-blur-sm"
                >
                    Cover
                </div>

                <!-- Hover actions -->
                <div
                    class="absolute inset-0 flex items-end justify-between gap-1 bg-black/50 p-2 opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                >
                    <button
                        v-if="!item.is_cover && !item.processing && !item.processing_failed"
                        type="button"
                        class="rounded bg-white/90 px-2 py-1 text-[10px] font-medium text-gray-900 hover:bg-white transition-colors"
                        @click.stop="setCover(item.uuid)"
                    >
                        Set cover
                    </button>
                    <span v-else class="flex-1" />

                    <button
                        type="button"
                        class="flex h-6 w-6 items-center justify-center rounded bg-red-500/90 text-white hover:bg-red-500 transition-colors"
                        @click.stop="deleteMedia(item.uuid)"
                    >
                        <svg
                            class="h-3 w-3"
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
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
