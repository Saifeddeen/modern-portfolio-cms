<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, Tag, FileUpload, Dialog, useToast } from 'primevue';
import { ref } from 'vue';

const toast = useToast();

const props = defineProps<{
    project: {
        id: number;
        title: Record<string, string>;
        subtitle: Record<string, string>;
        owner: string | null;
        short_description: Record<string, string>;
        long_description: Record<string, string>;
        hero_image: string | null;
        gallery_images: Array<{ id: number, url: string }>;
        github_link: string | null;
        project_link: string | null;
        start_date: string | null;
        project_date: string | null;
        is_featured: boolean;
        is_published: boolean;
        services: Array<any>;
        skills: Array<any>;
        technologies: Array<any>;
    };
    locales: Record<string, string>;
}>();

// Image Preview Modal State
const previewDialog = ref(false);
const selectedImage = ref<string | null>(null);

const openImagePreview = (url: string) => {
    selectedImage.value = url;
    previewDialog.value = true;
};

// Gallery Upload Logic
const fileUploadRef = ref(); // Reference to clear the FileUpload component later

const uploadGallery = (event: any) => {
    const files = event.files;
    if (!files || files.length === 0) {
        toast.add({ severity: 'info', summary: 'Info', detail: 'Please select images first.', life: 3000 });
        return;
    }

    // Create a one-off form for the files
    const galleryForm = useForm({
        images: files,
    });

    galleryForm.post(route('projects.gallery.store', props.project.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Images uploaded successfully.', life: 3000 });
            // FIX 1: Clear the selected files from the UI after upload
            fileUploadRef.value?.clear();
        },
        onError: (errors: any) => {
            const errorMessages = Object.values(errors || {}).flat().join(' ');
            toast.add({ severity: 'error', summary: 'Upload Failed', detail: errorMessages || 'An error occurred.', life: 5000 });
            fileUploadRef.value?.clear();
        }
    });
};
</script>

