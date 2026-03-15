import { useIntersectionObserver } from "@vueuse/core";
import { ref } from "vue";

type SectionId = "details" | "location" | "tickets";

export function useEventSections() {
    const activeSection = ref<SectionId>("details");

    const scrollContainerRef = ref<HTMLElement | null>(null);
    const detailsRef = ref<HTMLElement | null>(null);
    const locationRef = ref<HTMLElement | null>(null);
    const ticketsRef = ref<HTMLElement | null>(null);

    const sections: { id: SectionId; label: string }[] = [
        { id: "details", label: "Details" },
        { id: "location", label: "Location" },
        { id: "tickets", label: "Tickets" },
    ];

    const observerOptions = {
        root: scrollContainerRef,
        rootMargin: "-20% 0px -70% 0px",
        threshold: 0,
    };

    useIntersectionObserver(
        detailsRef,
        ([entry]) => {
            if (entry.isIntersecting) {
                activeSection.value = "details";
            }
        },
        observerOptions,
    );

    useIntersectionObserver(
        locationRef,
        ([entry]) => {
            if (entry.isIntersecting) {
                activeSection.value = "location";
            }
        },
        observerOptions,
    );

    useIntersectionObserver(
        ticketsRef,
        ([entry]) => {
            if (entry.isIntersecting) {
                activeSection.value = "tickets";
            }
        },
        observerOptions,
    );

    const scrollToSection = (id: SectionId) => {
        const refMap = { details: detailsRef, location: locationRef, tickets: ticketsRef };
        refMap[id].value?.scrollIntoView({ behavior: "smooth", block: "start" });
    };

    return {
        activeSection,
        sections,
        scrollContainerRef,
        detailsRef,
        locationRef,
        ticketsRef,
        scrollToSection,
    };
}
