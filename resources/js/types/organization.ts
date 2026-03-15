import type { VariantProps } from "class-variance-authority";
import type { badgeVariants } from "@/components/ui/badge";
import type { PaginatedResponse } from "@/types";
import type { App } from "@/wayfinder/types";

export type Organization = App.Models.Organization;
export type OrganizationStatus = App.Enums.OrganizationStatus;

type BadgeVariants = VariantProps<typeof badgeVariants>;
export type OrganizationStatusConfig = Record<
    OrganizationStatus,
    { variant: BadgeVariants["variant"]; class: string; label: string }
>;
export type OrganizationFilterProps = {
    search: string;
    status: OrganizationStatus | null;
};

export type OrganizationPageProps = {
    organizations: PaginatedResponse<Organization> & {
        filters: OrganizationFilterProps;
    };
};
