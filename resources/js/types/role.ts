import type { App } from "@/wayfinder/types";

export type Role = App.Models.Role;
export type RolePicker = Pick<App.Models.Role, "slug" | "name">;
export type Permission = App.Models.Permission;
export type GroupedPermissions = Record<string, Permission[]> | null;

export type RoleFilterProps = {
    search: string;
};
