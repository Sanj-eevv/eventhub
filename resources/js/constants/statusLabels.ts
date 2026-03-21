import EventStatus from "@/wayfinder/App/Enums/EventStatus";
import OrganizationStatus from "@/wayfinder/App/Enums/OrganizationStatus";
import type { EventStatus as EventStatusType } from "@/types/event";
import type { OrganizationStatus as OrganizationStatusType } from "@/types/organization";

export const eventStatusLabels: Record<EventStatusType, string> = {
    [EventStatus.Draft]: "Draft",
    [EventStatus.Published]: "Published",
    [EventStatus.Cancelled]: "Cancelled",
};

export const organizationStatusLabels: Record<OrganizationStatusType, string> = {
    [OrganizationStatus.Pending]: "Pending",
    [OrganizationStatus.Approved]: "Approved",
    [OrganizationStatus.Rejected]: "Rejected",
    [OrganizationStatus.Suspended]: "Suspended",
};
