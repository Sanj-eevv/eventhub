import type { PaginatedResponse } from "@/types";
import type { Organization } from "@/types/organization";
import type { Role } from "@/types/role";
import type { App } from "@/wayfinder/types";

export type User = App.Models.User;

export type UserFilterProps = {
    search: string;
};

export type UserPageProps = {
    users: PaginatedResponse<User> & {
        filters: UserFilterProps;
    };
    roles: Role[];
    organizations: Organization[];
};
