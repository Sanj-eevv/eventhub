import type { PaginatedResponse } from "@/types";
import type { OrganizationPicker } from "@/types/organization";
import type { RolePicker } from "@/types/role";
import type { App } from "@/wayfinder/types";

export type User = App.Models.User;

export type UserFilterProps = {
    search: string;
};

export type UserPageProps = {
    users: PaginatedResponse<User> & {
        filters: UserFilterProps;
    };
    roles: RolePicker[];
    organizations: OrganizationPicker[];
};
