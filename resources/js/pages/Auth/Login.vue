<script lang="ts" setup>
import { Form, Head, Link } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { register } from "@/wayfinder/routes/auth";
import { store } from "@/wayfinder/routes/auth/login";
import { request } from "@/wayfinder/routes/auth/password";

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Welcome back"
        description="Sign in to your account to continue"
    >
        <Head title="Log in" />

        <div v-if="status" class="mb-6 rounded-lg bg-sf-gold/10 border border-sf-gold/30 px-4 py-3 font-body text-sm text-sf-gold text-center">
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-1.5">
                <label for="email" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="font-body text-xs font-medium text-sf-muted uppercase tracking-wider">
                        Password
                    </label>
                    <Link :href="request()" :tabindex="5" class="font-body text-xs text-sf-gold hover:text-sf-text transition-colors">
                        Forgot password?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full bg-sf-bg border border-sf-border rounded-lg px-4 py-2.5 font-body text-sm text-sf-text placeholder:text-sf-tertiary focus:outline-none focus:border-sf-gold transition-colors duration-200"
                />
                <InputError :message="errors.password" />
            </div>

            <label for="remember" class="flex items-center gap-3 cursor-pointer group">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    :tabindex="3"
                    class="h-4 w-4 rounded border-sf-border bg-sf-bg accent-sf-gold cursor-pointer"
                />
                <span class="font-body text-sm text-sf-muted group-hover:text-sf-text transition-colors">Remember me</span>
            </label>

            <button
                type="submit"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
                class="mt-2 w-full py-3 rounded-lg bg-sf-ember text-white font-body text-sm tracking-wide hover:bg-sf-ember-hover active:scale-[0.99] transition-all duration-200 disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2"
            >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                {{ processing ? 'Signing in…' : 'Sign in' }}
            </button>

            <p class="text-center font-body text-sm text-sf-muted">
                Don't have an account?
                <Link :href="register()" :tabindex="5" class="text-sf-gold hover:text-sf-text transition-colors ml-1">
                    Sign up
                </Link>
            </p>
        </Form>
    </AuthLayout>
</template>
