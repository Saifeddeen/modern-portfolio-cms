<script setup lang="ts">
import { Button, InputText, Textarea, Tabs, TabList, Tab, TabPanels, TabPanel } from 'primevue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        username: string | null;
        title: string | null;
        bio: string | null;
    }
}>();

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    username: props.user.username,
    title: props.user.title,
    bio: props.user.bio,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// 1. Profile Update with Confirmation
const updateProfile = () => {
    confirm.require({
        message: 'Are you sure you want to save these profile changes?',
        header: 'Save Confirmation',
        icon: 'pi pi-save',
        acceptLabel: 'Yes, Save',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-success',
        accept: () => {
            profileForm.patch(route('profile.update'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Profile updated successfully!',
                        life: 3000
                    });
                }
            });
        }
    });
};

// 2. Password Update with Confirmation
const updatePassword = () => {
    confirm.require({
        message: 'Are you sure you want to update your password?',
        header: 'Password Confirmation',
        icon: 'pi pi-lock',
        acceptLabel: 'Yes, Update',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-success',
        accept: () => {
            passwordForm.put(route('password.update'), {
                preserveScroll: true,
                onSuccess: () => {
                    passwordForm.reset();
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Password updated successfully!',
                        life: 3000
                    });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Profile" />

    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-8 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <h1 class="text-xl font-semibold text-graphite-800 dark:text-white mb-6">Profile Settings</h1>

            <Tabs value="0">
                <TabList>
                    <Tab value="0">Profile Information</Tab>
                    <Tab value="1">Update Password</Tab>
                </TabList>
                <TabPanels>
                    <!-- Profile Information Tab -->
                    <TabPanel value="0">
                        <form @submit.prevent="updateProfile" class="space-y-6 mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Name</label>
                                    <InputText id="name" v-model="profileForm.name" class="w-full" />
                                    <p v-if="profileForm.errors.name" class="text-red-500 text-sm mt-1">{{
                                        profileForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Email</label>
                                    <InputText id="email" type="email" v-model="profileForm.email" class="w-full" />
                                    <p v-if="profileForm.errors.email" class="text-red-500 text-sm mt-1">{{
                                        profileForm.errors.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="username"
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Username</label>
                                    <InputText id="username" v-model="profileForm.username" class="w-full" />
                                    <p v-if="profileForm.errors.username" class="text-red-500 text-sm mt-1">{{
                                        profileForm.errors.username }}</p>
                                </div>
                                <div>
                                    <label for="title"
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Job
                                        Title</label>
                                    <InputText id="title" v-model="profileForm.title" class="w-full"
                                        placeholder="e.g. Senior Developer" />
                                    <p v-if="profileForm.errors.title" class="text-red-500 text-sm mt-1">{{
                                        profileForm.errors.title }}</p>
                                </div>
                            </div>

                            <div>
                                <label for="bio"
                                    class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Bio</label>
                                <Textarea id="bio" v-model="profileForm.bio" rows="5" class="w-full" />
                                <p v-if="profileForm.errors.bio" class="text-red-500 text-sm mt-1">{{
                                    profileForm.errors.bio }}</p>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" label="Save Changes" icon="pi pi-save"
                                    :loading="profileForm.processing" />
                            </div>
                        </form>
                    </TabPanel>

                    <!-- Password Tab -->
                    <TabPanel value="1">
                        <form @submit.prevent="updatePassword" class="space-y-6 mt-6">
                            <div>
                                <label for="current_password"
                                    class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Current
                                    Password</label>
                                <InputText id="current_password" type="password" v-model="passwordForm.current_password"
                                    class="w-full" />
                                <p v-if="passwordForm.errors.current_password" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.current_password }}</p>
                            </div>
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">New
                                    Password</label>
                                <InputText id="password" type="password" v-model="passwordForm.password"
                                    class="w-full" />
                                <p v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.password }}</p>
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Confirm
                                    Password</label>
                                <InputText id="password_confirmation" type="password"
                                    v-model="passwordForm.password_confirmation" class="w-full" />
                                <p v-if="passwordForm.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{
                                    passwordForm.errors.password_confirmation }}</p>
                            </div>
                            <div class="flex justify-end">
                                <Button type="submit" label="Update Password" icon="pi pi-lock"
                                    :loading="passwordForm.processing" severity="secondary"
                                    class="bg-graphite-800 border-graphite-800 text-white hover:bg-graphite-900" />
                            </div>
                        </form>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </DashboardLayout>
</template>