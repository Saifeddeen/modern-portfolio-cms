<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, DataTable, Column, Dialog, InputText, Textarea, Checkbox, useToast, useConfirm } from 'primevue';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    links: Array<any>;
}>();

const dialogVisible = ref(false);
const viewDialogVisible = ref(false);
const isEditing = ref(false);
const viewLinkData = ref<any>(null);

const form = useForm({
    id: null as number | null,
    name: '',
    link: '',
    vue_iconify: '',
    svg_icon: '',
    is_active: true,
});

const isSvgValid = computed(() => {
    const svg = form.svg_icon;
    if (typeof svg !== 'string' || svg.trim() === '') return true;
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svg, "image/svg+xml");
        return !doc.querySelector('parsererror') && doc.documentElement?.tagName.toLowerCase() === 'svg';
    } catch (e) {
        return false;
    }
});

const resetForm = () => {
    form.reset();
    form.is_active = true;
    form.clearErrors();
};

const openCreateDialog = () => {
    isEditing.value = false;
    resetForm();
    dialogVisible.value = true;
};

const openEditDialog = (link: any) => {
    isEditing.value = true;
    form.id = link.id;
    form.name = link.name;
    form.link = link.link;
    form.vue_iconify = link.vue_iconify || '';
    form.svg_icon = link.svg_icon || '';
    form.is_active = link.is_active;
    dialogVisible.value = true;
};

const openViewDialog = (link: any) => {
    viewLinkData.value = link;
    viewDialogVisible.value = true;
};

const saveLink = () => {
    if (!isSvgValid.value) {
        toast.add({ severity: 'error', summary: 'Validation Error', detail: 'Please fix the invalid SVG code before saving.', life: 3000 });
        return;
    }

    confirm.require({
        message: isEditing.value ? 'Save changes to this social link?' : 'Create this social link?',
        header: 'Save Confirmation',
        icon: 'pi pi-save',
        acceptLabel: 'Yes, Save',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-success',
        accept: () => {
            const options = {
                preserveScroll: true,
                onSuccess: () => {
                    dialogVisible.value = false;
                    toast.add({ severity: 'success', summary: 'Success', detail: `Social link ${isEditing.value ? 'updated' : 'created'} successfully.`, life: 3000 });
                },
                onError: (errors: any) => {
                    const errorMessages = Object.values(errors || {}).flat().join(' ');
                    toast.add({ severity: 'error', summary: 'Validation Failed', detail: errorMessages || 'Check the form.', life: 5000 });
                }
            };

            if (isEditing.value) {
                form.put(route('social-links.update', form.id), options);
            } else {
                form.post(route('social-links.store'), options);
            }
        }
    });
};

