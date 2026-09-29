<script setup lang="ts">
import { Button, InputText, Textarea, FileUpload } from 'primevue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    settings: {
        id: number;
        title: string | null;
        logo: string | null;
        avatar: string | null;
        cv_link: string | null;
        name: Record<string, string>;
        job_title: Record<string, string>;
        bio: Record<string, string>;
    };
    locales: Record<string, string>;
}>();

const activeLocale = ref(Object.keys(props.locales)[0]);

const form = useForm({
    title: props.settings.title,
    name: props.settings.name || {},
    job_title: props.settings.job_title || {},
    bio: props.settings.bio || {},
    logo: null as File | null,
    avatar: null as File | null,
    cv_link: null as File | null,
});

const onLogoSelect = (e: any) => { form.logo = e.files[0]; };
const onAvatarSelect = (e: any) => { form.avatar = e.files[0]; };
const onCvSelect = (e: any) => { form.cv_link = e.files[0]; };

const submit = () => {
    confirm.require({
        message: 'Are you sure you want to save these site settings?',
        header: 'Save Confirmation',
        icon: 'pi pi-save',
        acceptLabel: 'Yes, Save',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-success',
        accept: () => {
            form.post(route('settings.update'), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Settings updated successfully!',
                        life: 3000
                    });
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'There was an error updating the settings. Please check the form.',
                        life: 3000
                    });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Site Settings" />

    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-8 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <h1 class="text-xl font-semibold text-graphite-800 dark:text-white mb-6">Main Info & Settings</h1>

            <form @submit.prevent="submit" class="space-y-8">

                <!-- Language Switcher -->
                <div class="flex gap-2 border-b border-graphite-200 dark:border-graphite-800 pb-4">
                    <Button v-for="(label, code) in locales" :key="code" :label="label" @click="activeLocale = code"
                        :text="activeLocale !== code" :severity="activeLocale === code ? 'primary' : 'secondary'"
                        size="small" />
                </div>

                <!-- Non-translatable fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Site
                            Title</label>
                        <InputText v-model="form.title" class="w-full" />
                    </div>
                </div>

                <!-- Translatable Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(label, code) in locales" :key="code" v-show="activeLocale === code">
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Name ({{
                            label }})</label>
                        <InputText v-model="form.name[code]" class="w-full" />
                    </div>

                    <div v-for="(label, code) in locales" :key="code" v-show="activeLocale === code">
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Job Title
                            ({{ label }})</label>
                        <InputText v-model="form.job_title[code]" class="w-full" />
                    </div>
                </div>

                <div>
                    <div v-for="(label, code) in locales" :key="code" v-show="activeLocale === code">
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Bio ({{
                            label }})</label>
                        <Textarea v-model="form.bio[code]" rows="5" class="w-full" />
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Logo</label>
                        <FileUpload mode="basic" name="logo" accept="image/*" :maxFileSize="2000000"
                            @select="onLogoSelect" chooseLabel="Upload Logo" class="w-full" />
                        <img v-if="settings.logo" :src="settings.logo" class="mt-2 h-12 w-auto object-contain" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Avatar</label>
                        <FileUpload mode="basic" name="avatar" accept="image/*" :maxFileSize="2000000"
                            @select="onAvatarSelect" chooseLabel="Upload Avatar" class="w-full" />
                        <img v-if="settings.avatar" :src="settings.avatar"
                            class="mt-2 h-12 w-12 rounded-full object-cover" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">CV / Resume
                        (PDF/Doc)</label>
                    <FileUpload mode="basic" name="cv_link" accept=".pdf,.doc,.docx" :maxFileSize="5000000"
                        @select="onCvSelect" chooseLabel="Upload CV" class="w-full" />
                    <a v-if="settings.cv_link" :href="settings.cv_link" target="_blank"
                        class="mt-2 inline-block text-sm text-iris-500 hover:underline">
                        View Current CV
                    </a>
                </div>

                <div class="flex justify-end">
                    <Button type="submit" label="Save Settings" :loading="form.processing" />
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>