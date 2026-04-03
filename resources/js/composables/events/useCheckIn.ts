import { BrowserMultiFormatReader } from "@zxing/browser";
import { nextTick, onUnmounted, ref, shallowRef } from "vue";
import type { Ref } from "vue";
import { scan } from "@/wayfinder/routes/dashboard/events/check-in";

export type CheckInResult = {
    success: boolean;
    message: string;
    attendee_name?: string;
    ticket_type?: string;
};

export function useCheckIn(eventUuid: string) {
    const bookingReference = shallowRef("");
    const isLoading = shallowRef(false);
    const scanResult = ref<CheckInResult | null>(null);
    const isCameraActive = shallowRef(false);
    const cameraError = shallowRef<string | null>(null);
    let scannerControls: { stop: () => void } | null = null;

    async function submitScan(): Promise<void> {
        if (!bookingReference.value.trim() || isLoading.value) {
            return;
        }

        isLoading.value = true;
        scanResult.value = null;

        try {
            const response = await fetch(scan({ event: eventUuid }).url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-XSRF-TOKEN": decodeURIComponent(
                        document.cookie
                            .split("; ")
                            .find((row) => row.startsWith("XSRF-TOKEN="))
                            ?.split("=")[1] ?? "",
                    ),
                },
                body: JSON.stringify({
                    booking_reference: bookingReference.value,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                scanResult.value = {
                    success: true,
                    message: "Check-in successful",
                    attendee_name: data.attendee_name,
                    ticket_type: data.ticket_type?.name,
                };
                bookingReference.value = "";
            } else {
                scanResult.value = {
                    success: false,
                    message: data.error ?? "An error occurred.",
                };
            }
        } catch {
            scanResult.value = {
                success: false,
                message: "Network error. Please try again.",
            };
        } finally {
            isLoading.value = false;
        }
    }

    async function startCamera(
        videoRef: Ref<HTMLVideoElement | null>,
    ): Promise<void> {
        isCameraActive.value = true;
        cameraError.value = null;
        await nextTick();

        if (!videoRef.value) {
            return;
        }

        try {
            const codeReader = new BrowserMultiFormatReader();

            scannerControls = await codeReader.decodeFromVideoDevice(
                undefined,
                videoRef.value,
                (result) => {
                    if (!result) {
                        return;
                    }
                    stopCamera();
                    bookingReference.value = result.getText();
                    submitScan();
                },
            );
        } catch (error) {
            isCameraActive.value = false;
            cameraError.value =
                error instanceof DOMException &&
                error.name === "NotAllowedError"
                    ? "Camera access was denied. Please allow camera permission in your browser settings and try again."
                    : "Camera could not be started. Please check your device and try again.";
        }
    }

    function stopCamera(): void {
        scannerControls?.stop();
        scannerControls = null;
        isCameraActive.value = false;
    }

    onUnmounted(() => {
        stopCamera();
    });

    return {
        bookingReference,
        isLoading,
        scanResult,
        isCameraActive,
        cameraError,
        submitScan,
        startCamera,
        stopCamera,
    };
}
