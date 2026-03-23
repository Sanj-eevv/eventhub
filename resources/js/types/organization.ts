import type { StatusLabel } from "@/types";
import type { App } from "@/wayfinder/types";

export type OrganizationStatus = App.Enums.OrganizationStatus;
export type OrganizationStatusData = StatusLabel<OrganizationStatus>;

export type Organization = Omit<App.Models.Organization, "status"> & {
    status: OrganizationStatusData;
};

export type OrganizationResource = Pick<
    App.Models.Organization,
    "uuid" | "title" | "description" | "contact_address" | "contact_email" | "created_at"
> & { status: OrganizationStatusData };

export type OrganizationPicker = Pick<App.Models.Organization, "uuid" | "title">;

export type OrganizationFilterProps = {
    search: string;
    status: OrganizationStatus | null;
};
