<script setup lang="ts">
import { store as sendVerificationEmail } from '@/actions/App/Http/Controllers/Auth/EmailVerificationNotificationController';
import { update as updateProfile } from '@/actions/App/Http/Controllers/Settings/ProfileController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;
const form = useForm({
    name: user.name,
    email: user.email,
    photo: null as File | null,
});

const avatarPreview = computed(() => {
    if (form.photo) {
        return URL.createObjectURL(form.photo);
    }

    return user.avatar ?? null;
});

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.photo = target.files?.[0] ?? null;
};

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'patch',
        }))
        .post(updateProfile().url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.defaults({
                name: form.name,
                email: form.email,
                photo: null,
            });
            form.photo = null;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-4 rounded-3xl border border-white/10 bg-black/20 p-5 md:grid-cols-[auto_minmax(0,1fr)] md:items-center">
                        <div class="flex justify-center md:justify-start">
                            <Avatar class="size-24 overflow-hidden rounded-3xl border border-white/10">
                                <AvatarImage v-if="avatarPreview" :src="avatarPreview" :alt="user.name" />
                                <AvatarFallback class="bg-white/10 text-lg font-semibold text-white">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                        </div>

                        <div class="grid gap-2">
                            <Label for="photo">Profile image</Label>
                            <Input
                                id="photo"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="mt-1 block w-full"
                                @change="handlePhotoChange"
                            />
                            <p class="text-sm text-muted-foreground">Upload via `storage:link`. Accepted formats: JPG, PNG and WEBP up to 2 MB.</p>
                            <InputError class="mt-1" :message="form.errors.photo" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="sendVerificationEmail.url()"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
