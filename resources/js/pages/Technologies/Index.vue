<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, DataTable, Column, Dialog, InputText, Textarea, Tabs, TabList, Tab, TabPanels, TabPanel } from 'primevue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    technologies: Array<any>;
    locales?: Record<string, string>;
}>();

// Safe fallback for locales
const locales = props.locales || { en: 'English' };
const firstLocale = Object.keys(locales)[0];

// Dialog States
const dialogVisible = ref(false);
const viewDialogVisible = ref(false);
const isEditing = ref(false);
const viewTechnologyData = ref<any>(null);

// Tab State
const activeLocale = ref(firstLocale);

// Form
const form = useForm({
    id: null as number | null,
    vue_iconify: '',
    svg_icon: '',
    name: {} as Record<string, string>,
    short_description: {} as Record<string, string>,
});

// SVG Validation
const isSvgValid = computed(() => {
    const svg = form.svg_icon;
    if (typeof svg !== 'string' || svg.trim() === '') return true; // Optional field

    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svg, "image/svg+xml");
        // Safe check for parsererror and documentElement
        return !doc.querySelector('parsererror') && doc.documentElement?.tagName.toLowerCase() === 'svg';
    } catch (e) {
        return false;
    }
});

const resetForm = () => {
    form.reset();
    form.name = {};
    form.short_description = {};
    form.clearErrors();
};

const openCreateDialog = () => {
    isEditing.value = false;
    resetForm();
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const openEditDialog = (technology: any) => {
    isEditing.value = true;
    form.id = technology.id;
    form.vue_iconify = technology.vue_iconify || '';
    form.svg_icon = technology.svg_icon || '';
    form.name = technology.name || {};
    form.short_description = technology.short_description || {};
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const openViewDialog = (technology: any) => {
    viewTechnologyData.value = technology;
    viewDialogVisible.value = true;
};

const saveTechnology = () => {
    if (!isSvgValid.value) {
        toast.add({
            severity: 'error',
            summary: 'Validation Error',
            detail: 'Please fix the invalid SVG code before saving.',
            life: 3000
        });
        return;
    }

    confirm.require({
        message: isEditing.value ? 'Save changes to this technology?' : 'Create this technology?',
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
                    toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: `Technology ${isEditing.value ? 'updated' : 'created'} successfully.`,
                        life: 3000
                    });
                },
                onError: (errors: any) => {
                    // Safely extract error messages
                    const errorMessages = Object.values(errors || {}).flat().join(' ');
                    toast.add({
                        severity: 'error',
                        summary: 'Validation Failed',
                        detail: errorMessages || 'An unknown error occurred. Please check the form.',
                        life: 5000
                    });
                }
            };

            if (isEditing.value && form.id) {
                form.put(route('technologies.update', form.id), options);
            } else {
                form.post(route('technologies.store'), options);
            }
        }
    });
};

const deleteTechnology = (technology: any) => {
    confirm.require({
        message: `Are you sure you want to delete this technology?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Yes, Delete',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('technologies.destroy', technology.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Deleted',
                        detail: 'Technology deleted successfully.',
                        life: 3000
                    });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Technologies" />

    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-6 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-graphite-800 dark:text-white">Technologies Management</h1>
                <Button label="Add New Technology" icon="pi pi-plus" @click="openCreateDialog" />
            </div>

            <DataTable :value="props.technologies" tableStyle="min-width: 50rem">
                <Column field="id" header="ID" style="width: 5%"></Column>
                <Column header="Icon" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <span v-if="slotProps.data.vue_iconify" class="text-sm text-graphite-500">{{
                                slotProps.data.vue_iconify }}</span>
                            <div v-else-if="slotProps.data.svg_icon" v-html="slotProps.data.svg_icon"
                                class="w-6 h-6 text-graphite-600 dark:text-graphite-300"></div>
                            <span v-else class="text-graphite-400">-</span>
                        </div>
                    </template>
                </Column>
                <Column header="Name">
                    <template #body="slotProps">
                        <span class="font-medium text-graphite-800 dark:text-white">{{ slotProps.data.name?.en || 'N/A'
                            }}</span>
                    </template>
                </Column>
                <Column header="Short Description">
                    <template #body="slotProps">
                        <span class="text-sm text-graphite-500">{{ slotProps.data.short_description?.en || '-' }}</span>
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
                                @click="deleteTechnology(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Technology' : 'Add New Technology'" modal
            class="p-fluid w-[40rem]">
            <div class="space-y-6 pt-4">
                <!-- Language Switcher -->
                <Tabs :value="activeLocale">
                    <TabList>
                        <Tab v-for="(label, code) in locales" :key="code" :value="code">
                            {{ label }}
                        </Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel v-for="(label, code) in locales" :key="code" :value="code">
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Name
                                        ({{ label }})</label>
                                    <InputText v-model="form.name[code]" class="w-full" />
                                    <p v-if="form.errors[`name.${code}`]" class="text-red-500 text-sm mt-1">{{
                                        form.errors[`name.${code}`] }}</p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Short
                                        Description ({{ label }})</label>
                                    <Textarea v-model="form.short_description[code]" rows="3" class="w-full" />
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>

                <!-- Non-translatable fields -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Vue
                            Iconify Name
                            (Optional)</label>
                        <InputText v-model="form.vue_iconify" class="w-full" placeholder="e.g., mdi:language-php" />
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
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" text />
                <Button label="Save" icon="pi pi-check" @click="saveTechnology" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- View Details Dialog -->
        <Dialog v-model:visible="viewDialogVisible" header="Technology Details" modal class="p-fluid w-[40rem]">
            <div v-if="viewTechnologyData" class="space-y-6 pt-4">
                <div class="flex items-center gap-8 pb-4 border-b border-graphite-200 dark:border-graphite-700">
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-2">Vue Iconify</p>
                        <p class="text-sm font-medium text-graphite-800 dark:text-white">{{
                            viewTechnologyData.vue_iconify ||
                            'N/A'
                            }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-2">SVG Icon</p>
                        <div v-if="viewTechnologyData.svg_icon" v-html="viewTechnologyData.svg_icon"
                            class="w-8 h-8 inline-block text-graphite-700 dark:text-white"></div>
                        <p v-else class="text-sm font-medium text-graphite-800 dark:text-white">N/A</p>
                    </div>
                </div>

                <div v-for="(label, code) in locales" :key="code" class="space-y-2">
                    <h3 class="font-semibold text-iris-500 text-sm uppercase">{{ label }}</h3>
                    <div>
                        <p class="text-xs text-graphite-500">Name</p>
                        <p class="font-medium text-graphite-800 dark:text-white">{{ viewTechnologyData.name?.[code] ||
                            'N/A'
                            }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-graphite-500">Short Description</p>
                        <p class="text-sm text-graphite-700 dark:text-graphite-300">{{
                            viewTechnologyData.short_description?.[code]
                            || 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="viewDialogVisible = false" text />
            </template>
        </Dialog>
    </DashboardLayout>
</template>