<template>

    <Head title="Project Details" />

    <DashboardLayout>
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Back Button -->
            <div>
                <Link :href="route('projects.index')">
                    <Button icon="pi pi-arrow-left" label="Back to Projects" text />
                </Link>
            </div>

            <!-- Header & Status -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-graphite-800 dark:text-white">
                        {{ project.title?.en || 'Untitled Project' }}
                    </h1>
                    <p class="text-lg text-graphite-500 mt-1">{{ project.subtitle?.en }}</p>
                </div>
                <div class="flex gap-2">
                    <Tag v-if="project.is_published" severity="success" value="Published" icon="pi pi-check" />
                    <Tag v-else severity="secondary" value="Draft" icon="pi pi-pencil" />
                    <Tag v-if="project.is_featured" severity="warn" value="Featured" icon="pi pi-star" />
                </div>
            </div>

            <!-- Hero Image -->
            <div v-if="project.hero_image"
                class="w-full h-96 rounded-2xl overflow-hidden border border-graphite-200 dark:border-graphite-800 shadow-soft">
                <img :src="project.hero_image" alt="Hero Image" class="w-full h-full object-cover" />
            </div>

            <!-- Meta Info Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white dark:bg-graphite-950 p-6 rounded-xl border border-graphite-200 dark:border-graphite-800">
                <div>
                    <p class="text-xs uppercase text-graphite-400 mb-1">Owner</p>
                    <p class="font-medium text-graphite-800 dark:text-white">{{ project.owner || 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-graphite-400 mb-1">Start Date</p>
                    <p class="font-medium text-graphite-800 dark:text-white">{{ project.start_date || 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-graphite-400 mb-1">Project Date</p>
                    <p class="font-medium text-graphite-800 dark:text-white">{{ project.project_date || 'N/A' }}</p>
                </div>
                <div v-if="project.github_link">
                    <p class="text-xs uppercase text-graphite-400 mb-1">GitHub</p>
                    <a :href="project.github_link" target="_blank" class="text-iris-500 hover:underline break-all">{{
                        project.github_link }}</a>
                </div>
                <div v-if="project.project_link">
                    <p class="text-xs uppercase text-graphite-400 mb-1">Live URL</p>
                    <a :href="project.project_link" target="_blank" class="text-iris-500 hover:underline break-all">{{
                        project.project_link }}</a>
                </div>
            </div>

            <!-- Relations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-graphite-950 p-6 rounded-xl border border-graphite-200 dark:border-graphite-800">
                    <h3 class="text-sm font-semibold text-graphite-500 uppercase mb-4">Services</h3>
                    <div class="flex flex-wrap gap-2">
                        <Tag v-for="item in project.services" :key="item.id" :value="item.name?.en" severity="info" />
                        <span v-if="!project.services.length" class="text-sm text-graphite-400">None</span>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-graphite-950 p-6 rounded-xl border border-graphite-200 dark:border-graphite-800">
                    <h3 class="text-sm font-semibold text-graphite-500 uppercase mb-4">Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        <Tag v-for="item in project.skills" :key="item.id" :value="item.title?.en" severity="success" />
                        <span v-if="!project.skills.length" class="text-sm text-graphite-400">None</span>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-graphite-950 p-6 rounded-xl border border-graphite-200 dark:border-graphite-800">
                    <h3 class="text-sm font-semibold text-graphite-500 uppercase mb-4">Technologies</h3>
                    <div class="flex flex-wrap gap-2">
                        <Tag v-for="item in project.technologies" :key="item.id" :value="item.name?.en"
                            severity="warn" />
                        <span v-if="!project.technologies.length" class="text-sm text-graphite-400">None</span>
                    </div>
                </div>
            </div>

            <!-- Descriptions (All Languages) -->
            <div
                class="bg-white dark:bg-graphite-950 p-8 rounded-xl border border-graphite-200 dark:border-graphite-800 space-y-6">
                <h2 class="text-xl font-semibold text-graphite-800 dark:text-white">Content Details</h2>

                <div v-for="(label, code) in locales" :key="code"
                    class="space-y-4 pb-6 border-b border-graphite-100 dark:border-graphite-800 last:border-0 last:pb-0">
                    <h3 class="text-sm font-bold text-iris-500 uppercase">{{ label }}</h3>

                    <div>
                        <p class="text-xs uppercase text-graphite-400 mb-1">Short Description</p>
                        <p class="text-graphite-700 dark:text-graphite-300 whitespace-pre-line">{{
                            project.short_description?.[code] || 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-graphite-400 mb-1">Long Description</p>
                        <p class="text-graphite-700 dark:text-graphite-300 whitespace-pre-line">{{
                            project.long_description?.[code] || 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Image Gallery Section (Moved to Bottom) -->
            <div
                class="bg-white dark:bg-graphite-950 p-8 rounded-xl border border-graphite-200 dark:border-graphite-800 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-graphite-800 dark:text-white">Image Gallery</h2>
                </div>

                <!-- Upload New Images -->
                <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Upload
                            Multiple Images</label>
                        <FileUpload ref="fileUploadRef" mode="advanced" accept="image/*" :maxFileSize="4000000"
                            :multiple="true" chooseLabel="Select Images" uploadLabel="Upload" cancelLabel="Clear"
                            :auto="false" :customUpload="true" @uploader="uploadGallery" class="w-full">
                            <template #empty>
                                <p class="text-center text-graphite-400 py-4">Drag and drop files here to upload.</p>
                            </template>
                        </FileUpload>
                    </div>
                </div>

                <!-- Existing Gallery Images -->
                <div v-if="project.gallery_images && project.gallery_images.length > 0"
                    class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-graphite-100 dark:border-graphite-800">
                    <!-- FIX 2: Added @click to open preview modal -->
                    <div v-for="(img, index) in project.gallery_images" :key="index"
                        class="aspect-square rounded-xl overflow-hidden border border-graphite-200 dark:border-graphite-800 shadow-soft relative group cursor-pointer"
                        @click="openImagePreview(img.url)">
                        <img :src="img.url" alt="Gallery Image"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        <div
                            class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <i class="pi pi-search text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <div v-else
                    class="text-center py-8 text-graphite-400 border-2 border-dashed border-graphite-200 dark:border-graphite-800 rounded-xl">
                    No gallery images yet.
                </div>
            </div>
        </div>

        <!-- Image Preview Modal (Lightbox) -->
        <Dialog v-model:visible="previewDialog" modal header="Image Preview"
            :style="{ width: '80vw', maxWidth: '1200px' }" class="p-fluid">
            <div class="flex justify-center items-center bg-graphite-900 rounded-lg p-4">
                <img v-if="selectedImage" :src="selectedImage" alt="Full size preview"
                    class="max-w-full max-h-[75vh] object-contain" />
            </div>
            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="previewDialog = false" text />
            </template>
        </Dialog>

    </DashboardLayout>
</template>