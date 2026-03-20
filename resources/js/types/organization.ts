import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type OrganizationStatus = App.Enums.OrganizationStatus;
export type OrganizationStatusData = { value: OrganizationStatus; label: string };
export type Organization = Omit<App.Models.Organization, "status"> & { status: OrganizationStatusData };
export type OrganizationPicker = { uuid: string; title: string };
export type OrganizationFilterProps = {
    search: string;
    status: OrganizationStatus | null;
};

export type OrganizationPageProps = {
    organizations: PaginatedResponse<Organization> & {
        filters: OrganizationFilterProps;
    };
};
