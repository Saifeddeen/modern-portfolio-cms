<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, DataTable, Column, Dialog, InputText, Textarea, Tabs, TabList, Tab, TabPanels, TabPanel, FileUpload } from 'primevue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    services: Array<any>;
    locales?: Record<string, string>;
}>();

// Safe fallback for locales
const locales = props.locales || { en: 'English' };
const firstLocale = Object.keys(locales)[0];

// Dialog States
const dialogVisible = ref(false);
const viewDialogVisible = ref(false);
const isEditing = ref(false);
const viewServiceData = ref<any>(null);
const heroImagePreview = ref<string | null>(null);

// Tab State
const activeLocale = ref(firstLocale);

// Form
const form = useForm({
    id: null as number | null,
    vue_iconify: '',
    svg_icon: '',
    hero_image: null as File | null,
    name: {} as Record<string, string>,
    short_description: {} as Record<string, string>,
    full_description: {} as Record<string, string>,
});

// SVG Validation
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

const onHeroImageSelect = (event: any) => {
    const file = event.files ? event.files[0] : event.target?.files?.[0];
    if (file) {
        form.hero_image = file;
        heroImagePreview.value = URL.createObjectURL(file);
    }
};

const resetForm = () => {
    form.reset();
    form.name = {};
    form.short_description = {};
    form.full_description = {};
    form.hero_image = null;
    heroImagePreview.value = null;
    form.clearErrors();
};

const openCreateDialog = () => {
    isEditing.value = false;
    resetForm();
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const openEditDialog = (service: any) => {
    isEditing.value = true;
    form.id = service.id;
    form.vue_iconify = service.vue_iconify || '';
    form.svg_icon = service.svg_icon || '';
    form.hero_image = null;
    heroImagePreview.value = service.hero_image || null;
    form.name = service.name || {};
    form.short_description = service.short_description || {};
    form.full_description = service.full_description || {};
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const openViewDialog = (service: any) => {
    viewServiceData.value = service;
    viewDialogVisible.value = true;
};

const saveService = () => {
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
        message: isEditing.value ? 'Save changes to this service?' : 'Create this service?',
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
                        detail: `Service ${isEditing.value ? 'updated' : 'created'} successfully.`,
                        life: 3000
                    });
                },
                onError: (errors: any) => {
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
                form.transform((data) => ({
                    ...data,
                    _method: 'PUT',
                })).post(route('services.update', form.id), options);
            } else {
                form.post(route('services.store'), options);
            }
        }
    });
};

