import type { OrganizationResource } from "@/types/organization";
import type { Permission } from "@/types/role";
import type { App } from "@/wayfinder/types";

export type User = App.Models.User;

export type UserFilterProps = {
    search: string;
};

export type UserResource = Pick<
    App.Models.User,
    "uuid" | "name" | "email" | "created_at"
> & {
    email_verified: boolean;
    role: Pick<
        App.Models.Role,
        "slug" | "name" | "description" | "preserved"
    > & {
        permissions: Permission[];
    };
    organization?: OrganizationResource;
};

