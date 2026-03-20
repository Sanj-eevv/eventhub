import type { PaginatedResponse, PaginatedResponseMeta } from "@/types";
import type { App } from "@/wayfinder/types";

export type Role = App.Models.Role;
export type RolePicker = { slug: string; name: string };
export type Permission = App.Models.Permission;

export type RoleFilterProps = {
    search: string;
};

export type RolePageProps = {
    roles: PaginatedResponse<Role> & {
        meta: PaginatedResponseMeta & { sort: unknown[] };
        filters: RoleFilterProps;
    };
};

export type GroupedPermissions = Record<string, Permission[]> | null;