const deleteService = (service: any) => {
    confirm.require({
        message: `Are you sure you want to delete this service?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Yes, Delete',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('services.destroy', service.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: 'success',
                        summary: 'Deleted',
                        detail: 'Service deleted successfully.',
                        life: 3000
                    });
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Services" />

    <DashboardLayout>
        <div class="bg-white dark:bg-graphite-950 p-6 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-graphite-800 dark:text-white">Services Management</h1>
                <Button label="Add New Service" icon="pi pi-plus" @click="openCreateDialog" />
            </div>

            <DataTable :value="props.services" tableStyle="min-width: 50rem">
                <Column field="id" header="ID" style="width: 5%"></Column>

                <Column header="Hero Image" style="width: 12%">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <img v-if="slotProps.data.hero_image" :src="slotProps.data.hero_image" alt="Hero Image" class="w-12 h-12 object-cover rounded-lg border border-graphite-200 dark:border-graphite-700" />
                            <span v-else class="text-graphite-400 text-xs">No Image</span>
                        </div>
                    </template>
                </Column>

                <Column header="Icon" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <span v-if="slotProps.data.vue_iconify" class="text-sm text-graphite-500 font-mono">{{ slotProps.data.vue_iconify }}</span>
                            <div v-else-if="slotProps.data.svg_icon" v-html="slotProps.data.svg_icon" class="w-6 h-6 text-graphite-600 dark:text-graphite-300"></div>
                            <span v-else class="text-graphite-400">-</span>
                        </div>
                    </template>
                </Column>

                <Column header="Name">
                    <template #body="slotProps">
                        <span class="font-medium text-graphite-800 dark:text-white">{{ slotProps.data.name?.en || 'N/A' }}</span>
                    </template>
                </Column>

                <Column header="Short Description">
                    <template #body="slotProps">
                        <span class="text-sm text-graphite-500 line-clamp-2">{{ slotProps.data.short_description?.en || '-' }}</span>
                    </template>
                </Column>

                <Column header="Actions" style="width: 15%">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <Button icon="pi pi-eye" severity="secondary" text rounded @click="openViewDialog(slotProps.data)" />
                            <Button icon="pi pi-pencil" severity="info" text rounded @click="openEditDialog(slotProps.data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded @click="deleteService(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Service' : 'Add New Service'" modal class="p-fluid w-[44rem]">
            <div class="space-y-6 pt-4">
                <!-- Translatable Fields Switcher -->
                <Tabs :value="activeLocale">
                    <TabList>
                        <Tab v-for="(label, code) in locales" :key="code" :value="code">
                            {{ label }}
                        </Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel v-for="(label, code) in locales" :key="code" :value="code">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Name ({{ label }}) *</label>
                                    <InputText v-model="form.name[code]" class="w-full" placeholder="Service Name" />
                                    <p v-if="form.errors[`name.${code}`]" class="text-red-500 text-sm mt-1">{{ form.errors[`name.${code}`] }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Short Description ({{ label }}) *</label>
                                    <Textarea v-model="form.short_description[code]" rows="2" class="w-full" placeholder="Brief summary of the service" />
                                    <p v-if="form.errors[`short_description.${code}`]" class="text-red-500 text-sm mt-1">{{ form.errors[`short_description.${code}`] }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Full Description ({{ label }}) (Optional)</label>
                                    <Textarea v-model="form.full_description[code]" rows="4" class="w-full" placeholder="Detailed service description" />
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>

                <!-- Non-translatable fields -->
                <div class="space-y-4 pt-2 border-t border-graphite-200 dark:border-graphite-800">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Hero Image (Optional)</label>
                        <FileUpload mode="basic" chooseLabel="Choose Image" accept="image/*" class="p-button-outlined w-full mb-3" @select="onHeroImageSelect" />
                        <div v-if="heroImagePreview" class="relative w-32 h-32 rounded-lg border border-graphite-200 dark:border-graphite-700 overflow-hidden bg-graphite-50 dark:bg-graphite-800 flex items-center justify-center">
                            <img :src="heroImagePreview" alt="Hero Preview" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Vue Iconify Name (Optional)</label>
                            <InputText v-model="form.vue_iconify" class="w-full font-mono text-sm" placeholder="e.g., mdi:rocket-launch" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Raw SVG Code (Optional)</label>
                            <Textarea v-model="form.svg_icon" rows="3" class="w-full font-mono text-sm" placeholder="<svg>...</svg>" />

                            <p v-if="!isSvgValid" class="text-red-500 text-sm mt-1">
                                Invalid SVG code. Please ensure it starts with <code>&lt;svg&gt;</code> and is valid XML.
                            </p>

                            <div v-if="form.svg_icon && isSvgValid" class="mt-2 p-3 border border-graphite-200 dark:border-graphite-700 rounded-lg flex items-center justify-center bg-graphite-50 dark:bg-graphite-800">
                                <div v-html="form.svg_icon" class="w-8 h-8 text-graphite-700 dark:text-white"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" text />
                <Button label="Save" icon="pi pi-check" @click="saveService" :loading="form.processing" />
            </template>
        </Dialog>

        <!-- View Details Dialog -->
        <Dialog v-model:visible="viewDialogVisible" header="Service Details" modal class="p-fluid w-[44rem]">
            <div v-if="viewServiceData" class="space-y-6 pt-4">
                <div v-if="viewServiceData.hero_image" class="w-full h-48 rounded-xl overflow-hidden border border-graphite-200 dark:border-graphite-700 bg-graphite-50 dark:bg-graphite-800">
                    <img :src="viewServiceData.hero_image" alt="Hero Image" class="w-full h-full object-cover" />
                </div>

                <div class="flex items-center gap-8 pb-4 border-b border-graphite-200 dark:border-graphite-700">
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-1">Vue Iconify</p>
                        <p class="text-sm font-medium font-mono text-graphite-800 dark:text-white">{{ viewServiceData.vue_iconify || 'N/A' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-graphite-500 mb-1">SVG Icon</p>
                        <div v-if="viewServiceData.svg_icon" v-html="viewServiceData.svg_icon" class="w-8 h-8 inline-block text-graphite-700 dark:text-white"></div>
                        <p v-else class="text-sm font-medium text-graphite-800 dark:text-white">N/A</p>
                    </div>
                </div>

                <div v-for="(label, code) in locales" :key="code" class="space-y-3 p-4 rounded-lg bg-graphite-50 dark:bg-graphite-900/50 border border-graphite-100 dark:border-graphite-800">
                    <h3 class="font-semibold text-iris-500 text-sm uppercase tracking-wide">{{ label }}</h3>
                    <div>
                        <p class="text-xs text-graphite-500">Name</p>
                        <p class="font-medium text-graphite-800 dark:text-white">{{ viewServiceData.name?.[code] || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-graphite-500">Short Description</p>
                        <p class="text-sm text-graphite-700 dark:text-graphite-300">{{ viewServiceData.short_description?.[code] || 'N/A' }}</p>
                    </div>
                    <div v-if="viewServiceData.full_description?.[code]">
                        <p class="text-xs text-graphite-500">Full Description</p>
                        <p class="text-sm text-graphite-700 dark:text-graphite-300 whitespace-pre-line">{{ viewServiceData.full_description[code] }}</p>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" @click="viewDialogVisible = false" text />
            </template>
        </Dialog>
    </DashboardLayout>
</template>