const deleteLink = (link: any) => {
    confirm.require({
        message: `Are you sure you want to delete this social link?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Yes, Delete',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('social-links.destroy', link.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Deleted', detail: 'Social link deleted successfully.', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Social Links" />

    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-6 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-graphite-800 dark:text-white">Social Links Management</h1>
                <Button label="Add New Link" icon="pi pi-plus" @click="openCreateDialog" />
            </div>

            <DataTable :value="props.links" tableStyle="min-width: 50rem">
                <Column field="id" header="ID" style="width: 5%"></Column>
                <Column header="Icon" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <span v-if="slotProps.data.vue_iconify" class="text-sm text-graphite-500 font-mono">{{
                                slotProps.data.vue_iconify }}</span>
                            <div v-else-if="slotProps.data.svg_icon" v-html="slotProps.data.svg_icon"
                                class="w-6 h-6 text-graphite-600 dark:text-graphite-300"></div>
                            <span v-else class="text-graphite-400">-</span>
                        </div>
                    </template>
                </Column>
                <Column field="name" header="Name" style="width: 20%"></Column>
                <Column header="Link" style="width: 35%">
                    <template #body="slotProps">
                        <a :href="slotProps.data.link" target="_blank" class="text-iris-500 hover:underline text-sm">{{
                            slotProps.data.link }}</a>
                    </template>
                </Column>
                <Column header="Status" style="width: 10%">
                    <template #body="slotProps">
                        <span
                            :class="['px-2 py-1 text-xs rounded-full', slotProps.data.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                            {{ slotProps.data.is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </template>
                </Column>
                <Column header="Actions" style="width: 15%">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button icon="pi pi-eye" severity="secondary" text rounded
                                @click="openViewDialog(slotProps.data)" />
                            <Button icon="pi pi-pencil" severity="info" text rounded
                                @click="openEditDialog(slotProps.data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded
                                @click="deleteLink(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Social Link' : 'Add New Social Link'" modal
            class="p-fluid w-[40rem]">
            <div class="space-y-6 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Platform
                            Name
                            *</label>
                        <InputText v-model="form.name" class="w-full" placeholder="e.g., GitHub" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">URL
                            *</label>
                        <InputText v-model="form.link" class="w-full" placeholder="https://github.com/username" />
                        <p v-if="form.errors.link" class="text-red-500 text-sm mt-1">{{ form.errors.link }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 pt-4 border-t border-graphite-200 dark:border-graphite-700">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Vue
                            Iconify Name
                            (Optional)</label>
                        <InputText v-model="form.vue_iconify" class="w-full" placeholder="e.g., mdi:github" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Raw SVG
                            Code
                            (Optional)</label>
                        <Textarea v-model="form.svg_icon" rows="4" class="w-full font-mono text-sm"
                            placeholder="<svg>...</svg>" />
                        <p v-if="!isSvgValid" class="text-red-500 text-sm mt-1">
                            Invalid SVG code. Please ensure it starts with <code>&lt;svg&gt;</code> and is valid XML.
                        </p>
                        <div v-if="form.svg_icon && isSvgValid"
                            class="mt-4 p-4 border border-graphite-200 dark:border-graphite-700 rounded-lg flex items-center justify-center bg-graphite-50 dark:bg-graphite-800">
                            <div v-html="form.svg_icon" class="w-8 h-8 text-graphite-700 dark:text-white"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-graphite-200 dark:border-graphite-700">
                    <Checkbox v-model="form.is_active" :binary="true" inputId="active" />
                    <label for="active" class="text-sm text-graphite-600 dark:text-graphite-300">Active (Visible on
                        portfolio)</label>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" text />
                <Button label="Save" icon="pi pi-check" @click="saveLink" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- View Details Dialog -->
        <Dialog v-model:visible="viewDialogVisible" header="Social Link Details" modal class="p-fluid w-[40rem]">
            <div v-if="viewLinkData" class="space-y-6 pt-4">
                <div class="flex items-center gap-8 pb-4 border-b border-graphite-200 dark:border-graphite-700">
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-1">Vue Iconify</p>
                        <p class="text-sm font-medium font-mono text-graphite-800 dark:text-white">{{
                            viewLinkData.vue_iconify
                            || 'N/A' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-1">SVG Icon</p>
                        <div v-if="viewLinkData.svg_icon" v-html="viewLinkData.svg_icon"
                            class="w-8 h-8 inline-block text-graphite-700 dark:text-white"></div>
                        <p v-else class="text-sm font-medium text-graphite-800 dark:text-white">N/A</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-graphite-500">Platform Name</p>
                    <p class="font-medium text-graphite-800 dark:text-white">{{ viewLinkData.name }}</p>
                </div>
                <div>
                    <p class="text-xs text-graphite-500">URL</p>
                    <a :href="viewLinkData.link" target="_blank"
                        class="font-medium text-iris-500 hover:underline break-all">{{
                            viewLinkData.link }}</a>
                </div>
                <div>
                    <p class="text-xs text-graphite-500">Status</p>
                    <span
                        :class="['px-2 py-1 text-xs rounded-full', viewLinkData.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                        {{ viewLinkData.is_active ? 'Active' : 'Hidden' }}
                    </span>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="viewDialogVisible = false" text />
            </template>
        </Dialog>
    </DashboardLayout>
</template>