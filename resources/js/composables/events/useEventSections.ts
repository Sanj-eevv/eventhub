import { useEventListener } from "@vueuse/core";
import { type Ref, computed, ref, shallowRef } from "vue";

type SectionId = "details" | "location" | "tickets" | "media";

const allSections: { id: SectionId; label: string; editOnly?: boolean }[] = [
    { id: "details", label: "Details" },
    { id: "location", label: "Location" },
    { id: "tickets", label: "Tickets" },
    { id: "media", label: "Media", editOnly: true },
];

export function useEventSections(isEditing = false) {
    const activeSection = shallowRef<SectionId>("details");
    const scrollContainerRef = ref<HTMLElement | null>(null);

    const sections = computed(() =>
        allSections.filter((section) => !section.editOnly || isEditing),
    );

    const sectionRefs = Object.fromEntries(
        allSections.map((s) => [s.id, ref<HTMLElement | null>(null)]),
    ) as Record<SectionId, Ref<HTMLElement | null>>;

    const updateActiveSection = () => {
        const container = scrollContainerRef.value;
        if (!container) return;

        const containerRect = container.getBoundingClientRect();
        const threshold = containerRect.top + containerRect.height * 0.6;

        for (const { id } of [...sections.value].reverse()) {
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
