<script lang="ts" setup>
import { Form, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import Step1 from "@/components/Auth/Organization/Register/Step1.vue";
import Step2 from "@/components/Auth/Organization/Register/Step2.vue";
import StepCount from "@/components/Auth/Organization/Register/StepCount.vue";
import TextLink from "@/components/TextLink.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { login, register } from "@/wayfinder/routes/auth";
import { store } from "@/wayfinder/routes/auth/register/organization";

const totalSteps = 2;
const activeStep = ref(1);

const stepDescriptions: Record<number, string> = {
    1: "Tell us about your organization",
    2: "Create your admin account",
};
</script>
<template>
    <AuthLayout
        title="Register your organization"
        :description="stepDescriptions[activeStep]"
    >
        <Head title="Register Organization" />
        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            class="flex flex-col gap-6"
        >
            <StepCount :totalSteps="totalSteps" :activeStep="activeStep" />
            <Step1
                v-show="activeStep === 1"
                @next="activeStep = activeStep + 1"
            />
            <Step2
                v-show="activeStep === 2"
                @back="activeStep = activeStep - 1"
            />
            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="7"
                >
                    Log in
                </TextLink>
            </div>
            <div class="text-center text-sm text-muted-foreground">
                Just want a personal account?
                <TextLink
                    :href="register()"
                    class="underline underline-offset-4"
                    :tabindex="8"
                >
                    Register here
                </TextLink>
            </div>
        </Form>
    </AuthLayout>
</template>
