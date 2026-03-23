<script lang="ts" setup>
import { Form, Head, Link } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { login } from "@/wayfinder/routes/auth";
import { organization, store } from "@/wayfinder/routes/auth/register";
</script>

<template>
    <AuthLayout
        title="Create an account"
        :description="`Join ${$page.props.name} to start booking events`"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-1.5">
                <label for="name" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Full name
                </label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    placeholder="Your full name"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-1.5">
                <label for="email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <label for="password" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <label for="password_confirmation" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Confirm password
                </label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <button
                type="submit"
                :tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
                class="mt-2 w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Creating account…' : 'Create account' }}
            </button>

            <p class="text-center font-body text-sm text-sf-muted">
                Want to host events?
                <Link :href="organization()" :tabindex="6" class="text-sf-gold hover:text-sf-text transition-colors ml-1">
                    Register as an organizer
                </Link>
            </p>

            <p class="text-center font-body text-sm text-sf-muted">
                Already have an account?
                <Link :href="login()" :tabindex="7" class="text-sf-gold hover:text-sf-text transition-colors ml-1">
                    Sign in
                </Link>
            </p>
        </Form>
    </AuthLayout>
</template>
