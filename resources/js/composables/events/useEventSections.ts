import { useEventListener } from "@vueuse/core";
import { ref } from "vue";

type SectionId = "details" | "location" | "tickets";

const sections: { id: SectionId; label: string }[] = [
    { id: "details", label: "Details" },
    { id: "location", label: "Location" },
    { id: "tickets", label: "Tickets" },
];

export function useEventSections() {
    const activeSection = ref<SectionId>("details");
    const scrollContainerRef = ref<HTMLElement | null>(null);

    const sectionRefs = Object.fromEntries(
        sections.map((s) => [s.id, ref<HTMLElement | null>(null)]),
    ) as Record<SectionId, any>;

    const updateActiveSection = () => {
        const container = scrollContainerRef.value;
        if (!container) return;

        const containerRect = container.getBoundingClientRect();
        const threshold = containerRect.top + containerRect.height * 0.6;

        for (const { id } of [...sections].reverse()) {
            const el = sectionRefs[id].value;
            if (el && el.getBoundingClientRect().top <= threshold) {
                activeSection.value = id;
                return;
            }
        }
    };

    useEventListener(scrollContainerRef, "scroll", updateActiveSection, {
        passive: true,
    });

    const scrollToSection = (id: SectionId) => {
        sectionRefs[id].value?.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });
    };

    return {
        activeSection,
        sections,
        scrollContainerRef,
        sectionRefs,
        scrollToSection,
    };
}
