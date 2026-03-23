import type { App } from "@/wayfinder/types";
import type { Role } from "@/types/role";

export type User = App.Models.User;

export type UserFilterProps = {
    search: string;
};

export type UserShowProps = {
    user: User & {
        email_verified: boolean;
        role: Role;
    };
};
